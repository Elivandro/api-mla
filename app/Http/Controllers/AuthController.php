<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function login(Request $request)
    {
        $user = User::where('email', $request->get('email'))->first();

        if(!$user || !Hash::check($request->get('password'), $user->password)){
            throw ValidationException::withMessages([
                'credentials' => 'The credentials are incorrect'
            ]);
        }

        return [
            'access_token' => $user->createToken($user->created_at, ['client:index'])->plainTextToken,
        ];
    }

    public function logout()
    {
        $user = auth()->user();

        $user->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out'], JsonResponse::HTTP_OK);
    }
}
