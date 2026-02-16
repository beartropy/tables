@php
    $hasDaterangeFilter = collect($filters)->contains(fn($filter) => ($filter['type'] ?? null) === 'daterange');
@endphp

@if ($hasDaterangeFilter)
    @once
        <!-- Flatpickr CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

        <!-- Flatpickr JS -->
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @endonce
@endif

<div
    x-show="showFilters"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 -translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 -translate-y-2"
    class="mb-4"
    style="display: none;"
    x-collapse
>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($filters as $key => $filter)
            <div class="flex flex-col space-y-1">
                @if($filter['type'] === 'string')
                    <x-beartropy-ui::input
                        wire:model.live.debounce.500ms="filters.{{ $key }}.input"
                        label="{{ucfirst($filter['label'])}}"
                        clearable
                        color="{{ $inputThemeOverride ?? $theme }}"
                        placeholder="{{ucfirst($filter['label'])}}..."
                    />

                @elseif($filter['type'] === 'daterange')
                    <x-beartropy-ui::datetime
                        wire:model.live="filters.{{ $key }}.input"
                        label="{{ $filter['label'] }}"
                        color="{{ $inputThemeOverride ?? $theme }}"
                        range
                    />

                @elseif($filter['type'] === 'select')
                    <x-beartropy-ui::select
                        label="{{ $filter['label'] }}"
                        color="{{ $inputThemeOverride ?? $theme }}"
                        wire:model.live="filters.{{ $key }}.input"
                        :options="$filter['options']"
                        option-value="value"
                        option-label="label"
                        placeholder="{{ucfirst(__('yat::yat.all'))}}"
                        clearable
                    />

                @elseif($filter['type'] === 'bool')
                    @php
                        $boolOptions = [
                            ['value' => "all", 'label' => ucfirst(__('yat::yat.all'))],
                            ['value' => "true", 'label' => ucfirst(__('yat::yat.yes'))],
                            ['value' => "false", 'label' => ucfirst(__('yat::yat.no'))]
                        ];
                    @endphp
                    <x-beartropy-ui::select
                        label="{{ $filter['label'] }}"
                        color="{{ $inputThemeOverride ?? $theme }}"
                        wire:model.live="filters.{{ $key }}.input"
                        :options="$boolOptions"
                        placeholder="{{ucfirst(__('yat::yat.all'))}}"
                        option-label="label"
                        option-value="value"
                        :searchable="false"
                        :clearable="false"
                    />
                @endif
            </div>
        @endforeach
    </div>
</div>
