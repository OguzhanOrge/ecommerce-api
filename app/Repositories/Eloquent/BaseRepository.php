<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(array $with = []): Collection
    {
        return $this->model->with($with)->get();
    }

    public function paginate(int $perPage = 15, array $with = []): LengthAwarePaginator
    {
        return $this->model->with($with)->paginate($perPage);
    }

    public function find(int $id, array $with = [])
    {
        return $this->model->with($with)->findOrFail($id);
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function update(int $id, array $attributes)
    {
        $model = $this->find($id);
        $model->update($attributes);
        return $model;
    }

    public function delete(int $id)
    {
        return $this->find($id)->delete();
    }

    public function filter(array $filters = [], array $with = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->with($with);

        foreach ($filters as $key => $value) {
            if (is_null($value) || $value === '') continue;

            // Örnek filtreleme: name, status, created_at vs.
            if (is_array($value)) {
                $query->whereIn($key, $value);
            } else {
                $query->where($key, 'like', "%{$value}%");
            }
        }

        return $query->paginate($perPage);
    }
}
