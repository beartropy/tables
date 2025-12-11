<!-- Flatpickr CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<!-- Flatpickr JS -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

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
                @if($filter->type === 'string')
                    <x-beartropy-ui::input
                        wire:model.live.debounce.500ms="filters.{{ $key }}.input"
                        label="{{ucfirst($filter->label)}}"
                        clearable
                        color="{{ $theme }}"
                        placeholder="{{ucfirst($filter->label)}}..."
                    />

                @elseif($filter->type === 'daterange')
                    <x-beartropy-ui::datetime 
                        wire:model.live="filters.{{ $key }}.input"
                        label="{{ $filter->label }}"
                        color="{{ $theme }}"
                        range
                    />

                @elseif($filter->type === 'select')
                    <x-beartropy-ui::select 
                        label="{{ $filter->label }}"
                        color="{{ $theme }}"
                        wire:model.live="filters.{{ $key }}.input"
                        :options="$filter->options"
                        placeholder="{{ucfirst(__('yat::yat.all'))}}"
                        clearable
                    />

                @elseif($filter->type === 'bool')
                    <div class="w-full h-full flex items-center mt-4">
                        <x-beartropy-ui::toggle
                            wire:model.live="filters.{{ $key }}.input"
                            label="{{ $filter->label }}"
                            color="{{ $theme }}"
                        />                
                    </div>
                    {{-- @php
                        $boolOptions = [
                            ['value' => 1, 'label' => ucfirst(__('yat::yat.yes'))],
                            ['value' => 0, 'label' => ucfirst(__('yat::yat.no'))]
                        ];
                    @endphp
                    <x-beartropy-ui::select 
                        label="{{ $filter->label }}"
                        color="{{ $theme }}"
                        wire:model.live="filters.{{ $key }}.input"
                        :options="$boolOptions"
                        placeholder="{{ucfirst(__('yat::yat.all'))}}"
                        option-label="label"
                        option-value="value"
                        :searchable="false"
                    /> --}}
                @endif
            </div>
        @endforeach
    </div>

    @php
        $hasActiveFilters = false;
        if(isset($filters)) { // ensure variable exists
             foreach($filters as $filter) {
                if(isset($filter->input) && $filter->input !== '' && $filter->input !== null) {
                    $hasActiveFilters = true;
                    break;
                }
            }
        }
    @endphp

    {{-- @if($hasActiveFilters)
        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-end">
            <x-beartropy-ui::button 
                wire:click="clearAllFilters"
                icon-start="trash"
                label="{{ucfirst(__('yat::yat.remove_filters'))}}"
                color="red"
                size="sm"
                outline
            />
        </div>
    @endif --}}
</div>