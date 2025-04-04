<x-filament-widgets::widget>
    <x-filament::section>
        @if ($image)
            <div class="p-4 bg-white shadow-md rounded-lg">
                <img src="{{ $image }}" alt="Post Image" class="w-full h-auto rounded-md">
            </div>
        @endif

    </x-filament::section>
</x-filament-widgets::widget>
