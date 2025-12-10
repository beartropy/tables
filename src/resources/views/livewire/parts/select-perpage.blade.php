<div x-data="{ 
    open: false, 
    selected: @entangle('perPageDisplay') 
}" 
class="relative min-w-[5rem]"
@click.outside="open = false" @keydown.esc.window="open = false">

    <!-- Trigger Button -->
    <button 
        @click="open = !open" 
        type="button"
        class="w-full flex justify-between items-center gap-x-2 px-3 py-2 text-sm text-gray-900 shadow-sm transition-all duration-150 ease-in-out h-10 rounded-md ring-1 ring-inset ring-gray-300 dark:ring-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-400 dark:focus:ring-gray-400 bg-gray-100 dark:bg-gray-800 dark:text-gray-400"
    >
        <span x-text="selected"></span>
        <svg 
            class="w-4 h-4 text-gray-500 dark:text-gray-400 transition-transform duration-200"
            :class="open ? 'rotate-180' : ''"
            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </button>

    <!-- Dropdown Menu -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-50 mt-1 whitespace-nowrap min-w-[5rem] rounded-md shadow-lg max-h-60 overflow-y-auto bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600"
        style="display: none;"
    >
        <ul class="text-sm text-gray-700 dark:text-white">
            @foreach ($perPageOptions as $option)
                <li>
                    <button 
                        type="button"
                        @click="$wire.set('perPageDisplay', '{{ $option }}'); selected = '{{ $option }}'; open = false"
                        class="w-full px-4 py-2 text-left hover:bg-gray-100 dark:hover:bg-gray-700 dark:hover:text-white"
                        :class="selected == '{{ $option }}' ? 'bg-gray-100 dark:bg-gray-700 font-bold' : ''"
                    >
                        {{ $option }}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
</div>