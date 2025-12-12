<div class="p-4 grid grid-cols-1 gap-4">
    @foreach($columns as $column)
        <div class="flex flex-col">
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">{{ $column->label }}</span>
            <div class="mt-1">
                @if(property_exists($column, 'hasView') && $column->hasView)
                    @include($column->view, ['row' => $row])
                @elseif(property_exists($column, 'isToggle') && $column->isToggle)
                    @if(!isset($row[$column->key."_hidden"]) || !$row[$column->key."_hidden"])
                        <x-beartropy-ui::toggle 
                            :disabled="isset($row[$column->key.'_disabled']) && $row[$column->key.'_disabled']"
                            :checked="$row[$column->key]"
                            wire:change="toggleBoolean('{{$row[$row_id_name]}}', '{{$column->key}}')"
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
            </div>
        </div>
    @endforeach
</div>
