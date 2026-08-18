#!/usr/bin/env python3
"""End-to-end contract test for the /api/ems surface (Phase 0 + Phase 1).

Exercises every endpoint and the doc-verbatim error messages, plus the
enrolment side-effect, guardian primary-sync, register correction trail,
calendar audit chain, last-admin/last-campus guards, and viewer scoping
(parent + teacher). Reads invite/reset codes straight from MySQL.
"""
import json, subprocess, sys, time, urllib.request, urllib.error

BASE = "http://127.0.0.1:8765/api/ems"
DB = "tss"
MYSQL = "/usr/local/opt/mysql/bin/mysql"
SUF = str(int(time.time()))[-6:]           # unique-per-run suffix
ORIGIN = "http://localhost:5173"

results = []   # (section, name, ok, detail)
_section = ["general"]

def sect(name): _section[0] = name

def check(name, ok, detail=""):
    results.append((_section[0], name, bool(ok), detail))
    tag = "PASS" if ok else "FAIL"
    print(f"  [{tag}] {name}" + (f"  -- {detail}" if (detail and not ok) else ""))
    return ok

def clear_throttle(name="*throttle*"):
    # The rate-limit counters live in Cake's file cache and persist across runs;
    # clear them so functional auth tests aren't tripped. Pass a narrower glob
    # (e.g. "*throttle_register_*") to reset ONE bucket without disturbing the
    # others — the sign-in flood test at the end needs its own bucket intact.
    subprocess.run(f"find /Users/mac/Downloads/ltalms/tmp/cache -type f -name '{name}' -delete",
                   shell=True, capture_output=True)

def db(sql):
    out = subprocess.run([MYSQL, "-uroot", "-N", "-e", sql, DB],
                         capture_output=True, text=True)
    if out.returncode != 0:
        raise RuntimeError(f"mysql error: {out.stderr.strip()}\nSQL: {sql}")
    return out.stdout.strip()

def req(method, path, token=None, body=None, query=None, origin=None, raw=False):
    url = BASE + path
    if query:
        from urllib.parse import urlencode
        url += "?" + urlencode({k: v for k, v in query.items() if v is not None})
    data = None
    headers = {"Accept": "application/json"}
    if body is not None:
        data = json.dumps(body).encode()
        headers["Content-Type"] = "application/json"
    if token:
        headers["Authorization"] = "Bearer " + token
    if origin:
        headers["Origin"] = origin
    r = urllib.request.Request(url, data=data, headers=headers, method=method)
    try:
        resp = urllib.request.urlopen(r)
        status, hdrs, text = resp.status, dict(resp.headers), resp.read().decode()
    except urllib.error.HTTPError as e:
        status, hdrs, text = e.code, dict(e.headers), e.read().decode()
    except urllib.error.URLError as e:
        return -1, {}, str(e)
    if raw:
        return status, hdrs, text
    try:
        parsed = json.loads(text) if text else None
    except json.JSONDecodeError:
        parsed = text
    return status, hdrs, parsed

def msg(body):
    return body.get("message") if isinstance(body, dict) else None

def register(name, email, password, school_extra=None):
    """registerSchool takes the contract's nested {school, admin} body. This
    suite legitimately registers ~7 schools, but register is throttled 5/900s;
    reset just that bucket per call so functional coverage isn't throttle-gated.
    (Register throttling itself is covered by AuthThrottleTest, not here.)"""
    clear_throttle("*throttle_register_*")
    school = {"name": name}
    if school_extra:
        school.update(school_extra)
    return req("POST", "/auth/register-school",
               body={"school": school, "admin": {"name": "Root Admin", "email": email, "password": password}})

# ---------------------------------------------------------------------------
print("=" * 70)
print(f"EMS /api/ems E2E — run suffix {SUF}")
print("=" * 70)
clear_throttle()

# === PHASE 0: AUTH & REGISTRATION ==========================================
sect("auth/register")
school_name = f"E2E Academy {SUF}"
admin_email = f"admin.{SUF}@e2e.test"
st, h, b = register(school_name, admin_email, "password123")
ok = check("register-school 2xx", st in (200, 201), f"status={st} body={b}")
admin_token = school_id = slug = None
if ok:
    admin_token = b.get("token"); slug = b["school"]["slug"]
    school_id = b["school"]["id"]
    check("register returns {user,school,token}",
          all(k in b for k in ("user", "school", "token")) and admin_token,
          str(list(b.keys())))
    check("admin user role=administrator, status=active",
          b["user"]["role"] == "administrator" and b["user"]["status"] == "active",
          str(b["user"]))
    check("no password_hash leaked in user",
          "passwordHash" not in b["user"] and "password_hash" not in b["user"])
else:
    print("FATAL: registration failed, cannot continue"); sys.exit(2)

check("register blank name -> 422 'The school needs a name.'",
      (lambda r: r[0] == 422 and msg(r[2]) == "The school needs a name.")(
          register("  ", f"x.{SUF}@e2e.test", "password123")))
check("register dup name -> 422 name taken",
      (lambda r: r[0] == 422 and msg(r[2]) == "A school with this name is already registered.")(
          register(school_name, f"dup.{SUF}@e2e.test", "password123")))
check("register dup email -> 422 email exists",
      (lambda r: r[0] == 422 and msg(r[2]) == "An account with this e-mail already exists.")(
          register(f"Other {SUF}", admin_email, "password123")))
check("register short password -> 422 password min",
      (lambda r: r[0] == 422 and msg(r[2]) == "The password needs at least 8 characters.")(
          register(f"Short {SUF}", f"short.{SUF}@e2e.test", "abc")))

sect("auth/sign-in")
st, h, b = req("POST", "/auth/sign-in", body={"email": admin_email, "password": "password123"})
check("sign-in admin 200 + token", st == 200 and b.get("token"), f"status={st}")
if st == 200:
    admin_token = b["token"]
check("sign-in wrong password -> 401 'Incorrect e-mail or password.'",
      (lambda r: r[0] == 401 and msg(r[2]) == "Incorrect e-mail or password.")(
          req("POST", "/auth/sign-in", body={"email": admin_email, "password": "wrongpass1"})))
check("sign-in unknown email -> 401 (same message, enumeration-safe)",
      (lambda r: r[0] == 401 and msg(r[2]) == "Incorrect e-mail or password.")(
          req("POST", "/auth/sign-in", body={"email": f"ghost.{SUF}@e2e.test", "password": "password123"})))

sect("auth/by-slug + token guards")
st, h, b = req("GET", f"/schools/by-slug/{slug}", token=admin_token)
check("by-slug own school 200", st == 200 and b.get("slug") == slug, f"status={st}")
check("by-slug unknown -> 404 'That school could not be found.'",
      (lambda r: r[0] == 404 and msg(r[2]) == "That school could not be found.")(
          req("GET", f"/schools/by-slug/nope-{SUF}", token=admin_token)))
check("no-token protected route -> 401",
      req("GET", f"/schools/{school_id}/students")[0] == 401)
check("garbage token -> 401",
      req("GET", f"/schools/{school_id}/students", token="not.a.jwt")[0] == 401)

sect("cross-cutting headers/CORS")
st, h, b = req("GET", f"/schools/{school_id}/students", token=admin_token)
cc = h.get("Cache-Control", "")
check("Cache-Control: private, no-store present",
      "private" in cc and "no-store" in cc, f"Cache-Control={cc!r}")
st, h, b = req("OPTIONS", "/students/foo", origin=ORIGIN, raw=True)
acao = h.get("Access-Control-Allow-Origin", "")
check("CORS preflight OPTIONS -> 204 + echoes origin",
      st == 204 and acao in (ORIGIN, "*"), f"status={st} ACAO={acao!r}")

# === LIST ENVELOPE =========================================================
sect("envelope")
st, h, b = req("GET", f"/schools/{school_id}/students", token=admin_token)
check("list envelope has items,total,page,pageSize",
      isinstance(b, dict) and all(k in b for k in ("items", "total", "page", "pageSize")),
      str(b if not isinstance(b, dict) else list(b.keys())))
check("pageSize cap 100 (request 500 -> <=100)",
      (lambda ps: isinstance(ps, int) and ps <= 100)(
          req("GET", f"/schools/{school_id}/students", token=admin_token,
              query={"pageSize": 500})[2].get("pageSize")))

# === SETTINGS: SCHOOL ======================================================
sect("settings/school")
st, h, b = req("GET", f"/schools/{school_id}/school", token=admin_token)
check("school view 200", st == 200 and b.get("id") == school_id, f"status={st}")
st, h, b = req("PUT", f"/schools/{school_id}/school", token=admin_token,
               body={"motto": "Ad Astra", "logo": "data:image/png;base64,AAAA"})
check("school update sets motto+logo", st == 200 and b.get("motto") == "Ad Astra" and b.get("logo"),
      f"status={st}")
st, h, b = req("PUT", f"/schools/{school_id}/school", token=admin_token, body={"motto": "Kept"})
check("school update omit logo -> logo kept (tri-state)", b.get("logo") == "data:image/png;base64,AAAA",
      f"logo={b.get('logo')!r}")
st, h, b = req("PUT", f"/schools/{school_id}/school", token=admin_token, body={"logo": None})
check("school update logo:null -> cleared", b.get("logo") in (None, ""), f"logo={b.get('logo')!r}")

# === SETTINGS: USERS / INVITES =============================================
sect("settings/users")
st, h, b = req("GET", f"/schools/{school_id}/users", token=admin_token)
check("users index envelope", st == 200 and b.get("total", 0) >= 1, f"status={st}")
check("users list projects credentials away (no passwordHash/inviteCode)",
      all("passwordHash" not in u and "inviteCode" not in u for u in b["items"]))

# invite a plain admin (so we can later exercise last-admin needing 2nd admin)
st, h, b = req("POST", f"/schools/{school_id}/users/invite", token=admin_token,
               body={"name": "Second Admin", "email": f"admin2.{SUF}@e2e.test", "role": "administrator"})
check("invite administrator 201 status=invited", st == 201 and b.get("status") == "invited", f"status={st} {b}")
check("invite response hides inviteCode", "inviteCode" not in (b or {}))
admin2_id = b.get("id") if st == 201 else None
check("invite dup email -> 422 email exists",
      (lambda r: r[0] == 422 and msg(r[2]) == "An account with this e-mail already exists.")(
          req("POST", f"/schools/{school_id}/users/invite", token=admin_token,
              body={"name": "Dup", "email": admin_email, "role": "registrar"})))

# accept the 2nd admin invite (activate) so the school has two admins
code2 = db(f"SELECT invite_code FROM ems_users WHERE id='{admin2_id}'") if admin2_id else ""
check("invite code persisted in ems_users", bool(code2), f"code={code2!r}")
check("sign-in invited account -> 403 pending",
      (lambda r: r[0] == 403 and msg(r[2]) == "This account has a pending invitation. Redeem your invite code to finish setting up.")(
          req("POST", "/auth/sign-in", body={"email": f"admin2.{SUF}@e2e.test", "password": "password123"})))
check("invite lookup blank -> 422 'Enter your invite code.'",
      (lambda r: r[0] == 422 and msg(r[2]) == "Enter your invite code.")(
          req("POST", "/auth/invite/lookup", body={"code": "  "})))
check("invite lookup unknown -> 404",
      (lambda r: r[0] == 404 and msg(r[2]) == "That invite code was not recognised. Check it with your school.")(
          req("POST", "/auth/invite/lookup", body={"code": "ZZZZ-ZZZZ"})))
st, h, b = req("POST", "/auth/invite/lookup", body={"code": code2})
check("invite lookup valid 200", st == 200, f"status={st} {b}")
st, h, b = req("POST", "/auth/invite/accept", body={"code": code2, "password": "password123"})
check("invite accept 200/activates", st in (200, 201) and (b.get("token") or b.get("user")), f"status={st} {b}")
check("accepted admin2 can now sign in",
      req("POST", "/auth/sign-in", body={"email": f"admin2.{SUF}@e2e.test", "password": "password123"})[0] == 200)
# fresh invite (unburned code) to exercise the accept password-min guard
st, h, b = req("POST", f"/schools/{school_id}/users/invite", token=admin_token,
               body={"name": "PwTest", "email": f"pwtest.{SUF}@e2e.test", "role": "registrar"})
pwcode = db(f"SELECT invite_code FROM ems_users WHERE id='{b.get('id')}'")
check("invite accept short password -> 422 (fresh code)",
      (lambda r: r[0] == 422 and msg(r[2]) == "The password needs at least 8 characters.")(
          req("POST", "/auth/invite/accept", body={"code": pwcode, "password": "abc"})))

