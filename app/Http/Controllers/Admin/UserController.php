<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')
            ->withCount('pemesanans')
            ->latest()
            ->paginate(15);

        return view('admin.user.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load(['pemesanans.mobil', 'pemesanans.payment']);
        return view('admin.user.show', compact('user'));
    }
}