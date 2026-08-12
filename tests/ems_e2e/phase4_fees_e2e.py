#!/usr/bin/env python3
"""
Phase 4 (Fees) contract E2E — exercises document.md §3.7 against the live backend
at http://127.0.0.1:8765/api/ems. Standalone: registers a fresh school, invites a
bursar + a registrar, seeds a class and students, then walks fee structures,
awards, invoice pricing (award application, no-compounding, floor-at-zero),
instalments, payments, provider checkout, refunds (separation of duties),
reconciliation, the financial report and the student ledger — asserting exact
kobo figures so the backend matches the mock's money arithmetic to the byte.

Reads invite codes straight from MySQL. Run the PHP 8.3 dev server first.
"""
import json, subprocess, sys, time, uuid, urllib.request, urllib.error, datetime

BASE = "http://127.0.0.1:8765/api/ems"
DB = "tss"
SUF = str(int(time.time()))[-6:]
TODAY = datetime.date.today()
FUTURE = (TODAY + datetime.timedelta(days=30)).isoformat()
FUTURE2 = (TODAY + datetime.timedelta(days=60)).isoformat()
PAST = (TODAY - datetime.timedelta(days=5)).isoformat()

_pass, _fail = [0], [0]


def db(sql):
    out = subprocess.run(["mysql", "-uroot", "-N", "-e", sql, DB], capture_output=True, text=True)
    if out.returncode != 0:
        raise RuntimeError(f"mysql: {out.stderr.strip()}\n{sql}")
    return out.stdout.strip()


def req(method, path, token=None, body=None, query=None):
    url = BASE + path
    if query:
        from urllib.parse import urlencode
        url += "?" + urlencode({k: v for k, v in query.items() if v is not None})
    data = json.dumps(body).encode() if body is not None else None
    headers = {"Accept": "application/json"}
    if body is not None:
        headers["Content-Type"] = "application/json"
    if token:
        headers["Authorization"] = "Bearer " + token
    r = urllib.request.Request(url, data=data, headers=headers, method=method)
    try:
        resp = urllib.request.urlopen(r)
        status, text = resp.status, resp.read().decode()
    except urllib.error.HTTPError as e:
        status, text = e.code, e.read().decode()
    try:
        return status, (json.loads(text) if text else None)
    except json.JSONDecodeError:
        return status, text


def check(name, ok, detail=""):
    if ok:
        _pass[0] += 1
        # print(f"  ok  {name}")
    else:
        _fail[0] += 1
        print(f"  FAIL {name}  {detail}")


def m(b):
    return b.get("message") if isinstance(b, dict) else b


uid = lambda: str(uuid.uuid4())

# --- register admin --------------------------------------------------------
admin_email = f"ada.{SUF}@gda.test"
st, b = req("POST", "/auth/register-school", body={
    "school": {"name": f"Fees Demo Academy {SUF}", "shortName": "GDA",
               "motto": "x", "address": "1 Road"},
    "admin": {"name": "Ada Okafor", "email": admin_email, "password": "password123"}})
check("register-school 200", st == 200, f"{st} {b}")
school_id, admin_token = b["school"]["id"], b["token"]
S = f"/schools/{school_id}"


def api(method, path, token=admin_token, body=None, query=None):
    return req(method, S + path, token=token, body=body, query=query)


def invite_user(name, role):
    st, b = api("POST", "/users/invite", body={"name": name, "email": f"{name.split()[0].lower()}.{SUF}@gda.test", "role": role})
    code = db(f"SELECT invite_code FROM ems_users WHERE id='{b['id']}'")
    st2, b2 = req("POST", "/auth/invite/accept", body={"code": code, "password": "password123"})
    return b2.get("token")


bursar_token = invite_user("Grace Bello", "bursar")
registrar_token = invite_user("Rita Registrar", "registrar")
check("bursar + registrar provisioned", bool(bursar_token) and bool(registrar_token))

# --- a class + students ----------------------------------------------------
cg = uid()
db(f"""INSERT INTO ems_class_groups (id,school_id,name,level,stream,capacity,created,modified)
       VALUES ('{cg}','{school_id}','JSS 1A','JSS 1','A',30,NOW(),NOW())""")
