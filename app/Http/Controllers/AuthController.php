<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Helpers\ApiHelper;
use Tymon\JWTAuth\Facades\JWTAuth;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{

    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'phone'      => $data['phone'],
            'birth_date' => $data['birth_date'],
            'password'   => bcrypt($data['password']),
            'verification_state' => 'pending'
        ]);

        ApiHelper::saveMedia($user, $request->file('profile_image'), 'profile_image');
        ApiHelper::saveMedia($user, $request->file('id_image'), 'id_image');

        $token = JWTAuth::fromUser($user);

        return ApiHelper::success("Registered successfully", [
            "token" => $token,
            "user"  => new UserResource($user)
        ]);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        $token = JWTAuth::attempt($credentials);
        if (!$token) {
            return ApiHelper::error("Invalid phone or password", 401);
        }

        return ApiHelper::success("Logged in", [
            "token" => $token,
            "user"  => new UserResource(auth()->user())
        ]);
    }

    #[OA\Get(
        path: "/api/auth/me",
        summary: "Fetch authenticated user",
        tags: ["Auth"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Authenticated user data",
                content: [
                    "application/json" => new OA\MediaType(
                        schema: new OA\Schema(ref: "#/components/schemas/UserResource")
                    )
                ]
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: "Unauthorized")
        ]
    )]
    public function me()
    {
        return ApiHelper::success(null, new UserResource(auth()->user()));
    }
}
