<!-- Options Button -->
<div x-data="{ isOpenOptionsToggle: false}" class="relative" @keydown.esc.window="isOpenOptionsToggle = false">
    <!-- Toggle Button -->
    <!-- Toggle Button -->
    <button @click="isOpenOptionsToggle = ! isOpenOptionsToggle" class="w-full flex justify-between items-center gap-x-2 px-3 py-2 text-sm text-gray-900 shadow-sm transition-all duration-150 ease-in-out h-10 rounded-md ring-1 ring-inset ring-gray-300 dark:ring-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-400 dark:focus:ring-blue-400 bg-background-white dark:bg-background-dark dark:text-gray-400" type="button">
        {{ucfirst(__('yat::yat.options'))}}
        <div>
            <svg 
                aria-hidden="true" 
                fill="none" 
                xmlns="http://www.w3.org/2000/svg" 
                viewBox="0 0 24 24" 
                stroke-width="2" 
                stroke="currentColor" 
                class="w-4 h-4 transition-transform duration-300" 
                :class="!isOpenOptionsToggle ? 'rotate-180' : 'rotate-0'"
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
        x-show="isOpenOptionsToggle" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.outside="isOpenOptionsToggle = false" 
        @keydown.down.prevent="$focus.wrap().next()" 
        @keydown.up.prevent="$focus.wrap().previous()" 
        class="absolute z-50 top-11 min-w-56 whitespace-nowrap rounded-md shadow-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 {{$yat_is_mobile ? 'left-1/2 transform -translate-x-1/2' : 'right-0'}}" 
        role="menu"
    >
        <ul class="py-1 text-sm text-gray-700 dark:text-gray-200 divide-y divide-gray-100 dark:divide-gray-700/50">
        @foreach ($options as $function => $label)
            <li>
                <div class="flex items-center">
                    <button 
                        wire:click="{{$function}}" 
                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white transition-colors duration-150 flex items-center justify-between group" 
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50"
                    >
                        <span>{!! $label !!}</span>

                        <!-- Spinner next to the text when loading -->
                        <span wire:loading wire:target="{{$function}}" class="ml-2 flex items-center"> 
                            <svg class="w-4 h-4 text-emerald-500 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 100 8v4a8 8 0 01-8-8z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </li>
        @endforeach       
        </ul>                 
    </div>
</div>