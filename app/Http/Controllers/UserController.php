<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $users = $this->userService->getAllUsers($perPage);
            
            return response()->json([
                'success' => true,
                'data' => $users,
                'message' => 'Users retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified user
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = $this->userService->getUserById($id);
            
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'role' => 'required|string|in:admin,customer,manager'
            ]);

            $user = $this->userService->createUser($validated);
            
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User created successfully'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'password' => 'sometimes|string|min:8',
                'role' => 'sometimes|string|in:admin,customer,manager'
            ]);

            $user = $this->userService->updateUser($id, $validated);
            
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User updated successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->userService->deleteUser($id);
            
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Get user with relations (carts and invoices)
     */
    public function showWithRelations(int $id): JsonResponse
    {
        try {
            $user = $this->userService->getUserWithRelations($id);
            
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User with relations retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * Get users by role
     */
    public function getByRole(string $role): JsonResponse
    {
        try {
            $users = $this->userService->getUsersByRole($role);
            
            return response()->json([
                'success' => true,
                'data' => $users,
                'message' => "Users with role '{$role}' retrieved successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
