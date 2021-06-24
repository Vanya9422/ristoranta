<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Repository
 * @package App\Repositories\Eloquent
 */
abstract class Repository implements RepositoryInterface
{
    /**
     * @var array
     */
    protected array $selects;

    /**
     * @var Model
     */
    protected Model $model;

    /**
     * Repository constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->selects = $this->model->getFillable();
    }

    /**
     * returned Repository name (example - return 'MenuRepository' )
     *
     * @return string
     */
    public abstract function getRepositoryName(): string;

    /**
     * Get one
     * @param $id
     * @param array $relations
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function find($id, array $relations = [])
    {
        return $this->newQuery()->select($this->selects ?: ['*'])->with($relations)->findOrFail($id);
    }

    /**
     * @param array $conditions
     * @return bool
     */
    public function exists(array $conditions = []): bool
    {
        return $this->newQuery()->where($conditions)->exists();
    }

    /**
     * @param $id
     * @param array $data
     * @param array $relations
     * @return Builder|Builder[]|Collection|Model|null
     */
    public function updateAndGiveSelf($id, array $data, array $relations = [])
    {
        $this->update($id, $data);

        return $this->find($id, $relations);
    }

    /**
     * @return Builder[]|Collection
     */
    public function all()
    {
        return $this->newQuery()->get($this->selects ?: ['*']);
    }

    /**
     * @param int $perPage
     * @param array $conditions
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 1, array $conditions = []): LengthAwarePaginator
    {
        return $this->newQuery()->where($conditions)->paginate($perPage, $this->selects ?: ['*']);
    }

    /**
     * @param array $data
     * @return Builder|Model
     */
    public function create(array $data)
    {
        return $this->newQuery()->create($data);
    }

    /**
     * @param $id
     * @param array $data
     */
    public function update($id, array $data)
    {
        $this->newQuery()->findOrFail($id)->update($data);
    }

    /**
     * @param array $conditions
     * @param array $data
     * @return Builder|Model
     */
    public function updateOrCreate(array $conditions, array $data)
    {
        return $this->newQuery()->updateOrCreate($conditions, $data);
    }

    /**
     * @param $id
     * @param bool $forceDelete
     * @return mixed
     */
    public function firstAndDelete($id, bool $forceDelete = false)
    {
        $model = $this->newQuery();

        if ($forceDelete) return $model->withTrashed()->findOrFail($id)->forceDelete();

        return $model->findOrFail($id)->delete();
    }

    /**
     * @param array|int $id
     * @return int
     */
    public function destroy($id): int
    {
        return $this->model->destroy($id);
    }

    /**
     * Find a collection of models by the given query conditions.
     *
     * @param array $criteria
     * @param array $relations
     * @param bool|string $get
     * @return Builder|Builder[]|Collection|Model|object|null
     */
    public function findByCriteria(array $criteria, array $relations = [], $get = false)
    {
        $model = $this->newQuery()->with($relations)->where($criteria);

        if ($get === 'first') return $model->first($this->selects ?: ['*']);

        return $get
            ? $model->get($this->selects ?: ['*'])
            : $model->firstOrFail($this->selects ?: ['*']);
    }

    /**
     * @param Model $model
     * @param bool $forceDelete
     */
    public function deleteModel($model, bool $forceDelete = false): void
    {
        if ($model instanceof Model) {
            $forceDelete ? $model->forceDelete() : $model->delete();
        }

        if ($model instanceof Collection) {
            $model->map(function ($item) use ($model, $forceDelete) {
                $forceDelete ? $item->forceDelete() : $item->delete();
            });
        }
    }

    /**
     * @return Builder
     */
    public function newQuery(): Builder
    {
        return $this->model->newQuery();
    }

    /**
     * @param array $columns
     * @param bool $unset
     */
    public function setUnsetColumns(array $columns = [], bool $unset = false): void
    {
        if (!empty($columns)) {
            if (!empty($this->selects)) {
                $this->selects = collect($this->selects)->filter(function ($item) use ($unset, $columns) {
                    return $unset ? !in_array($item, $columns) : in_array($item, $columns);
                })->toArray();
            } else {
                $this->selects = $columns;
            }
        }
    }

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @return array|string[]
     */
    public function getSelfRelations(): array
    {
        return $this->getModel()->selfRelations;
    }
}
