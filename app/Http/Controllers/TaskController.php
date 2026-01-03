<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function index(): JsonResponse
    {
        $tasks = auth()->user()->tasks();
        return response()->json($tasks);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $task = auth()->user()->tasks()->create($data);
        return response()->json($task, 201);
    }

    public function show(Task $task): JsonResponse
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['error' => 'Задача не найдена'], 404);
        }
        return response()->json($task);
    }

    public function update(Request $request, Task $task): JsonResponse
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['error' => 'Задача не найдена'], 404);
        }

        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'completed' => 'sometimes|boolean',
        ]);


        $task->update($data);
        return response()->json($task);
    }

    public function destroy(Task $task): JsonResponse
    {
        if ($task->user_id !== auth()->id()) {
            return response()->json(['error' => 'Задача не найдена'], 404);
        }

        $task->delete();
        return response()->json(null, 204);
    }
}
