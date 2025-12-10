<!-- Columns Button -->
<div x-data="{ isOpenColumnToggle: $wire.entangle('column_toggle_dd_status') }" class="relative " @keydown.esc.window="isOpenColumnToggle = false">
    <!-- Toggle Button -->
    <!-- Toggle Button -->
    <button @click="isOpenColumnToggle = ! isOpenColumnToggle" class="w-full flex justify-between items-center gap-x-2 px-3 py-2 text-sm text-gray-900 shadow-sm transition-all duration-150 ease-in-out h-10 rounded-md ring-1 ring-inset ring-gray-300 dark:ring-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-400 bg-gray-100 dark:bg-gray-800 dark:text-gray-400" type="button">
        {{ucfirst(__('yat::yat.columns'))}}
        <div>
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
    </button>
    <!-- Dropdown Menu -->
    <div 
        x-cloak 
        x-show="isOpenColumnToggle" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.outside="isOpenColumnToggle = false" 
        @keydown.down.prevent="$focus.wrap().next()" 
        @keydown.up.prevent="$focus.wrap().previous()" 
        class="absolute z-50 top-11 min-w-56 whitespace-nowrap rounded-md shadow-lg overflow-hidden bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 {{$yat_is_mobile ? 'left-1/2 transform -translate-x-1/2' : 'right-0'}}" 
        role="menu"
    >
        <ul class="text-sm text-gray-700 dark:text-gray-200 divide-y divide-gray-100 dark:divide-gray-700/50">
            @if(!$yat_is_mobile && $handle_state)
            <li class="w-full">
                <div class="px-4 py-2.5">
                    <button 
                        wire:click="saveTableState" 
                        type="button" 
                        class="w-full outline-none inline-flex justify-center items-center group hover:shadow-md transition-all ease-in-out duration-200 focus:ring-2 disabled:opacity-80 disabled:cursor-not-allowed text-white bg-emerald-500 dark:bg-emerald-600 hover:bg-emerald-600 dark:hover:bg-emerald-500 focus:ring-offset-2 focus:ring-emerald-500 dark:focus:ring-emerald-500 rounded-md gap-x-1 text-xs px-3 py-2 font-medium"
                        wire:loading.attr="disabled"
                        wire:target="saveTableState"
                    >   
                        <span wire:loading.remove wire:target="saveTableState">{{ucfirst(__('yat::yat.save_column_election'))}}</span>
                        <span wire:loading wire:target="saveTableState" class="ml-2">{{ucfirst(__('yat::yat.saving'))}}...</span>
                        <!-- Spinner next to the text when loading -->
                        <span wire:loading wire:target="saveTableState" class="flex items-center justify-center">
                            <svg class="w-3 h-3 ml-1 text-white animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 100 8v4a8 8 0 01-8-8z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </li>
            @endif
        @foreach ($columns as $key => $column)
            @if(!$column->hideFromSelector)
                <li>
                    <label for="{{ $column->key }}" class="flex items-center justify-between w-full px-4 py-3 cursor-pointer group hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-150">
                        <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-white transition-colors duration-150">{{ $column->label }}</span>
                        
                        <div class="relative inline-flex items-center cursor-pointer">
                            <input 
                                id="{{ $column->key }}" 
                                type="checkbox" 
                                wire:model.live="columns.{{ $key }}.isVisible" 
                                class="sr-only peer"
                            >
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-500 dark:peer-focus:ring-emerald-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-emerald-500"></div>
                        </div>
                    </label>
                </li>
            @endif
        @endforeach       
        </ul>                 
    </div>
</div>