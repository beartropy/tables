<!-- Options Button -->
<div x-data="{ isOpenOptionsToggle: false}" class="relative" @keydown.esc.window="isOpenOptionsToggle = false">
    <!-- Toggle Button -->
    <x-beartropy-ui::button 
        variant="{{ $yat_button_variant }}"
        color="{{ $theme }}"
        @click="isOpenOptionsToggle = ! isOpenOptionsToggle"
        class="w-full"
    >
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-vertical mr-2">
            <circle cx="12" cy="12" r="1"></circle>
            <circle cx="12" cy="5" r="1"></circle>
            <circle cx="12" cy="19" r="1"></circle>
        </svg>
        {{ucfirst(__('yat::yat.options'))}}
        <div class="ml-2">
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
    </x-beartropy-ui::button>
    <!-- Dropdown Menu -->
    <!-- Dropdown Menu / Modal -->
    <div 
        x-cloak 
        x-show="isOpenOptionsToggle" 
        x-transition 
        @click.outside="isOpenOptionsToggle = false" 
        @keydown.down.prevent="$focus.wrap().next()" 
        @keydown.up.prevent="$focus.wrap().previous()" 
        class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900/50 backdrop-blur-[2px] p-4 md:absolute md:top-12 md:right-0 md:inset-auto md:bg-transparent md:block md:p-0 md:w-auto" 
        role="menu"
    >
        <!-- Backdrop click handler for mobile -->
        <div class="absolute inset-0 md:hidden" @click="isOpenOptionsToggle = false"></div>

        <ul class="relative w-[90%] max-w-md md:min-w-52 md:w-auto md:max-w-none max-h-[80vh] overflow-y-auto md:overflow-visible shadow-xl rounded-md text-base md:text-sm font-medium z-10 {{ $themeConfig['dropdowns']['bg'] }} {{ $themeConfig['dropdowns']['border'] }} {{ $themeConfig['dropdowns']['text'] }}">
            @if($yat_is_mobile)
                <li class="w-full border-b border-gray-200 dark:border-gray-600">
                    <div class="p-3">
                        {{ucfirst(__('yat::yat.options'))}}
                    </div>
                </li>
            @endif
            @foreach ($options as $function => $option)
                <li class="w-full border-b last:border-b-0 rounded-mc border-gray-300 dark:border-gray-600 {{ $themeConfig['dropdowns']['hover_bg'] }}">
                    <div class="flex items-center ps-3 pr-3">
                        <div 
                            wire:click="{{$function}}" 
                            class="cursor-pointer w-full pr-3 py-3 md:py-3 ms-2 text-base md:text-sm font-medium flex items-center {{ $themeConfig['dropdowns']['text'] }}" 
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50"
                        >
                            {{-- 1. ICONO CON CLASE TAILWIND --}}
                            @if($option['icon'])
                                {{-- 
                                    Aquí inyectamos la clase directamente. 
                                    Si es null, no pasa nada. 
                                    Ejemplo resultante: class="mr-2 text-base leading-none text-blue-600"
                                --}}
                                <span class="mr-2 -ml-2 text-lg md:text-base leading-none">
                                    {!! $option['icon'] !!}
                                </span>
                            @endif

                            {{-- 2. LABEL --}}
                            <span>{!! $option['label'] !!}</span>

                            {{-- 3. SPINNER --}}
                            <span wire:loading wire:target="{{$function}}" class="ml-2 flex items-center"> 
                                <svg class="w-4 h-4 animate-spin {{ $themeConfig['loading']['text'] }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 100 8v4a8 8 0 01-8-8z"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </li>
            @endforeach     
        </ul>                 
    </div>
</div>