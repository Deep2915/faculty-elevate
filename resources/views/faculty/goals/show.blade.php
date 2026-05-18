<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">Goal Detail</h2></x-slot>
    <div class="p-6">
        <div class="font-semibold">{{ $goal->title }}</div>
        <p>{{ $goal->description }}</p>
    </div>
</x-app-layout>
