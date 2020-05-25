<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class UserAvatarController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a new user avatar.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        request()->validate([
            'avatar' => ['required', 'image'],
        ]);

        auth()->user()->update([
            'avatar_path' => request()->file('avatar')->store('avatars', 'public'),
        ]);

        return response([], 204);
    }
}
