<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class UserService
{
    /**
     * Get all users with pagination
     */
    public function getAllUsers(int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return User::paginate($perPage);
    }

    /**
     * Get a specific user by ID
     */
    public function getUserById(int $id): User
    {
        $user = User::find($id);
        
        if (!$user) {
            throw new \Exception('User not found', 404);
        }
        
        return $user;
    }

    /**
     * Create a new user
     */
    public function createUser(array $data): User
    {
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return User::create($data);
    }

    /**
     * Update an existing user
     */
    public function updateUser(int $id, array $data): User
    {
        $user = $this->getUserById($id);
        
        // Hash password if provided
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        
        $user->update($data);
        
        return $user->fresh();
    }

    /**
     * Delete a user
     */
    public function deleteUser(int $id): bool
    {
        $user = $this->getUserById($id);
        
        return $user->delete();
    }

    /**
     * Get user with their carts and invoices
     */
    public function getUserWithRelations(int $id): User
    {
        return User::with(['carts.cartItems.product', 'invoices.invoiceItems.product'])->findOrFail($id);
    }

    /**
     * Get users by role
     */
    public function getUsersByRole(string $role): Collection
    {
        return User::where('role', $role)->get();
    }
}