# last-admin guard requires demoting BOTH admins; with 2 active admins these should SUCCEED,
# so instead disable admin2 then attempt to disable the original -> guard fires.
st, _, _ = req("PUT", f"/schools/{school_id}/users/{admin2_id}/status", token=admin_token,
               body={"status": "disabled"})
admin_user_id = None
_, _, ub = req("GET", f"/schools/{school_id}/users", token=admin_token, query={"pageSize": 100})
for u in ub["items"]:
    if u["email"] == admin_email:
        admin_user_id = u["id"]
check("disable 2nd admin ok (still one active admin left)", st == 200, f"status={st}")
check("disable last active admin -> 422 last-admin",
      (lambda r: r[0] == 422 and msg(r[2]) == "A school needs at least one active administrator.")(
          req("PUT", f"/schools/{school_id}/users/{admin_user_id}/status", token=admin_token,
              body={"status": "disabled"})))
check("demote last active admin role -> 422 last-admin",
      (lambda r: r[0] == 422 and msg(r[2]) == "A school needs at least one active administrator.")(
          req("PUT", f"/schools/{school_id}/users/{admin_user_id}/role", token=admin_token,
              body={"role": "registrar"})))

# revoke-invite guards
st, h, b = req("POST", f"/schools/{school_id}/users/invite", token=admin_token,
               body={"name": "ToRevoke", "email": f"revoke.{SUF}@e2e.test", "role": "bursar"})
revoke_id = b.get("id")
check("revoke active user's invite -> 422 only-pending",
      (lambda r: r[0] == 422 and msg(r[2]) == "Only a pending invitation can be revoked.")(
          req("DELETE", f"/schools/{school_id}/users/{admin_user_id}/invite", token=admin_token)))
check("revoke pending invite -> 204",
      req("DELETE", f"/schools/{school_id}/users/{revoke_id}/invite", token=admin_token)[0] == 204)

# password reset
sect("auth/reset")
st, h, b = req("POST", "/auth/reset/request", body={"email": admin_email})
check("reset request -> {sent:true} always", st == 200 and b.get("sent") is True, f"{st} {b}")
check("reset request unknown email -> still {sent:true}",
      (lambda r: r[0] == 200 and r[2].get("sent") is True)(
          req("POST", "/auth/reset/request", body={"email": f"ghost.{SUF}@e2e.test"})))
reset_code = db(f"SELECT code FROM ems_password_resets r JOIN ems_users u ON u.id=r.user_id "
                f"WHERE u.email='{admin_email}' ORDER BY r.created ASC LIMIT 1")
check("reset confirm bad code -> 400 invalid",
      (lambda r: r[0] == 400 and msg(r[2]) == "That reset code is not valid any more. Ask for a new one and try again.")(
          req("POST", "/auth/reset/confirm", body={"email": admin_email, "code": "000000", "password": "newpassw0rd"})))
st, h, b = req("POST", "/auth/reset/confirm",
               body={"email": admin_email, "code": reset_code, "password": "newpassw0rd"})
check("reset confirm valid code 200", st in (200, 201), f"status={st} {b}")
check("sign-in with new password works",
      req("POST", "/auth/sign-in", body={"email": admin_email, "password": "newpassw0rd"})[0] == 200)
check("reset code single-use (reuse -> 400)",
      req("POST", "/auth/reset/confirm",
          body={"email": admin_email, "code": reset_code, "password": "another0ne"})[0] == 400)
# refresh admin token (password changed)
admin_token = req("POST", "/auth/sign-in", body={"email": admin_email, "password": "newpassw0rd"})[2]["token"]

# === TEACHERS ==============================================================
sect("teachers")
def add_teacher(staff, fn, ln, subjects):
    return req("POST", f"/schools/{school_id}/teachers", token=admin_token, body={
        "staffNumber": staff, "firstName": fn, "lastName": ln,
        "email": f"{staff}.{SUF}@e2e.test", "phone": "08000000000",
        "gender": "female", "subjects": subjects, "status": "active"})
st, h, t1 = add_teacher("TSC001", "Grace", "Bello", ["Mathematics", "Physics"])
check("teacher add 201", st == 201 and t1.get("id"), f"status={st} {t1}")
st, h, t2 = add_teacher("TSC002", "Musa", "Ade", ["English"])
teacher1_id = t1.get("id"); teacher2_id = t2.get("id")
st, h, b = req("GET", f"/schools/{school_id}/teachers", token=admin_token)
check("teacher index envelope >=2", st == 200 and b.get("total", 0) >= 2, f"{st} {b.get('total')}")
check("teacher query filter (staffNumber) narrows",
      req("GET", f"/schools/{school_id}/teachers", token=admin_token,
          query={"query": "TSC001"})[2]["total"] == 1)
check("teacher view 200", req("GET", f"/schools/{school_id}/teachers/{teacher1_id}", token=admin_token)[0] == 200)
check("teacher view unknown -> 404",
      (lambda r: r[0] == 404 and msg(r[2]) == "That teacher record could not be found.")(
          req("GET", f"/schools/{school_id}/teachers/does-not-exist", token=admin_token)))
st, h, b = req("GET", f"/schools/{school_id}/teachers/subjects", token=admin_token)
check("teacher subjects union sorted",
      isinstance(b, list) and "Mathematics" in b and "English" in b, str(b))
st, h, b = req("PUT", f"/schools/{school_id}/teachers/{teacher2_id}", token=admin_token,
               body={"staffNumber": "TSC002", "firstName": "Musa", "lastName": "Adeyemi",
                     "email": f"TSC002.{SUF}@e2e.test", "subjects": ["English", "Literature"], "status": "active"})
check("teacher edit 200 (lastName updated)", st == 200 and b.get("lastName") == "Adeyemi", f"{st} {b}")

# === SEED classes / allocations / timetable (no Phase-1 create endpoints) ==
sect("seed classes")
import uuid as _uuid
def uid(): return str(_uuid.uuid4())
cg1, cg2 = uid(), uid()
db(f"""INSERT INTO ems_class_groups (id,school_id,name,level,stream,form_teacher_id,capacity,created,modified)
       VALUES ('{cg1}','{school_id}','JSS 1A','JSS 1','A','{teacher1_id}',30,NOW(),NOW()),
              ('{cg2}','{school_id}','JSS 2A','JSS 2','A','{teacher2_id}',30,NOW(),NOW())""")
maths_id = db(f"SELECT id FROM ems_subjects WHERE school_id='{school_id}' AND name='Mathematics'").strip().splitlines()[-1]
db(f"""INSERT INTO ems_subject_allocations (id,school_id,class_group_id,subject_id,teacher_id,created,modified)
       VALUES ('{uid()}','{school_id}','{cg1}','{maths_id}','{teacher1_id}',NOW(),NOW())""")
db(f"""INSERT INTO ems_timetable_slots (id,school_id,class_group_id,day,period,subject_id,teacher_id,created,modified)
       VALUES ('{uid()}','{school_id}','{cg1}','Mon',1,'{maths_id}','{teacher1_id}',NOW(),NOW())""")
check("seeded 2 class groups + allocation + timetable", True)

check("teacher assignments (allocation joined to class name)",
      (lambda r: r[0] == 200 and any(a.get("className") == "JSS 1A" for a in r[2]))(
          req("GET", f"/schools/{school_id}/teachers/{teacher1_id}/assignments", token=admin_token)))
check("teacher day schedule (?day=Mon) has a slot with times",
      (lambda r: r[0] == 200 and isinstance(r[2], list) and any(s.get("subject") == "Mathematics" for s in r[2]))(
          req("GET", f"/schools/{school_id}/teachers/{teacher1_id}/day", token=admin_token, query={"day": "Mon"})))

# === CALENDAR (need an open session for student enrolment side-effect) =====
sect("calendar")
st, h, b = req("POST", f"/schools/{school_id}/calendar/sessions", token=admin_token,
               body={"name": "2026/2027", "startsOn": "2026-09-01", "endsOn": "2027-07-31"})
check("createSession 201 open", st == 201 and b.get("status") == "open", f"{st} {b}")
session_id = b.get("id")
check("createSession bad name -> 422 'Name a session like 2026/2027.'",
      (lambda r: r[0] == 422 and msg(r[2]) == "Name a session like 2026/2027.")(
          req("POST", f"/schools/{school_id}/calendar/sessions", token=admin_token,
              body={"name": "2026-2027", "startsOn": "2026-09-01", "endsOn": "2027-07-31"})))
check("createSession missing dates -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "Both a start and an end date are needed.")(
          req("POST", f"/schools/{school_id}/calendar/sessions", token=admin_token, body={"name": "2030/2031"})))
check("createSession date order -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "The start date must come before the end date.")(
          req("POST", f"/schools/{school_id}/calendar/sessions", token=admin_token,
              body={"name": "2031/2032", "startsOn": "2032-01-01", "endsOn": "2031-01-01"})))
check("createSession dup name -> 422 exists",
      (lambda r: r[0] == 422 and msg(r[2]) == "The 2026/2027 session already exists.")(
          req("POST", f"/schools/{school_id}/calendar/sessions", token=admin_token,
              body={"name": "2026/2027", "startsOn": "2030-09-01", "endsOn": "2031-07-31"})))
check("createSession overlap -> 422 overlaps",
      (lambda r: r[0] == 422 and "overlap the 2026/2027 session." in (msg(r[2]) or ""))(
          req("POST", f"/schools/{school_id}/calendar/sessions", token=admin_token,
              body={"name": "2027/2028", "startsOn": "2027-01-01", "endsOn": "2028-06-30"})))

# terms
def add_term(name, s, e):
    return req("POST", f"/schools/{school_id}/calendar/sessions/{session_id}/terms", token=admin_token,
               body={"name": name, "startsOn": s, "endsOn": e})
st, h, term1 = add_term("First", "2026-09-01", "2026-12-15")
check("createTerm First 201", st == 201, f"{st} {term1}")
st, h, term2 = add_term("Second", "2027-01-05", "2027-04-10")
check("createTerm Second 201", st == 201, f"{st} {term2}")
term1_id = term1.get("id"); term2_id = term2.get("id")
check("createTerm dup -> 422 exists",
      (lambda r: r[0] == 422 and msg(r[2]) == "The First term already exists in 2026/2027.")(
          add_term("First", "2026-09-02", "2026-12-14")))
check("createTerm dates outside session -> 422 inside",
      (lambda r: r[0] == 422 and msg(r[2]) == "Term dates must fall inside the 2026/2027 session.")(
          add_term("Third", "2027-05-01", "2027-09-30")))

# close/reopen term -> audit
st, h, b = req("POST", f"/schools/{school_id}/calendar/terms/{term1_id}/close", token=admin_token, body={})
check("closeTerm 200 status=closed", st == 200 and b.get("status") == "closed", f"{st} {b}")
check("updateTermDates on closed term -> 422 term closed",
      (lambda r: r[0] == 422 and msg(r[2]) == "This term is closed. Reopen it, with a reason, before changing it.")(
          req("PUT", f"/schools/{school_id}/calendar/terms/{term1_id}/dates", token=admin_token,
              body={"startsOn": "2026-09-01", "endsOn": "2026-12-10"})))
check("reopenTerm without reason -> 422 needs reason",
      (lambda r: r[0] == 422 and msg(r[2]) == "Reopening a closed term needs a reason for the audit history.")(
          req("POST", f"/schools/{school_id}/calendar/terms/{term1_id}/reopen", token=admin_token, body={})))
st, h, b = req("POST", f"/schools/{school_id}/calendar/terms/{term1_id}/reopen", token=admin_token,
               body={"reason": "Correction to results"})
check("reopenTerm with reason 200 open", st == 200 and b.get("status") == "open", f"{st} {b}")

# close session requires all terms closed
check("closeSession with open terms -> 422 lists open terms",
      (lambda r: r[0] == 422 and "term" in (msg(r[2]) or "") and "first." in (msg(r[2]) or ""))(
          req("POST", f"/schools/{school_id}/calendar/sessions/{session_id}/close", token=admin_token, body={})))
# close both terms then session
req("POST", f"/schools/{school_id}/calendar/terms/{term1_id}/close", token=admin_token, body={})
req("POST", f"/schools/{school_id}/calendar/terms/{term2_id}/close", token=admin_token, body={})
st, h, b = req("POST", f"/schools/{school_id}/calendar/sessions/{session_id}/close", token=admin_token, body={})
check("closeSession (all terms closed) 200 closed", st == 200 and b.get("status") == "closed", f"{st} {b}")
check("reopenSession without reason -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "Reopening a closed session needs a reason for the audit history.")(
          req("POST", f"/schools/{school_id}/calendar/sessions/{session_id}/reopen", token=admin_token, body={})))