students = {}
for adm, fn, ln in [("GDA/001", "Amaka", "Adeyemi"), ("GDA/002", "Bode", "Balogun"), ("GDA/003", "Chika", "Chukwu")]:
    st, b = api("POST", "/students", body={"admissionNumber": adm, "firstName": fn, "lastName": ln,
                "dateOfBirth": "2013-04-04", "gender": "female", "classGroup": "JSS 1A",
                "status": "enrolled", "guardianName": f"{fn} Parent", "guardianPhone": "080"})
    students[ln] = b["id"]
S1 = students["Adeyemi"]

# ===========================================================================
# 1. Fee structures
# ===========================================================================
st, b = api("POST", "/fee-structures", body={
    "session": "2025/2026", "term": "First", "level": "JSS 1",
    "items": [{"name": "Tuition", "amount": 9000000}, {"name": "", "amount": 500}, {"name": "Uniform", "amount": 1500000}],
    "schedule": [{"label": "First", "dueOn": FUTURE, "percent": 40}, {"label": "Second", "dueOn": FUTURE2, "percent": 60}]})
check("fee-structure 201", st == 201, f"{st} {b}")
check("fee-structure drops blank item (2 items)", len(b["items"]) == 2, b.get("items"))
check("fee-structure total = 10,500,000", b["total"] == 10500000, b.get("total"))
check("fee-structure schedule kept + sorted", len(b["schedule"]) == 2 and b["schedule"][0]["dueOn"] == FUTURE)

st, b = api("POST", "/fee-structures", body={"session": "2025/2026", "term": "First", "level": "JSS 2",
            "items": [{"name": "Tuition", "amount": 100}], "schedule": [{"label": "x", "dueOn": FUTURE, "percent": 70}]})
check("fee-structure bad percent -> 422",
      st == 422 and m(b) == "The instalments come to 70% of the bill. A payment schedule has to account for all of it.", f"{st} {b}")

# a valid JSS 2 structure, so the term,level sort has two rows to order
api("POST", "/fee-structures", body={"session": "2025/2026", "term": "First", "level": "JSS 2",
    "items": [{"name": "Tuition", "amount": 12000000}]})
st, b = api("GET", "/fee-structures", query={"page": 1, "pageSize": 20})
check("fee-structure list envelope", isinstance(b, dict) and "items" in b and "total" in b, b)
check("fee-structure list sorted term,level (JSS 1 before JSS 2)",
      [x["level"] for x in b["items"]][:2] == ["JSS 1", "JSS 2"], [x["level"] for x in b["items"]])

# ===========================================================================
# 2. Awards
# ===========================================================================
st, b = api("POST", "/fee-awards", body={"name": "Principal's scholarship", "kind": "scholarship",
            "basis": "percentage", "value": 25, "appliesToItem": "Tuition", "scope": "student",
            "studentId": S1, "session": "2025/2026", "term": "First"})
check("award scholarship 201", st == 201, f"{st} {b}")
check("award denorm studentName", b.get("studentName") == "Amaka Adeyemi", b)
award1 = b["id"]

st, b = api("POST", "/fee-awards", body={"name": "Sibling discount", "kind": "discount",
            "basis": "amount", "value": 100000, "appliesToItem": "all", "scope": "level",
            "level": "JSS 1", "session": "2025/2026", "term": "all"})
check("award level discount 201", st == 201 and b.get("level") == "JSS 1", f"{st} {b}")

