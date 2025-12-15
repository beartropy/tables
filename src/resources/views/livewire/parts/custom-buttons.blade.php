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
        variant="{{ $yat_button_variant }}"
        class="w-full"
    >
        {!! $button['label'] !!}
    </x-beartropy-ui::button>
@endforeach