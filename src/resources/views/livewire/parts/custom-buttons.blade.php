@foreach ($yat_custom_buttons as $button)
    @php
        $color = $button['color'] ?? 'default'; // Default color if none is set

        $colorClasses = [
            'red' => 'bg-red-300 text-red-700 hover:bg-red-400 hover:text-red-900 focus:bg-red-400 focus:text-red-900 focus:ring-red-400 dark:text-red-900',
            'green' => 'bg-green-300 text-green-700 hover:bg-green-400 hover:text-green-900 focus:bg-green-400 focus:text-green-900 focus:ring-green-400 dark:text-green-900',
            'yellow' => 'bg-yellow-300 text-yellow-800 hover:bg-yellow-400 hover:text-yellow-900 focus:bg-yellow-400 focus:text-yellow-900 focus:ring-yellow-400 dark:text-yellow-900',
            'blue' => 'bg-blue-300 text-blue-700 hover:bg-blue-400 hover:text-blue-900 focus:bg-blue-400 focus:text-blue-900 focus:ring-blue-400 dark:text-blue-900',
            'purple' => 'bg-purple-300 text-purple-700 hover:bg-purple-400 hover:text-purple-900 focus:bg-purple-400 focus:text-purple-900 focus:ring-purple-400 dark:text-purple-900',
            'sky' => 'bg-sky-300 text-sky-700 hover:bg-sky-400 hover:text-sky-900 focus:bg-sky-400 focus:text-sky-900 focus:ring-sky-400 dark:text-sky-900',
            'default' => 'bg-opacity-60 dark:bg-opacity-30 text-gray-600 bg-gray-300 dark:bg-gray-600 dark:text-gray-400 hover:bg-opacity-60 dark:hover:bg-opacity-30 hover:text-gray-800 hover:bg-gray-400 dark:hover:text-gray-400 dark:hover:bg-gray-500 focus:bg-opacity-60 dark:focus:bg-opacity-30 focus:ring-offset-2 focus:text-gray-800 focus:bg-gray-400 focus:ring-gray-400 dark:focus:text-gray-400 dark:focus:bg-gray-500 dark:focus:ring-gray-700',
        ];

        $colorClass = $colorClasses[$color] ?? $colorClasses['default'];
    @endphp

    <button @isset($button['action']) wire:click="{{$button['action']}}" @endisset class="whitespace-nowrap outline-none inline-flex justify-center items-center group hover:shadow-sm focus:ring-offset-background-white dark:focus:ring-offset-background-dark transition-all ease-in-out duration-200 focus:ring-2 disabled:opacity-80 disabled:cursor-not-allowed {{ $colorClass }} rounded-md gap-x-2 text-sm px-4 py-2" type="button">
        @if(isset($button['action']))
            <span wire:target="{{$button['action']}}" wire:loading.remove>{!! $button['label'] !!}</span>
            <span wire:target="{{$button['action']}}" wire:loading>
                <svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><style>.spinner_GuJz{transform-origin:center;animation:spinner_STY6 1.5s linear infinite}@keyframes spinner_STY6{100%{transform:rotate(360deg)}}</style><g class="spinner_GuJz"><circle cx="3" cy="12" r="2"/><circle cx="21" cy="12" r="2"/><circle cx="12" cy="21" r="2"/><circle cx="12" cy="3" r="2"/><circle cx="5.64" cy="5.64" r="2"/><circle cx="18.36" cy="18.36" r="2"/><circle cx="5.64" cy="18.36" r="2"/><circle cx="18.36" cy="5.64" r="2"/></g></svg>             
            </span>
        @else
            {!! $button['label'] !!}
        @endif
    </button>
@endforeach