st, h, b = req("POST", f"/schools/{school_id}/calendar/sessions/{session_id}/reopen", token=admin_token,
               body={"reason": "Reopen for enrolment"})
check("reopenSession with reason 200 open", st == 200 and b.get("status") == "open", f"{st} {b}")

st, h, b = req("GET", f"/schools/{school_id}/calendar/audit", token=admin_token)
check("calendar audit paginated envelope + events recorded",
      st == 200 and b.get("total", 0) >= 4, f"{st} total={b.get('total')}")
if isinstance(b, dict) and b.get("items"):
    ats = [e.get("at") for e in b["items"] if e.get("at")]
    check("audit newest-first ordering", ats == sorted(ats, reverse=True), str(ats[:3]))

# reopen the two terms so we have an open session+term for later enrolment (level path)
req("POST", f"/schools/{school_id}/calendar/terms/{term1_id}/reopen", token=admin_token, body={"reason": "x"})

# === CAMPUSES ==============================================================
sect("campuses")
st, h, b = req("POST", f"/schools/{school_id}/campuses", token=admin_token,
               body={"name": "Main Campus", "address": "1 Road"})
check("campus add 201 active", st == 201 and b.get("active") is True, f"{st} {b}")
camp1 = b.get("id")
check("campus dup name (case-insensitive) -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "A campus called MAIN CAMPUS already exists.")(
          req("POST", f"/schools/{school_id}/campuses", token=admin_token, body={"name": "MAIN CAMPUS"})))
check("campus blank name -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "The campus needs a name.")(
          req("POST", f"/schools/{school_id}/campuses", token=admin_token, body={"name": " "})))
st, h, b2 = req("POST", f"/schools/{school_id}/campuses", token=admin_token, body={"name": "Annex"})
camp2 = b2.get("id")
check("campus edit 200", req("PUT", f"/schools/{school_id}/campuses/{camp1}", token=admin_token,
      body={"name": "Main Campus", "address": "2 Road"})[0] == 200)
req("POST", f"/schools/{school_id}/campuses/{camp2}/active", token=admin_token, body={"active": False})
check("deactivate last active campus -> 422 needs one active",
      (lambda r: r[0] == 422 and msg(r[2]) == "A school needs at least one active campus.")(
          req("POST", f"/schools/{school_id}/campuses/{camp1}/active", token=admin_token, body={"active": False})))

# === STUDENTS + enrolment side effect ======================================
sect("students")
def add_student(adm, fn, ln, cls, status="enrolled"):
    return req("POST", f"/schools/{school_id}/students", token=admin_token, body={
        "admissionNumber": adm, "firstName": fn, "lastName": ln,
        "dateOfBirth": "2015-05-05", "gender": "male", "classGroup": cls,
        "status": status, "guardianName": "Parent One", "guardianPhone": "08011112222"})
st, h, s1 = add_student("ADM001", "Bola", "Ade", "JSS 1A")
check("student add 201 (enrolled)", st == 201 and s1.get("id"), f"{st} {s1}")
student1_id = s1.get("id")
st, h, s2 = add_student("ADM002", "Chidi", "Obi", "JSS 1A")
student2_id = s2.get("id")
st, h, s3 = add_student("ADM003", "Ada", "Eze", "JSS 2A", status="prospective")
student3_id = s3.get("id")
# enrolment side-effect: enrolled -> active enrolment, level=JSS 1
st, h, en = req("GET", f"/schools/{school_id}/students/{student1_id}/enrolments", token=admin_token)
check("enrolled student gained active enrolment (side effect)",
      st == 200 and isinstance(en, list) and any(e.get("status") == "active" for e in en), f"{st} {en}")
if en:
    check("enrolment level = classGroup minus stream ('JSS 1A' -> 'JSS 1')",
          any(e.get("level") == "JSS 1" for e in en), str([e.get("level") for e in en]))
st, h, en3 = req("GET", f"/schools/{school_id}/students/{student3_id}/enrolments", token=admin_token)
check("non-enrolled student has NO enrolment (no side effect)", en3 == [], str(en3))

st, h, b = req("GET", f"/schools/{school_id}/students/class-groups", token=admin_token)
check("student class-groups distinct sorted", isinstance(b, list) and "JSS 1A" in b and "JSS 2A" in b, str(b))
# filters + sort + query
check("students status filter",
      req("GET", f"/schools/{school_id}/students", token=admin_token,
          query={"status": "enrolled"})[2]["total"] == 2)
check("students classGroup filter",
      req("GET", f"/schools/{school_id}/students", token=admin_token,
          query={"classGroup": "JSS 2A"})[2]["total"] == 1)
check("students query filter (admission number)",
      req("GET", f"/schools/{school_id}/students", token=admin_token,
          query={"query": "ADM002"})[2]["total"] == 1)
# Contract name-sort = "lastName firstName" ascending (mock seed.ts:810 parity)
st, h, b = req("GET", f"/schools/{school_id}/students", token=admin_token, query={"sort": "name"})
keys = [f"{i['lastName']} {i['firstName']}" for i in b["items"]]
check("students sort=name ascending (by lastName firstName)", keys == sorted(keys), str(keys))
check("student view 200", req("GET", f"/schools/{school_id}/students/{student1_id}", token=admin_token)[0] == 200)
check("student view unknown -> 404",
      (lambda r: r[0] == 404 and msg(r[2]) == "That student record could not be found.")(
          req("GET", f"/schools/{school_id}/students/ghost", token=admin_token)))
# edit / assignClass
st, h, b = req("PUT", f"/schools/{school_id}/students/{student2_id}", token=admin_token, body={
    "admissionNumber": "ADM002", "firstName": "Chidi", "lastName": "Obinna",
    "dateOfBirth": "2015-05-05", "gender": "male", "classGroup": "JSS 1A", "status": "enrolled"})
check("student edit (lastName) 200", st == 200 and b.get("lastName") == "Obinna", f"{st} {b}")
st, h, b = req("POST", f"/schools/{school_id}/students/{student2_id}/class", token=admin_token,
               body={"classGroup": "JSS 2A"})
check("student assignClass 200 updates placement", st == 200 and b.get("classGroup") == "JSS 2A", f"{st} {b}")
req("POST", f"/schools/{school_id}/students/{student2_id}/class", token=admin_token, body={"classGroup": "JSS 1A"})

# guardians + primary sync
sect("guardians")
st, h, g1 = req("POST", f"/schools/{school_id}/students/{student1_id}/guardians", token=admin_token,
                body={"firstName": "Femi", "lastName": "Ade", "relationship": "father",
                      "phone": "08033334444", "email": f"femi.{SUF}@e2e.test"})
check("first guardian add 201 forced primary", st == 201 and g1.get("isPrimary") is True, f"{st} {g1}")
guardian1_id = g1.get("id")
st, h, g2 = req("POST", f"/schools/{school_id}/students/{student1_id}/guardians", token=admin_token,
                body={"firstName": "Ngozi", "lastName": "Ade", "relationship": "mother",
                      "phone": "08055556666", "isPrimary": True})
check("second guardian isPrimary:true demotes sibling", st == 201 and g2.get("isPrimary") is True, f"{st} {g2}")
guardian2_id = g2.get("id")
st, h, glist = req("GET", f"/schools/{school_id}/students/{student1_id}/guardians", token=admin_token)
check("guardians list primary-first", glist[0]["isPrimary"] is True and glist[0]["id"] == guardian2_id, str(glist))
check("only one primary after demotion", sum(1 for g in glist if g["isPrimary"]) == 1)
# student denormalized contact re-synced to new primary (Ngozi)
st, h, sv = req("GET", f"/schools/{school_id}/students/{student1_id}", token=admin_token)
check("student guardianName re-synced to primary (Ngozi Ade)",
      sv.get("guardianName") == "Ngozi Ade" and sv.get("guardianPhone") == "08055556666",
      f"name={sv.get('guardianName')} phone={sv.get('guardianPhone')}")
# setPrimary back to guardian1, delete primary promotes sibling
req("POST", f"/schools/{school_id}/guardians/{guardian1_id}/primary", token=admin_token, body={})
st, _, sv = req("GET", f"/schools/{school_id}/students/{student1_id}", token=admin_token)
check("setPrimary re-syncs student contact (Femi Ade)", sv.get("guardianName") == "Femi Ade", str(sv.get("guardianName")))
check("guardian edit 200",
      req("PUT", f"/schools/{school_id}/guardians/{guardian2_id}", token=admin_token,
          body={"firstName": "Ngozi", "lastName": "Adewale", "relationship": "mother", "phone": "08055556666"})[0] == 200)
check("delete primary guardian -> 204 & promotes sibling",
      req("DELETE", f"/schools/{school_id}/guardians/{guardian1_id}", token=admin_token)[0] == 204)
st, _, glist = req("GET", f"/schools/{school_id}/students/{student1_id}/guardians", token=admin_token)
check("after deleting primary, remaining sibling promoted",
      len(glist) == 1 and glist[0]["isPrimary"] is True, str(glist))

# attendance / academics
sect("students/attendance+academics")
st, h, att = req("GET", f"/schools/{school_id}/students/{student1_id}/attendance", token=admin_token)
check("attendance returns {records, summary}", st == 200 and "records" in att and "summary" in att, str(att)[:120])
check("attendance rate 0 when no records", att["summary"].get("rate") == 0 or att["summary"].get("total") == 0, str(att["summary"]))
check("academics 200 list", req("GET", f"/schools/{school_id}/students/{student1_id}/academics", token=admin_token)[0] == 200)

# === CLASSES (read surfaces) ===============================================
sect("classes")
st, h, b = req("GET", f"/schools/{school_id}/classes", token=admin_token)
check("classes index envelope", st == 200 and b.get("total", 0) >= 2, f"{st} {b.get('total')}")
summ = {c["name"]: c for c in b["items"]}
check("class summary studentCount by NAME match (JSS 1A has 2)",
      summ.get("JSS 1A", {}).get("studentCount") == 2, str(summ.get("JSS 1A")))
check("class summary formTeacherName populated",
      bool(summ.get("JSS 1A", {}).get("formTeacherName")), str(summ.get("JSS 1A", {}).get("formTeacherName")))
st, h, b = req("GET", f"/schools/{school_id}/classes", token=admin_token, query={"sort": "size"})
sizes = [c["studentCount"] for c in b["items"]]
check("classes sort=size descending", sizes == sorted(sizes, reverse=True), str(sizes))
check("class view 200", req("GET", f"/schools/{school_id}/classes/{cg1}", token=admin_token)[0] == 200)
check("class view unknown -> 404",
      (lambda r: r[0] == 404 and msg(r[2]) == "That class could not be found.")(
          req("GET", f"/schools/{school_id}/classes/ghost", token=admin_token)))
check("class levels list", "JSS 1" in (req("GET", f"/schools/{school_id}/classes/levels", token=admin_token)[2] or []))
st, h, roster = req("GET", f"/schools/{school_id}/classes/{cg1}/roster", token=admin_token)
check("class roster lists enrolled students in class", st == 200 and len(roster) == 2, f"{st} n={len(roster) if isinstance(roster,list) else roster}")
check("class allocations 200", req("GET", f"/schools/{school_id}/classes/{cg1}/allocations", token=admin_token)[0] == 200)
check("class timetable 200", req("GET", f"/schools/{school_id}/classes/{cg1}/timetable", token=admin_token)[0] == 200)

# register GET/PUT + correction trail
sect("classes/register")
st, h, reg = req("GET", f"/schools/{school_id}/classes/{cg1}/register", token=admin_token, query={"date": "2026-09-07"})
check("register GET 200 with rows default present",
      st == 200 and reg.get("rows") and all(r["status"] == "present" for r in reg["rows"]), f"{st}")
check("register GET missing date -> 400",
      (lambda r: r[0] == 400 and msg(r[2]) == "A register needs a date.")(
          req("GET", f"/schools/{school_id}/classes/{cg1}/register", token=admin_token)))
entries = [{"studentId": student1_id, "status": "absent"}, {"studentId": student2_id, "status": "present"}]
st, h, b = req("PUT", f"/schools/{school_id}/classes/{cg1}/register", token=admin_token,
               query={"date": "2026-09-07"}, body={"entries": entries})
