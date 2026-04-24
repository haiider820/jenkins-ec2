@extends('layouts.app')

@section('title', 'Todo List')

@section('content')
<div class="px-4 py-6 sm:px-0" x-data="todoApp()">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">Your Todos</h2>
        <div class="flex items-center space-x-4">
            <div class="relative">
                <input type="text" id="search" placeholder="Search todos..." class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" x-model="searchTerm" @input="filterTodos">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
            <select id="filter" class="block w-full pl-3 pr-10 py-2 text-base border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white" x-model="filterValue" @change="filterTodos">
                <option value="all">All Todos</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
            </select>
        </div>
    </div>

    <div id="todos-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($todos as $todo)
            <div class="todo-item bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200" data-todo-id="{{ $todo->id }}">
                <div class="p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2 {{ $todo->completed ? 'line-through text-gray-500 dark:text-gray-400' : '' }}">
                                {{ $todo->title }}
                            </h3>
                            @if($todo->description)
                                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                                    {{ Str::limit($todo->description, 100) }}
                                </p>
                            @endif
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @if($todo->priority === 'high') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                @elseif($todo->priority === 'medium') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                @else bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 @endif">
                                {{ ucfirst($todo->priority) }}
                            </span>
                            <button @click="toggleTodo({{ $todo->id }})"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors duration-200 {{ $todo->completed ? 'bg-green-100 text-green-800 hover:bg-green-200 dark:bg-green-900 dark:text-green-200 dark:hover:bg-green-800' : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200 dark:bg-yellow-900 dark:text-yellow-200 dark:hover:bg-yellow-800' }}">
                                {{ $todo->completed ? 'Completed' : 'Pending' }}
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between mt-4">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            Created {{ $todo->created_at->diffForHumans() }}
                        </div>
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('todos.show', $todo->id) }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400 dark:hover:text-blue-300 text-sm font-medium">
                                View
                            </a>
                            <a href="{{ route('todos.edit', $todo->id) }}" class="text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium">
                                Edit
                            </a>
                            <form action="{{ route('todos.destroy', $todo->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this todo?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No todos</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new todo.</p>
                    <div class="mt-6">
                        <a href="{{ route('todos.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Todo
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<script>
function todoApp() {
    return {
        searchTerm: '',
        filterValue: 'all',
        todos: @json($todos),

        filterTodos() {
            const searchTerm = this.searchTerm.toLowerCase();
            const filterValue = this.filterValue;

            this.todos.forEach(todo => {
                const title = todo.title.toLowerCase();
                const description = todo.description ? todo.description.toLowerCase() : '';
                const status = todo.completed ? 'completed' : 'pending';

                const matchesSearch = title.includes(searchTerm) || description.includes(searchTerm);
                const matchesFilter = filterValue === 'all' ||
                                    (filterValue === 'completed' && status === 'completed') ||
                                    (filterValue === 'pending' && status === 'pending');

                const element = document.querySelector(`[data-todo-id="${todo.id}"]`);
                if (element) {
                    element.style.display = (matchesSearch && matchesFilter) ? 'block' : 'none';
                }
            });
        },

        async toggleTodo(id) {
            try {
                const response = await fetch(`/todos/${id}/toggle`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    const todo = this.todos.find(t => t.id === id);
                    if (todo) {
                        todo.completed = data.completed;
                        // Update the UI
                        const element = document.querySelector(`[data-todo-id="${id}"]`);
                        if (element) {
                            const button = element.querySelector('button');
                            const title = element.querySelector('h3');
                            if (button && title) {
                                button.className = `inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors duration-200 ${data.completed ? 'bg-green-100 text-green-800 hover:bg-green-200 dark:bg-green-900 dark:text-green-200 dark:hover:bg-green-800' : 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200 dark:bg-yellow-900 dark:text-yellow-200 dark:hover:bg-yellow-800'}`;
                                button.textContent = data.completed ? 'Completed' : 'Pending';
                                title.className = `text-lg font-medium text-gray-900 dark:text-white mb-2 ${data.completed ? 'line-through text-gray-500 dark:text-gray-400' : ''}`;
                            }
                        }
                    }
                }
            } catch (error) {
                console.error('Error toggling todo:', error);
            }
        }
    }
}
</script>
@endsection