<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRoleRequest;
use App\Models\User;
use App\Services\Admin\UserService;
use Inertia\Inertia;

class UserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function index()
    {
        return Inertia::render('Admin/Users', [
            'auth'  => ['user' => auth()->user()],
            'users' => $this->userService->all(),
        ]);
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user)
    {
        try {
            $this->userService->updateRole($user, $request->validated('role'), auth()->id());
        } catch (\RuntimeException $e) {
            return back()->withErrors(['role' => $e->getMessage()]);
        }

        return back();
    }

    public function ban(User $user)
    {
        try {
            $this->userService->ban($user, auth()->id());
        } catch (\RuntimeException $e) {
            return back()->withErrors(['ban' => $e->getMessage()]);
        }

        return back();
    }

    public function unban(User $user)
    {
        $this->userService->unban($user);

        return back();
    }
}
