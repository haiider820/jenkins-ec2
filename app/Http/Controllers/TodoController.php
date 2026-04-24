<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $todos = Todo::orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
                    ->orderBy('created_at', 'desc')
                    ->get();
        return view('todos.index', compact('todos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('todos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
        ]);

        Todo::create($request->all());

        return redirect()->route('todos.index')->with('success', 'Todo created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $todo = Todo::findOrFail($id);
        return view('todos.show', compact('todo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $todo = Todo::findOrFail($id);
        return view('todos.edit', compact('todo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'completed' => 'boolean',
            'priority' => 'required|in:low,medium,high',
        ]);

        $todo = Todo::findOrFail($id);
        $todo->update($request->all());

        return redirect()->route('todos.index')->with('success', 'Todo updated successfully.');
    }

    /**
     * Toggle the completion status of the specified todo.
     */
    public function toggle(Request $request, string $id)
    {
        $todo = Todo::findOrFail($id);
        $todo->completed = !$todo->completed;
        $todo->save();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'completed' => $todo->completed
            ]);
        }

        return redirect()->back()->with('success', 'Todo status updated successfully.');
    }
}