check("register PUT first submit ok", st in (200, 204), f"{st} {b}")
check("register resubmit WITHOUT reason -> 422 correction",
      (lambda r: r[0] == 422 and msg(r[2]) == "This register is already submitted. A correction needs a reason for the record.")(
          req("PUT", f"/schools/{school_id}/classes/{cg1}/register", token=admin_token,
              query={"date": "2026-09-07"}, body={"entries": entries})))
st, h, b = req("PUT", f"/schools/{school_id}/classes/{cg1}/register", token=admin_token,
               query={"date": "2026-09-07"},
               body={"entries": entries, "reason": "Marked Bola present after note from parent"})
check("register resubmit WITH reason ok", st in (200, 204), f"{st} {b}")
st, h, reg = req("GET", f"/schools/{school_id}/classes/{cg1}/register", token=admin_token, query={"date": "2026-09-07"})
corr = (reg.get("session") or {}).get("corrections") or []
check("correction appended to session trail (by/on/reason)",
      len(corr) >= 1 and corr[0].get("reason", "").startswith("Marked Bola"), str(corr))
# attendance summary now reflects the absent record
st, h, att = req("GET", f"/schools/{school_id}/students/{student1_id}/attendance", token=admin_token)
check("attendance now has record + rate computed",
      att["summary"].get("total", 0) >= 1, str(att["summary"]))

# === VIEWER SCOPING: parent + teacher ======================================
sect("scope/parent")
# invite a parent linked to student1 only
st, h, b = req("POST", f"/schools/{school_id}/users/invite", token=admin_token,
               body={"name": "Parent Bola", "email": f"parent.{SUF}@e2e.test", "role": "parent",
                     "link": {"kind": "parent", "studentIds": [student1_id]}})
parent_id = b.get("id")
pcode = db(f"SELECT invite_code FROM ems_users WHERE id='{parent_id}'")
req("POST", "/auth/invite/accept", body={"code": pcode, "password": "password123"})
ptoken = req("POST", "/auth/sign-in", body={"email": f"parent.{SUF}@e2e.test", "password": "password123"})[2]["token"]
check("parent reads own ward -> 200",
      req("GET", f"/schools/{school_id}/students/{student1_id}", token=ptoken)[0] == 200)
check("parent reading other family -> 403 'This record belongs to another family.'",
      (lambda r: r[0] == 403 and msg(r[2]) == "This record belongs to another family.")(
          req("GET", f"/schools/{school_id}/students/{student3_id}", token=ptoken)))
st, h, b = req("GET", f"/schools/{school_id}/students", token=ptoken)
check("parent students list narrowed to their ward(s)",
      b.get("total") == 1 and b["items"][0]["id"] == student1_id, f"total={b.get('total')}")

sect("scope/teacher")
# invite a teacher account linked to teacher1 (form teacher of JSS 1A only)
st, h, b = req("POST", f"/schools/{school_id}/users/invite", token=admin_token,
               body={"name": "Grace Teacher", "email": f"teacher.{SUF}@e2e.test", "role": "teacher",
                     "link": {"kind": "teacher", "teacherId": teacher1_id}})
tuid = b.get("id")
tcode = db(f"SELECT invite_code FROM ems_users WHERE id='{tuid}'")
req("POST", "/auth/invite/accept", body={"code": tcode, "password": "password123"})
ttoken = req("POST", "/auth/sign-in", body={"email": f"teacher.{SUF}@e2e.test", "password": "password123"})[2]["token"]
st, h, b = req("GET", f"/schools/{school_id}/classes", token=ttoken)
tnames = [c["name"] for c in b.get("items", [])]
check("teacher classes list narrowed to assigned (JSS 1A only)",
      b.get("total") == 1 and tnames == ["JSS 1A"], f"total={b.get('total')} names={tnames}")
check("teacher saves own class register -> ok",
      req("PUT", f"/schools/{school_id}/classes/{cg1}/register", token=ttoken,
          query={"date": "2026-09-08"}, body={"entries": [{"studentId": student1_id, "status": "present"}]})[0] in (200, 204))
check("teacher saves UNASSIGNED class register -> 403 'This class is not assigned to you.'",
      (lambda r: r[0] == 403 and msg(r[2]) == "This class is not assigned to you.")(
          req("PUT", f"/schools/{school_id}/classes/{cg2}/register", token=ttoken,
              query={"date": "2026-09-08"}, body={"entries": []})))

# cross-tenant: build a 2nd school, ensure its admin can't reach school 1
sect("scope/tenant")
st, h, b2 = register(f"Other School {SUF}", f"other.{SUF}@e2e.test", "password123")
other_token = b2.get("token")
check("foreign-school token on school1 -> 403 (membership gate)",
      req("GET", f"/schools/{school_id}/students", token=other_token)[0] == 403)

# ===========================================================================
# PHASE 2 — ADMISSIONS & DOCUMENTS
# ===========================================================================
import uuid as _uuid2
def uid2(): return str(_uuid2.uuid4())
PDF = "data:application/pdf;base64,JVBERi0xLjQKJcOkCg=="
def doc(name="Birth cert", ctype="application/pdf", size=2048, dtype="birth_certificate", body=PDF):
    return {"name": name, "type": dtype, "contentType": ctype, "sizeBytes": size, "body": body}
def application_input(fn, ln, level="JSS 1", docs=None):
    return {"firstName": fn, "lastName": ln, "dateOfBirth": "2015-03-03", "gender": "female",
            "desiredLevel": level, "previousSchool": "Little Steps",
            "guardian": {"firstName": "Ada", "lastName": ln, "relationship": "mother",
                         "phone": "08099998888", "email": f"g.{ln}@e2e.test", "occupation": "Trader"},
            "note": "Please consider.", "documents": docs or []}

# seed an OPEN admission cycle (no create-cycle endpoint in Phase 2)
cyc_id = uid2()
db(f"""INSERT INTO ems_admission_cycles (id,school_id,name,session,opens_on,closes_on,status,created,modified)
       VALUES ('{cyc_id}','{school_id}','2026/2027 Admissions','2026/2027','2026-01-01','2026-12-31','open',NOW(),NOW())""")

sect("admissions/public")
st, h, b = req("GET", f"/public/apply/{slug}")
check("public intake returns school+cycle+levels",
      st == 200 and b and b["school"]["id"] == school_id and b.get("cycle") and "JSS 1" in b.get("levels", []),
      f"{st} {b}")
check("public intake unknown slug -> null (200)",
      (lambda r: r[0] == 200 and r[2] is None)(req("GET", f"/public/apply/nobody-{SUF}")))
# submit a valid application with a document (PUBLIC, no token)
st, h, appA = req("POST", f"/public/schools/{school_id}/apply", body=application_input("Zoe", f"Ade{SUF}", docs=[doc()]))
check("public apply 201 submitted + APP- number",
      st == 201 and appA.get("status") == "submitted" and str(appA.get("applicationNumber", "")).startswith("APP-"),
      f"{st} {appA}")
appA_id = appA.get("id")
check("public apply blank name -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "The applicant needs a first and last name.")(
          req("POST", f"/public/schools/{school_id}/apply", body=application_input("", "Nameless"))))
check("public apply bad file type -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "That file type cannot be accepted. Please send a PDF, JPEG or PNG, up to 2 MB.")(
          req("POST", f"/public/schools/{school_id}/apply",
              body=application_input("Bad", "Type", docs=[doc(ctype="text/plain")]))))
check("public apply oversized file -> 413",
      (lambda r: r[0] == 413 and msg(r[2]) == "That file is larger than 2 MB. Please send a smaller copy.")(
          req("POST", f"/public/schools/{school_id}/apply",
              body=application_input("Big", "File", docs=[doc(size=3 * 1024 * 1024)]))))
check("public apply empty file -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "That file is empty.")(
          req("POST", f"/public/schools/{school_id}/apply",
              body=application_input("Empty", "File", docs=[doc(size=0)]))))
# a cycle-less school rejects applications
st, h, nc = register(f"No Cycle {SUF}", f"nocycle.{SUF}@e2e.test", "password123")
check("public apply with no open cycle -> 422 admissions closed",
      (lambda r: r[0] == 422 and msg(r[2]) == "Admissions are closed at the moment. Please check back later.")(
          req("POST", f"/public/schools/{nc['school']['id']}/apply", body=application_input("Nope", "Closed"))))

sect("admissions/queue")
st, h, b = req("GET", f"/schools/{school_id}/applications", token=admin_token)
check("applications list envelope >=1", st == 200 and b.get("total", 0) >= 1, f"{st} {b.get('total')}")
st, h, sm = req("GET", f"/schools/{school_id}/applications/summary", token=admin_token)
check("applications summary has 9 keys, submitted>=1",
      isinstance(sm, dict) and len(sm) == 9 and sm.get("submitted", 0) >= 1, str(sm))
check("applications query filter by name -> 1",
      req("GET", f"/schools/{school_id}/applications", token=admin_token, query={"query": f"Ade{SUF}"})[2]["total"] == 1)
st, h, det = req("GET", f"/schools/{school_id}/applications/{appA_id}", token=admin_token)
check("application detail {application,cycle,reviews}",
      st == 200 and det["application"]["id"] == appA_id and det.get("cycle") and isinstance(det.get("reviews"), list),
      f"{st}")
check("application view unknown -> 404",
      (lambda r: r[0] == 404 and msg(r[2]) == "That application could not be found.")(
          req("GET", f"/schools/{school_id}/applications/ghost", token=admin_token)))

sect("admissions/state-machine")
def review(id, action, **kw):
    return req("POST", f"/schools/{school_id}/applications/{id}/review", token=admin_token, body={"action": action, **kw})
check("accept from submitted -> 409 illegal",
      (lambda r: r[0] == 409 and msg(r[2]) == "A submitted application cannot take this action.")(review(appA_id, "accept")))
check("start_review submitted->under_review", review(appA_id, "start_review")[2].get("status") == "under_review")
check("accept from under_review -> 409 (status with space)",
      (lambda r: r[0] == 409 and msg(r[2]) == "A under review application cannot take this action.")(review(appA_id, "accept")))
check("waitlist under_review->waitlisted", review(appA_id, "waitlist")[2].get("status") == "waitlisted")
check("offer without expiry -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "An offer needs an expiry date.")(review(appA_id, "offer", offer={"note": "x"})))
check("offer with past expiry -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "The offer expiry must be a future date.")(
          review(appA_id, "offer", offer={"expiresOn": "2020-01-01", "note": "x"})))
st, h, off = review(appA_id, "offer", offer={"expiresOn": "2026-12-01", "note": "Bring originals"})
check("offer with future expiry -> offered + offer stored",
      off.get("status") == "offered" and off.get("offer", {}).get("expiresOn") == "2026-12-01", str(off.get("offer")))
check("mark_expired before expiry passed -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "This offer has not passed its expiry date yet.")(review(appA_id, "mark_expired")))
check("accept offered->accepted", review(appA_id, "accept")[2].get("status") == "accepted")
st, h, det = req("GET", f"/schools/{school_id}/applications/{appA_id}", token=admin_token)
check("reviews appended per transition (>=4, newest first)",
      len(det["reviews"]) >= 4 and det["reviews"][0]["action"] == "accepted", str([r["action"] for r in det["reviews"]]))

# decline flow on a fresh application
st, h, appB = req("POST", f"/public/schools/{school_id}/apply", body=application_input("Dee", f"Cline{SUF}"))
appB_id = appB["id"]
check("decline blank note -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "Declining an application needs a reason for the record.")(
          review(appB_id, "decline")))
check("decline with reason -> declined", review(appB_id, "decline", note="Outside catchment")[2].get("status") == "declined")
check("withdraw from declined -> 409 illegal",
      (lambda r: r[0] == 409 and "declined application cannot take this action." in (msg(r[2]) or ""))(
          review(appB_id, "withdraw")))

sect("admissions/enrol")
def enrol(id, cls, tok=admin_token):
    return req("POST", f"/schools/{school_id}/applications/{id}/enrol", token=tok, body={"classGroup": cls})
check("enrol non-accepted -> 409",
      (lambda r: r[0] == 409 and msg(r[2]) == "Only an accepted application can be enrolled.")(enrol(appB_id, "JSS 1A")))
