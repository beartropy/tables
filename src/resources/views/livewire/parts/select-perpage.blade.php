<!-- Select Per Page Button -->
<div x-data="{ isSelectPerPageOpen: false }" class="relative min-w-18" @keydown.esc.window="isSelectPerPageOpen = false">
    <x-beartropy-ui::button 
        @click="isSelectPerPageOpen = ! isSelectPerPageOpen"
        outline
        color="{{ $theme }}"
        class="w-full"
    >
        <div class="flex justify-between items-center w-full">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list mr-2">
                <line x1="8" y1="6" x2="21" y2="6"></line>
                <line x1="8" y1="12" x2="21" y2="12"></line>
                <line x1="8" y1="18" x2="21" y2="18"></line>
                <line x1="3" y1="6" x2="3.01" y2="6"></line>
                <line x1="3" y1="12" x2="3.01" y2="12"></line>
            <line x1="3" y1="18" x2="3.01" y2="18"></line>
        </svg>
            {{$perPageDisplay}}
            <div class="ml-2">
                <svg
                    aria-hidden="true"
                    fill="none"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    stroke-width="2"
                    stroke="currentColor"
                    class="w-4 h-4 transition-transform duration-300"
                    :class="!isSelectPerPageOpen ? 'rotate-180' : 'rotate-0'"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M19.5 15.75l-7.5-7.5-7.5 7.5"
                    />
                </svg>
            </div>
        </div>
    </x-beartropy-ui::button>
    <!-- Dropdown Menu -->
    <div x-cloak x-show="isSelectPerPageOpen" x-transition @click.outside="isSelectPerPageOpen = false" @keydown.down.prevent="$focus.wrap().next()" @keydown.up.prevent="$focus.wrap().previous()" class="shadow-xl z-30 absolute top-12 w-full rounded-md whitespace-nowrap {{$yat_is_mobile ? 'left-1/2 transform -translate-x-1/2' : 'right-0'}}" role="menu">
        <ul class="rounded-md text-sm font-medium border {{ $themeConfig['dropdowns']['bg'] }} {{ $themeConfig['dropdowns']['border'] }} {{ $themeConfig['dropdowns']['text'] }}">
        @foreach ($perPageOptions as $option)
            <li class="w-full border-b last:border-b-0 rounded-mc {{ $themeConfig['dropdowns']['border'] }} {{ $themeConfig['dropdowns']['hover_bg'] }}">
                <div class="flex items-center ps-3 pr-3">
                    <div 
                        @click="$wire.set('perPageDisplay', '{{ $option }}'); isSelectPerPageOpen = false;" 
                        class="{{ $option == $perPageDisplay ? 'font-extrabold' : 'font-medium' }} px-2 py-2 w-full text-sm items-center text-center {{ $themeConfig['dropdowns']['text'] }}" 
                    >
                        {!! $option !!}
                    </div>
                </div>
            </li>
        @endforeach       
        </ul>                 
    </div>
</div>