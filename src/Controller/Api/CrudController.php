<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Cake\Http\Exception\NotFoundException;

/**
 * Generic REST CRUD controller for the API.
 *
 * Resource controllers extend this and (optionally) tune a few properties. With
 * zero config it already provides list / view / create / update / delete plus
 * filtering, free-text search, sorting, pagination and eager-loaded relations —
 * all driven off the resource's existing Table model, so no business logic is
 * duplicated and the API stays in sync with the web app.
 *
 * Override points (set as properties in the subclass):
 *   $searchFields  - columns matched by ?q=  (default: the model displayField)
 *   $filterFields  - columns allowed as ?field=value (default: all real columns)
 *   $indexContain  - associations eager-loaded on list  (default: none)
 *   $viewContain   - associations eager-loaded on view  (default: all belongsTo)
 *   $writeRoles    - role ids allowed to create/update  (default admins 1,5,7)
 *   $deleteRoles   - role ids allowed to delete         (default 1,5)
 *   $deleteAllowApiKey - may server-to-server keys delete (default false)
 *
 * For custom domain actions, add methods to the subclass and route them
 * explicitly before resources() in config/routes.php.
 */
class CrudController extends AppController
{
    /**
     * @var \Cake\ORM\Table
     */
    protected $Model;

    /**
     * @var array<int, string>
     */
    protected array $searchFields = [];

    /**
     * @var array<int, string>|null
     */
    protected ?array $filterFields = null;

    /**
     * @var array<int, string>
     */
    protected array $indexContain = [];

    /**
     * @var array<int, string>|null
     */
    protected ?array $viewContain = null;

    /**
     * Role ids allowed to READ (index/view). Null = any authenticated caller.
     * Set to admin roles for sensitive resources (users, roles, logs, …).
     *
     * @var array<int, int>|null
     */
    protected ?array $readRoles = null;

    /**
     * @var array<int, int>
     */
    protected array $writeRoles = [1, 5, 7];

    /**
     * @var array<int, int>
     */
    protected array $deleteRoles = [1, 5];

    /**
     * @var bool
     */
    protected bool $deleteAllowApiKey = false;

    /**
     * Whether server-to-server API keys may create/update this resource. Set to
     * false for sensitive resources (e.g. financial records) so only real
     * authenticated users can write.
     *
     * @var bool
     */
    protected bool $writeAllowApiKey = true;

    /**
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Model = $this->fetchTable();

        // The web app declares several belongsTo joins as INNER; force LEFT for
        // read endpoints so records with a null FK are not silently dropped.
        foreach ($this->Model->associations() as $assoc) {
            if ($assoc->type() === 'manyToOne') {
                $assoc->setJoinType('LEFT');
            }
        }
    }

    /**
     * List records with filtering, search, sorting and pagination.
     *
     * @return \Cake\Http\Response
     */
    public function index()
    {
        $this->request->allowMethod(['get']);
        $this->guardRead();
        ['page' => $page, 'limit' => $limit] = $this->paginationParams();

        $query = $this->Model->find();
        if ($this->indexContain) {
            $query->contain($this->indexContain);
        }

        $this->applyFilters($query);
        $this->applySearch($query);
        $this->applySort($query);

        $total = (clone $query)->count();
        $rows = $query->limit($limit)->offset(($page - 1) * $limit)->all()->toList();

        return $this->respond($rows, 200, [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => (int)ceil($total / max(1, $limit)),
        ]);
    }

    /**
     * View a single record with its belongsTo relations.
     *
     * @param string|null $id Record id.
     * @return \Cake\Http\Response
     */
    public function view($id = null)
    {
        $this->request->allowMethod(['get']);
        $this->guardRead();
        $record = $this->findOrFail($id, $this->viewContain());

        return $this->respond($record);
    }

    /**
     * Create a record.
     *
     * @return \Cake\Http\Response
     */
    public function add()
    {
        $this->request->allowMethod(['post']);
        $this->requireRole($this->writeRoles, $this->writeAllowApiKey);

        $entity = $this->Model->newEntity($this->request->getData());
        if (!$this->Model->save($entity)) {
            return $this->respondError('Validation failed.', 422, $entity->getErrors());
        }

        return $this->respond($entity, 201);
    }

    /**
     * Update a record.
     *
     * @param string|null $id Record id.
     * @return \Cake\Http\Response
     */
    public function edit($id = null)
    {
        $this->request->allowMethod(['put', 'patch']);
        $this->requireRole($this->writeRoles, $this->writeAllowApiKey);

        $entity = $this->findOrFail($id);
        $entity = $this->Model->patchEntity($entity, $this->request->getData());
        if (!$this->Model->save($entity)) {
            return $this->respondError('Validation failed.', 422, $entity->getErrors());
        }

        return $this->respond($entity);
    }

    /**
     * Delete a record.
     *
     * @param string|null $id Record id.
     * @return \Cake\Http\Response
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['delete']);
        $this->requireRole($this->deleteRoles, $this->deleteAllowApiKey);

        $entity = $this->findOrFail($id);
        if (!$this->Model->delete($entity)) {
            return $this->respondError('Could not delete record.', 409);
        }

        return $this->respond(['deleted' => true, 'id' => $id]);
    }

    /**
     * Apply exact-match filters from the query string.
     *
     * @param \Cake\ORM\Query $query Query to mutate.
     * @return void
     */
    protected function applyFilters($query): void
    {
        $allowed = $this->filterFields ?? $this->Model->getSchema()->columns();
        $alias = $this->Model->getAlias();
        foreach ($allowed as $field) {
            $value = $this->request->getQuery($field);
            if ($value !== null && $value !== '') {
                $query->where([$alias . '.' . $field => $value]);
            }
        }
    }

