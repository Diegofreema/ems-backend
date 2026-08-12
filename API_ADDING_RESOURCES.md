# Adding a resource to the API

The `Students` API is the reference implementation. To expose any other resource
(e.g. `Teachers`, `Subjects`, `Invoices`, `Attendances`, …) repeat this pattern.
Nothing here touches the existing web controllers — you only add files under
`src/Controller/Api/` and a few routes.

## The pattern (per resource)

### 1. Create `src/Controller/Api/<Name>Controller.php`
Extend the API base controller (NOT `App\Controller\AppController`) and reuse the
existing `Table` model — never copy business logic out of the model layer.

```php
<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Cake\Http\Exception\NotFoundException;

class TeachersController extends AppController
{
    protected $Teachers;

    public function initialize(): void
    {
        parent::initialize();
        $this->Teachers = $this->fetchTable('Teachers');
        // If the model declares INNER belongsTo joins, force LEFT for reads so
        // rows with null FKs are not silently dropped:
        // $this->Teachers->getAssociation('Users')->setJoinType('LEFT');
    }

    public function index()
    {
        $this->request->allowMethod(['get']);
        ['page' => $page, 'limit' => $limit] = $this->paginationParams();

        $query = $this->Teachers->find();
        $q = trim((string)$this->request->getQuery('q'));
        if ($q !== '') {
            $like = '%' . $q . '%';
            $query->where(['OR' => ['Teachers.fname LIKE' => $like, 'Teachers.lname LIKE' => $like]]);
        }
        $total = (clone $query)->count();
        $rows = $query->limit($limit)->offset(($page - 1) * $limit)->all()->toList();

        return $this->respond($rows, 200, [
            'page' => $page, 'limit' => $limit, 'total' => $total, 'pages' => (int)ceil($total / $limit),
        ]);
    }

    public function view($id = null)
    {
        $this->request->allowMethod(['get']);
        $row = $this->Teachers->find()->where(['Teachers.id' => (int)$id])->first();
        if ($row === null) { throw new NotFoundException('Teacher not found.'); }
        return $this->respond($row);
    }

    public function add()
    {
        $this->request->allowMethod(['post']);
        $this->requireRole([1, 5, 7]);
        $row = $this->Teachers->newEntity($this->request->getData());
        if (!$this->Teachers->save($row)) {
            return $this->respondError('Validation failed.', 422, $row->getErrors());
        }
        return $this->respond($row, 201);
    }

    public function edit($id = null)
    {
        $this->request->allowMethod(['put', 'patch']);
        $this->requireRole([1, 5, 7]);
        $row = $this->Teachers->get((int)$id);
        $row = $this->Teachers->patchEntity($row, $this->request->getData());
        if (!$this->Teachers->save($row)) {
            return $this->respondError('Validation failed.', 422, $row->getErrors());
        }
        return $this->respond($row);
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['delete']);
        $this->requireRole([1, 5], false);
        $row = $this->Teachers->get((int)$id);
        if (!$this->Teachers->delete($row)) {
            return $this->respondError('Could not delete.', 409);
        }
        return $this->respond(['deleted' => true, 'id' => (int)$id]);
    }
}
```

### 2. Add routes in `config/routes.php`
Inside the existing `$routes->prefix('Api', ['path' => '/api/v1'], ...)` block:

```php
// custom actions FIRST (so they aren't shadowed by /teachers/{id})
$builder->get('/teachers/search', ['controller' => 'Teachers', 'action' => 'search']);
// then standard CRUD
$builder->resources('Teachers');
```

### 3. Custom/domain actions
Add extra methods for the important business operations of that module (the ones
that in the web controller do more than CRUD — e.g. "promote student", "post
result", "record payment"). Route them explicitly *before* `resources()` and
guard them with `requireRole([...])`.

## Helpers available from the base controller
- `respond($data, $status = 200, $meta = [])` — success envelope
- `respondError($message, $status = 400, $errors = [])` — error envelope
- `requireRole([1,5,7], $allowApiKey = true)` — 403 if the caller's role isn't allowed
- `paginationParams()` — `['page' => n, 'limit' => n]` from the query string (limit capped at 100)
- `$this->identity` — `['type' => 'user', 'user' => <jwt claims>]` or `['type' => 'apikey', 'client' => <ApiKey entity>]`
- Any thrown `HttpException` (NotFound/Forbidden/…) is auto-rendered as JSON.

## Guidelines / gotchas
- **Reuse the model.** Put shared query/business logic in the `Table` class so the
  web app and API stay in sync. The API controller should be thin.
- **Shape your output.** For large tables, return a curated field set (like the
  `transform()` method in `StudentsController`) instead of dumping every column —
  avoids leaking sensitive fields and keeps payloads small.
- **Never expose secrets.** `password`, tokens, and `api_secret_hash` are already
  hidden via entity `$_hidden`; keep it that way for any sensitive column.
- **INNER joins.** Several models declare `belongsTo(..., ['joinType' => 'INNER'])`.
  When you `contain()` those for reads, force `LEFT` (see `initialize()` above) or
  records with null FKs vanish from results.
- **Mark public actions.** If an action must skip auth, add its name to
  `protected array $publicActions` in that controller (rare outside `Auth`).

## Bulk approach (optional)
There are ~107 web controllers. Rather than hand-writing all of them, you can:
1. Keep hand-written controllers for the ~10–15 core domain resources that have
   meaningful custom actions (Students, Teachers, Results, Invoices,
   Transactions, Attendances, Sparents, Fees, Subjects, Courseregistrations…).
2. For the many simple lookup/reference tables (Countries, States, Lgas, Levels,
   Roles, Categories, Departments, …), generate uniform CRUD controllers from a
   small template, since they are pure CRUD with no custom logic.

Either way, every new controller extends `Api\AppController` and gets auth,
CORS, JSON errors and the response envelope for free.
