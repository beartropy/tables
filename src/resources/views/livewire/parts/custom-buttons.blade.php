@foreach ($yat_custom_buttons as $button)
    @php
        $button = (array) $button;
        $color = $button['color'] ?? null;
        if ($color === 'default') $color = null;
    @endphp

    <x-beartropy-ui::button
        class="whitespace-nowrap"
        :color="$color"
        :wire:click="$button['action'] ?? null"
        outline
    >
        {!! $button['label'] !!}
    </x-beartropy-ui::button>
@endforeach