    /**
     * Apply free-text search over the configured/display fields.
     *
     * @param \Cake\ORM\Query $query Query to mutate.
     * @return void
     */
    protected function applySearch($query): void
    {
        $term = trim((string)$this->request->getQuery('q'));
        if ($term === '') {
            return;
        }
        $fields = $this->searchFields ?: $this->defaultSearchFields();
        // Guard: only search columns that actually exist on the table.
        $columns = $this->Model->getSchema()->columns();
        $fields = array_values(array_intersect($fields, $columns));
        if (!$fields) {
            return;
        }
        $alias = $this->Model->getAlias();
        $like = '%' . $term . '%';
        $conditions = [];
        foreach ($fields as $field) {
            $conditions[$alias . '.' . $field . ' LIKE'] = $like;
        }
        $query->where(['OR' => $conditions]);
    }

    /**
     * Apply ?sort= and ?direction= (whitelisted to real columns).
     *
     * @param \Cake\ORM\Query $query Query to mutate.
     * @return void
     */
    protected function applySort($query): void
    {
        $sort = (string)$this->request->getQuery('sort');
        $columns = $this->Model->getSchema()->columns();
        if ($sort !== '' && in_array($sort, $columns, true)) {
            $dir = strtolower((string)$this->request->getQuery('direction')) === 'asc' ? 'ASC' : 'DESC';
            $query->order([$this->Model->getAlias() . '.' . $sort => $dir]);
        } else {
            $query->order([$this->Model->getAlias() . '.' . $this->Model->getPrimaryKey() => 'DESC']);
        }
    }

    /**
     * Default search fields = the model's display field, if it's a real column.
     *
     * @return array<int, string>
     */
    protected function defaultSearchFields(): array
    {
        $display = $this->Model->getDisplayField();
        $columns = $this->Model->getSchema()->columns();

        return is_string($display) && in_array($display, $columns, true) ? [$display] : [];
    }

    /**
     * Associations to eager-load on view: all belongsTo unless overridden.
     *
     * @return array<int, string>
     */
    protected function viewContain(): array
    {
        if ($this->viewContain !== null) {
            return $this->viewContain;
        }
        $contain = [];
        foreach ($this->Model->associations() as $assoc) {
            if ($assoc->type() === 'manyToOne') {
                $contain[] = $assoc->getName();
            }
        }

        return $contain;
    }

    /**
     * List records of a hasMany/belongsToMany association for a parent record.
     * Reusable by subclasses to expose nested collections, e.g.
     * GET /exams/{id}/questions.
     *
     * @param mixed $id Parent record id.
     * @param string $assoc Association name on this model.
     * @param array $contain Associations to eager-load on the child rows.
     * @return \Cake\Http\Response
     */
    protected function relatedList($id, string $assoc, array $contain = [])
    {
        $this->request->allowMethod(['get']);
        $this->findOrFail($id); // 404 if parent missing

        if (!$this->Model->hasAssociation($assoc)) {
            return $this->respondError("Association {$assoc} is not available.", 404);
        }

        ['page' => $page, 'limit' => $limit] = $this->paginationParams();
        $association = $this->Model->getAssociation($assoc);
        $target = $association->getTarget();
        $foreignKey = $association->getForeignKey();

        $query = $target->find();
        if ($contain) {
            $query->contain($contain);
        }
        $query->where([$target->getAlias() . '.' . $foreignKey => $id]);

        $total = (clone $query)->count();
        $rows = $query->limit($limit)->offset(($page - 1) * $limit)->all()->toList();

        return $this->respond($rows, 200, [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => (int)ceil($total / max(1, $limit)),
        ]);
    }

    /**
     * The authenticated user's id (from a JWT), or null for API-key callers.
     *
     * @return int|null
     */
    protected function currentUserId(): ?int
    {
        if ($this->identity && $this->identity['type'] === 'user') {
            return (int)($this->identity['user']['sub'] ?? 0) ?: null;
        }

        return null;
    }

    /**
     * Enforce $readRoles on read endpoints when set.
     *
     * @return void
     * @throws \Cake\Http\Exception\ForbiddenException
     */
    protected function guardRead(): void
    {
        if ($this->readRoles !== null) {
            $this->requireRole($this->readRoles);
        }
    }

    /**
     * Fetch a record by primary key (LEFT joins) or throw 404.
     *
     * @param mixed $id Primary key value.
     * @param array $contain Associations to eager-load.
     * @return \Cake\Datasource\EntityInterface
     * @throws \Cake\Http\Exception\NotFoundException
     */
    protected function findOrFail($id, array $contain = [])
    {
        $alias = $this->Model->getAlias();
        $record = $this->Model->find()
            ->contain($contain)
            ->where([$alias . '.' . $this->Model->getPrimaryKey() => $id])
            ->first();
        if ($record === null) {
            throw new NotFoundException($this->Model->getAlias() . ' record not found.');
        }

        return $record;
    }
}