# validations
for label, body_, exp in [
    ("no name", {"name": " ", "basis": "amount", "value": 1, "scope": "level", "level": "JSS 1", "session": "2025/2026", "term": "all"}, "Give the award a name families will recognise."),
    ("value 0", {"name": "X", "basis": "amount", "value": 0, "scope": "level", "level": "JSS 1", "session": "2025/2026", "term": "all"}, "Enter how much the award is worth."),
    ("pct>100", {"name": "X", "basis": "percentage", "value": 150, "scope": "level", "level": "JSS 1", "session": "2025/2026", "term": "all"}, "A percentage award cannot be more than 100% of the fee."),
    ("no student", {"name": "X", "basis": "amount", "value": 1, "scope": "student", "studentId": "nope", "session": "2025/2026", "term": "all"}, "Choose the student this award is for."),
    ("no level", {"name": "X", "basis": "amount", "value": 1, "scope": "level", "session": "2025/2026", "term": "all"}, "Choose the level this discount applies to."),
]:
    st, b = api("POST", "/fee-awards", body=body_)
    check(f"award validation: {label} -> 422", st == 422 and m(b) == exp, f"{st} {b}")

check("audit fee_award.granted written",
      int(db(f"SELECT COUNT(*) FROM ems_audit_events WHERE school_id='{school_id}' AND action='fee_award.granted'")) >= 2)

# ===========================================================================
# 3. Invoice pricing — preview (awards applied, no compounding, floor at zero)
# ===========================================================================
line_items = [{"name": "Tuition", "amount": 9000000}, {"name": "Uniform", "amount": 1500000}]
st, b = api("POST", "/invoices/preview", body={"studentId": S1, "session": "2025/2026", "term": "First", "lineItems": line_items})
check("preview 200", st == 200, f"{st} {b}")
check("preview charged 10,500,000", b["charged"] == 10500000, b.get("charged"))
check("preview awarded -2,350,000", b["awarded"] == -2350000, b.get("awarded"))
check("preview total 8,150,000", b["total"] == 8150000, b.get("total"))
check("preview 2 applied awards", len(b["applied"]) == 2, b.get("applied"))
ap = b["applied"]
check("applied[0] = scholarship, amount 2,250,000 on base 9,000,000 (no compounding)",
      ap[0]["award"]["scope"] == "student" and ap[0]["amount"] == 2250000 and ap[0]["base"] == 9000000, ap[0])
check("applied[1] = level discount, amount 100,000 on base 10,500,000",
      ap[1]["amount"] == 100000 and ap[1]["base"] == 10500000, ap[1])
check("preview lineItems = 2 charges + 2 award lines", len(b["lineItems"]) == 4, b.get("lineItems"))
check("award line negative + kind award + awardId",
      b["lineItems"][2]["amount"] == -2250000 and b["lineItems"][2]["kind"] == "award" and b["lineItems"][2].get("awardId") == award1, b["lineItems"][2])

# preview missing student -> 404
st, b = api("POST", "/invoices/preview", body={"studentId": "nope", "session": "2025/2026", "term": "First", "lineItems": line_items})
check("preview unknown student -> 404", st == 404 and m(b) == "That student could not be found.", f"{st} {b}")

# ===========================================================================
# 4. Issue invoices (numbering, instalment validation)
# ===========================================================================
st, b = api("POST", "/invoices", body={"studentId": S1, "session": "2025/2026", "term": "First",
            "dueDate": FUTURE, "lineItems": line_items})
check("issue invoice 201", st == 201, f"{st} {b}")
check("invoice total 8,150,000", b["total"] == 8150000, b.get("total"))
check("invoice number PREFIX/INV/2526T1/0001", b["invoiceNumber"] == "GDA/INV/2526T1/0001", b.get("invoiceNumber"))
check("invoice status issued", b["status"] == "issued")
check("issued line items include award lines (4)", len(b["lineItems"]) == 4)
inv1 = b["id"]

# no line items -> 422
st, b = api("POST", "/invoices", body={"studentId": S1, "session": "2025/2026", "term": "First", "dueDate": FUTURE, "lineItems": [{"name": " ", "amount": 0}]})
check("invoice no items -> 422", st == 422 and m(b) == "An invoice needs at least one line item.", f"{st} {b}")

# instalment sum mismatch -> 422 with formatted currency
st, b = api("POST", "/invoices", body={"studentId": students["Balogun"], "session": "2025/2026", "term": "First",
            "dueDate": FUTURE, "lineItems": [{"name": "Tuition", "amount": 9000000}],
            "instalments": [{"label": "A", "dueOn": FUTURE, "amount": 4000000}, {"label": "B", "dueOn": FUTURE2, "amount": 4000000}]})
