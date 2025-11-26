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
    #[OA\Post(
        path: "/api/auth/register",
        summary: "Register a new user",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                "multipart/form-data" => new OA\MediaType(
                    mediaType: "multipart/form-data",
                    schema: new OA\Schema(
                        required: ["first_name","last_name","phone","password","birth_date","profile_image","id_image"],
                        properties: [
                            new OA\Property(property: "first_name", type: "string", example: "Ravo"),
                            new OA\Property(property: "last_name", type: "string", example: "King"),
                            new OA\Property(property: "phone", type: "string", example: "0912345678"),
                            new OA\Property(property: "password", type: "string", format: "password", example: "123456"),
                            new OA\Property(property: "birth_date", type: "string", format: "date", example: "2003-01-01"),
                            new OA\Property(property: "profile_image", type: "string", format: "binary"),
                            new OA\Property(property: "id_image", type: "string", format: "binary"),
                        ]
                    )
                )
            ]
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "User registered successfully",
                content: [
                    "application/json" => new OA\MediaType(
                        schema: new OA\Schema(ref: "#/components/schemas/AuthResponse")
                    )
                ]
            ),
            new OA\Response(response: Response::HTTP_UNPROCESSABLE_ENTITY, description: "Validation error")
        ]
    )]
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

    #[OA\Post(
        path: "/api/auth/login",
        summary: "Login user",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: [
                "application/json" => new OA\MediaType(
                    mediaType: "application/json",
                    schema: new OA\Schema(
                        required: ["phone","password"],
                        properties: [
                            new OA\Property(property: "phone", type: "string", example: "0912345678"),
                            new OA\Property(property: "password", type: "string", example: "123456")
                        ]
                    )
                )
            ]
        ),
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "Login successful",
                content: [
                    "application/json" => new OA\MediaType(
                        schema: new OA\Schema(ref: "#/components/schemas/AuthResponse")
                    )
                ]
            ),
            new OA\Response(response: Response::HTTP_UNAUTHORIZED, description: "Invalid credentials")
        ]
    )]
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (!$token = auth()->attempt($credentials)) {
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
