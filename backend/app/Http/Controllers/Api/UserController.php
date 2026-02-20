<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * GET /api/users
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection(User::all()),
        ]);
    }

    /**
     * POST /api/users
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'fullName' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'role' => 'required|in:Super admin,admin,manager,karyawan,marketing,mitra',
        ]);

        $user = User::create([
            'full_name' => $validated['fullName'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
        ]);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ], 201);
    }

    /**
     * PUT /api/users/{id}
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $this->authorizeAdmin($request);

        $user = User::findOrFail($id);

        $validated = $request->validate([
            'fullName' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:100|unique:users,username,' . $user->id,
            'email' => 'sometimes|email|max:255',
            'password' => 'sometimes|string|min:6',
            'role' => 'sometimes|in:Super admin,admin,manager,karyawan,marketing,mitra',
        ]);

        $updateData = [];
        if (isset($validated['fullName']))
            $updateData['full_name'] = $validated['fullName'];
        if (isset($validated['username']))
            $updateData['username'] = $validated['username'];
        if (isset($validated['email']))
            $updateData['email'] = $validated['email'];
        if (isset($validated['password']))
            $updateData['password'] = $validated['password'];
        if (isset($validated['role']))
            $updateData['role'] = $validated['role'];

        $user->update($updateData);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user->fresh()),
        ]);
    }

    /**
     * DELETE /api/users/{id}
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $this->authorizeAdmin($request);

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true]);
    }

    private function authorizeAdmin(Request $request): void
    {
        if (!$request->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
    }
}