# Balogun has the level discount (100,000 off) -> total 8,900,000; instalments sum 8,000,000.
check("instalment mismatch -> 422 with ₦ figures",
      st == 422 and m(b) == "The instalments add up to ₦80,000, but this invoice comes to ₦89,000 once scholarships and discounts are applied.", f"{st} {b}")

# valid instalments -> dueDate becomes last instalment
st, b = api("POST", "/invoices", body={"studentId": students["Balogun"], "session": "2025/2026", "term": "First",
            "dueDate": FUTURE, "lineItems": [{"name": "Tuition", "amount": 9000000}],
            "instalments": [{"label": "Part 1", "dueOn": FUTURE, "amount": 4000000}, {"label": "Part 2", "dueOn": FUTURE2, "amount": 4900000}]})
check("scheduled invoice 201, total 8,900,000", st == 201 and b["total"] == 8900000, f"{st} {b}")
check("scheduled invoice dueDate = last instalment", b["dueDate"] == FUTURE2, b.get("dueDate"))
check("scheduled invoice 2 instalments numbered", len(b["instalments"]) == 2 and b["instalments"][0]["number"] == 1)
inv2 = b["id"]

# ===========================================================================
# 5. Detail + payments + waterfall
# ===========================================================================
st, b = api("GET", f"/invoices/{inv2}")
check("invoice detail unpaid balance 8,900,000", b["balance"] == 8900000 and b["paymentStatus"] == "unpaid", (b.get("balance"), b.get("paymentStatus")))
check("invoice detail schedule 2, nextDue #1", len(b["schedule"]) == 2 and b["nextDue"]["number"] == 1)
check("invoice detail empty payments/refunds", b["payments"] == [] and b["refunds"] == [])

st, b = api("POST", f"/invoices/{inv2}/payments", body={"amount": 4000000, "method": "cash", "paidOn": TODAY.isoformat()})
check("record payment 201 completed", st == 201 and b["state"] == "completed", f"{st} {b}")
check("receipt number PREFIX/RCP/00001", b["receiptNumber"] == "GDA/RCP/00001", b.get("receiptNumber"))
pay1 = b["id"]

st, b = api("GET", f"/invoices/{inv2}")
check("after payment: paid 4,000,000 balance 4,900,000 part_paid",
      b["paid"] == 4000000 and b["balance"] == 4900000 and b["paymentStatus"] == "part_paid", (b.get("paid"), b.get("balance"), b.get("paymentStatus")))
check("waterfall: instalment 1 paid, nextDue #2",
      b["schedule"][0]["status"] == "paid" and b["schedule"][0]["balance"] == 0 and b["nextDue"]["number"] == 2, b.get("schedule"))

# list sorts + status filter
st, b = api("GET", "/invoices", query={"page": 1, "pageSize": 20, "sort": "balance", "status": "all", "term": "all"})
check("invoice list sort balance desc", b["items"][0]["balance"] >= b["items"][-1]["balance"])
st, b = api("GET", "/invoices", query={"status": "part_paid"})
check("invoice list status=part_paid derived filter", all(i["paymentStatus"] == "part_paid" for i in b["items"]) and any(i["id"] == inv2 for i in b["items"]))

# reverse the payment
st, b = api("POST", f"/payments/{pay1}/reverse", body={"reason": "bounced"})
check("reverse payment 200 reversed", st == 200 and b["state"] == "reversed" and b.get("reversalReason") == "bounced", f"{st} {b}")
st, b = api("GET", f"/invoices/{inv2}")
check("after reverse: paid 0 balance 8,900,000", b["paid"] == 0 and b["balance"] == 8900000, (b.get("paid"), b.get("balance")))
st, b = api("POST", f"/payments/{pay1}/reverse", body={"reason": "again"})
check("double reverse -> 422", st == 422 and m(b) == "This payment has already been reversed.", f"{st} {b}")

