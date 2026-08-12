# Postman collection — ltalms API

File: **`ltalms_api.postman_collection.json`** (84 folders, 438 requests — every API endpoint).

## Import
1. Postman → **Import** → drop in `ltalms_api.postman_collection.json`.
2. Open the collection → **Variables** tab → set **`baseUrl`** to your host:
   - XAMPP (Apache): `http://localhost/ltalms`  ← default
   - `bin/cake server`: `http://localhost:8765`
   (Requests already include the `/api/v1/...` part, so don't add it to `baseUrl`.)

## Use it
1. Open **Auth → login**, put a real `username` / `password` in the body, **Send**.
   A test script auto-saves the `access_token` (and `refresh_token`) into the
   collection variables — you don't copy anything.
2. Every other request inherits **Bearer `{{token}}`** automatically. Just Send.
3. When the token expires (1h), run **Auth → refresh** (uses the saved
   `refresh_token`) or log in again.

## Path variables & filters
- Requests like `view [GET] /api/v1/results/{id}` expose an `:id` path variable
  — set it on the request's **Params → Path Variables** (defaults to `1`).
- List endpoints accept query params you add under **Params**, e.g.
  `?student_id=7`, `?q=john`, `?paystatus=paid`, `?page=2&limit=50`,
  `?sort=lastname&direction=asc`.

## Server-to-server (API keys)
1. As an admin, run **Auth → api-keys** with `{ "name": "..." }` — copy the
   returned `api_key` and `api_secret` (shown once).
2. Set the collection variables `apiKey` / `apiSecret`.
3. For a key-authenticated call, override that request's **Authorization** to
   *No Auth* and add headers:
   `X-Api-Key: {{apiKey}}` and `X-Api-Secret: {{apiSecret}}`.
   (API keys can read everything but cannot write payment records or sensitive
   resources — see API.md.)

## Notes
- Write requests (POST/PUT/PATCH) ship with a placeholder JSON body — replace the
  `"//"` hint with the resource's real fields (a `422` response lists the
  required/invalid fields for you).
- The collection is generated from `config/routes.php`. If you add resources,
  regenerate it (see the generator step in the project history) or add requests
  manually.
