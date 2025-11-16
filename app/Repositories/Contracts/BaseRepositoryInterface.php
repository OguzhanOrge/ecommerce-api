<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface BaseRepositoryInterface
{
    public function all(array $with = []): Collection;

    public function paginate(int $perPage = 15, array $with = []): LengthAwarePaginator;

    public function find(int $id, array $with = []);

    public function create(array $attributes);

    public function update(int $id, array $attributes);

    public function delete(int $id);

    public function filter(array $filters = [], array $with = [], int $perPage = 15): LengthAwarePaginator;
}
