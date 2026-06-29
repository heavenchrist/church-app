<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        
        <div class="mt-4 flex justify-end">
            <button type="submit" class="fi-btn fi-btn-primary">
                Save Settings
            </button>
        </div>
    </form>
</x-filament-panels::page>
