<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserStatusRequest;
use App\Models\User;
use App\UserStatus;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/users/index', [
            'users' => User::query()
                ->with('vCard:id,user_id,slug,status')
                ->latest('id')
                ->paginate(20)
                ->through(fn (User $user) => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role->value,
                    'status' => $user->status->value,
                    'created_at' => $user->created_at?->toDateString(),
                    'vcard' => $user->vCard ? [
                        'slug' => $user->vCard->slug,
                        'status' => $user->vCard->status->value,
                    ] : null,
                ]),
        ]);
    }

    public function update(UpdateUserStatusRequest $request, User $user): RedirectResponse
    {
        $user->update([
            'status' => UserStatus::from($request->string('status')->toString()),
        ]);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'El estado de la cuenta fue actualizado.',
        ]);

        return back();
    }
}