# ===========================================================================
# 6. Provider checkout (pending -> confirm) + receipt gating
# ===========================================================================
st, b = api("POST", f"/invoices/{inv2}/checkout", body={"amount": 999999999})
check("checkout over balance -> 422", st == 422 and m(b) == "The amount is more than the outstanding balance.", f"{st} {b}")
st, b = api("POST", f"/invoices/{inv2}/checkout", body={"amount": 0})
check("checkout amount 0 -> 422", st == 422 and m(b) == "Enter the amount to pay.", f"{st} {b}")
st, b = api("POST", f"/invoices/{inv2}/checkout", body={"amount": 5000000})
check("checkout 201 pending provider, empty receipt",
      st == 201 and b["state"] == "pending" and b["channel"] == "provider" and b["receiptNumber"] == "", f"{st} {b}")
pay2 = b["id"]

st, b = api("GET", f"/payments/{pay2}/receipt")
check("receipt while pending -> 422", st == 422 and m(b) == "A receipt is issued only after the payment is confirmed.", f"{st} {b}")
st, b = api("GET", f"/invoices/{inv2}")
check("pending checkout counts nothing", b["paid"] == 0, b.get("paid"))

st, b = api("POST", f"/checkout/{pay2}/confirm", body={"outcome": "success"})
check("confirm success -> completed + receipt", st == 200 and b["state"] == "completed" and b["receiptNumber"].startswith("GDA/RCP/"), f"{st} {b}")
st, b2 = api("POST", f"/checkout/{pay2}/confirm", body={"outcome": "failure"})
check("confirm replay idempotent (stays completed)", b2["state"] == "completed" and b2["receiptNumber"] == b["receiptNumber"], b2)
st, b = api("GET", f"/invoices/{inv2}")
check("after confirm: paid 5,000,000 balance 3,900,000", b["paid"] == 5000000 and b["balance"] == 3900000, (b.get("paid"), b.get("balance")))
st, b = api("GET", f"/payments/{pay2}/receipt")
check("receipt after confirm 200", st == 200 and b["paidToDate"] == 5000000 and b["balanceAfter"] == 3900000, f"{st} {b}")

# ===========================================================================
# 7. Refunds — role gate, separation of duties, waterfall
# ===========================================================================
st, b = api("POST", "/refunds", token=registrar_token, body={"paymentId": pay2, "amount": 100000, "reason": "x"})
check("registrar cannot request refund -> 403", st == 403 and m(b) == "Only a bursar or administrator can request a refund.", f"{st} {b}")

st, b = api("POST", "/refunds", token=bursar_token, body={"paymentId": pay2, "amount": 6000000, "reason": "overpaid"})
check("refund over payment -> 422", st == 422 and m(b) == "A refund cannot exceed the ₦50,000 paid.", f"{st} {b}")

st, b = api("POST", "/refunds", token=bursar_token, body={"paymentId": pay2, "amount": 2000000, "reason": "partial overpay"})
check("bursar requests refund 201 pending", st == 201 and b["status"] == "pending", f"{st} {b}")
ref1 = b["id"]
st, b = api("GET", f"/invoices/{inv2}")
check("pending refund subtracts nothing (paid still 5,000,000)", b["paid"] == 5000000, b.get("paid"))

# remaining refundable now 3,000,000
st, b = api("POST", "/refunds", token=bursar_token, body={"paymentId": pay2, "amount": 4000000, "reason": "y"})
check("refund over remaining -> 422", st == 422 and m(b) == "Only ₦30,000 of this payment is left to refund.", f"{st} {b}")

# bursar cannot process
st, b = api("POST", f"/refunds/{ref1}/process", token=bursar_token)
check("bursar cannot process refund -> 403", st == 403 and m(b) == "Processing a refund needs an administrator.", f"{st} {b}")

# admin (Ada) requests a refund, then Ada tries to process her own -> separation of duties
st, b = api("POST", "/refunds", token=admin_token, body={"paymentId": pay2, "amount": 500000, "reason": "admin req"})
ref_admin = b["id"]
st, b = api("POST", f"/refunds/{ref_admin}/process", token=admin_token)
check("self-approval blocked -> 403 separation of duties",
      st == 403 and m(b) == "A refund must be approved by someone other than the person who requested it.", f"{st} {b}")

