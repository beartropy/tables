<!-- Columns Button -->
<div x-data="{ isOpenColumnToggle: $wire.entangle('column_toggle_dd_status') }" class="relative " @keydown.esc.window="isOpenColumnToggle = false">
    <x-beartropy-ui::button 
        @click="isOpenColumnToggle = ! isOpenColumnToggle"
        outline
        color="{{ $theme }}"
    >
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-columns mr-2">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="9" y1="3" x2="9" y2="21"></line>
            <line x1="15" y1="3" x2="15" y2="21"></line>
        </svg>
        {{ucfirst(__('yat::yat.columns'))}}
        <div class="ml-2">
            <svg
                aria-hidden="true"
                fill="none"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                stroke-width="2"
                stroke="currentColor"
                class="w-4 h-4 transition-transform duration-300"
                :class="!isOpenColumnToggle ? 'rotate-180' : 'rotate-0'"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M19.5 15.75l-7.5-7.5-7.5 7.5"
                />
            </svg>
        </div>
    </x-beartropy-ui::button>
    <!-- Dropdown Menu -->
    <div x-cloak x-show="isOpenColumnToggle" x-transition @click.outside="isOpenColumnToggle = false" @keydown.down.prevent="$focus.wrap().next()" @keydown.up.prevent="$focus.wrap().previous()" class="shadow-xl min-w-52 z-30 absolute top-12 inline-block rounded-md whitespace-nowrap {{$yat_is_mobile ? 'left-1/2 transform -translate-x-1/2' : 'right-0'}}" role="menu">
        <ul class="rounded-md text-sm font-medium border {{ $themeConfig['dropdowns']['bg'] }} {{ $themeConfig['dropdowns']['border'] }} {{ $themeConfig['dropdowns']['text'] }}">
            @if(!$yat_is_mobile && $handle_state)
            <li class="w-full border-b rounded-t-lg rounded-md {{ $themeConfig['dropdowns']['border'] }}">
                <div class="p-3">
                    {{-- <button 
                        wire:click="saveTableState" 
                        type="button" 
                        class="w-full outline-none inline-flex justify-center items-center group hover:shadow-sm focus:ring-offset-background-white dark:focus:ring-offset-background-dark transition-all ease-in-out duration-200 focus:ring-2 disabled:opacity-80 disabled:cursor-not-allowed text-white bg-emerald-500 dark:bg-emerald-700 hover:text-white hover:bg-emerald-600 dark:hover:bg-emerald-600 focus:text-white focus:ring-offset-2 focus:bg-emerald-600 focus:ring-emerald-600 dark:focus:bg-emerald-600 dark:focus:ring-emerald-600 rounded-md gap-x-1 text-xs px-2.5 py-1.5"
                        wire:loading.attr="disabled"
                        wire:target="saveTableState"
                    >   
                        <span wire:loading.remove wire:target="saveTableState">{{ucfirst(__('yat::yat.save_column_election'))}}</span>
                        <span wire:loading wire:target="saveTableState" class="ml-2">{{ucfirst(__('yat::yat.saving'))}}...</span>
                        <!-- Spinner next to the text when loading -->
                        <span wire:loading wire:target="saveTableState" class="flex items-center justify-center">
                            <svg class="w-2 h-2 mr-2 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 100 8v4a8 8 0 01-8-8z"></path>
                            </svg>
                        </span>
                    </button> --}}
                    <x-beartropy-ui::button xs outline emerald class="w-full" label="{{ucfirst(__('yat::yat.save_column_election'))}}" wire:click="saveTableState"/>
                </div>
            </li>
            @endif
        @foreach ($columns as $key => $column)
            @if(!$column->hideFromSelector)
                <li class="w-full rounded-mc {{ $themeConfig['dropdowns']['hover_bg'] }}">
                    {{-- <div class="flex items-center ps-3">
                        <input id="{{ $column->key }}" type="checkbox" wire:model.live="columns.{{ $key }}.isVisible" class="cursor-pointer w-4 h-4 rounded focus:ring-2 {{ $themeConfig['inputs']['checkbox'] }}">
                        <label for="{{ $column->key }}" class="cursor-pointer pr-3  w-full py-2 ms-2 text-sm font-medium {{ $themeConfig['dropdowns']['text'] }}">{{ $column->label }}</label>
                    </div> --}}
                    <div class="flex items-center ps-3">
                        <x-beartropy-ui::checkbox 
                            wire:model.live="columns.{{ $key }}.isVisible" 
                            id="{{ $column->key }}" 
                            label="{{ $column->label }}"
                            class="w-full py-1.5"
                            color="{{ $theme }}"
                            sm
                        />
                    </div>
                </li>
            @endif
        @endforeach       
        </ul>                 
    </div>
</div>