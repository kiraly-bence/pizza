<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateUserRoleRequest;
use App\Models\User;
use App\Services\Admin\UserService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Users', [
            'users' => $this->userService->all(),
        ]);
    }

    public function updateRole(UpdateUserRoleRequest $request, User $user): RedirectResponse
    {
        try {
            $this->userService->updateRole($user, $request->validated('role'), auth()->id());
        } catch (\RuntimeException $e) {
            return back()->withErrors(['role' => $e->getMessage()]);
        }

        return back();
    }

    public function ban(User $user): RedirectResponse
    {
        try {
            $this->userService->ban($user, auth()->id());
        } catch (\RuntimeException $e) {
            return back()->withErrors(['ban' => $e->getMessage()]);
        }

        return back();
    }

    public function unban(User $user): RedirectResponse
    {
        $this->userService->unban($user);

        return back();
    }
}
