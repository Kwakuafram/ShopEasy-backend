<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\PersonalAccessToken;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'customer',
        ]);

        return response()->json($user, 200);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ], 200);
    }

    public function getUser(Request $request)
    {
        $token = $request->bearerToken();
    
        // Find the user based on the token
        $accessToken = PersonalAccessToken::findToken($token);
    
        if (!$accessToken) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        // Get the user ID from the token
        $userId = $accessToken->tokenable_id;
        return User::where('id', $userId)->get();
    }

    public function getUsers(Request $request)
    {
        $token = $request->bearerToken();
    
        // Find the user based on the token
        $accessToken = PersonalAccessToken::findToken($token);
    
        if (!$accessToken) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        // Get the user ID from the token
        $userId = $accessToken->tokenable_id;
        return User::where('id', $userId)->get();
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out'], 200);
    }

    public function verifyEmail(Request $request)
    {
        

        $user = User::where('email', $request->email)->first();

        if ($user) {
            return response()->json(['isValid' => true], 200);
        } else {
            return response()->json(['isValid' => false], 404);
        }
    }


    public function resetPassword(Request $request)
    {
       

        // Find the user by email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        // Update the user's password
        $user->password = Hash::make($request->newPassword);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Password reset successfully'], 200);
    }


}
