<div class="relative w-full sm:w-64">
    <x-beartropy-ui::input
        wire:model.live.debounce.300ms="yat_global_search"
        placeholder="{{ucfirst(__('yat::yat.search'))}}"
        clearable
        class="w-full"
        color="{{ $inputThemeOverride ?? $theme }}"
        icon-start="magnifying-glass"
    />
</div>