# admin processes the bursar's refund -> money leaves
st, b = api("POST", f"/refunds/{ref1}/process", token=admin_token, body={"note": "ok"})
check("admin processes bursar refund 200 processed", st == 200 and b["status"] == "processed" and b.get("decidedBy") == "Ada Okafor", f"{st} {b}")
st, b = api("GET", f"/invoices/{inv2}")
check("processed refund drops paid to 3,000,000 (balance 5,900,000)", b["paid"] == 3000000 and b["balance"] == 5900000, (b.get("paid"), b.get("balance")))

# reject admin's own request (a different admin isn't available, but reject only needs admin != none)
st, b = api("POST", f"/refunds/{ref_admin}/reject", token=admin_token, body={"reason": "not needed"})
check("reject refund 200 rejected", st == 200 and b["status"] == "rejected", f"{st} {b}")
st, b = api("POST", f"/refunds/{ref_admin}/reject", token=admin_token, body={"reason": "again"})
check("re-decide rejected -> 409", st == 409 and m(b) == "Only a refund awaiting approval can be rejected.", f"{st} {b}")

check("audit refund.processed written",
      int(db(f"SELECT COUNT(*) FROM ems_audit_events WHERE school_id='{school_id}' AND action='refund.processed'")) == 1)

# ===========================================================================
# 8. Reconciliation + report + ledger
# ===========================================================================
st, b = api("GET", "/reconciliation")
check("reconciliation shape", all(k in b for k in ("pending", "failed", "completedCount", "completedAmount", "refundsPending", "refundedAmount")), b)
check("reconciliation refundedAmount 2,000,000 count 1", b["refundedAmount"] == 2000000 and b["refundedCount"] == 1, (b.get("refundedAmount"), b.get("refundedCount")))
check("reconciliation completed gross (not net of refund) = 5,000,000",
      b["completedAmount"] == 5000000 and b["completedCount"] == 1, (b.get("completedAmount"), b.get("completedCount")))

st, b = api("GET", "/fees/report", query={"term": "First"})
check("report totals net of refund: collected 3,000,000",
      b["totalCollected"] == 3000000, b.get("totalCollected"))
check("report totalInvoiced = 8,150,000 + 8,900,000 = 17,050,000", b["totalInvoiced"] == 17050000, b.get("totalInvoiced"))
check("report totalAwarded positive (2,350,000 + 100,000 = 2,450,000)", b["totalAwarded"] == 2450000, b.get("totalAwarded"))
check("report byClass has JSS 1A outstanding", any(r["key"] == "JSS 1A" and r["outstanding"] > 0 for r in b["byClass"]), b.get("byClass"))
check("report collectionRate 0..1", 0 <= b["collectionRate"] <= 1)

st, b = api("GET", f"/students/{S1}/ledger")
check("ledger 200 for admin", st == 200 and b["studentName"] == "Amaka Adeyemi", f"{st} {b}")
check("ledger lists the student's invoice + awards", any(i["id"] == inv1 for i in b["invoices"]) and len(b["awards"]) >= 1, (len(b["invoices"]), len(b["awards"])))
check("ledger totalInvoiced excludes cancelled", b["totalInvoiced"] == 8150000, b.get("totalInvoiced"))

# cancel an unpaid invoice, then confirm it can't take payments
st, b = api("POST", f"/invoices/{inv1}/cancel", body={"reason": "raised in error"})
check("cancel unpaid invoice 200", st == 200 and b["status"] == "cancelled", f"{st} {b}")
st, b = api("POST", f"/invoices/{inv2}/cancel", body={"reason": "x"})
check("cancel invoice with payments -> 422", st == 422 and m(b) == "This invoice has payments recorded against it. Reverse them before cancelling.", f"{st} {b}")

# ===========================================================================
print(f"\nPhase 4 Fees E2E: {_pass[0]} passed, {_fail[0]} failed")
sys.exit(1 if _fail[0] else 0)
