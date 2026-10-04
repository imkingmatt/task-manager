{{-- create.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ __('New Task') }}</h2>
    </x-slot>
    <div class="py-12 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-6 shadow sm:rounded-lg">
            <form method="POST" action="{{ route('tasks.store') }}">
                @include('tasks._form')
            </form>
        </div>
    </div>
</x-app-layout>