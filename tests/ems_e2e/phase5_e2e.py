#!/usr/bin/env python3
"""Phase 5 E2E — portals, communication, reporting, governance, and the
domain-completion trio (merge / promotion / imports). Standalone: registers a
fresh school, seeds the minimum it needs via the API (+ a little SQL for rows
with no create endpoint), then asserts each module's happy path, its verbatim
error strings and its viewer scoping against document.md §3.15–§3.24.

Run with the backend on 127.0.0.1:8765 and MySQL `tss` (root/no-password):
    python3 tests/ems_e2e/phase5_e2e.py
"""
import json, subprocess, sys, time, uuid, urllib.request, urllib.error, datetime

BASE = "http://127.0.0.1:8765/api/ems"
DB = "tss"
TS = str(int(time.time()))[-6:]
TODAY = datetime.date.today()
FUT = (TODAY + datetime.timedelta(days=30)).isoformat()
PAST = (TODAY - datetime.timedelta(days=7)).isoformat()

_passed = 0
_failed = 0


def check(name, cond, extra=""):
    global _passed, _failed
    if cond:
        _passed += 1
    else:
        _failed += 1
        print(f"  FAIL: {name} {extra}")


def db(sql):
    out = subprocess.run(["mysql", "-uroot", "-N", "-e", sql, DB], capture_output=True, text=True)
    if out.returncode != 0:
        raise RuntimeError(out.stderr)
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
        return resp.status, json.loads(resp.read().decode() or "null")
    except urllib.error.HTTPError as e:
        return e.code, json.loads(e.read().decode() or "null")


