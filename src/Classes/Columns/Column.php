<?php

namespace Beartropy\Tables\Classes\Columns;

use Illuminate\Support\Str;
use Beartropy\Tables\Traits\Columns;

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
    public $isVisible = true;
    public $isHidden = false;
    public $hideFromSelector = false;
    public $customData = null;
    public $classes = '';
    public $th_classes = '';
    public $th_wrapper_classes = '';
    public $has_modified_data = false;
    public $hide_on_mobile = false;
    public $collapseOnMobile = false;
    public $show_on_mobile = false;
    public $cardTitle = false;
    public $cardTitleCallback = null;
    public $triggerCardInfoModal = true;
    public $showOnCard = false;

    public $isEditable = false;
    public $editableType = 'input'; // input, select
    public $editableOptions = [];
    public $editableCallback = null;

    public $sortableCallback = null;
    public $searchableCallback = null;
    public $isSortable = true;
    public $isSearchable = true;

    protected static $existingKeys = [];

    /**
     * Create a new Column instance.
     *
     * @param string $label
     * @param string|null $index
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
     * @param string $label
     * @param string|null $key
     * @return static
     */
    public static function make(string $label, ?string $key = null): Column
    {
        return new static($label, $key);
    }

    /**
     * Set the column to collapse on mobile devices.
     *
     * @param bool $bool
     * @return self
     */
    public function collapseOnMobile(bool $bool = true): self
    {
        $this->collapseOnMobile = $bool;
        return $this;
    }

    /**
     * Set the column as the card title for mobile devices.
     * 
     * @param bool|callable $callback
     * @return self
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

    public function triggerCardInfoModal(bool $bool = true): self
    {
        $this->triggerCardInfoModal = $bool;
        return $this;
    }

    public function showOnCard(bool $bool = true): self
    {
        $this->showOnCard = $bool;
        return $this;
    }

    /**
     * Mark the column as sortable.
     *
     * @param bool|callable $callback
     * @return self
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
     * @param bool|callable $callback
     * @return self
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
     * @param string $type
     * @param array $options
     * @param callable|null $onUpdate
     * @return self
     */
    public function editable($type = 'input', $options = [], $onUpdate = null): self
    {
        $this->isEditable = true;
        $this->editableType = $type;
        $this->editableOptions = $options;
        $this->editableCallback = $onUpdate;
        return $this;
    }

    public $updateField = null;

    public function setUpdateField($field): self
    {
        $this->updateField = $field;
        return $this;
    }

    public function pushLeft(): self
    {
        $this->classes .= ' text-left';
        if (!isset($this->th_wrapper_classes)) {
            $this->th_wrapper_classes = '';
        }
        $this->th_wrapper_classes .= ' flex justify-start text-left';
        return $this;
    }

    public function pushRight(): self
    {
        $this->classes .= ' text-right';
        if (!isset($this->th_wrapper_classes)) {
            $this->th_wrapper_classes = '';
        }
        $this->th_wrapper_classes .= ' flex justify-end text-right';
        return $this;
    }

    public function centered(): self
    {
        $this->classes .= ' text-center';
        if (!isset($this->th_wrapper_classes)) {
            $this->th_wrapper_classes = '';
        }
        $this->th_wrapper_classes .= ' flex justify-center text-center';
        return $this;
    }

    public static function resetStaticKeys()
    {
        static::$existingKeys = [];
    }

    protected function generateUniqueKey(string $label): string
    {

        // Convert label to a slug with underscore separator
        if ($label == "#") $label = "hash";
        $baseKey = Str::slug($label, '_');
        $key = $baseKey;
        $counter = 1;

        // Ensure uniqueness across all created Column objects
        while (in_array($key, static::$existingKeys)) {
            $key = $baseKey . "_" . $counter++;
        }

        // Store the key to prevent future duplicates
        static::$existingKeys[] = $key;

        return $key;
    }
}
