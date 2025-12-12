<!-- Columns Button -->
<div x-data="{ isOpenColumnToggle: $wire.entangle('column_toggle_dd_status') }" class="relative " @keydown.esc.window="isOpenColumnToggle = false">
    <x-beartropy-ui::button 
        @click="isOpenColumnToggle = ! isOpenColumnToggle"
        outline
        color="{{ $theme }}"
        class="w-full"
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
    <!-- Dropdown Menu / Modal -->
    <div 
        x-cloak 
        x-show="isOpenColumnToggle" 
        x-transition 
        @keydown.down.prevent="$focus.wrap().next()" 
        @keydown.up.prevent="$focus.wrap().previous()"
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-[2px] p-4 md:absolute md:top-12 md:right-0 md:inset-auto md:bg-transparent md:block md:p-0 md:w-auto"
        role="menu"
    >
        <!-- Backdrop click handler for mobile (clicks on the background close the modal) -->
        <div class="absolute inset-0 md:hidden" @click="isOpenColumnToggle = false"></div>
        
        <!-- Content -->
        <ul 
            @click.outside="isOpenColumnToggle = false"
            class="relative w-[90%] max-w-md md:min-w-52 md:w-auto md:max-w-none max-h-[80vh] overflow-y-auto md:overflow-visible shadow-xl rounded-md text-base md:text-sm font-medium z-10 {{ $themeConfig['dropdowns']['bg'] }} {{ $themeConfig['dropdowns']['border'] }} {{ $themeConfig['dropdowns']['text'] }}"
        >
            @if(!$yat_is_mobile && $handle_state)
                <li class="w-full border-b rounded-t-lg rounded-md {{ $themeConfig['dropdowns']['border'] }}">
                    <div class="p-3">
                        <x-beartropy-ui::button xs outline emerald class="w-full" label="{{ucfirst(__('yat::yat.save_column_election'))}}" wire:click="saveTableState"/>
                    </div>
                </li>
            @endif
            @if($yat_is_mobile)
                <li class="w-full border-b border-gray-200 dark:border-gray-600">
                    <div class="p-3">
                        {{ucfirst(__('yat::yat.columns'))}}
                    </div>
                </li>
            @endif
            @foreach ($columns as $key => $column)
                @if(!$column->hideFromSelector)
                    <li class="w-full rounded-mc {{ $themeConfig['dropdowns']['hover_bg'] }}">
                        <div class="flex items-center ps-3">
                            <x-beartropy-ui::checkbox 
                                wire:model.live="columns.{{ $key }}.isVisible" 
                                id="{{ $column->key }}" 
                                label="{{ $column->label }}"
                                class="w-full py-3 md:py-1.5"
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