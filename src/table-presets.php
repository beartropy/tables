<?php

$themes = [
    'slate',
    'gray',
    'zinc',
    'neutral',
    'stone',
    'indigo',
];

$presets = [];

foreach ($themes as $theme) {
    $presets[$theme] = [
        'general' => [
            'title' => "text-{$theme}-600 dark:text-{$theme}-300",
            'text_main' => "text-{$theme}-900 dark:text-{$theme}-400",
            'text_secondary' => "text-{$theme}-700 dark:text-{$theme}-300",
            'bg_main' => "bg-{$theme}-100 dark:bg-{$theme}-800",
            'ring_main' => "ring-{$theme}-300 dark:ring-{$theme}-500",
            'focus_ring' => "focus:ring-{$theme}-400 dark:focus:ring-{$theme}-400",
        ],
        'table' => [
            'wrapper' => "border-{$theme}-200 dark:border-{$theme}-700",
            'thead_bg' => "bg-{$theme}-50 dark:bg-{$theme}-800",
            'tr_thead' => "border-b dark:border-b-{$theme}-700 bg-{$theme}-100 dark:bg-{$theme}-900 text-{$theme}-600 dark:text-{$theme}-300",
            'th' => "text-{$theme}-500 dark:bg-{$theme}-900 dark:text-{$theme}-400",
            'tr_body_hover' => "hover:bg-{$theme}-200 dark:hover:bg-{$theme}-700",
            'tr_body_odd' => "odd:bg-{$theme}-100 dark:odd:bg-{$theme}-900",
            'tr_body_even' => "dark:even:bg-{$theme}-800",
            'td_text' => "text-{$theme}-600 dark:text-{$theme}-300",
            'td_text_secondary' => "text-{$theme}-700 dark:text-{$theme}-300",
            'border_b' => "border-b dark:border-b-{$theme}-700",
            'empty_text' => "text-{$theme}-700 dark:text-{$theme}-300",
        ],
        'buttons' => [
            'filter_toggle' => "text-{$theme}-900 ring-{$theme}-300 dark:ring-{$theme}-500 focus:ring-{$theme}-400 dark:focus:ring-{$theme}-400 bg-{$theme}-100 dark:bg-{$theme}-800 dark:text-{$theme}-400",
            'column_toggle' => "text-{$theme}-900 ring-{$theme}-300 dark:ring-{$theme}-500 focus:ring-{$theme}-400 dark:focus:ring-{$theme}-400 bg-{$theme}-100 dark:bg-{$theme}-800 dark:text-{$theme}-400",
            'options_toggle' => "text-{$theme}-900 ring-{$theme}-300 dark:ring-{$theme}-500 focus:ring-{$theme}-400 dark:focus:ring-{$theme}-400 bg-{$theme}-100 dark:bg-{$theme}-800 dark:text-{$theme}-400",
            'per_page' => "text-{$theme}-900 ring-{$theme}-300 dark:ring-{$theme}-500 focus:ring-{$theme}-400 dark:focus:ring-{$theme}-400 bg-{$theme}-100 dark:bg-{$theme}-800 dark:text-{$theme}-400",
            'pagination' => "text-{$theme}-900 ring-{$theme}-300 dark:ring-{$theme}-500 focus:ring-{$theme}-400 dark:focus:ring-{$theme}-400 bg-{$theme}-100 dark:bg-{$theme}-800 dark:text-{$theme}-400",
            'default' => "bg-opacity-60 dark:bg-opacity-30 text-{$theme}-600 bg-{$theme}-300 dark:bg-{$theme}-600 dark:text-{$theme}-400 hover:bg-opacity-60 dark:hover:bg-opacity-30 hover:text-{$theme}-800 hover:bg-{$theme}-400 dark:hover:text-{$theme}-400 dark:hover:bg-{$theme}-500 focus:bg-opacity-60 dark:focus:bg-opacity-30 focus:ring-offset-2 focus:text-{$theme}-800 focus:bg-{$theme}-400 focus:ring-{$theme}-400 dark:focus:text-{$theme}-400 dark:focus:bg-{$theme}-500 dark:focus:ring-{$theme}-700",
        ],
        'dropdowns' => [
            'bg' => "bg-white dark:bg-{$theme}-800",
            'border' => "border-{$theme}-200 dark:border-{$theme}-600",
            'text' => "text-{$theme}-700 dark:text-{$theme}-200",
            'divide' => "divide-{$theme}-100 dark:divide-{$theme}-700/50",
            'hover_bg' => "hover:bg-{$theme}-100 dark:hover:bg-{$theme}-700",
            'hover_text' => "text-{$theme}-700 dark:text-{$theme}-300 dark:hover:text-white",
            'active_bg' => "bg-{$theme}-100 dark:bg-{$theme}-700",
        ],
        'inputs' => [
            'border' => "ring-{$theme}-300 dark:ring-{$theme}-500",
            'text' => "text-{$theme}-900 dark:text-{$theme}-400",
            'placeholder' => "placeholder:text-{$theme}-400 dark:placeholder:text-{$theme}-500",
            'wrapper_bg' => 'bg-background-white dark:bg-background-dark',
            'checkbox' => "text-{$theme}-500 bg-{$theme}-100 border-{$theme}-400 focus:ring-{$theme}-500 dark:focus:ring-{$theme}-600 dark:ring-offset-{$theme}-700 dark:focus:ring-offset-{$theme}-700 dark:bg-{$theme}-600 dark:border-{$theme}-500",
            'toggle_bg' => "bg-{$theme}-200 dark:bg-{$theme}-700",
            'toggle_circle_border' => "border-{$theme}-300 dark:border-{$theme}-600",
            'icon' => "text-{$theme}-500 dark:text-{$theme}-400",
            'icon_remove' => "text-{$theme}-400",
        ],
        'filters' => [
            'label' => "text-{$theme}-700 dark:text-{$theme}-300",
            'label_secondary' => "text-{$theme}-600 dark:text-{$theme}-300",
        ],
        'pagination' => [
            'text' => "text-{$theme}-700 dark:text-{$theme}-400",
        ],
        'loading' => [
            'text' => "text-{$theme}-500 dark:text-{$theme}-200",
            'border' => "border-{$theme}-300",
        ]
    ];
}

return $presets;
