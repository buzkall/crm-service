<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        // Resource Permissions handled in UserPolicy
        $this->authorizeResource(User::class, 'user');
    }

    public function index(): JsonResponse
    {
        return $this->sendResponse(User::all());
    }

    public function show(User $user): JsonResponse
    {
        return $this->sendResponse($user);
    }

    public function store(UserRequest $request): JsonResponse
    {
        // the password is hashed in the prepareForValidation function in UserRequest
        $user = User::create($request->validated());

        return $this->sendCreated($user, 'New user successfully created');
    }

    public function update(UserRequest $request, User $user): JsonResponse
    {
        $user->update($request->validated());

        return $this->sendResponse($user, 'User successfully updated');
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();
        return $this->sendNoContent();
    }

    public function updateAdminStatus(Request $request, User $user): JsonResponse
    {
        $this->authorize('updateAdminStatus', [User::class, $user]);

        if (!$request->has('is_admin')) {
            echo 'hola0';
            die();
        }

        $user->is_admin = $request->is_admin;
        $user->save();
        return $this->sendSuccess('Status successfully changed');
    }
}
