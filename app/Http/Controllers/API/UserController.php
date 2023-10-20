<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserStoreRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Http\Resources\User\UserIndexResource;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = UserIndexResource::collection(User::query()->paginate(5));

        return response()->json([
            'status' => true,
            'message' => 'Berhasil get data pengguna',
            'data' => $users,
            'meta' => [
                'total' => $users->total(),
                'per-page' => $users->perPage(),
                'current_page' => $users->currentPage(),
                'last-page' => $users->lastPage(),
                'first-item' => $users->firstItem(),
            ],
        ]);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {

        $user = User::create($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Berhasil menambahkan data pengguna',
            'data' => new \App\Http\Resources\User\UserResource($user),
        ], \Symfony\Component\HttpFoundation\Response::HTTP_CREATED);

        //
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return response()->json([
            'status' => true,
            'message' => 'Berhasil Menampilkan Data Pengguna',
            'data' => new \App\Http\Resources\User\UserResource($user),

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $user->update($request->validated());

        return response()->json([
            'status' => true,
            'message' => 'Berhasil Mengubah Data Pengguna',
            'data' => new \App\Http\Resources\User\UserResource($user),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
