<x-filament-panels::page>
    {{ $this->table }}

    <div class="mt-4 flex justify-end">
        <x-filament::button wire:click="save" color="primary">
            Save Attendance
        </x-filament::button>
    </div>
</x-filament-panels::page>
