<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use App\Helpers\ApiHelper;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;

class AdminController extends Controller
{
    #[OA\Get(
        path: "/api/admin/pending-users",
        summary: "Get all pending users",
        tags: ["Admin"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "List of pending users",
                content: [
                    "application/json" => new OA\MediaType(
                        schema: new OA\Schema(
                            type: "object",
                            properties: [
                                new OA\Property(
                                    property: "data",
                                    type: "array",
                                    items: new OA\Property(ref: "#/components/schemas/UserResource")
                                )
                            ]
                        )
                    )
                ]
            )
        ]
    )]
    public function pendingUsers()
    {
        $users = User::where('verification_state', 'pending')->get();
        return ApiHelper::success(null, UserResource::collection($users));
    }

    #[OA\Post(
        path: "/api/admin/verify-user/{id}",
        summary: "Verify a user",
        tags: ["Admin"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "User verified",
                content: [
                    "application/json" => new OA\MediaType(
                        schema: new OA\Schema(ref: "#/components/schemas/UserResource")
                    )
                ]
            )
        ]
    )]
    public function verifyUser($id)
    {
        $user = User::findOrFail($id);
        $user->verification_state = 'verified';
        $user->save();

        return ApiHelper::success("User verified", new UserResource($user));
    }

    #[OA\Post(
        path: "/api/admin/reject-user/{id}",
        summary: "Reject a user",
        tags: ["Admin"],
        security: [["bearerAuth" => []]],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: Response::HTTP_OK,
                description: "User rejected",
                content: [
                    "application/json" => new OA\MediaType(
                        schema: new OA\Schema(ref: "#/components/schemas/UserResource")
                    )
                ]
            )
        ]
    )]
    public function rejectUser($id)
    {
        $user = User::findOrFail($id);
        $user->verification_state = 'rejected';
        $user->save();

        return ApiHelper::success("User rejected", new UserResource($user));
    }
}
