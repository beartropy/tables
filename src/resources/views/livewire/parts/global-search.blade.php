<div class="relative w-full sm:w-64">
    <x-beartropy-ui::input 
        wire:model.live.debounce.300ms="yat_global_search" 
        placeholder="{{ucfirst(__('yat::yat.search'))}}" 
        clearable 
        class="w-full"
        color="{{ $inputThemeOverride ?? $theme }}"
    >
        <x-slot:start>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="gray" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
        </x-slot:start>
    </x-beartropy-ui::input>
</div>