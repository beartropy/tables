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

    public $yat_is_mobile = false;

    public $yat_custom_buttons = [];

    public function gatherEnvData() {
        $userAgent = request()->header('User-Agent');
        if(preg_match('/(android|webos|iphone|ipad|ipod|blackberry|windows phone)/i', $userAgent)) {
            $this->yat_is_mobile = true;
        }
    }

    public function addButtons(array $buttons) {
        $this->yat_custom_buttons = $buttons;
    }

    public function showCounter(bool $bool) {
        $this->has_counter = $bool;
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
}