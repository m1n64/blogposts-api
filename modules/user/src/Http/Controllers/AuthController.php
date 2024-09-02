<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\User\Http\Requests\Auth\LoginRequest;
use Modules\User\Http\Resources\Auth\AuthUserResource;
use Modules\User\Http\Requests\Auth\RegisterRequest;
use Modules\User\Models\User;

class AuthController extends Controller
{
    /**
     * @param RegisterRequest $request
     * @return AuthUserResource
     * @unauthenticated
     */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'username' => Str::random(8),
        ]);

        $neo4j = app('neo4j');
        $neo4j->run('CREATE (u:User {id: $id})', [
            'id' => $user->id
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return AuthUserResource::make($user)->additional([
            'token' => $token,
        ]);
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse|AuthUserResource
     * @unauthenticated
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials)) {
            return response()->json(['message' => 'Invalid login details'], 401);
        }

        $user = auth()->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return AuthUserResource::make($user)->additional([
            'token' => $token,
        ]);
    }
}
