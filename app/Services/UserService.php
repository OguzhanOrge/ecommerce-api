<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserService
{
    protected UserRepository $user;

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    /**
     * Get all users
     */
    public function getAllUsers()
    {
        return $this->user->all();
    }

    /**
     * Get user by ID
     */
    public function getUserById($id)
    {
        try {
            return $this->user->find($id);
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    /**
     * Create a new user
     */
    public function createUser(array $data)
    {
        return DB::transaction(function () use ($data) {
            $data['password'] = Hash::make($data['password']);
            return $this->user->create($data);
        });
    }

    /**
     * Update user
     */
    public function updateUser($id, array $data)
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                if (isset($data['password'])) {
                    $data['password'] = Hash::make($data['password']);
                }
                return $this->user->update($id, $data);
            });
        } catch (ModelNotFoundException $e) {
            return null;
        }
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                return $this->user->delete($id);
            });
        } catch (ModelNotFoundException $e) {
            return false;
        }
    }
}