check("enrol unknown class -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "Choose the class the student joins.")(enrol(appA_id, "ZZ 9Z")))
check("enrol wrong-level class -> 422 mismatch",
      (lambda r: r[0] == 422 and msg(r[2]) == "JSS 2A is a JSS 2 class — this applicant applied for JSS 1.")(
          enrol(appA_id, "JSS 2A")))
st, h, en = enrol(appA_id, "JSS 1A")
check("enrol accepted -> enrolled + studentId set", st == 200 and en.get("status") == "enrolled" and en.get("studentId"), f"{st} {en}")
new_student_id = en.get("studentId")
st, h, sres = req("GET", f"/schools/{school_id}/students", token=admin_token, query={"query": f"Ade{SUF}"})
check("enrolment created the student (with '/' admission number)",
      sres["total"] == 1 and "/" in sres["items"][0]["admissionNumber"], str(sres.get("total")))
st, h, sdocs = req("GET", f"/schools/{school_id}/documents", token=admin_token,
                   query={"owner": "student", "ownerId": new_student_id})
check("application document carried onto student (verification preserved)",
      isinstance(sdocs, list) and len(sdocs) == 1 and sdocs[0]["verification"] == "pending", str(len(sdocs) if isinstance(sdocs, list) else sdocs))
st, h, det = req("GET", f"/schools/{school_id}/applications/{appA_id}", token=admin_token)
check("enrol review note names class + documents moved",
      det["reviews"][0]["note"].startswith("Enrolled into JSS 1A.") and "1 document moved" in det["reviews"][0]["note"],
      det["reviews"][0]["note"])
check("primary guardian created from application",
      any(g["isPrimary"] for g in req("GET", f"/schools/{school_id}/students/{new_student_id}/guardians", token=admin_token)[2]))

sect("documents/crud")
def upload(owner, owner_id, d, tok=admin_token):
    return req("POST", f"/schools/{school_id}/documents", token=tok, body={"owner": owner, "ownerId": owner_id, **d})
st, h, du = upload("student", student1_id, doc(name="Report card", dtype="report_card"))
check("upload student doc -> 201 pending", st == 201 and du.get("verification") == "pending", f"{st} {du}")
doc1 = du.get("id")
check("upload bad type -> 422", upload("student", student1_id, doc(ctype="image/gif"))[0] == 422)
check("upload oversized -> 413", upload("student", student1_id, doc(size=3 * 1024 * 1024))[0] == 413)
check("upload empty -> 422", upload("student", student1_id, doc(size=0))[0] == 422)
check("upload blank name -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "Give the document a name.")(upload("student", student1_id, doc(name="  "))))
check("list student docs (uploadedOn desc)",
      (lambda r: r[0] == 200 and isinstance(r[2], list) and any(x["id"] == doc1 for x in r[2]))(
          req("GET", f"/schools/{school_id}/documents", token=admin_token, query={"owner": "student", "ownerId": student1_id})))
check("storagePath never exposed (empty string on wire)", du.get("storagePath") == "")

sect("documents/verify+gates")
check("verify as non-officer (teacher) -> 403",
      (lambda r: r[0] == 403 and msg(r[2]) == "Only the admissions office can check documents.")(
          req("POST", f"/schools/{school_id}/documents/{doc1}/verify", token=ttoken, body={"decision": "verified", "note": ""})))
check("reject with blank note -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "Say what is wrong with it, so the family can send a better copy.")(
          req("POST", f"/schools/{school_id}/documents/{doc1}/verify", token=admin_token, body={"decision": "rejected", "note": ""})))
st, h, vr = req("POST", f"/schools/{school_id}/documents/{doc1}/verify", token=admin_token, body={"decision": "verified", "note": "Looks good"})
check("verify verified -> verification=verified + verifiedBy", vr.get("verification") == "verified" and vr.get("verifiedBy"), str(vr))
check("application docs are admissions-office only (teacher list -> 403)",
      (lambda r: r[0] == 403 and msg(r[2]) == "Admission documents are for the admissions office.")(
          req("GET", f"/schools/{school_id}/documents", token=ttoken, query={"owner": "application", "ownerId": appA_id})))
check("parent listing another family's student docs -> 403",
      (lambda r: r[0] == 403 and msg(r[2]) == "This record belongs to another family.")(
          req("GET", f"/schools/{school_id}/documents", token=ptoken, query={"owner": "student", "ownerId": student2_id})))

sect("documents/signed-links")
st, h, link = req("POST", f"/schools/{school_id}/documents/{doc1}/link", token=admin_token)
check("link issues SignedLink {token,path,expiresAt,documentId,filename}",
      st == 200 and link.get("token") and link.get("path") == f"/files/{link['token']}"
      and link.get("documentId") == doc1 and str(link.get("filename", "")).endswith(".pdf"), f"{st} {link}")
tok1 = link.get("token")
st, h, f = req("GET", f"/files/{tok1}", token=admin_token)
check("redeem as issuer -> 200 DocumentFile (bytes+filename)",
      st == 200 and f.get("body") and f.get("filename") == link["filename"] and f.get("documentName") == "Report card", f"{st}")
check("redeem by a stranger (different user) -> 403",
      (lambda r: r[0] == 403 and msg(r[2]) == "This link was issued to somebody else.")(
          req("GET", f"/files/{tok1}", token=ttoken)))
check("redeem with no session -> 401",
      (lambda r: r[0] == 401 and msg(r[2]) == "Sign in to open this document.")(req("GET", f"/files/{tok1}")))
check("unknown token -> 410 invalid",
      (lambda r: r[0] == 410 and msg(r[2]) == "This link is not valid. Open the document from the record instead.")(
          req("GET", f"/files/deadbeef{SUF}")))
# expired link (force expiry in the grant store)
st, h, link2 = req("POST", f"/schools/{school_id}/documents/{doc1}/link", token=admin_token)
db(f"UPDATE ems_document_grants SET expires_at = 1000 WHERE token = '{link2['token']}'")
check("expired link -> 410 expired",
      (lambda r: r[0] == 410 and msg(r[2]) == "This link has expired. Open the document again from the record to get a fresh one.")(
          req("GET", f"/files/{link2['token']}", token=admin_token)))
# a VERIFIED document is part of the student's record — deletion refused
check("verified document delete -> 409 'A verified document is part of the student's record and cannot be removed.'",
      (lambda r: r[0] == 409 and msg(r[2]) == "A verified document is part of the student's record and cannot be removed.")(
          req("DELETE", f"/schools/{school_id}/documents/{doc1}", token=admin_token)))
# delete revokes outstanding grants — exercised on a fresh UNVERIFIED upload
st, h, doc_tmp = req("POST", f"/schools/{school_id}/documents", token=admin_token, body={
    "owner": "student", "ownerId": student1_id, "name": "Disposable note", "type": "other",
    "contentType": "application/pdf", "sizeBytes": 5, "body": "data:application/pdf;base64,JVBERi0="})
doc_tmp_id = doc_tmp["id"]
st, h, link3 = req("POST", f"/schools/{school_id}/documents/{doc_tmp_id}/link", token=admin_token)
check("unverified document delete -> 204", req("DELETE", f"/schools/{school_id}/documents/{doc_tmp_id}", token=admin_token)[0] == 204)
check("redeeming a link after delete -> 410 (grant revoked)",
      req("GET", f"/files/{link3['token']}", token=admin_token)[0] == 410)
check("deleted document no longer listed",
      not any(x["id"] == doc_tmp_id for x in req("GET", f"/schools/{school_id}/documents", token=admin_token,
              query={"owner": "student", "ownerId": student1_id})[2]))
_audit_actions = db(f"SELECT DISTINCT action FROM ems_audit_events WHERE school_id='{school_id}' AND action LIKE 'document.%'")
check("audit recorded document.downloaded + document.uploaded events",
      "document.downloaded" in _audit_actions and "document.uploaded" in _audit_actions, _audit_actions.replace("\n", ","))

# === student delete idempotency (last) =====================================
sect("students/delete")
# A student record can never be hard-deleted by a school — the endpoint always
# refuses (409), dependents or not; withdrawal/merge are the only paths.
check("student delete always refused -> 409 verbatim",
      (lambda r: r[0] == 409 and msg(r[2]) == "A student's record cannot be deleted. Withdraw the student instead, or merge duplicates.")(
          req("DELETE", f"/schools/{school_id}/students/{student3_id}", token=admin_token)))
check("refused student delete leaves the record intact",
      req("GET", f"/schools/{school_id}/students/{student3_id}", token=admin_token)[0] == 200)
# teacher2 holds an allocation/timetable? teacher1 does — teacher2 may be clean.
check("referenced teacher delete -> 409 'A teacher's record cannot be deleted while classes reference it. Mark the teacher as former instead.'",
      (lambda r: r[0] == 409 and msg(r[2]) == "A teacher's record cannot be deleted while classes reference it. Mark the teacher as former instead.")(
          req("DELETE", f"/schools/{school_id}/teachers/{teacher1_id}", token=admin_token)))
# teacher2 is cg2's form teacher, so they are refused too — prove the guard,
# then delete a genuinely unreferenced hire.
check("form-teacher delete -> 409",
      req("DELETE", f"/schools/{school_id}/teachers/{teacher2_id}", token=admin_token)[0] == 409)
st, h, t_tmp = req("POST", f"/schools/{school_id}/teachers", token=admin_token, body={
    "staffNumber": "STF-TMP-1", "firstName": "Temp", "lastName": "Hire",
    "email": "temp.hire@e2e.test", "phone": "", "gender": "female", "subjects": [], "status": "active"})
check("unreferenced teacher delete idempotent -> 204",
      req("DELETE", f"/schools/{school_id}/teachers/{t_tmp['id']}", token=admin_token)[0] == 204)

# ===========================================================================
# PHASE 3 — ACADEMICS (exams, assessments, grading, questions, transcripts)
# ===========================================================================
S = f"/schools/{school_id}"
def audited(action):
    # The /calendar/audit read is scoped to session/term events, so academics
    # audit rows are verified straight from the append-only table.
    return int(db(f"SELECT COUNT(*) FROM ems_audit_events WHERE school_id='{school_id}' AND action='{action}'")) >= 1
DEFAULT_BANDS = [
    {"letter": "A", "min": 70, "label": "Excellent", "tone": "text-success"},
    {"letter": "B", "min": 60, "label": "Very good", "tone": "text-success"},
    {"letter": "C", "min": 50, "label": "Credit", "tone": "text-info"},
    {"letter": "D", "min": 45, "label": "Pass", "tone": "text-warning"},
    {"letter": "E", "min": 40, "label": "Fair", "tone": "text-warning"},
    {"letter": "F", "min": 0, "label": "Fail", "tone": "text-destructive"},
]

# --- Grading schemes (§3.3): default, validation, no-op guard, versioning ---
sect("grading")
st, h, active = req("GET", f"{S}/grading/active", token=admin_token)
check("grading active is synthesised default (v1, System, 6 bands)",
      st == 200 and active.get("version") == 1 and active.get("createdBy") == "System"
      and len(active.get("bands", [])) == 6 and active["bands"][0]["min"] == 70, f"{st} {active}")
check("grading history empty when only the default exists",
      req("GET", f"{S}/grading/history", token=admin_token)[2] == [])

def put_grading(bands, note=None):
    b = {"bands": bands}
    if note is not None:
        b["note"] = note
    return req("PUT", f"{S}/grading", token=admin_token, body=b)

check("grading <2 bands -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "A grading scale needs at least two grades.")(
          put_grading([DEFAULT_BANDS[0]])))
check("grading blank letter -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "Every grade needs a letter.")(
          put_grading([{"letter": "", "min": 70, "label": "X", "tone": "text-success"}, DEFAULT_BANDS[5]])))
check("grading blank label -> 422 'Grade A needs a name.'",
      (lambda r: r[0] == 422 and msg(r[2]) == "Grade A needs a name.")(
          put_grading([{"letter": "A", "min": 70, "label": " ", "tone": "text-success"}, DEFAULT_BANDS[5]])))
check("grading min out of range -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "The mark for grade A must be a whole number from 0 to 100.")(
          put_grading([{"letter": "A", "min": 150, "label": "X", "tone": "text-success"}, DEFAULT_BANDS[5]])))
check("grading not descending -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "Each grade must start below the one above it.")(
          put_grading([{"letter": "A", "min": 50, "label": "X", "tone": "text-success"},
                       {"letter": "B", "min": 60, "label": "Y", "tone": "text-info"},
                       DEFAULT_BANDS[5]])))
check("grading last min != 0 -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "The lowest grade must start at 0 so every mark has a grade.")(
          put_grading([{"letter": "A", "min": 70, "label": "X", "tone": "text-success"},
                       {"letter": "B", "min": 10, "label": "Y", "tone": "text-info"}])))
check("grading duplicate letters -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "Two grades share a letter — each letter must be distinct.")(
          put_grading([{"letter": "A", "min": 70, "label": "X", "tone": "text-success"},
                       {"letter": "a", "min": 0, "label": "Y", "tone": "text-info"}])))

