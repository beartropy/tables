<?php

namespace Beartropy\Tables\Traits;

trait View
{
    public $title;
    public $titleClasses;
    public $customHeader;
    public $main_wrapper_classes;
    public $table_classes;
    public $override_table_classes = false;
    public $sticky_header = false;
    public $has_counter = true;
    
    public $modals_view;
    
    public $yat_most_left_view;
    public $yat_less_left_view;
    public $yat_most_right_view;
    public $yat_less_right_view;

    public $showCardsOnMobile = false;
    public $useCards = false;

    public $yat_is_mobile = false;

    public $yat_custom_buttons = [];

    public $yat_button_variant = 'glass';

    public string $theme = 'slate';

    public ?string $bulkThemeOverride = null;
    public ?string $buttonThemeOverride = null;
    public ?string $inputThemeOverride = null;

    public array $themeConfig = [];

    public function setTheme(string $theme) {
        $this->theme = $theme;
        $presets = require __DIR__ . '/../resources/views/livewire/table-presets.php';
        $this->themeConfig = $presets[$theme] ?? $presets['slate'];
    }

    public function setBulkThemeOverride(?string $theme) {
        $this->bulkThemeOverride = $theme;
    }

    public function setButtonThemeOverride(?string $theme) {
        $this->buttonThemeOverride = $theme;
    }

    public function setInputThemeOverride(?string $theme) {
        $this->inputThemeOverride = $theme;
    }

    public function mountView() {
        if (empty($this->themeConfig)) {
            $this->setTheme($this->theme);
        }
    }

    public function gatherEnvData() {
        $userAgent = request()->header('User-Agent');
        if(preg_match('/(android|webos|iphone|ipad|ipod|blackberry|windows phone)/i', $userAgent)) {
            $this->yat_is_mobile = true;
        }
    }

    public function addButtons(array $buttons) {
        $this->yat_custom_buttons = $buttons;
    }

    public $yat_card_modal_buttons = [];

    public function addCardModalButtons(array $buttons) {
        $this->yat_card_modal_buttons = $buttons;
    }

    public function showCounter(bool $bool) {
        $this->has_counter = $bool;
    }

    public function showCardsOnMobile(bool $bool = true) {
        $this->showCardsOnMobile = $bool;
    }

    public function useCards(bool $bool = true) {
        $this->useCards = $bool;
    }

    public $mobileDetailsModalOpen = false;
    public $mobileDetailsRow = [];

    public function openMobileCardDetails($rowId) {
        $this->mobileDetailsRow = $this->getAllData()->firstWhere($this->column_id, $rowId);
        $this->mobileDetailsModalOpen = true;
    }

    public function closeMobileCardDetails() {
        $this->mobileDetailsModalOpen = false;
        $this->mobileDetailsRow = [];
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function overrideTitleClasses($classes) {
        $this->titleClasses = $classes;
    }

    public function setCustomHeader($html) {
        $this->customHeader = $html;
    }

    public function setComponentClasses(string $classes) {
        $this->main_wrapper_classes = $classes;
    }

    public function addTableClasses(string $classes) {
        $this->table_classes = $classes;
    }

    public function setTableClasses(string $classes) {
        $this->override_table_classes = true;
        $this->table_classes = $classes;
    }
    
    public function setStickyHeader() {
        $this->sticky_header = true;
    }



    public function setModalsView(string $view) {
        $this->modals_view = $view;
    }

    public function setMostLeftView(string $view) {
        $this->yat_most_left_view = $view;
    }

    public function setLessLeftView(string $view) {
        $this->yat_less_left_view = $view;
    }

    public function setMostRightView(string $view) {
        $this->yat_most_right_view = $view;
    }

    public function setLessRightView(string $view) {
        $this->yat_less_right_view = $view;
    }

    public function setButtonVariant(string $variant) {
        $this->yat_button_variant = $variant;
    }

    public $showOptionsOnlyOnRowSelect = false;

    public function showOptionsOnlyOnRowSelect(bool $value = true) {
        $this->showOptionsOnlyOnRowSelect = $value;
    }

    public $layout;

    public function setLayout($layout) {
        $this->layout = $layout;
    }

    public $stripRows = true;

    public function stripRows(bool $strip = true) {
        $this->stripRows = $strip;
    }

    public function getRowStripingClasses() {
        if ($this->stripRows) {
            return $this->themeConfig['table']['tr_body_odd'] . ' ' . $this->themeConfig['table']['tr_body_even'];
        }

        return str_replace(['odd:', 'even:'], '', $this->themeConfig['table']['tr_body_odd']);
    }
}