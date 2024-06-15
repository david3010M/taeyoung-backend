<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Optionmenu;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * @OA\Post (
     *       path="/taeyoung-backend/public/api/login",
     *       tags={"Authentication"},
     *       summary="Login user",
     *       @OA\RequestBody(
     *           required=true,
     *           @OA\JsonContent(
     *              @OA\Property( property="username", type="string", example="username" ),
     *              @OA\Property( property="password", type="string", example="123456" ),
     *           )
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="User authenticated",
     *         @OA\JsonContent(
     *            @OA\Property( property="access_token", type="string", example="11|SphTzJDxcMxPjpTA3GAMnGMepQKaWMpC05NKn10a1d2879de" ),
     *            @OA\Property( property="expires_at", type="string", example="2021-05-19 04:44:04" ),
     *            @OA\Property( property="user", ref="#/components/schemas/User" )
     *        )
     *      ),
     *      @OA\Response(
     *         response=401,
     *         description="User not authenticated",
     *         @OA\JsonContent(
     *            @OA\Property( property="message", type="string", example="Unauthorized." )
     *        )
     *      ),
     *      @OA\Response(
     *        response=400,
     *        description="Credentials are invalid",
     *        @OA\JsonContent(
     *           @OA\Property( property="message", type="string", example="Invalid credentials." )
     *        )
     *      )
     * )
     *
     */
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');
        $validator = Validator::make($credentials, [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid credentials'], 400);
        }

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            $token = $user->createToken('AuthToken', expiresAt: now()->addDays(7));

            $typeuser = $user->typeuser;

//            $typeuserAccess = $typeuser->getAccess($typeuser->id);

//            PERMISSIONS IN A STRING FORMAT
//            $typeuserHasPermission = $typeuser->getHasPermission($typeuser->id);

            return response()->json([
                'access_token' => $token->plainTextToken,
                'expires_at' => Carbon::parse($token->accessToken->expires_at)->toDateTimeString(),
                'user' => $user,
//                'typeuser' => $typeuser,
//                'optionMenuAccess' => $typeuserAccess,
//                'permissions' => $typeuserHasPermission

            ]);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    /**
     * @OA\Get (
     *     path="/taeyoung-backend/public/api/authenticate",
     *     tags={"Authentication"},
     *     security={{"bearerAuth": {}}},
     *     summary="Authenticate user",
     *     @OA\Response(
     *     response=200,
     *     description="User authenticated",
     *     @OA\JsonContent(
     *         @OA\Property( property="access_token", type="string", example="11|SphTzJDxcMxPjpTA3GAMnGMepQKaWMpC05NKn10a1d2879de" ),
     *         @OA\Property( property="expires_at", type="string", example="2021-05-19 04:44:04" ),
     *         @OA\Property( property="user", ref="#/components/schemas/User" )
     *     )
     *    ),
     *     @OA\Response(
     *     response=401,
     *     description="User not authenticated",
     *     @OA\JsonContent(
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             example="User not authenticated"
     *         )
     *     )
     *    )
     * )
     *
     */
    public function authenticate(Request $request)
    {
        $user = auth('sanctum')->user();
        $token = $request->bearerToken();

        if ($user) {
            $typeuser = $user->typeuser;

            return response()->json([
                'access_token' => $token,
                'expires_at' => Carbon::parse($user->currentAccessToken()->expires_at)->toDateTimeString(),
                'user' => $user,
            ]);
        } else {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    }


    /**
     * @OA\Get (
     *     path="/taeyoung-backend/public/api/logout",
     *     tags={"Authentication"},
     *     summary="Logout user",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *     response=200,
     *     description="Logged out successfully",
     *     @OA\JsonContent(
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             example="Logged out successfully"
     *         )
     *     )
     *    ),
     *     @OA\Response(
     *     response=401,
     *     description="User not authenticated",
     *     @OA\JsonContent(
     *         @OA\Property(
     *             property="message",
     *             type="string",
     *             example="User not authenticated"
     *         )
     *     )
     *    )
     * )
     */
    public function logout(Request $request)
    {
        if (auth('sanctum')->user()) {
            auth('sanctum')->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out successfully']);
        } else {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    }

}
