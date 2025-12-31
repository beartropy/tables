<?php

namespace Beartropy\Tables\Traits;

trait View
{
    /**
     * @var string|null
     */
    public $title;

    /**
     * @var string|null
     */
    public $titleClasses;

    /**
     * @var string|null
     */
    public $customHeader;

    /**
     * @var string|null
     */
    public $main_wrapper_classes;

    /**
     * @var string|null
     */
    public $table_classes;

    /**
     * @var bool
     */
    public $override_table_classes = false;

    /**
     * @var bool
     */
    public $sticky_header = false;

    /**
     * @var bool
     */
    public $has_counter = true;

    /**
     * @var string|null
     */
    public $modals_view;

    /**
     * @var string|null
     */
    public $yat_most_left_view;
    /**
     * @var string|null
     */
    public $yat_less_left_view;
    /**
     * @var string|null
     */
    public $yat_most_right_view;
    /**
     * @var string|null
     */
    public $yat_less_right_view;

    /**
     * @var bool
     */
    public $showCardsOnMobile = false;

    /**
     * @var bool
     */
    public $useCards = false;

    /**
     * @var bool
     */
    public $yat_is_mobile = false;

    /**
     * @var array
     */
    public $yat_custom_buttons = [];

    /**
     * @var string
     */
    public $yat_button_variant = 'outline';

    /**
     * @var string
     */
    public string $theme = 'gray';

    /**
     * @var string|null
     */
    public ?string $bulkThemeOverride = 'gray';

    /**
     * @var string|null
     */
    public ?string $buttonThemeOverride = 'beartropy';

    /**
     * @var string|null
     */
    public ?string $inputThemeOverride = 'beartropy';

    /**
     * @var array
     */
    public array $themeConfig = [];

    /**
     * Set the table theme.
     *
     * @param string $theme
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
     * @param string $theme
     * @return array
     */
    public function getThemeConfig(string $theme)
    {
        $presets = require __DIR__ . '/../resources/views/livewire/table-presets.php';
        return $presets[$theme] ?? $presets['gray'];
    }

    /**
     * Override the bulk actions theme.
     *
     * @param string|null $theme
     * @return void
     */
    public function setBulkThemeOverride(?string $theme)
    {
        $this->bulkThemeOverride = $theme;
    }

    /**
     * Override the button theme.
     *
     * @param string|null $theme
     * @return void
     */
    public function setButtonThemeOverride(?string $theme)
    {
        $this->buttonThemeOverride = $theme;
    }

    /**
     * Override specific theme settings.
     *
     * @param string|null $theme
     * @return void
     */
    public function setInputThemeOverride(?string $theme)
    {
        $this->inputThemeOverride = $theme;
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
     * @param array $buttons
     * @return void
     */
    public function addButtons(array $buttons)
    {
        $this->yat_custom_buttons = $buttons;
    }

    /**
     * @var array
     */
    public $yat_card_modal_buttons = [];

    /**
     * Add buttons to the card modal view.
     *
     * @param array $buttons
     * @return void
     */
    public function addCardModalButtons(array $buttons)
    {
        $this->yat_card_modal_buttons = $buttons;
    }

    /**
     * Toggle the record counter display.
     *
     * @param bool $bool
     * @return void
     */
    public function showCounter(bool $bool)
    {
        $this->has_counter = $bool;
    }

    public function showCardsOnMobile(bool $bool = true)
    {
        $this->showCardsOnMobile = $bool;
    }

    public function useCards(bool $bool = true)
    {
        $this->useCards = $bool;
    }

    public $mobileDetailsModalOpen = false;
    public $mobileDetailsRow = [];

    /**
     * Open the mobile details modal for a specific row.
     *
     * @param mixed $rowId
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
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Override default title classes.
     *
     * @param string $classes
     * @return void
     */
    public function overrideTitleClasses($classes)
    {
        $this->titleClasses = $classes;
    }

    /**
     * Set custom HTML content for the header.
     *
     * @param string $html
     * @return void
     */
    public function setCustomHeader($html)
    {
        $this->customHeader = $html;
    }

    /**
     * Set CSS classes for the main component wrapper.
     *
     * @param string $classes
     * @return void
     */
    public function setComponentClasses(string $classes)
    {
        $this->main_wrapper_classes = $classes;
    }

    /**
     * Add CSS classes to the table element.
     *
     * @param string $classes
     * @return void
     */
    public function addTableClasses(string $classes)
    {
        $this->table_classes = $classes;
    }

    /**
     * Set (overwrite) CSS classes for the table element.
     *
     * @param string $classes
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
     * @param string $view
     * @return void
     */
    public function setModalsView(string $view)
    {
        $this->modals_view = $view;
    }

    /**
     * Set the view for the leftmost header area.
     *
     * @param string $view
     * @return void
     */
    public function setMostLeftView(string $view)
    {
        $this->yat_most_left_view = $view;
    }

    /**
     * Set the view for the inner left header area.
     *
     * @param string $view
     * @return void
     */
    public function setLessLeftView(string $view)
    {
        $this->yat_less_left_view = $view;
    }

    /**
     * Set the view for the rightmost header area.
     *
     * @param string $view
     * @return void
     */
    public function setMostRightView(string $view)
    {
        $this->yat_most_right_view = $view;
    }

    /**
     * Set the view for the inner right header area.
     *
     * @param string $view
     * @return void
     */
    public function setLessRightView(string $view)
    {
        $this->yat_less_right_view = $view;
    }

    /**
     * Set the button variant style.
     *
     * @param string $variant
     * @return void
     */
    public function setButtonVariant(string $variant)
    {
        $this->yat_button_variant = $variant;
    }

    public $showOptionsOnlyOnRowSelect = false;

    /**
     * Configure options visibility based on row selection specific logic.
     *
     * @param bool $value
     * @return void
     */
    public function showOptionsOnlyOnRowSelect(bool $value = true)
    {
        $this->showOptionsOnlyOnRowSelect = $value;
    }

    public $layout;

    /**
     * Set the main layout view.
     *
     * @param mixed $layout
     * @return void
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public $stripRows = true;

    /**
     * Enable or disable striped rows.
     *
     * @param bool $strip
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
            return $this->themeConfig['table']['tr_body_odd'] . ' ' . $this->themeConfig['table']['tr_body_even'];
        }

        return str_replace(['odd:', 'even:'], '', $this->themeConfig['table']['tr_body_odd']);
    }
}
