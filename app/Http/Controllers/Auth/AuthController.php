<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\GroupMenu;
use App\Models\Optionmenu;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
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
     *            @OA\Property( property="user", ref="#/components/schemas/User" ),
     *            @OA\Property( property="group_menus", type="array", @OA\Items( type="object", ref="#/components/schemas/GroupMenu" ) ),
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

            $groupMenu = GroupMenu::getFilteredGroupMenus($user->typeuser->id);

            return response()->json([
                'access_token' => $token->plainTextToken,
//                'expires_at' => Carbon::parse($token->accessToken->expires_at)->toDateTimeString(),
                'user' => $user,
                'group_menus' => $groupMenu,

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
     *         @OA\Property( property="user", ref="#/components/schemas/User" ),
     *         @OA\Property( property="group_menus", type="array", @OA\Items( type="object", ref="#/components/schemas/GroupMenu" ) ),
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

        if ($user) {
            $user = User::find($user->id);

            $user->tokens()->delete();

            $token = $user->createToken('AuthToken', ['expires_at' => now()->addDays(7)])->plainTextToken;
            $groupMenu = GroupMenu::getFilteredGroupMenus($user->typeuser->id);

            return response()->json([
                'access_token' => $token,
                'user' => $user,
                'group_menus' => $groupMenu,
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

    /**
     * @OA\Get (
     *     path="/taeyoung-backend/public/api/logs",
     *     tags={"Authentication"},
     *     summary="Get logs",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Successful operation", @OA\JsonContent(type="object", @OA\Property(property="errors", type="array", @OA\Items(
     *         @OA\Property(property="date", type="string", example="2021-10-01 00:00:00"),
     *         @OA\Property(property="environment", type="string", example="local"),
     *         @OA\Property(property="error_type", type="string", example="ERROR"),
     *         @OA\Property(property="message", type="string", example="Error message")
     *     )))),
     *     @OA\Response(response=404, description="No logs found", @OA\JsonContent(type="object", @OA\Property(property="message", type="string", example="No logs found.")))
     * )
     */
    public function logs()
    {
        $logFile = storage_path('logs/laravel.log');

        if (File::exists($logFile)) {
            $logs = File::get($logFile);
            $logLines = explode("\n", $logs);
            $errorLogs = array_filter($logLines, function ($line) {
                return strpos($line, 'ERROR') !== false;
            });
            $errorLogs = array_reverse($errorLogs);
            $errorObjects = array_map(function ($line) {
                preg_match('/^\[(.*?)\] (.*?)\.(.*?): (.*?)$/', $line, $matches);

                return [
                    'date' => $matches[1] ?? null,
                    'environment' => $matches[2] ?? null,
                    'error_type' => $matches[3] ?? null,
                    'message' => $matches[4] ?? $line,
                ];
            }, $errorLogs);

            return response()->json([
                'errors' => array_values($errorObjects)
            ]);
        }

        return response()->json([
            'message' => 'No logs found.'
        ], 404);
    }

}
