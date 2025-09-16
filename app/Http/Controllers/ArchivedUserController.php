<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ArchivedUserController extends Controller
{
    public function __construct(private UserService $userService) {}

    public function index(Request $request): Response
    {
        $perPage = $request->input('per_page', 10);

        $search = $request->input('search', '');

        $filters = $request->input('filters', []);

        $data = $this->userService->getAllUsers($perPage, $search, $filters, true);

        return Inertia::render('archives/users/Index', compact('data'));
    }

    public function update(User $user): RedirectResponse
    {
        $this->userService->restoreArchivedUser($user);

        $message = __('responses.restore.user', ['first_name' => $user->employee->first_name]);

        return back()->with(compact('message'));
    }

    public function bulkRestore(Request $request): RedirectResponse
    {
        $userIds = $request->input('userIds', []);

        $this->userService->bulkRestoreArchivedUsers($userIds);

        $message = __('responses.batch_restore.user', ['count' => count($userIds)]);

        return back()->with(compact('message'));
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->userService->permanentlyDeleteUser($user);

        $message = __('responses.permanent_delete.user', ['first_name' => $user->employee->first_name]);

        return back()->with(compact('message'));
    }
}
