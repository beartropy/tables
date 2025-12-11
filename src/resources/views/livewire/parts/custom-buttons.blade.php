@foreach ($yat_custom_buttons as $button)
    @php
        $button = (array) $button;
        $color = $button['color'] ?? null;
        if ($color === 'default') $color = null;
    @endphp

    <x-beartropy-ui::button
        class="whitespace-nowrap"
        :color="$color"
        @if(isset($button['action'])) wire:click="{{$button['action']}}" @endif
    >
        {!! $button['label'] !!}
    </x-beartropy-ui::button>
@endforeach