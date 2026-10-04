<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ __('All Tasks (Admin)') }}</h2>
    </x-slot>

    <div class="py-12 max-w-4xl mx-auto sm:px-6 lg:px-8">

        @if (session('status'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
        @endif

        <div class="bg-white shadow sm:rounded-lg divide-y">
            @forelse ($tasks as $task)
                <div class="p-4 flex justify-between items-center">
                    <div>
                        <p class="font-medium {{ $task->status === 'completed' ? 'line-through text-gray-400' : '' }}">
                            {{ $task->title }}
                        </p>
                        <p class="text-sm text-gray-500">
                            {{ __('Owner:') }} {{ $task->user->name }} ·
                            {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.tasks.edit', $task) }}"
                            class="text-sm text-indigo-600">{{ __('Edit') }}</a>
                        <form method="POST" action="{{ route('admin.tasks.destroy', $task) }}"
                            onsubmit="return confirm('{{ __('Delete this task?') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-sm text-red-600">{{ __('Delete') }}</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="p-4 text-gray-500">{{ __('No tasks found.') }}</p>
            @endforelse
        </div>

        <div class="mt-4">{{ $tasks->links() }}</div>
    </div>
</x-app-layout>