def main():
    email = f"admin.{TS}@p5.test"
    pw = "password123"
    st, b = req("POST", "/auth/register-school", body={
        "school": {"name": f"Phase5 Academy {TS}", "shortName": "P5A", "motto": "x", "address": "1 Rd"},
        "admin": {"name": "Ada Admin", "email": email, "password": pw}})
    sid, token = b["school"]["id"], b["token"]

    def A(method, p, body=None, query=None, tok=token):
        return req(method, f"/schools/{sid}{p}", tok, body, query)

    # --- seed: class, students, guardians, an open session -------------------
    cg = str(uuid.uuid4())
    db(f"INSERT INTO ems_class_groups (id,school_id,name,level,stream,capacity,created,modified) "
       f"VALUES ('{cg}','{sid}','JSS 1A','JSS 1','A',30,NOW(),NOW())")
    db(f"INSERT INTO ems_academic_sessions (id,school_id,name,starts_on,ends_on,status,created,modified) "
       f"VALUES (UUID(),'{sid}','2025/2026','2025-09-01','2026-07-31','open',NOW(),NOW())")
    students = {}
    for adm, fn, ln, gender in [("P5A/001", "Amara", "Ade", "female"), ("P5A/002", "Bola", "Bell", "male"),
                                ("P5A/003", "Chi", "Cole", "female")]:
        s = A("POST", "/students", {"admissionNumber": adm, "firstName": fn, "lastName": ln,
              "dateOfBirth": "2013-04-04", "gender": gender, "classGroup": "JSS 1A", "status": "enrolled",
              "guardianName": f"{fn} Parent", "guardianPhone": "080"})[1]
        students[ln] = s["id"]
        A("POST", f"/students/{s['id']}/guardians", {"firstName": "Gua", "lastName": ln,
          "relationship": "mother", "phone": f"0803111{adm[-3:]}", "email": f"g{ln.lower()}@mail.test",
          "isPrimary": True})

    # ===================== §3.22 Analytics ==============================
    print("Analytics …")
    s, ov = A("GET", "/analytics/overview")
    check("overview 200", s == 200)
    check("overview enrolled=3", ov["enrolled"] == 3, ov.get("enrolled"))
    check("overview ratio raw", ov["studentTeacherRatio"] == 0)
    s, en = A("GET", "/analytics/enrolment")
    check("enrolment byLevel JSS 1 3", en["byLevel"][0]["enrolled"] == 3 and en["byLevel"][0]["capacity"] == 30)
    check("enrolment gender 2f/1m", en["genderSplit"] == {"female": 2, "male": 1})

    # ===================== §3.19 Portal =================================
    print("Portal …")
    s, ident = A("GET", "/portal/identity")
    check("portal identity user", ident["user"]["role"] == "administrator" and ident["wards"] == [])
    s, ward = A("GET", f"/portal/wards/{students['Ade']}")
    check("ward overview 200", s == 200 and ward["attendance"]["windowDays"] == 20)
    s, _ = A("GET", "/portal/wards/does-not-exist")
    check("ward 404", s == 404)

    # A sibling relationship repeats the same household contact row. The
    # communication audience must still count and contact that household once.
    sibling = A("POST", "/students", {"admissionNumber": "P5A/004", "firstName": "Dayo",
        "lastName": "Ade", "dateOfBirth": "2014-04-04", "gender": "male",
        "classGroup": "JSS 1A", "status": "enrolled", "guardianName": "Ade Parent",
        "guardianPhone": "080"})[1]
    sibling_id = sibling["id"]
    A("POST", f"/students/{sibling_id}/guardians", {"firstName": "Second", "lastName": "Ade",
      "relationship": "mother", "phone": "0803111001", "email": "gade@mail.test", "isPrimary": True})

    # ===================== §3.20 Communication ==========================
    print("Communication …")
    s, r = A("POST", "/announcements", {"data": {"title": "", "body": "x",
              "audience": "everyone", "category": "general"}, "publish": False})
    check("announcement title validation 422", s == 422 and r["message"] == "Give the announcement a title.")
    s, r = A("GET", "/announcements/audience-preview",
             query={"audience": "everyone", "purpose": "school_news", "channel": "push"})
    check("audience preview channel validation 422", s == 422 and r["message"] == "Choose email or SMS.")
    missing_announcement = str(uuid.uuid4())
    s, r = A("GET", f"/announcements/{missing_announcement}/delivery")
    check("missing delivery report 404", s == 404 and r["message"] == "That announcement could not be found.")
    s, ann = A("POST", "/announcements", {"data": {"title": "Open day", "body": "Come\n\nAlong",
               "audience": "parents", "category": "event", "pinned": True}, "publish": False})
    aid = ann["id"]
    check("announce draft", ann["status"] == "draft" and ann["pinned"] is True)
    s, r = A("POST", f"/announcements/{aid}/deliver", {"channel": "email", "purpose": "transactional"})
    check("deliver-before-publish 422", s == 422 and r["message"] == "Publish this announcement before sending it out.")
    A("POST", f"/announcements/{aid}/publish")
    s, ap = A("GET", "/announcements/audience-preview", query={"audience": "parents", "purpose": "transactional", "channel": "email"})
    check("audience preview total=3 reachable=3", ap["total"] == 3 and ap["reachable"] == 3)
    check("address masked", ap["sample"][0]["address"].count("•") > 0 and "@" in ap["sample"][0]["address"])
    s, rep = A("POST", f"/announcements/{aid}/deliver", {"channel": "email", "purpose": "transactional"})
    check("deliver total=3", rep["total"] == 3 and rep["sent"] + rep["failed"] == 3)
    s, r = A("POST", f"/announcements/{aid}/deliver", {"channel": "email", "purpose": "transactional"})
    check("deliver-again 409", s == 409 and r["message"] == "This announcement has already been sent.")
    # school_news with no consent → all suppressed
    s, ann2 = A("POST", "/announcements", {"data": {"title": "News", "body": "n", "audience": "parents",
                "category": "general"}, "publish": True})
    s, rep2 = A("POST", f"/announcements/{ann2['id']}/deliver", {"channel": "email", "purpose": "school_news"})
    check("school_news all suppressed", rep2["suppressed"] == 3,
          [x.get("suppressedReason") for x in rep2["recipients"]])
    check("suppressed reason", rep2["recipients"][0]["suppressedReason"] == "No consent recorded for school news")
    s, empty_ann = A("POST", "/announcements", {"data": {"title": "Teachers only", "body": "x",
                "audience": "teachers", "category": "general"}, "publish": True})
    empty_id = empty_ann["id"]
    s, empty_report = A("POST", f"/announcements/{empty_id}/deliver",
                        {"channel": "email", "purpose": "transactional"})
    check("empty audience first send succeeds", s == 200 and empty_report["total"] == 0)
    s, r = A("POST", f"/announcements/{empty_id}/deliver",
             {"channel": "email", "purpose": "transactional"})
    check("empty audience second send 409", s == 409 and r["message"] == "This announcement has already been sent.")
    check("empty audience has one send marker",
          db(f"SELECT COUNT(*) FROM ems_notifications WHERE school_id='{sid}' AND announcement_id='{empty_id}'") == "1")
    s, feed = A("GET", "/announcements/feed")
    check("feed pinned first", feed["items"][0]["title"] == "Open day")

    # Every actor crosses the real auth, policy and audience boundaries.
    actor_tokens = {"administrator": token}
    for role in ["registrar", "bursar", "teacher", "parent", "student"]:
        s, invited = A("POST", "/users/invite", {
            "name": f"{role.title()} Actor", "email": f"{role}.{TS}@p5.test", "role": role})
        code = db(f"SELECT invite_code FROM ems_users WHERE id='{invited['id']}'")
        s, accepted = req("POST", "/auth/invite/accept", body={"code": code, "password": "password123"})
        actor_tokens[role] = accepted["token"]
        check(f"{role} account activated", s == 200 and bool(actor_tokens[role]))

    for role in ["administrator", "registrar", "bursar"]:
        s, managed = A("GET", "/announcements", tok=actor_tokens[role])
        check(f"{role} can manage announcements", s == 200 and managed["total"] >= 3)
        s, log = A("GET", "/notifications", tok=actor_tokens[role])
        check(f"{role} can read notification log", s == 200)

    expected_feed = {
        "teacher": ({"Teachers only"}, {"Open day", "News"}),
        "parent": ({"Open day", "News"}, {"Teachers only"}),
        "student": (set(), {"Open day", "News", "Teachers only"}),
    }
    for role in ["teacher", "parent", "student"]:
        tok = actor_tokens[role]
        s, refused = A("GET", "/announcements", tok=tok)
        check(f"{role} cannot manage announcements",
              s == 403 and refused["message"] == "This is only available to school staff.")
        s, role_feed = A("GET", "/announcements/feed", tok=tok)
        titles = {item["title"] for item in role_feed["items"]}
        required, forbidden = expected_feed[role]
        check(f"{role} feed is audience scoped",
              s == 200 and required.issubset(titles) and titles.isdisjoint(forbidden), sorted(titles))
        s, refused = A("GET", "/notifications", tok=tok)
        check(f"{role} cannot read recipient log", s == 403)

    s, refused = A("GET", f"/announcements/{aid}", tok=actor_tokens["teacher"])
    check("teacher cannot open parent announcement", s == 403 and refused["message"] == "You cannot open this announcement.")

    other_email = f"other.{TS}@p5.test"
    s, other = req("POST", "/auth/register-school", body={
        "school": {"name": f"Other Academy {TS}", "shortName": "OA", "motto": "x", "address": "2 Rd"},
        "admin": {"name": "Other Admin", "email": other_email, "password": "password123"}})
    s, refused = req("GET", f"/schools/{other['school']['id']}/announcements/feed", token=token)
    check("communication rejects another school tenant", s == 403 and refused["message"] == "You do not have access to this school.")

    # Alert sends use the same recipient delivery trail, rather than only
    # writing a success-looking notification log row.
    overdue_invoice = str(uuid.uuid4())
    db(f"INSERT INTO ems_invoices (id,school_id,invoice_number,student_id,student_name,class_group,session,term,issued_on,due_date,line_items,total,status,created,modified) "
       f"VALUES ('{overdue_invoice}','{sid}','INV-ALERT-{TS}','{students['Ade']}','Amara Ade','JSS 1A','2025/2026','First','{PAST}','{PAST}',"
       f"'[{{\"name\":\"Tuition\",\"amount\":100000,\"kind\":\"charge\"}}]',100000,'issued',NOW(),NOW())")
    s, alert_message = A("POST", "/alerts/send", {"kind": "fee_overdue", "channel": "email"})
    check("alert send 200", s == 200 and alert_message["kind"] == "fee_reminder")
    delivery_counts = db(
        f"SELECT COUNT(*), COALESCE(SUM(status='sent'),0), COALESCE(MAX(notification_id IS NOT NULL),0) "
        f"FROM ems_message_recipients WHERE school_id='{sid}' AND notification_id='{alert_message['id']}'"
    ).split("\t")
    check("alert has recipient trail", delivery_counts[0] == "1" and delivery_counts[2] == "1", delivery_counts)
    check("alert reports actual successes", int(delivery_counts[1]) == alert_message["recipientCount"], delivery_counts)
    db(f"DELETE FROM ems_guardians WHERE school_id='{sid}' AND student_id='{sibling_id}'")
    db(f"DELETE FROM ems_students WHERE school_id='{sid}' AND id='{sibling_id}'")

    # ===================== §3.21 Reports ================================
    print("Reports …")
    s, r = A("POST", "/reports/jobs", {"reportType": "nope", "filters": {}})
    check("report unknown 404", s == 404)
    s, job = A("POST", "/reports/jobs", {"reportType": "class_list", "filters": {"classGroup": "JSS 1A"}})
    jid = job["id"]
    check("report queued", job["status"] == "queued")
    s, r = A("POST", f"/reports/jobs/{jid}/download")
    check("download too-early 409", s == 409 and r["message"] == "This report is not ready yet.")
    ready = False
    for _ in range(12):
        time.sleep(0.8)
        s, js = A("GET", "/reports/jobs")
        if next(j["status"] for j in js["items"] if j["id"] == jid) == "ready":
            ready = True
            break
    check("report becomes ready", ready)
    s, f = A("POST", f"/reports/jobs/{jid}/download")
    check("download ready 200", s == 200 and f["rowCount"] == 3)
    check("csv confidential header", "# CONFIDENTIAL" in f["content"])
    check("csv filename", f["filename"] == f"class_list-{TODAY.isoformat()}.csv")
    # expire it → 410
    db(f"UPDATE ems_report_jobs SET expires_on='{PAST}' WHERE id='{jid}'")
    s, r = A("POST", f"/reports/jobs/{jid}/download")
    check("download expired 410", s == 410 and r["message"].startswith("This export has expired"))

    # ===================== §3.23 Audit & privacy ========================
    print("Audit & privacy …")
    s, pr = A("POST", "/privacy-requests", {"kind": "deletion", "subjectName": "Amara Ade",
              "requestedBy": "Parent", "contact": "p@x.com", "detail": "Delete please"})
    pid = pr["id"]
    check("privacy PRV-0001", pr["reference"] == "PRV-0001" and pr["status"] == "received")
    s, r = A("POST", f"/privacy-requests/{pid}/verify", {"evidence": ""})
    check("verify needs evidence 422", s == 422 and r["message"] == "Record how the requester proved who they are.")
    A("POST", f"/privacy-requests/{pid}/verify", {"evidence": "Passport"})
    s, r = A("POST", f"/privacy-requests/{pid}/decide", {"approve": True, "note": "ok"})
    check("deletion needs retention 422", s == 422 and r["message"].startswith("Name what must be kept"))
    A("POST", f"/privacy-requests/{pid}/decide", {"approve": True, "note": "ok", "retentionNote": "Finance 7y"})
    s, fr = A("POST", f"/privacy-requests/{pid}/fulfil", {"note": "Done"})
    check("privacy fulfilled", fr["status"] == "fulfilled" and fr["retentionNote"] == "Finance 7y")
    s, ev = A("GET", "/audit/events", query={"entityType": "privacy_request"})
    actions = [e["action"] for e in ev["items"]]
    check("audit newest-first fulfilled leads", actions[0] == "privacy_request.fulfilled", actions[:4])
    check("audit logged present", "privacy_request.logged" in actions)

    # ===================== §3.24 Incidents ==============================
    print("Incidents …")
    s, r = A("POST", "/incidents", {"title": "", "description": "x", "dataCategories": ["health"], "discoveredOn": PAST})
    check("incident title 422", s == 422 and r["message"] == "Give the incident a short title.")
    s, inc = A("POST", "/incidents", {"title": "Breach", "description": "Laptop lost", "severity": "high",
               "dataCategories": ["student_records", "health"], "discoveredOn": PAST})
    iid = inc["id"]
    check("incident INC-0001 lead", inc["reference"] == "INC-0001" and inc["responders"][0]["lead"] is True)
    check("incident first entry", inc["entries"][0]["kind"] == "recorded")
    s, r = A("POST", f"/incidents/{iid}/advance", {"to": "investigating", "note": "x"})
    check("incident skip step 409", s == 409 and "cannot move to" in r["message"])
    s, adv = A("POST", f"/incidents/{iid}/advance", {"to": "contained", "note": "Locked accounts"})
    check("incident contained", adv["status"] == "contained" and adv["containmentNote"] == "Locked accounts")

    # invite a second admin and prove the responder seal
    s, inv = A("POST", "/users/invite", {"name": "Bee Second", "email": f"bee.{TS}@p5.test", "role": "administrator"})
    code = db(f"SELECT invite_code FROM ems_users WHERE school_id='{sid}' AND email='bee.{TS}@p5.test'")
    s, acc = req("POST", "/auth/invite/accept", body={"code": code, "password": "password123"})
    tok2 = acc["token"]
    s, r = req("GET", f"/schools/{sid}/incidents/{iid}", tok2)
    check("non-responder detail 403", s == 403 and r["message"] == "Incident detail is restricted to the responders named on this case.")
    s, lst = req("GET", f"/schools/{sid}/incidents", tok2)
    check("non-responder sees register row", lst["total"] == 1 and lst["items"][0]["viewerIsResponder"] is False)
    check("register hides detail", "description" not in lst["items"][0] and lst["items"][0]["dataCategoryCount"] == 2)

    # ===================== §3.15 Merge ==================================
    print("Merge …")
    d1 = A("POST", "/students", {"admissionNumber": "DUP/1", "firstName": "Tunde", "lastName": "Duo",
           "dateOfBirth": "2012-02-02", "gender": "male", "classGroup": "JSS 1A", "status": "enrolled",
           "guardianName": "Ma", "guardianPhone": "08090001111"})[1]
    d2 = A("POST", "/students", {"admissionNumber": "DUP/2", "firstName": "Tunde", "lastName": "Duo",
           "dateOfBirth": "2012-02-02", "gender": "male", "classGroup": "JSS 1A", "status": "enrolled",
           "guardianName": "Pa", "guardianPhone": "08090001111"})[1]
    s, cand = A("GET", "/merge/candidates")
    pair = [c for c in cand["items"] if {c["a"]["admissionNumber"], c["b"]["admissionNumber"]} == {"DUP/1", "DUP/2"}]
    check("merge candidate 0.95", pair and pair[0]["score"] == 0.95)
    s, r = A("GET", "/merge/preview", query={"survivorId": d1["id"], "retiredId": d1["id"]})
    check("merge same 422", s == 422 and r["message"] == "Choose two different records to merge.")
    s, r = A("POST", "/merge", {"survivorId": d1["id"], "retiredId": d2["id"], "reason": ""})
    check("merge needs reason 422", s == 422 and r["message"] == "Record why these two records are the same person.")
    s, mr = A("POST", "/merge", {"survivorId": d1["id"], "retiredId": d2["id"], "reason": "One child"})
    check("merge ok", s == 200 and mr["survivorId"] == d1["id"])
    s, _ = A("GET", "/merge/preview", query={"survivorId": d1["id"], "retiredId": d2["id"]})
    check("retired gone 404", s == 404)
    # bursar cannot merge
    s, inv = A("POST", "/users/invite", {"name": "Cee Bursar", "email": f"cee.{TS}@p5.test", "role": "bursar"})
    code = db(f"SELECT invite_code FROM ems_users WHERE school_id='{sid}' AND email='cee.{TS}@p5.test'")
    tok3 = req("POST", "/auth/invite/accept", body={"code": code, "password": "password123"})[1]["token"]
    s, r = req("GET", f"/schools/{sid}/merge/candidates", tok3)
    check("bursar merge 403", s == 403 and r["message"] == "Merging records needs a registrar or administrator.")

    # ===================== §3.17 Imports ================================
    print("Imports …")
    csv = "\n".join([
        "admission_number,first_name,last_name,date_of_birth,gender,class_group,status,guardian_name,guardian_phone,enrolled_on",
        ",Dan,Fresh,2014-01-01,male,JSS 1A,,Papa,08031234567,",
        ",Amara,Ade,2013-04-04,female,JSS 1A,,Dup,08031234500,",  # dup of P5A/001
        ",Bad,Row,not-a-date,alien,No Class,,G,080,"])
    s, pv = A("POST", "/imports", {"kind": "students", "filename": "roll.csv", "text": csv})
    bid = pv["batch"]["id"]
    by_line = {r["lineNumber"]: r for r in pv["rows"]}
    check("import valid row", by_line[2]["check"] == "valid" and by_line[2]["decision"] == "import")
    check("import duplicate row", by_line[3]["check"] == "duplicate" and by_line[3]["decision"] == "undecided"
          and by_line[3]["matches"][0]["score"] == 0.95)
    check("import invalid row", by_line[4]["check"] == "invalid" and len(by_line[4]["issues"]) == 4)
    s, r = A("POST", f"/imports/{bid}/commit")
    check("commit blocked while undecided 422", s == 422 and "decision before this file" in r["message"])
    # merge the duplicate into the matched student
    A("PUT", f"/imports/{bid}/rows/{by_line[3]['id']}/decision",
      {"decision": "merge", "mergeTargetId": by_line[3]["matches"][0]["targetId"]})
    s, res = A("POST", f"/imports/{bid}/commit")
    check("commit tally", res["batch"]["result"] == {"created": 1, "merged": 1, "skipped": 0, "rejected": 1})
    s, r = A("POST", f"/imports/{bid}/discard")
    check("committed discard 409", s == 409 and r["message"] == "A committed import cannot be discarded.")
    s, ev = A("GET", "/audit/events", query={"entityType": "import"})
    check("import.committed audited", any(e["action"] == "import.committed" for e in ev["items"]))

    # ===================== §3.16 Promotion ==============================
    print("Promotion …")
    s, pp = A("GET", "/promotion/preview")
    check("promotion preview session", pp["fromSession"] == "2025/2026" and pp["toSession"] == "2026/2027")
    check("promotion suggests promote (no results)",
          all(r["suggested"] == "promote" and r["nextClass"] == "JSS 2A" for r in pp["rows"]))
    decisions = [{"studentId": r["studentId"], "decision": "promote"} for r in pp["rows"]]
    s, pres = A("POST", "/promotion/commit", {"fromSession": "2025/2026", "toSession": "2026/2027",
                "passMark": 40, "decisions": decisions})
    check("promotion committed", s == 200 and pres["promoted"] == len(decisions))
    s, r = A("POST", "/promotion/commit", {"fromSession": "2025/2026", "toSession": "2026/2027",
             "passMark": 40, "decisions": decisions})
    check("promotion idempotent 409", s == 409 and "already been promoted" in r["message"])

    print(f"\n{_passed} passed, {_failed} failed")
    sys.exit(1 if _failed else 0)


if __name__ == "__main__":
    main()
