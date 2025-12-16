<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
    @foreach ($rows as $row)
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 space-y-3">
             @php
                $titleColumn = collect($columns)->first(function($col) {
                    return property_exists($col, 'cardTitle') && $col->cardTitle;
                });
            @endphp

            <div class="flex justify-between items-start border-b pb-2 mb-2 dark:border-gray-700">
                <div 
                    class="font-bold text-lg break-words text-gray-900 dark:text-gray-100 flex-1 pr-2 cursor-pointer flex items-center gap-2 group"
                    wire:click="openMobileCardDetails('{{ $row[$column_id] }}')"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                      <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>

                    @if($titleColumn)
                        @if(property_exists($titleColumn, 'hasView') && $titleColumn->hasView)
                            @include($titleColumn->view)
                        @elseif(property_exists($titleColumn, 'isHtml') && $titleColumn->isHtml)
                            {!! $row[$titleColumn->key] ?? '' !!}
                        @else
                            {{ $row[$titleColumn->key] ?? '' }}
                        @endif
                    @endif
                </div>
                 @if($has_bulk)
                     <div class="flex-shrink-0 flex flex-col items-center">
                        <x-beartropy-ui::checkbox sm
                            value="{{ $row[$column_id] }}"
                            id="mobile_{{ $row[$column_id] }}"
                            wire:model.live="yat_selected_checkbox"
                            color="{{ $bulkThemeOverride ?? $theme }}"
                        />
                     </div>
                @endif
            </div>

            <div class="space-y-3">
                @foreach ($columns as $column)
                    @if ((!$titleColumn || $column->key !== $titleColumn->key) && property_exists($column, 'showOnCard') && $column->showOnCard)
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
                                        <a href="{{$link_data[0]}}" class="{{$column->tag_classes ?? 'cursor-pointer'}}" target="{{ $column->target ?? '' }}">{!! $link_data[1] !!}</a>
                                    @else
                                        {{ $row[$column->key] ?? '' }}
                                    @endif
                            </span>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endforeach
</div>
