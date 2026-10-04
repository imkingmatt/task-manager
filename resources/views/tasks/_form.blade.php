@csrf
@if(isset($task)) @method('PUT') @endif

<div class="mb-4">
    <x-input-label for="title" :value="__('Title')" />
    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full"
        value="{{ old('title', $task->title ?? '') }}" required autofocus />
    <x-input-error :messages="$errors->get('title')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="description" :value="__('Description')" />
    <textarea id="description" name="description" rows="3"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $task->description ?? '') }}</textarea>
    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="due_date" :value="__('Due Date')" />
    <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full"
        value="{{ old('due_date', isset($task) ? $task->due_date?->format('Y-m-d') : '') }}" />
    <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
</div>

<div class="mb-4">
    <x-input-label for="status" :value="__('Status')" />
    <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
        @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed'] as $value => $label)
            <option value="{{ $value }}" @selected(old('status', $task->status ?? 'pending') === $value)>
                {{ $label }}
            </option>
        @endforeach
    </select>
    <x-input-error :messages="$errors->get('status')" class="mt-2" />
</div>

<div class="flex items-center gap-4">
    <x-primary-button>{{ isset($task) ? __('Update Task') : __('Create Task') }}</x-primary-button>
    <a href="{{ route('tasks.index') }}" class="text-sm text-gray-600 underline">{{ __('Cancel') }}</a>
</div>