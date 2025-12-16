@if($mobileDetailsModalOpen)
    <x-beartropy-ui::modal wire:model="mobileDetailsModalOpen" styled>
        <x-slot name="title">
            {{__('yat::yat.details')}}
        </x-slot>

        <div class="space-y-4">
             @php
                 // Re-use logic to render fields, similar to mobile-cards body but for ALL visible columns
                 $row = $mobileDetailsRow;
             @endphp
             @foreach ($columns as $column)
                @if (!$column->isHidden && $column->isVisible)
                    @php
                        // Check if we should render this column
                        $shouldRender = true;
                    @endphp
                    @if($shouldRender)
                        <div class="flex flex-col gap-1">
                            <span class="font-medium text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ $column->label }}</span>
                            <span class="text-sm text-gray-900 dark:text-gray-100 break-words">
                                    @if(property_exists($column, 'hasView') && $column->hasView)
                                        @include($column->view)
                                    @elseif(property_exists($column, 'isToggle') && $column->isToggle)
                                        @if(!isset($row[$column->key."_hidden"]) || !$row[$column->key."_hidden"])
                                            <x-beartropy-ui::toggle 
                                                :disabled="isset($row[$column->key.'_disabled']) && $row[$column->key.'_disabled']"
                                                :checked="$row[$column->key]"
                                                wire:change="toggleBoolean('{{$row[$column_id]}}', '{{$column->key}}')"
                                            />
                                        @endif
                                    @elseif(property_exists($column, 'isBool') && $column->isBool)
                                        @if($row[$column->key] === true || strtolower($row[$column->key]) == "true" || strtolower($row[$column->key]) == "1" || strtolower($row[$column->key]) === 1)
                                            {!! $column->true_icon !!}
                                        @else
                                            {!! $column->false_icon !!}
                                        @endif
                                    @elseif(property_exists($column, 'isHtml') && $column->isHtml)
                                        {!! $row[$column->key] ?? '' !!}
                                    @elseif(property_exists($column, 'isLink') && $column->isLink)
                                        @php
                                            $link_data = json_decode($row[$column->key],true);
                                        @endphp
                                        @if(is_array($link_data) && count($link_data) >= 2)
                                            <a href="{{$link_data[0]}}" class="{{$column->tag_classes ?? 'cursor-pointer'}}" target="{{ $column->target ?? '' }}">{!! $link_data[1] !!}</a>
                                        @else
                                            {{ $row[$column->key] ?? '' }}
                                        @endif
                                    @else
                                        {{ $row[$column->key] ?? '' }}
                                    @endif
                            </span>
                        </div>
                    @endif
                @endif
            @endforeach
        </div>

        <x-slot:footer>
            <div class="flex gap-2">
                @foreach($yat_card_modal_buttons as $button)
                    @php
                        $button = (array) $button;
                        $color = $button['color'] ?? null;
                        if ($color === 'default') $color = null;
                    @endphp
                    <x-beartropy-ui::button
                        class="whitespace-nowrap"
                        :color="$color"
                        wire:click="{{$button['action']}}({{ json_encode($row) }})"
                        variant="{{ $yat_button_variant }}"
                        class="w-full sm:w-auto"
                    >
                        {!! $button['label'] !!}
                    </x-beartropy-ui::button>
                @endforeach
                <x-beartropy-ui::button 
                    wire:click="closeMobileCardDetails" 
                    variant="glass"
                    color="{{ $theme }}"
                >
                    {{__('yat::yat.close')}}
                </x-beartropy-ui::button>
            </div>
        </x-slot:footer>
    </x-beartropy-ui::modal>
@endif
