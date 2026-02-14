<?php

namespace Beartropy\Tables\Classes\Columns;

use Beartropy\Tables\Traits\Columns;
use Illuminate\Support\Str;

class Column
{
    use Columns;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $key;

    /**
     * @var string
     */
    public $index;

    /** @var bool */
    public $isVisible = true;

    /** @var bool */
    public $isHidden = false;

    /** @var bool */
    public $hideFromSelector = false;

    /** @var \Closure|null */
    public $customData = null;

    /** @var string */
    public $classes = '';

    /** @var string */
    public $th_classes = '';

    /** @var string */
    public $th_wrapper_classes = '';

    /** @var bool */
    public $has_modified_data = false;

    /** @var bool */
    public $hide_on_mobile = false;

    /** @var bool */
    public $collapseOnMobile = false;

    /** @var bool */
    public $show_on_mobile = false;

    /** @var bool */
    public $cardTitle = false;

    /** @var \Closure|null */
    public $cardTitleCallback = null;

    /** @var bool */
    public $triggerCardInfoModal = true;

    /** @var bool */
    public $showOnCard = false;

    /** @var bool */
    public $isEditable = false;

    /** @var string */
    public $editableType = 'input';

    /** @var array */
    public $editableOptions = [];

    /** @var callable|string|null */
    public $editableCallback = null;

    /** @var callable|null */
    public $sortableCallback = null;

    /** @var callable|null */
    public $searchableCallback = null;

    /** @var bool */
    public $isSortable = true;

    /** @var bool */
    public $isSearchable = true;

    /** @var array<string> */
    protected static $existingKeys = [];

    /**
     * Create a new Column instance.
     */
    public function __construct(string $label, ?string $index = null)
    {
        $this->label = trim($label);
        $this->key = $index ?? $this->generateUniqueKey($label);
        $this->index = $index ?? $this->key;
    }

    /**
     * static Constructor.
     *
     * @return static
     */
    public static function make(string $label, ?string $key = null): Column
    {
        return new static($label, $key);
    }

    /**
     * Set the column to collapse on mobile devices.
     */
    public function collapseOnMobile(bool $bool = true): self
    {
        $this->collapseOnMobile = $bool;

        return $this;
    }

    /**
     * Set the column as the card title for mobile devices.
     *
     * @param  bool|callable  $callback
     */
    public function cardTitle($callback = true): self
    {
        if (is_callable($callback)) {
            $this->cardTitleCallback = $callback;
            $this->cardTitle = true;
        } else {
            $this->cardTitle = $callback;
        }

        return $this;
    }

    /**
     * Toggle whether clicking the card title opens the info modal.
     */
    public function triggerCardInfoModal(bool $bool = true): self
    {
        $this->triggerCardInfoModal = $bool;

        return $this;
    }

    /**
     * Toggle whether this column is displayed on mobile cards.
     */
    public function showOnCard(bool $bool = true): self
    {
        $this->showOnCard = $bool;

        return $this;
    }

    /**
     * Mark the column as sortable.
     *
     * @param  bool|callable  $callback
     */
    public function sortable($callback = true): self
    {
        if (is_callable($callback)) {
            $this->sortableCallback = $callback;
            $this->isSortable = true;
        } else {
            $this->isSortable = $callback;
        }

        return $this;
    }

    /**
     * Mark the column as searchable.
     *
     * @param  bool|callable  $callback
     */
    public function searchable($callback = true): self
    {
        if (is_callable($callback)) {
            $this->searchableCallback = $callback;
            $this->isSearchable = true;
        } else {
            $this->isSearchable = $callback;
        }

        return $this;
    }

    /**
     * Mark the column as editable.
     *
     * @param  string  $type
     * @param  array  $options
     * @param  callable|null  $onUpdate
     */
    public function editable($type = 'input', $options = [], $onUpdate = null): self
    {
        $this->isEditable = true;
        $this->editableType = $type;
        $this->editableOptions = $options;
        $this->editableCallback = $onUpdate;

        return $this;
    }

    /** @var string|null */
    public $updateField = null;

    /**
     * Set the database field to use when saving editable values.
     *
     * @param  string  $field
     */
    public function setUpdateField($field): self
    {
        $this->updateField = $field;

        return $this;
    }

    /**
     * Align the column content to the left.
     */
    public function pushLeft(): self
    {
        $this->classes .= ' text-left';
        if (! isset($this->th_wrapper_classes)) {
            $this->th_wrapper_classes = '';
        }
        $this->th_wrapper_classes .= ' flex justify-start text-left';

        return $this;
    }

    /**
     * Align the column content to the right.
     */
    public function pushRight(): self
    {
        $this->classes .= ' text-right';
        if (! isset($this->th_wrapper_classes)) {
            $this->th_wrapper_classes = '';
        }
        $this->th_wrapper_classes .= ' flex justify-end text-right';

        return $this;
    }

    /**
     * Center the column content.
     */
    public function centered(): self
    {
        $this->classes .= ' text-center';
        if (! isset($this->th_wrapper_classes)) {
            $this->th_wrapper_classes = '';
        }
        $this->th_wrapper_classes .= ' flex justify-center text-center';

        return $this;
    }

    /**
     * Reset the static key registry (used between test runs).
     *
     * @return void
     */
    public static function resetStaticKeys()
    {
        static::$existingKeys = [];
    }

    /**
     * Generate a unique slug key from the column label.
     */
    protected function generateUniqueKey(string $label): string
    {

        // Convert label to a slug with underscore separator
        if ($label == '#') {
            $label = 'hash';
        }
        $baseKey = Str::slug($label, '_');
        $key = $baseKey;
        $counter = 1;

        // Ensure uniqueness across all created Column objects
        while (in_array($key, static::$existingKeys)) {
            $key = $baseKey.'_'.$counter++;
        }

        // Store the key to prevent future duplicates
        static::$existingKeys[] = $key;

        return $key;
    }
}
