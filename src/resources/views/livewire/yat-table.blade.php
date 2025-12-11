<section>
    <div class="{{$main_wrapper_classes}}" x-data="{showFilters: false}">
        @if($customHeader) {!! $customHeader !!} @endif
        @if($title) <div class="{{ $titleClasses ?? 'text-3xl font-thin mb-4 ' . $themeConfig['general']['title'] }}">{{$title}}</div> @endif

        @if(!$yat_is_mobile)
            <div class="flex flex-col sm:flex-row sm:justify-between items-center mb-4 space-y-2 sm:space-y-0">
                <!-- Search Input && Filters -->
                <div class="flex w-full space-x-2">
                    @includeWhen($yat_most_left_view, $yat_most_left_view)
                    @if($has_filters)
                        <x-beartropy-ui::button 
                            @click="showFilters = ! showFilters" 
                            outline
                            color="{{ $theme }}"
                        >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-filter mr-2">
                            <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                        </svg>
                        {{ucfirst(__('yat::yat.filters'))}}
                        <div class="ml-2">
                            <svg
                                aria-hidden="true"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                                stroke="currentColor"
                                class="w-4 h-4 transition-transform duration-300"
                                :class="!showFilters ? 'rotate-180' : 'rotate-0'"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M19.5 15.75l-7.5-7.5-7.5 7.5"
                                />
                            </svg>
                        </div>
                    </x-beartropy-ui::button>
                    @endif
                    @include('yat::livewire.parts.global-search')
                    @includeWhen($yat_less_left_view, $yat_less_left_view)
                </div>

                <div class="flex items-center space-x-2">
                    @includeWhen($yat_less_right_view, $yat_less_right_view)
                    @includeWhen($yat_custom_buttons, 'yat::livewire.parts.custom-buttons')
                    @includeWhen($options, 'yat::livewire.parts.options')
                    @includeWhen($show_column_toggle, 'yat::livewire.parts.column-toggle')
                    @includeWhen($with_pagination, 'yat::livewire.parts.select-perpage')
                    @includeWhen($yat_most_right_view, $yat_most_right_view)
                </div>

            </div>
        @else
            <div class="flex flex-col space-y-2 mb-2">
                @includeWhen($yat_most_left_view, $yat_most_left_view)
                @includeWhen($yat_less_left_view, $yat_less_left_view)
                @includeWhen($options, 'yat::livewire.parts.options')
                @includeWhen($show_column_toggle, 'yat::livewire.parts.column-toggle')
                @includeWhen($with_pagination, 'yat::livewire.parts.select-perpage')
                @includeWhen($yat_less_right_view, $yat_less_right_view)
                @includeWhen($yat_most_right_view, $yat_most_right_view)
                @includeWhen($yat_custom_buttons, 'yat::livewire.parts.custom-buttons')
                @include('yat::livewire.parts.global-search')
            </div>
        @endif

        <!-- Filters -->
        @includeWhen($has_filters, 'yat::livewire.parts.filters')

        <!-- Data Table -->
        <div class="{{ $override_table_classes ? $table_classes : $table_classes. 'w-full overflow-x-auto rounded-lg'}}" >
            <table class="min-w-full border-collapse border {{ $themeConfig['table']['wrapper'] }}">
                <thead class="min-w-full {{ $themeConfig['table']['thead_bg'] }} {{ $sticky_header ? 'sticky -top-[0.125rem]' : '' }}">
                    <tr class="md:border-none uppercase text-sm leading-normal {{ $themeConfig['table']['tr_thead'] }}">
                        @if($has_counter)
                                <td class="pl-2">#</td>
                            @endif
                        @if ($has_bulk)
                            <th class="text-left px-5">
                                <x-beartropy-ui::checkbox wire:model.live="selectAll" color="{{ $theme }}" />
                            </th>
                        @endif
                        @foreach ($columns as $column)
                            @if (!$column->isHidden && $column->isVisible)
                                <th wire:click="sortBy('{{ $column->key }}')" class="px-5 py-3 cursor-pointer text-xs font-medium whitespace-nowrap uppercase tracking-wider {{ $themeConfig['table']['th'] }} {{$column->th_classes}}" >
                                    <div class="{{ ((property_exists($column, 'isBool') && $column->isBool) || (property_exists($column, 'isToggle') && $column->isToggle)) ? 'text-center' : 'text-left' }} {{ (property_exists($column, 'th_wrapper_classes')) ? $column->th_wrapper_classes : '' }}">
                                        <span class="">{{ $column->label }}</span>
                                        <span class="text-xs">
                                            @if ($sortColumn === $column->key)
                                                @if ($sortDirection === 'asc')
                                                    &#8593;
                                                @else
                                                    &#8595;
                                                @endif
                                            @endif
                                        </span>
                                    </div>
                                </th>
                            @endif
                        @endforeach
                    </tr>
                </thead>

                <tbody class="min-w-full">
                    @if($selectAll && $all_data_count != count($rows))
                    <tr >
                        <td colspan="{{ $cols = ($has_bulk) ? count($columns) + 1 : count($columns) }}">
                            <div class="px-5 py-3 whitespace-nowrap text-pretty text-base font-normal {{ $themeConfig['table']['empty_text'] }}">
                                @if($filtered_data_count && $filtered_data_count != $all_data_count)
                                    Se seleccionaron {{ count($yat_selected_checkbox) }} de {{ $filtered_data_count }} registros. Haga <span class="cursor-pointer font-bold underline link" wire:click="select_all_data(true)">click aquí</span> para seleccionar todos los registros.<br> Existen filtros aplicados <span class="cursor-pointer font-bold underline link" wire:click="clearAllFilters(true)">{{__('yat::yat.remove_all_filters')}}</span>
                                @else
                                    @if($pageSelected)
                                        Se seleccionaron {{ count($yat_selected_checkbox) }} de {{ $all_data_count }} registros (página actual). Haga <span class="cursor-pointer font-bold underline link" wire:click="select_all_data(true)">click aquí</span> para seleccionar todos los registros.
                                    @else
                                        Se seleccionaron todos los registros. Haga <span class="cursor-pointer font-bold underline link" wire:click="selectCurrentPage(true)">click aquí</span> para seleccionar la página actual.
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endif
                    @if($loading_table_spinner)
                    <tr
                        class="hidden d-none bg-red"
                        @if($loading_table_spinner)
                            wire:loading.long.class.remove="hidden d-none"
                            wire:target="{{$trigger_spinner}}"
                        @endif

                    >
                        <td colspan="{{ $cols = ($has_bulk) ? count($columns) + 1 : count($columns) }}">
                            @includeUnless($loading_table_spinner_custom_view, 'yat::livewire.parts.loading-table')
                            @includeWhen($loading_table_spinner_custom_view, $loading_table_spinner_custom_view)
                        </td>
                    </tr>
                    @endif
                    @forelse ($rows as $key => $row)
                        <tr
                            class="md:border-none transition-colors even:bg-white {{ $themeConfig['table']['tr_body_hover'] }} {{ $themeConfig['table']['border_b'] }} {{ $themeConfig['table']['tr_body_odd'] }} {{ $themeConfig['table']['tr_body_even'] }}"
                            @if($loading_table_spinner)
                                wire:loading.long.class.add="hidden d-none"
                                wire:target="{{$trigger_spinner}}"
                            @endif
                        >
                            @if($has_counter)
                                <td class="pl-2 text-sm font-extralight {{ $themeConfig['table']['td_text'] }}">{{ $loop->iteration }}</td>
                            @endif
                            @if ($has_bulk)
                                <td class="px-5">
                                    <x-beartropy-ui::checkbox 
                                        value="{{ $row[$column_id] }}"
                                        id="{{ $row[$column_id] }}"
                                        wire:model.live="yat_selected_checkbox"
                                        color="{{ $theme }}"
                                    />
                                    {{-- <input
                                        type="checkbox"
                                        value="{{ $row[$column_id] }}"
                                        id="{{ $row[$column_id] }}"
                                        wire:model.live="yat_selected_checkbox"
                                        class="cursor-pointer text-gray-500 bg-gray-100 border-gray-400 rounded focus:ring-gray-500 dark:focus:ring-gray-600 dark:ring-offset-gray-700 dark:focus:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500"
                                    > --}}
                                </td>
                            @endif
                            @foreach ($columns as $column)
                              @if (!$column->isHidden && $column->isVisible)
                                    @if(property_exists($column, 'hasView') && $column->hasView)
                                    <td class="">
                                        @include($column->view)
                                    </td>
                                    @elseif(property_exists($column, 'isToggle') && $column->isToggle)
                                    <td class="text-center {{$column->classes}} ">
                                        <div class="pt-5">
                                            @if(!isset($row[$column->key."_hidden"]) || !$row[$column->key."_hidden"])
                                                <x-beartropy-ui::toggle 
                                                    :disabled="isset($row[$column->key.'_disabled']) && $row[$column->key.'_disabled']"
                                                    :checked="$row[$column->key]"
                                                    wire:change="toggleBoolean('{{$row[$column_id]}}', '{{$column->key}}')"
                                                />
                                            @endif
                                        </div>
                                    </td>
                                    @elseif(property_exists($column, 'isBool') && $column->isBool)
                                    <td class="text-center {{$column->classes}} ">
                                        @if($row[$column->key] === true || strtolower($row[$column->key]) == "true" || strtolower($row[$column->key]) == "1" || strtolower($row[$column->key]) === 1)
                                            {!! $column->true_icon !!}
                                        @else
                                            {!! $column->false_icon !!}
                                        @endif
                                    </td>
                                    @elseif(property_exists($column, 'isHtml') && $column->isHtml)
                                    <td class="px-5 py-3 whitespace-nowrap text-pretty text-sm font-normal {{ $themeConfig['table']['td_text'] }} {{$column->classes}} ">
                                        {!! $row[$column->key] ?? '' !!}
                                    </td>
                                    @elseif(property_exists($column, 'isLink') && $column->isLink)
                                    <td class="px-5 py-3 whitespace-nowrap text-pretty text-sm font-normal {{ $themeConfig['table']['td_text'] }} {{$column->classes}} ">
                                    @php
                                        $link_data = json_decode($row[$column->key],true);
                                    @endphp
                                    <a href="{{$link_data[0]}}" class="{{$column->tag_classes ?? 'cursor-pointer'}}">{!! $link_data[1] !!}</a>
                                    </td>
                                    @else
                                    <td class="px-5 py-3 whitespace-nowrap text-pretty text-sm font-normal {{ $themeConfig['table']['td_text'] }} {{$column->classes}} ">
                                        {{ $row[$column->key] ?? '' }}
                                    </td>
                                    @endif
                                @endif
                            @endforeach
                        </tr>
                        @if (isset($row[$column_id]) && in_array($row[$column_id], $yatable_expanded_rows))
                            <tr>
                                <td colspan="{{ $cols = ($has_bulk) ? count($columns) + 1 : count($columns) }}" class="p-1">
                                    @if($yatable_expanded_rows_is_component)
                                        @livewire($yatable_expanded_rows_content[$row[$column_id]]['component'], $yatable_expanded_rows_content[$row[$column_id]]['parameters'], key('yatable_expanded_rows_content'.$row[$column_id]))
                                    @else
                                        {!! $yatable_expanded_rows_content[$row[$column_id]] !!}
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr
                            @if($loading_table_spinner)
                                wire:loading.long.class.add="hidden d-none"
                                wire:target="{{$trigger_spinner}}"
                            @endif
                        >
                            <td colspan="{{ $cols = ($has_bulk) ? count($columns) + 1 : count($columns) }}" class="text-center py-5">
                                <div class="text-xl p-3 {{ $themeConfig['table']['empty_text'] }}">{{ucfirst(__('yat::yat.empty_search'))}}</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @includeWhen($with_pagination, 'yat::livewire.parts.pagination')
    </div>

    @includeWhen($modals_view,$modals_view)

    <!-- PopUp -->
    @foreach ($columns as $column)
        @if(property_exists($column, 'isLink') && $column->isLink && property_exists($column, 'popup'))
            <script>
                function openPopup{{$column->key}}(url) {
                    const width = {{ $column->popup["width"] }};
                    const height = {{ $column->popup["height"] }};
                    const left = (screen.width - width) / 2;
                    const top = (screen.height - height) / 2;

                    window.open(
                        url,
                        '',
                        `width=${width},height=${height},top=${top},left=${left},resizable,scrollbars`
                    );
                }
            </script>
        @endif
    @endforeach

    <div x-data
        x-on:copy-yatable-to-clipboard.window="navigator.clipboard.writeText($event.detail.csv)">
    </div>
  </section>
