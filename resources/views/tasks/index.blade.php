<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ __('My Tasks') }}</h2>
    </x-slot>

    <div class="py-12 max-w-4xl mx-auto sm:px-6 lg:px-8">

        @if (session('status'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
        @endif

        <div class="mb-4 flex justify-between items-center">
            <a href="{{ route('tasks.create') }}"
                class="px-4 py-2 bg-gray-800 text-white rounded">{{ __('+ New Task') }}</a>
        </div>

        <div class="bg-white shadow sm:rounded-lg divide-y">
            @forelse ($tasks as $task)
                <div class="p-4 flex justify-between items-center">
                    <div>
                        <p class="font-medium {{ $task->status === 'completed' ? 'line-through text-gray-400' : '' }}">
                            {{ $task->title }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            @if($task->due_date) · Due {{ $task->due_date->format('M j, Y') }} @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        @can('update', $task)
                            <a href="{{ route('tasks.edit', $task) }}" class="text-sm text-indigo-600">{{ __('Edit') }}</a>
                        @endcan
                        @can('delete', $task)
                            <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                                onsubmit="return confirm('{{ __('Delete this task?') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-sm text-red-600">{{ __('Delete') }}</button>
                            </form>
                        @endcan
                    </div>
                </div>
            @empty
                <p class="p-4 text-gray-500">{{ __('No tasks yet.') }}</p>
            @endforelse
        </div>

        <div class="mt-4">{{ $tasks->links() }}</div>
    </div>
</x-app-layout>