<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\task\TaskIndexResource;
use App\Http\Resources\task\taskresource;
use App\Models\Task;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $task = TaskIndexResource::collection(
            Task::query()->with('user')->paginate(5));

        return response()->json([
            'status' => true,
            'message' => 'Berhasil get data pengguna',
            'data' => $task,
            'meta' => [
                'total' => $task->total(),
                'per-page' => $task->perPage(),
                'current_page' => $task->currentPage(),
                'last-page' => $task->lastPage(),
                'first-item' => $task->firstItem(),
            ],
        ]); //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $file = $request->file('upload_file')->getClientOriginalExtension();

        if (in_array($file, ['jpg', 'png', 'jpeg'])) {
            $upload_file = $request->file('upload_file')->store('public/images');
        } else {
            $upload_file = $request->file('upload_file')->store('public/files');
        }

        $task = Task::create([
            'title' => $request->get('title'),
            'description' => $request->get('description'),
            'upload_file' => $upload_file,
            'user_id' => auth()->user()->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Berhasil Menambahkan Tugas',
            'data' => new taskresource($task),

        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {

        return response()->json([
            'status' => true,
            'message' => 'Berhasil Menampilkan Data Pengguna',
            'data' => new taskresource($task),

        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'upload_file' => 'nullable|file',
        ]);

        $upload_file = null;
        if ($request->hasFile('upload_file')) {
            \File::delete('storage_path'("app/{$upload_file}"));
            $extension = $request->file('upload_file')->getClientOriginalExtension();
            if (in_array($extension, ['jpg', 'png', 'jpeg'])) {
                $upload_file = $request->file('upload_file')->store('public/images');
            } else {
                $upload_file = $request->file('upload_file')->store('public_files');
            }
        }

        $task->update([
            'title' => $request->get('title'),
            'description' => $request->get('description'),
            'upload_file' => $request->get('upload_file'),
            'user_id' => auth()->user()->id,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Berhasil Mengubah Tugas',
            'data' => new taskresource($task),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        \File::delete('storage_path'("app/{$task->$upload_file}"));
        $task->delete();

        return response()->json([], Response::HTTP_NO_CONTENT);
    }
}
