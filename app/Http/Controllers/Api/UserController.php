<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;

use App\Models\User;

class UserController extends Controller
{
    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Register a new user",
     *     description="Registers a new user with a unique email and returns an authentication token.",
     *     operationId="registerUser",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(
     *                 property="name",
     *                 type="string",
     *                 maxLength=255,
     *                 example="John Doe"
     *             ),
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 maxLength=255,
     *                 example="john.doe@example.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 minLength=8,
     *                 example="strongpassword123"
     *             ),
     *             @OA\Property(
     *                 property="password_confirmation",
     *                 type="string",
     *                 format="password",
     *                 minLength=8,
     *                 example="strongpassword123"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User registered successfully."),
     *             @OA\Property(property="token", type="string", example="your_auth_token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to register user",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Failed to register user"
     *             )
     *        )
     *     ),
     *     security={}
     * )
     */

    // Register a new user
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['message' => 'User registered successfully.', 'token' => $token], 201);
        } catch (\Exception $e) {
            Log::error('Failed to register: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to register'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="Login a user",
     *     description="Authenticates a user and returns an authentication token.",
     *     operationId="loginUser",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 example="john.doe@example.com"
     *             ),
     *             @OA\Property(
     *                 property="password",
     *                 type="string",
     *                 format="password",
     *                 example="strongpassword123"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Login successful."),
     *             @OA\Property(property="token", type="string", example="your_auth_token")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid credentials.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to login",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                  property="error",
     *                  type="string",
     *                  example="Failed to login"
     *             )
     *         )
     *     ),
     *
     *     security={}
     * )
     */

    // Login a user
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if (!Auth::attempt($credentials)) {
                return response()->json(['message' => 'Invalid credentials.'], 401);
            }

            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json(['message' => 'Login successful.', 'token' => $token], 200);
        } catch (\Exception $e) {
            Log::error('Failed to login: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to login'], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/password/reset",
     *     summary="Reset a user's password",
     *     description="Sends a password reset link to the user's email address.",
     *     operationId="resetPassword",
     *     tags={"User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(
     *                 property="email",
     *                 type="string",
     *                 format="email",
     *                 example="john.doe@example.com"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset link sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password reset link sent.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Unable to send reset link",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unable to send reset link.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     security={}
     * )
     */

    // Reset a user's password
    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|string|email',
            ]);

            $status = Password::sendResetLink(
                $request->only('email')
            );

            return $status === Password::RESET_LINK_SENT
                ? response()->json(['message' => 'Password reset link sent.'], 200)
                : response()->json(['message' => 'Unable to send reset link.'], 500);
        } catch (\Exception $e) {
            Log::error('Failed to send password reset link: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send password reset link'], 500);
        }
    }

}