st, h, v1 = put_grading(DEFAULT_BANDS)
check("grading first real save -> version 1 active", st == 200 and v1.get("version") == 1 and v1.get("status") == "active", f"{st} {v1}")
st, h, noop = put_grading(DEFAULT_BANDS)
check("grading identical save is a no-op (still version 1)", noop.get("version") == 1)
check("grading history has exactly 1 version after no-op",
      len(req("GET", f"{S}/grading/history", token=admin_token)[2]) == 1)
V2_BANDS = [dict(b) for b in DEFAULT_BANDS]
V2_BANDS[5] = {"letter": "F", "min": 0, "label": "Weak", "tone": "text-destructive"}
st, h, v2 = put_grading(V2_BANDS, note="Relabelled the failing grade")
check("grading real change -> version 2", st == 200 and v2.get("version") == 2 and v2.get("note") == "Relabelled the failing grade", f"{st} {v2}")
hist = req("GET", f"{S}/grading/history", token=admin_token)[2]
check("grading history newest-first [2,1]", [s["version"] for s in hist] == [2, 1], str([s["version"] for s in hist]))
check("grading.updated audit written", audited("grading.updated"))

# --- Question bank (§3.4) --------------------------------------------------
sect("questions")
def add_q(subject, topic, qtype, marks, difficulty="medium", level="JSS 3", options=None, answer="A"):
    return req("POST", f"{S}/questions", token=admin_token, body={
        "subject": subject, "level": level, "type": qtype, "difficulty": difficulty,
        "topic": topic, "text": f"{topic} question?", "options": options or [], "answer": answer, "marks": marks})
st, h, q1 = add_q("Mathematics", "Algebra", "objective", 5, options=["A", "B", "C"])
check("question create 201", st == 201 and q1.get("id"), f"{st} {q1}")
q1_id = q1["id"]
q2_id = add_q("Mathematics", "Geometry", "theory", 10)[2]["id"]
q3_id = add_q("Mathematics", "Fractions", "objective", 5)[2]["id"]
add_q("English", "Grammar", "objective", 5)
check("questions subject filter narrows",
      req("GET", f"{S}/questions", token=admin_token, query={"subject": "Mathematics"})[2]["total"] == 3)
check("questions type filter narrows",
      req("GET", f"{S}/questions", token=admin_token, query={"subject": "Mathematics", "type": "theory"})[2]["total"] == 1)
check("questions query filter (topic) narrows",
      req("GET", f"{S}/questions", token=admin_token, query={"query": "Algebra"})[2]["total"] == 1)
check("questions sorted subject -> topic",
      (lambda items: [(i["subject"], i["topic"]) for i in items] == sorted([(i["subject"], i["topic"]) for i in items]))(
          req("GET", f"{S}/questions", token=admin_token, query={"pageSize": 100})[2]["items"]))
check("question subjects distinct sorted",
      (lambda b: "English" in b and "Mathematics" in b and b == sorted(b))(
          req("GET", f"{S}/questions/subjects", token=admin_token)[2]))
check("question view 200", req("GET", f"{S}/questions/{q1_id}", token=admin_token)[0] == 200)
check("question view unknown -> 404 'That question could not be found.'",
      (lambda r: r[0] == 404 and msg(r[2]) == "That question could not be found.")(
          req("GET", f"{S}/questions/ghost", token=admin_token)))
st, h, q1u = req("PUT", f"{S}/questions/{q1_id}", token=admin_token, body={
    "subject": "Mathematics", "level": "JSS 3", "type": "objective", "difficulty": "medium",
    "topic": "Algebra", "text": "Algebra question?", "options": ["A", "B", "C"], "answer": "B", "marks": 6})
check("question edit updates marks", st == 200 and q1u.get("marks") == 6, f"{st} {q1u}")

# --- Exams (§3.1): lifecycle, sittings, papers -----------------------------
sect("exams")
st, h, exam = req("POST", f"{S}/exams", token=admin_token, body={
    "title": "Third Term Examination", "session": "2025/2026", "term": "First",
    "startDate": "2025-09-01", "endDate": "2025-12-15", "status": "grading",
    "caMax": 40, "examMax": 60})
check("exam create 201 grading", st == 201 and exam.get("id") and exam.get("caMax") == 40, f"{st} {exam}")
exam_id = exam["id"]
check("exam list filter by status", req("GET", f"{S}/exams", token=admin_token, query={"status": "grading"})[2]["total"] >= 1)
check("exam list query filter (title/session)",
      req("GET", f"{S}/exams", token=admin_token, query={"query": "2025/2026"})[2]["total"] >= 1)
check("exam view unknown -> 404 'That examination could not be found.'",
      (lambda r: r[0] == 404 and msg(r[2]) == "That examination could not be found.")(
          req("GET", f"{S}/exams/ghost", token=admin_token)))

# start-grading: the one forward lifecycle step the API was missing. A fresh
# draft exam (kept separate so the main exam_id stays in grading) proves the
# transition and its guards; teachers are refused like release/reopen.
sect("exams/start-grading")
st, h, dexam = req("POST", f"{S}/exams", token=admin_token, body={
    "title": "Draft Term Exam", "session": "2025/2026", "term": "Second",
    "startDate": "2025-09-01", "endDate": "2025-12-15", "status": "draft",
    "caMax": 40, "examMax": 60})
draft_id = dexam["id"]
check("draft exam created (status draft)", st == 201 and dexam.get("status") == "draft", f"{st} {dexam.get('status')}")
check("teacher cannot start grading -> 403",
      (lambda r: r[0] == 403 and msg(r[2]) == "A teacher cannot start grading — that needs the academic lead.")(
          req("POST", f"{S}/exams/{draft_id}/start-grading", token=ttoken, body={})))
st, h, sg = req("POST", f"{S}/exams/{draft_id}/start-grading", token=admin_token, body={})
check("admin start-grading 200 -> exam now grading", st == 200 and sg.get("status") == "grading", f"{st} {sg}")
check("start-grading persisted to grading",
      req("GET", f"{S}/exams/{draft_id}", token=admin_token)[2]["status"] == "grading")
check("exam.grading_started audit written", audited("exam.grading_started"))
check("start-grading on an already-grading exam -> 409",
      (lambda r: r[0] == 409 and msg(r[2]) == "Only a draft or scheduled examination can start grading.")(
          req("POST", f"{S}/exams/{draft_id}/start-grading", token=admin_token, body={})))

# paper compose
sect("exams")
check("paper empty selection -> 422 'A paper needs at least one question.'",
      (lambda r: r[0] == 422 and msg(r[2]) == "A paper needs at least one question.")(
          req("PUT", f"{S}/exams/{exam_id}/paper", token=admin_token,
              body={"subject": "Mathematics", "level": "JSS 3", "questionIds": []})))
check("paper unknown question -> 422 references question that no longer exists",
      (lambda r: r[0] == 422 and msg(r[2]) == "The paper references a question that no longer exists.")(
          req("PUT", f"{S}/exams/{exam_id}/paper", token=admin_token,
              body={"subject": "Mathematics", "level": "JSS 3", "questionIds": [q1_id, "ghost"]})))
st, h, paper = req("PUT", f"{S}/exams/{exam_id}/paper", token=admin_token,
                   body={"subject": "Mathematics", "level": "JSS 3", "questionIds": [q1_id, q2_id]})
check("paper upsert 200", st == 200 and paper.get("questionIds") == [q1_id, q2_id], f"{st} {paper}")
st, h, pd = req("GET", f"{S}/exams/{exam_id}/paper", token=admin_token, query={"subject": "Mathematics", "level": "JSS 3"})
check("paper detail totalMarks = 6 + 10 = 16 in saved order",
      st == 200 and [q["id"] for q in pd["questions"]] == [q1_id, q2_id] and pd["totalMarks"] == 16, f"{st} tm={pd.get('totalMarks')}")
# subject seam (twin): a read model with an unknown subject fails loudly, not silent-empty
check("paper detail unknown subject -> 422 'That subject is not in the school's catalogue.'",
      (lambda r: r[0] == 422 and msg(r[2]) == "That subject is not in the school's catalogue.")(
          req("GET", f"{S}/exams/{exam_id}/paper", token=admin_token,
              query={"subject": "Nonexistent Subject", "level": "JSS 3"})))
check("question delete idempotent -> 204",
      req("DELETE", f"{S}/questions/{q3_id}", token=admin_token)[0] == 204
      and req("DELETE", f"{S}/questions/{q3_id}", token=admin_token)[0] == 204)

# schedules
sect("exams/schedules")
def add_sched(subject, level, date, start="09:00", end="11:00", venue="Hall A"):
    return req("POST", f"{S}/exams/{exam_id}/schedules", token=admin_token,
               body={"subject": subject, "level": level, "date": date,
                     "startTime": start, "endTime": end, "venue": venue})
st, h, sc = add_sched("Mathematics", "JSS 3", "2025-11-10")
check("schedule create 201", st == 201 and sc.get("id"), f"{st} {sc}")
sched_id = sc["id"]
add_sched("English", "JSS 3", "2025-11-12")
check("schedule duplicate (subject, level) -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "Mathematics is already scheduled for JSS 3.")(
          add_sched("Mathematics", "JSS 3", "2025-11-11")))
check("schedule outside window -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "The sitting must fall inside the examination window.")(
          add_sched("Chemistry", "JSS 3", "2026-01-05")))
check("schedules sorted date->startTime->level->subject",
      (lambda items: [i["date"] for i in items] == sorted([i["date"] for i in items]))(
          req("GET", f"{S}/exams/{exam_id}/schedules", token=admin_token)[2]))
check("delete sitting -> 204", req("DELETE", f"{S}/exam-schedules/{sched_id}", token=admin_token)[0] == 204)
check("delete sitting again -> 404 'That sitting could not be found.'",
      (lambda r: r[0] == 404 and msg(r[2]) == "That sitting could not be found.")(
          req("DELETE", f"{S}/exam-schedules/{sched_id}", token=admin_token)))
add_sched("Mathematics", "JSS 3", "2025-11-10")  # re-add for release-preview

# --- Seed a dedicated JSS 3A class + 3 enrolled students (no enrolment rows) -
sect("exams/seed")
cg3 = uid()
db(f"""INSERT INTO ems_class_groups (id,school_id,name,level,stream,form_teacher_id,capacity,created,modified)
       VALUES ('{cg3}','{school_id}','JSS 3A','JSS 3','A',NULL,30,NOW(),NOW())""")
stuA, stuB, stuC = uid(), uid(), uid()
db(f"""INSERT INTO ems_students (id,school_id,admission_number,first_name,last_name,date_of_birth,gender,class_group,status,guardian_name,guardian_phone,enrolled_on,created,modified)
       VALUES ('{stuA}','{school_id}','P3-001','Anna','Ade','2013-01-01','female','JSS 3A','enrolled','G','080',CURDATE(),NOW(),NOW()),
              ('{stuB}','{school_id}','P3-002','Ben','Bello','2013-01-01','male','JSS 3A','enrolled','G','080',CURDATE(),NOW(),NOW()),
              ('{stuC}','{school_id}','P3-003','Cara','Cole','2013-01-01','female','JSS 3A','enrolled','G','080',CURDATE(),NOW(),NOW())""")
check("seeded JSS 3A with 3 enrolled students", True)

def save_grades(subject, entries):
    return req("PUT", f"{S}/exams/{exam_id}/grades", token=admin_token,
               query={"classId": cg3, "subject": subject}, body={"entries": entries})
save_grades("Mathematics", [{"studentId": stuA, "ca": 30, "exam": 50},
                            {"studentId": stuB, "ca": 20, "exam": 40},
                            {"studentId": stuC, "ca": 35, "exam": 55}])
save_grades("English", [{"studentId": stuA, "ca": 25, "exam": 45},
                        {"studentId": stuB, "ca": 30, "exam": 50},
                        {"studentId": stuC, "ca": 20, "exam": 30}])
check("grades saved (both subjects, 3 students)", True)

# --- Gradesheet / broadsheet / report card numeric parity ------------------
sect("exams/results")
st, h, gs = req("GET", f"{S}/exams/{exam_id}/gradesheet", token=admin_token, query={"classId": cg3, "subject": "Mathematics"})
gsrows = {r["student"]["id"]: r for r in gs["rows"]}
check("gradesheet roster order by surname (Ade, Bello, Cole)",
      [r["student"]["lastName"] for r in gs["rows"]] == ["Ade", "Bello", "Cole"], str([r["student"]["lastName"] for r in gs["rows"]]))
