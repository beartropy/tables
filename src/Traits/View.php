<?php

namespace Beartropy\Tables\Traits;

trait View
{
    public ?string $title = null;

    public ?string $titleClasses = null;

    public ?string $customHeader = null;

    public ?string $main_wrapper_classes = null;

    public ?string $table_classes = null;

    public bool $override_table_classes = false;

    public bool $sticky_header = false;

    public bool $has_counter = true;

    public ?string $modals_view = null;

    public ?string $yat_most_left_view = null;

    public ?string $yat_less_left_view = null;

    public ?string $yat_most_right_view = null;

    public ?string $yat_less_right_view = null;

    public bool $showCardsOnMobile = false;

    public bool $useCards = false;

    public bool $yat_is_mobile = false;

    public array $yat_custom_buttons = [];

    public string $yat_button_variant = 'outline';

    public string $theme = 'gray';

    public ?string $bulkThemeOverride = null;

    public ?string $buttonThemeOverride = null;

    public ?string $inputThemeOverride = null;

    public ?string $componentSizeOverride = null;

    public array $themeConfig = [];

    /**
     * Set the table theme.
     *
     * @return void
     */
    public function setTheme(string $theme)
    {
        $this->theme = $theme;
        $this->themeConfig = $this->getThemeConfig($theme);
    }

    /**
     * Get configuration for a specific theme from presets.
     *
     * @return array
     */
    public function getThemeConfig(string $theme)
    {
        $presets = require __DIR__.'/../resources/views/livewire/table-presets.php';

        return $presets[$theme] ?? $presets['gray'];
    }

    /**
     * Override the bulk actions theme.
     *
     * @return void
     */
    public function setBulkThemeOverride(?string $theme)
    {
        $this->bulkThemeOverride = $theme;
    }

    /**
     * Override the button theme.
     *
     * @return void
     */
    public function setButtonThemeOverride(?string $theme)
    {
        $this->buttonThemeOverride = $theme;
    }

    /**
     * Override specific theme settings.
     *
     * @return void
     */
    public function setInputThemeOverride(?string $theme)
    {
        $this->inputThemeOverride = $theme;
    }

    /**
     * Override the size of all beartropy-ui components in the table header.
     *
     * @return void
     */
    public function setComponentSize(?string $size)
    {
        $this->componentSizeOverride = $size;
    }

    /**
     * Initialize view settings.
     *
     * @return void
     */
    public function mountView()
    {
        if (empty($this->themeConfig)) {
            $this->setTheme($this->theme);
        }
    }

    /**
     * Detect environmental data (e.g. mobile device).
     *
     * @return void
     */
    public function gatherEnvData()
    {
        $userAgent = request()->header('User-Agent');
        if (preg_match('/(android|webos|iphone|ipad|ipod|blackberry|windows phone)/i', $userAgent)) {
            $this->yat_is_mobile = true;
        }
    }

    /**
     * Add custom buttons to the table header.
     *
     * @return void
     */
    public function addButtons(array $buttons)
    {
        $this->yat_custom_buttons = $buttons;
    }

    public array $yat_card_modal_buttons = [];

    /**
     * Add buttons to the card modal view.
     *
     * @return void
     */
    public function addCardModalButtons(array $buttons)
    {
        $this->yat_card_modal_buttons = $buttons;
    }

    /**
     * Toggle the record counter display.
     *
     * @return void
     */
    public function showCounter(bool $bool)
    {
        $this->has_counter = $bool;
    }

    /**
     * Enable or disable card view on mobile devices.
     *
     * @return void
     */
    public function showCardsOnMobile(bool $bool = true)
    {
        $this->showCardsOnMobile = $bool;
    }

    /**
     * Enable or disable card layout for all devices.
     *
     * @return void
     */
    public function useCards(bool $bool = true)
    {
        $this->useCards = $bool;
    }

    public bool $mobileDetailsModalOpen = false;

    public array $mobileDetailsRow = [];

    /**
     * Open the mobile details modal for a specific row.
     *
     * @param  mixed  $rowId
     * @return void
     */
    public function openMobileCardDetails($rowId)
    {
        $this->mobileDetailsRow = $this->getAllData()->firstWhere($this->column_id, $rowId);
        $this->mobileDetailsModalOpen = true;
    }

    /**
     * Close the mobile details modal.
     *
     * @return void
     */
    public function closeMobileCardDetails()
    {
        $this->mobileDetailsModalOpen = false;
        $this->mobileDetailsRow = [];
    }

    /**
     * Set the table title.
     *
     * @param  string  $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Override default title classes.
     *
     * @param  string  $classes
     * @return void
     */
    public function overrideTitleClasses($classes)
    {
        $this->titleClasses = $classes;
    }

    /**
     * Set custom HTML content for the header.
     *
     * @param  string  $html
     * @return void
     */
    public function setCustomHeader($html)
    {
        $this->customHeader = $html;
    }

    /**
     * Set CSS classes for the main component wrapper.
     *
     * @return void
     */
    public function setComponentClasses(string $classes)
    {
        $this->main_wrapper_classes = $classes;
    }

    /**
     * Add CSS classes to the table element.
     *
     * @return void
     */
    public function addTableClasses(string $classes)
    {
        $this->table_classes = $classes;
    }

    /**
     * Set (overwrite) CSS classes for the table element.
     *
     * @return void
     */
    public function setTableClasses(string $classes)
    {
        $this->override_table_classes = true;
        $this->table_classes = $classes;
    }

    /**
     * Enable sticky header for the table.
     *
     * @return void
     */
    public function setStickyHeader()
    {
        $this->sticky_header = true;
    }

    /**
     * Set the view for modals.
     *
     * @return void
     */
    public function setModalsView(string $view)
    {
        $this->modals_view = $view;
    }

    /**
     * Set the view for the leftmost header area.
     *
     * @return void
     */
    public function setMostLeftView(string $view)
    {
        $this->yat_most_left_view = $view;
    }

    /**
     * Set the view for the inner left header area.
     *
     * @return void
     */
    public function setLessLeftView(string $view)
    {
        $this->yat_less_left_view = $view;
    }

    /**
     * Set the view for the rightmost header area.
     *
     * @return void
     */
    public function setMostRightView(string $view)
    {
        $this->yat_most_right_view = $view;
    }

    /**
     * Set the view for the inner right header area.
     *
     * @return void
     */
    public function setLessRightView(string $view)
    {
        $this->yat_less_right_view = $view;
    }

    /**
     * Set the button variant style.
     *
     * @return void
     */
    public function setButtonVariant(string $variant)
    {
        $this->yat_button_variant = $variant;
    }

    public bool $showOptionsOnlyOnRowSelect = false;

    /**
     * Configure options visibility based on row selection specific logic.
     *
     * @return void
     */
    public function showOptionsOnlyOnRowSelect(bool $value = true)
    {
        $this->showOptionsOnlyOnRowSelect = $value;
    }

    public mixed $layout = null;

    /**
     * Set the main layout view.
     *
     * @param  mixed  $layout
     * @return void
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public bool $stripRows = true;

    /**
     * Enable or disable striped rows.
     *
     * @return void
     */
    public function stripRows(bool $strip = true)
    {
        $this->stripRows = $strip;
    }

    /**
     * Get the CSS classes for row striping based on configuration.
     *
     * @return string
     */
    public function getRowStripingClasses()
    {
        if ($this->stripRows) {
            return $this->themeConfig['table']['tr_body_odd'].' '.$this->themeConfig['table']['tr_body_even'];
        }

        return str_replace(['odd:', 'even:'], '', $this->themeConfig['table']['tr_body_odd']);
    }
}
