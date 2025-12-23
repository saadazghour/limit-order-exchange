<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserProfileResource;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Display the authenticated user's profile, including their balance and assets.
     */
    public function show(Request $request): UserProfileResource
    {
        return UserProfileResource::make($request->user()->load('assets'));
    }
}