check("gradesheet caFromAssessments false (no assessments)", gs["caFromAssessments"] is False)
check("gradesheet stuA Math ca30 exam50 total80 grade A",
      gsrows[stuA]["ca"] == 30 and gsrows[stuA]["exam"] == 50 and gsrows[stuA]["total"] == 80 and gsrows[stuA]["grade"]["letter"] == "A", str(gsrows[stuA]))
check("gradesheet stuB Math total60 grade B", gsrows[stuB]["total"] == 60 and gsrows[stuB]["grade"]["letter"] == "B")
check("gradesheet gradeBands is 6-band scale", len(gs["gradeBands"]) == 6)
# subject seam: an unknown subject fails loudly, not as a silent empty gradesheet
check("gradesheet unknown subject -> 422 'That subject is not in the school's catalogue.'",
      (lambda r: r[0] == 422 and msg(r[2]) == "That subject is not in the school's catalogue.")(
          req("GET", f"{S}/exams/{exam_id}/gradesheet", token=admin_token,
              query={"classId": cg3, "subject": "Nonexistent Subject"})))

st, h, bs = req("GET", f"{S}/exams/{exam_id}/broadsheet", token=admin_token, query={"classId": cg3})
check("broadsheet subjects sorted [English, Mathematics]", bs["subjects"] == ["English", "Mathematics"], str(bs["subjects"]))
bsrows = {r["student"]["id"]: r for r in bs["rows"]}
check("broadsheet stuA avg 75.0 position 1", bsrows[stuA]["average"] == 75 and bsrows[stuA]["position"] == 1, str(bsrows[stuA]))
check("broadsheet tie: stuB & stuC both avg 70 share position 2",
      bsrows[stuB]["average"] == 70 and bsrows[stuB]["position"] == 2 and bsrows[stuC]["position"] == 2,
      f"B={bsrows[stuB]['position']} C={bsrows[stuC]['position']}")
check("broadsheet cell grade (stuA English total 70 -> A)",
      bsrows[stuA]["cells"]["English"]["total"] == 70 and bsrows[stuA]["cells"]["English"]["grade"]["letter"] == "A")

st, h, rc = req("GET", f"{S}/exams/{exam_id}/report-card/{stuA}", token=admin_token)
rcsub = {s["subject"]: s for s in rc["subjects"]}
check("report card average 75 position 1 classSize 3",
      rc["average"] == 75 and rc["position"] == 1 and rc["classSize"] == 3, f"avg={rc['average']} pos={rc['position']} n={rc['classSize']}")
check("report card English classAverage 66.7 (200/3 @1dp)", rcsub["English"]["classAverage"] == 66.7, str(rcsub["English"]["classAverage"]))
check("report card Mathematics classAverage 76.7 (230/3 @1dp)", rcsub["Mathematics"]["classAverage"] == 76.7, str(rcsub["Mathematics"]["classAverage"]))
check("report card subject remark = band label ('Excellent')", rcsub["Mathematics"]["remark"] == "Excellent")
check("report card attendance summary present (rate 0, total 0 for fresh students)",
      rc["attendance"]["total"] == 0 and rc["attendance"]["rate"] == 0, str(rc["attendance"]))

# --- Release workflow (§3.1) -----------------------------------------------
sect("exams/release")
st, h, pv = req("GET", f"{S}/exams/{exam_id}/release-preview", token=admin_token)
check("release preview expected 6 entered 6 missing 0 nextVersion 1",
      pv == {"expectedScores": 6, "enteredScores": 6, "missingScores": 0, "nextVersion": 1}, str(pv))
check("teacher cannot release -> 403",
      (lambda r: r[0] == 403 and msg(r[2]) == "A teacher cannot release results — that needs the academic lead.")(
          req("POST", f"{S}/exams/{exam_id}/release", token=ttoken, body={})))
st, h, rel = req("POST", f"{S}/exams/{exam_id}/release", token=admin_token, body={})
check("admin release 200 version1 pins scheme v2, exam published",
      st == 200 and rel.get("version") == 1 and rel.get("schemeVersion") == 2, f"{st} {rel}")
check("exam is now published", req("GET", f"{S}/exams/{exam_id}", token=admin_token)[2]["status"] == "published")
check("release-only-grading guard: releasing a published exam -> 409",
      (lambda r: r[0] == 409 and msg(r[2]) == "Only an examination in grading can release its results.")(
          req("POST", f"{S}/exams/{exam_id}/release", token=admin_token, body={})))
check("results.released audit written", audited("results.released"))
check("saveGrades on published exam -> 422 must reopen",
      (lambda r: r[0] == 422 and msg(r[2]) == "Results for this examination are released. Reopen it for correction first.")(
          save_grades("Mathematics", [{"studentId": stuA, "ca": 10, "exam": 10}])))

# AC-5: editing the scale AFTER release must not rewrite the released card.
V3_BANDS = [{"letter": "A", "min": 85, "label": "Excellent", "tone": "text-success"},
            {"letter": "B", "min": 70, "label": "Very good", "tone": "text-success"},
            {"letter": "C", "min": 55, "label": "Credit", "tone": "text-info"},
            {"letter": "D", "min": 45, "label": "Pass", "tone": "text-warning"},
            {"letter": "E", "min": 40, "label": "Fair", "tone": "text-warning"},
            {"letter": "F", "min": 0, "label": "Fail", "tone": "text-destructive"}]
st, h, v3 = put_grading(V3_BANDS, note="Raise the A threshold")
check("grading version 3 active (A now >= 85)", v3.get("version") == 3 and v3["bands"][0]["min"] == 85, str(v3.get("version")))
st, h, rc2 = req("GET", f"{S}/exams/{exam_id}/report-card/{stuA}", token=admin_token)
rc2sub = {s["subject"]: s for s in rc2["subjects"]}
check("AC-5: published report card still graded on pinned v2 (total 80 -> A, not B)",
      rc2sub["Mathematics"]["grade"]["letter"] == "A", str(rc2sub["Mathematics"]["grade"]))
check("AC-5: report card gradeBands are the pinned v2 (A min 70), not active v3 (A min 85)",
      rc2["gradeBands"][0]["min"] == 70, str(rc2["gradeBands"][0]))

# --- Transcript (§3.5): released term + migrated history row ---------------
sect("transcripts")
db(f"""INSERT INTO ems_academic_term_records (id,school_id,student_id,session,term,class_group,average,position,class_size,remark,created,modified)
       VALUES ('{uid()}','{school_id}','{stuA}','2024/2025','Third','JSS 2A',68.0,2,20,'Very good',NOW(),NOW())""")
st, h, tr = req("GET", f"{S}/students/{stuA}/transcript", token=admin_token)
check("transcript 200 with two sessions (2024/2025, 2025/2026)",
      st == 200 and [s["session"] for s in tr["sessions"]] == ["2024/2025", "2025/2026"], str([s["session"] for s in tr["sessions"]]))
sess = {s["session"]: s for s in tr["sessions"]}
rel_term = sess["2025/2026"]["terms"][0]
check("transcript released term: source released, schemeVersion 2, avg 75, position 1, classSize 3",
      rel_term["source"] == "released" and rel_term["schemeVersion"] == 2 and rel_term["average"] == 75
      and rel_term["position"] == 1 and rel_term["classSize"] == 3, str(rel_term))
check("transcript released term subjects graded on pinned v2 (English total 70 -> A)",
      any(s["subject"] == "English" and s["grade"]["letter"] == "A" for s in rel_term["subjects"]))
hist_term = sess["2024/2025"]["terms"][0]
check("transcript history term: source history, schemeVersion null, subjects []",
      hist_term["source"] == "history" and hist_term["schemeVersion"] is None and hist_term["subjects"] == [], str(hist_term))
check("transcript cumulativeAverage (75 + 68)/2 = 71.5, termsCounted 2",
      tr["cumulativeAverage"] == 71.5 and tr["termsCounted"] == 2, f"cum={tr['cumulativeAverage']} n={tr['termsCounted']}")
check("transcript gradeBands = current active v3 (A min 85), key printed from live scale",
      tr["gradeBands"][0]["min"] == 85, str(tr["gradeBands"][0]))
check("transcript family scoping: parent reading another student -> 403",
      (lambda r: r[0] == 403 and msg(r[2]) == "This record belongs to another family.")(
          req("GET", f"{S}/students/{stuA}/transcript", token=ptoken)))

# --- Reopen (§3.1) ---------------------------------------------------------
sect("exams/reopen")
check("teacher cannot reopen -> 403",
      (lambda r: r[0] == 403 and msg(r[2]) == "A teacher cannot reopen released results.")(
          req("POST", f"{S}/exams/{exam_id}/reopen", token=ttoken, body={"reason": "x"})))
check("reopen without reason -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "Reopening released results needs a reason for the record.")(
          req("POST", f"{S}/exams/{exam_id}/reopen", token=admin_token, body={})))
st, h, re = req("POST", f"{S}/exams/{exam_id}/reopen", token=admin_token, body={"reason": "Correct a Maths mark"})
check("admin reopen 200 -> exam back to grading", st == 200 and re.get("status") == "grading", f"{st} {re}")
rels = req("GET", f"{S}/exams/{exam_id}/releases", token=admin_token)[2]
check("release history: v1 superseded (kept, not deleted)",
      len(rels) == 1 and rels[0]["status"] == "superseded" and rels[0].get("supersededOn"), str(rels))
check("results.reopened audit written", audited("results.reopened"))

# --- Assessments (§3.2): teacher-created CA, derivation, class access -------
sect("assessments")
def add_assessment(subject, name, maximum, due=None):
    b = {"examId": exam_id, "classGroupId": cg3, "subject": subject, "name": name, "maximum": maximum}
    if due:
        b["dueOn"] = due
    return req("POST", f"{S}/assessments", token=admin_token, body=b)
check("assessment blank name -> 422", (lambda r: r[0] == 422 and msg(r[2]) == "An assessment needs a name.")(
    add_assessment("Basic Science", "  ", 20)))
check("assessment max out of range -> 422", (lambda r: r[0] == 422 and msg(r[2]) == "The maximum score must be between 1 and 100.")(
    add_assessment("Basic Science", "Test 1", 0)))
check("assessment create on foreign class -> 403 (teacher not assigned JSS 3A)",
      (lambda r: r[0] == 403 and msg(r[2]) == "This class is not assigned to you.")(
          req("POST", f"{S}/assessments", token=ttoken,
              body={"examId": exam_id, "classGroupId": cg3, "subject": "Basic Science", "name": "T", "maximum": 10})))
st, h, a1 = add_assessment("Basic Science", "Test 1", 20)
check("assessment create 201 draft", st == 201 and a1.get("status") == "draft" and a1.get("maximum") == 20, f"{st} {a1}")
a1_id = a1["id"]
a2_id = add_assessment("Basic Science", "Test 2", 30)[2]["id"]
check("assessment.created audit written", audited("assessment.created"))
# illegal transition draft->published
check("assessment illegal transition draft->published -> 409",
      (lambda r: r[0] == 409 and msg(r[2]) == "An assessment cannot go from draft to published.")(
          req("POST", f"{S}/assessments/{a1_id}/status", token=admin_token, body={"status": "published"})))
def set_status(aid, status, reason=None):
    b = {"status": status}
    if reason is not None:
        b["reason"] = reason
    return req("POST", f"{S}/assessments/{aid}/status", token=admin_token, body=b)
check("assessment draft->open ok", set_status(a1_id, "open")[2]["status"] == "open")
set_status(a2_id, "open")
# scores validation (validate all before writing any)
def save_scores(aid, entries):
    return req("PUT", f"{S}/assessments/{aid}/scores", token=admin_token, body=entries)
check("score for non-enrolled student -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "A score was entered for a student not enrolled in this class.")(
          save_scores(a1_id, [{"studentId": student1_id, "score": 5}])))
check("negative score -> 422", (lambda r: r[0] == 422 and msg(r[2]) == "A score cannot be negative.")(
    save_scores(a1_id, [{"studentId": stuA, "score": -1}])))
check("score above assessment maximum -> 422 (verbatim n and max)",
      (lambda r: r[0] == 422 and msg(r[2]) == "A score of 25 is above this assessment's maximum of 20.")(
          save_scores(a1_id, [{"studentId": stuA, "score": 25}])))
# stuA: 10/20 and 15/30 -> derived CA = round((10+15)/(20+30) * 40) = 20 (complete)
save_scores(a1_id, [{"studentId": stuA, "score": 10}, {"studentId": stuB, "score": 12}])
save_scores(a2_id, [{"studentId": stuA, "score": 15}])
st, h, sheet = req("GET", f"{S}/assessments/{a1_id}/scoresheet", token=admin_token)
check("scoresheet lists enrolled roster with recorded flags",
      st == 200 and len(sheet["rows"]) == 3 and any(r["studentId"] == stuA and r["recorded"] for r in sheet["rows"]), str(sheet)[:160])
