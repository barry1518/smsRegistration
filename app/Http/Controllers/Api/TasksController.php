<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaskResource;
use Illuminate\Http\Request;
use App\Task;


class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TaskResource::collection(auth()->user()->tasks()->with('creator')->latest()->paginate(4));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255'
        ]);

        $task = auth()->user()->tasks()->create($request->all());

        return new TaskResource($task->load('creator'));
    }

    /**
     * Display the specified resource.
     *
     * @param Task $task
     * @return TaskResource
     */
    public function show(Task $task)
    {
        return new TaskResource($task->load('creator'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Task $task
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|max:255'
        ]);

        $task->update($request->all());

        return new TaskResource($task->load('creator'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Task $task
     */
    public function destroy(Task $task)
    {
        $task->delete();

        return response(['message' => 'Deleted!']);
    }
}