# assessment-backed offering flips the gradesheet to derived CA
st, h, gsci = req("GET", f"{S}/exams/{exam_id}/gradesheet", token=admin_token, query={"classId": cg3, "subject": "Basic Science"})
gsci_rows = {r["student"]["id"]: r for r in gsci["rows"]}
check("Basic Science gradesheet caFromAssessments true", gsci["caFromAssessments"] is True)
check("derived CA: stuA = round(25/50*40) = 20, caMissing 0",
      gsci_rows[stuA]["ca"] == 20 and gsci_rows[stuA].get("caMissing") == 0, str(gsci_rows[stuA]))
check("derived CA: stuB scored Test1 only -> caMissing 1 (Test 2 unscored)",
      gsci_rows[stuB].get("caMissing") == 1, str(gsci_rows[stuB]))
# saveGrades ignores incoming ca for an assessment-backed offering
save_grades("Basic Science", [{"studentId": stuA, "ca": 99, "exam": 40}])
st, h, gsci2 = req("GET", f"{S}/exams/{exam_id}/gradesheet", token=admin_token, query={"classId": cg3, "subject": "Basic Science"})
row = {r["student"]["id"]: r for r in gsci2["rows"]}[stuA]
check("assessment-backed offering ignores incoming ca (stays derived 20, not 99); exam mark written",
      row["ca"] == 20 and row["exam"] == 40 and row["total"] == 60, str(row))
# reopen (closed->open) requires a reason
set_status(a1_id, "closed")
check("scores locked once closed -> 409",
      (lambda r: r[0] == 409 and msg(r[2]) == "This assessment is closed. Reopen it for correction before changing scores.")(
          save_scores(a1_id, [{"studentId": stuA, "score": 11}])))
check("reopen closed assessment without reason -> 422",
      (lambda r: r[0] == 422 and msg(r[2]) == "Reopening a closed assessment needs a reason for the record.")(
          set_status(a1_id, "open")))
check("reopen closed assessment with reason -> 200 open",
      set_status(a1_id, "open", reason="Fix a mark")[2]["status"] == "open")

# === RATE LIMITING (must run LAST — exhausts the sign-in bucket) ============
sect("throttle")
got_429 = False
for _ in range(14):  # limit is 10 / 5-min window
    if req("POST", "/auth/sign-in", body={"email": f"flood.{SUF}@e2e.test", "password": "x"})[0] == 429:
        got_429 = True
        break
check("sign-in rate limit -> 429 'Too many attempts...'", got_429)
clear_throttle()  # leave the cache clean for the UI / next run

# ===========================================================================
# COMPLETENESS PASS — subjects catalogue, class CRUD, allocations, timetable,
# admission cycles, correction tier, Q10 archival guards
# ===========================================================================
sect("subjects")
st, h, subs = req("GET", f"{S}/subjects", token=admin_token)
check("subjects list seeded with the standard curriculum", st == 200 and len(subs) >= 18)
st, h, subj = req("POST", f"{S}/subjects", token=admin_token, body={"name": "Music"})
check("subject create 201", st == 201 and subj.get("name") == "Music")
music_id = subj["id"]
check("duplicate subject -> 422 'That subject is already in the catalogue.'",
      (lambda r: r[0] == 422 and msg(r[2]) == "That subject is already in the catalogue.")(
          req("POST", f"{S}/subjects", token=admin_token, body={"name": "music"})))
st, h, ren = req("PUT", f"{S}/subjects/{music_id}", token=admin_token, body={"name": "Music Studies"})
check("subject rename follows through", st == 200 and ren.get("name") == "Music Studies")
check("unknown subject on question create -> 422 verbatim",
      (lambda r: r[0] == 422 and msg(r[2]) == "That subject is not in the school's catalogue.")(
          req("POST", f"{S}/questions", token=admin_token, body={
              "subject": "Alchemy", "level": "JSS 1", "type": "objective",
              "difficulty": "easy", "topic": "x", "text": "y", "options": [], "answer": "z", "marks": 1})))
st, h, q_mus = req("POST", f"{S}/questions", token=admin_token, body={
    "subject": "Music Studies", "level": "JSS 1", "type": "objective",
    "difficulty": "easy", "topic": "Notes", "text": "How many lines has a staff?",
    "options": ["4", "5"], "answer": "5", "marks": 1})
check("question created under renamed subject", st == 201 and q_mus.get("subject") == "Music Studies")
check("referenced subject delete -> 409 'That subject is referenced by existing records. Retire it instead.'",
      (lambda r: r[0] == 409 and msg(r[2]) == "That subject is referenced by existing records. Retire it instead.")(
          req("DELETE", f"{S}/subjects/{music_id}", token=admin_token)))
st, h, deact = req("POST", f"{S}/subjects/{music_id}/deactivate", token=admin_token)
check("subject deactivate 200 inactive", st == 200 and deact.get("active") is False)
req("DELETE", f"{S}/questions/{q_mus['id']}", token=admin_token)
check("unreferenced subject delete -> 204",
      req("DELETE", f"{S}/subjects/{music_id}", token=admin_token)[0] == 204)
check("teacher cannot manage subjects -> 403",
      req("POST", f"{S}/subjects", token=ttoken, body={"name": "Drama"})[0] == 403)

sect("class-crud")
st, h, cg = req("POST", f"{S}/classes", token=admin_token, body={"name": "JSS 3Z", "level": "JSS 3", "capacity": 25})
check("class create 201", st == 201 and cg.get("name") == "JSS 3Z")
cgz = cg["id"]
# A level may hold a second arm with the same name — identity is the class id, not the name.
st, h, dup = req("POST", f"{S}/classes", token=admin_token, body={"name": "JSS 3Z", "level": "JSS 3"})
check("duplicate arm allowed -> 201, distinct id", st == 201 and dup.get("id") and dup["id"] != cgz)
req("DELETE", f"{S}/classes/{dup['id']}", token=admin_token)
st, h, ren = req("PUT", f"{S}/classes/{cgz}", token=admin_token, body={"name": "JSS 3 Omega"})
check("class rename 200", st == 200 and ren.get("name") == "JSS 3 Omega")
check("teacher cannot create class -> 403",
      req("POST", f"{S}/classes", token=ttoken, body={"name": "Nope", "level": "JSS 1"})[0] == 403)
st, h, alloc = req("POST", f"{S}/classes/{cgz}/allocations", token=admin_token, body={"subject": "Mathematics", "teacherId": teacher1_id})
check("allocation create 201", st == 201 and alloc.get("subject") == "Mathematics")
check("duplicate allocation -> 422 'That subject is already allocated for this class.'",
      (lambda r: r[0] == 422 and msg(r[2]) == "That subject is already allocated for this class.")(
          req("POST", f"{S}/classes/{cgz}/allocations", token=admin_token, body={"subject": "Mathematics"})))
st, h, slot = req("POST", f"{S}/classes/{cgz}/timetable", token=admin_token, body={"day": "Tue", "period": 2, "subject": "Mathematics", "teacherId": teacher1_id})
check("slot create 201", st == 201 and slot.get("period") == 2)
check("slot clash -> 422 'That period already has a subject scheduled.'",
      (lambda r: r[0] == 422 and msg(r[2]) == "That period already has a subject scheduled.")(
          req("POST", f"{S}/classes/{cgz}/timetable", token=admin_token, body={"day": "Tue", "period": 2, "subject": "English"})))
check("teacher double-booked -> 422 'That teacher is already teaching another class in this period.'",
      (lambda r: r[0] == 422 and msg(r[2]) == "That teacher is already teaching another class in this period.")(
          req("POST", f"{S}/classes/{cg1}/timetable", token=admin_token, body={"day": "Tue", "period": 2, "subject": "English", "teacherId": teacher1_id})))
check("class with allocations delete -> 409 'That class has students or academic history. Move its students first.'",
      (lambda r: r[0] == 409 and msg(r[2]) == "That class has students or academic history. Move its students first.")(
          req("DELETE", f"{S}/classes/{cgz}", token=admin_token)))
req("DELETE", f"{S}/classes/{cgz}/allocations/{alloc['id']}", token=admin_token)
req("DELETE", f"{S}/classes/{cgz}/timetable/{slot['id']}", token=admin_token)
check("cleaned class delete -> 204", req("DELETE", f"{S}/classes/{cgz}", token=admin_token)[0] == 204)

sect("cycles")
st, h, cyc = req("POST", f"{S}/admission-cycles", token=admin_token, body={
    "name": "E2E Intake", "session": "2026/2027", "opensOn": "2026-01-01", "closesOn": "2026-12-31"})
check("cycle create 201 open", st == 201 and cyc.get("status") == "open")
check("cycle bad dates -> 422 'The cycle must open before it closes.'",
      (lambda r: r[0] == 422 and msg(r[2]) == "The cycle must open before it closes.")(
          req("POST", f"{S}/admission-cycles", token=admin_token, body={"name": "Bad", "opensOn": "2026-12-01", "closesOn": "2026-01-01"})))
st, h, closed = req("POST", f"{S}/admission-cycles/{cyc['id']}/close", token=admin_token)
check("cycle close 200 closed", st == 200 and closed.get("status") == "closed")

sect("correction-tier")
st, h, ex = req("POST", f"{S}/exams", token=admin_token, body={
    "title": "Deletable Exam", "session": "2026/2027", "term": "First",
    "startDate": "2026-11-01", "endDate": "2026-11-05"})
exd = ex["id"]
st, h, exed = req("PUT", f"{S}/exams/{exd}", token=admin_token, body={"title": "Renamed Exam"})
check("exam edit 200", st == 200 and exed.get("title") == "Renamed Exam")
check("empty exam delete -> 204", req("DELETE", f"{S}/exams/{exd}", token=admin_token)[0] == 204)
check("exam with records delete -> 409 'This examination already has schedules, papers or grades. It cannot be deleted.'",
      (lambda r: r[0] == 409 and msg(r[2]) == "This examination already has schedules, papers or grades. It cannot be deleted.")(
          req("DELETE", f"{S}/exams/{exam_id}", token=admin_token)))
st, h, ann = req("POST", f"{S}/announcements", token=admin_token, body={
    "title": "Draft", "body": "b", "audience": "everyone", "category": "general"})
st, h, anned = req("PUT", f"{S}/announcements/{ann['id']}", token=admin_token, body={"title": "Draft v2"})
check("announcement draft edit 200", st == 200 and anned.get("title") == "Draft v2")
check("announcement draft delete -> 204", req("DELETE", f"{S}/announcements/{ann['id']}", token=admin_token)[0] == 204)

sect("q10-archival")
check("student with history delete -> 409 verbatim",
      (lambda r: r[0] == 409 and msg(r[2]) == "A student's record cannot be deleted. Withdraw the student instead, or merge duplicates.")(
          req("DELETE", f"{S}/students/{student1_id}", token=admin_token)))
st, h, wd = req("POST", f"{S}/students/{student1_id}/withdraw", token=admin_token)
check("student withdraw 200 status withdrawn", st == 200 and wd.get("status") == "withdrawn")
st, h, mf = req("POST", f"{S}/teachers/{teacher1_id}/mark-former", token=admin_token)
check("teacher mark-former 200 status former", st == 200 and mf.get("status") == "former")

# ---------------------------------------------------------------------------
print("\n" + "=" * 70)
by_sect = {}
for s, n, ok, d in results:
    by_sect.setdefault(s, [0, 0])
    by_sect[s][0 if ok else 1]  # touch
    by_sect[s][0] += 1 if ok else 0
    by_sect[s][1] += 0 if ok else 1
total_pass = sum(1 for r in results if r[2])
total_fail = sum(1 for r in results if not r[2])
print(f"SECTION SUMMARY ({total_pass} pass / {total_fail} fail / {len(results)} total)")
print("-" * 70)
for s, (p, f) in by_sect.items():
    flag = "" if f == 0 else "  <-- FAILURES"
    print(f"  {s:28s} {p:3d} pass  {f:3d} fail{flag}")
print("-" * 70)
if total_fail:
    print("\nFAILURES:")
    for s, n, ok, d in results:
        if not ok:
            print(f"  [{s}] {n}\n        {d}")
print("\nSCHOOL:", school_id, "SLUG:", slug, "ADMIN:", admin_email, "PW: newpassw0rd")
sys.exit(1 if total_fail else 0)
