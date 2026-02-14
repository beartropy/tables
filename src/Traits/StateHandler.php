<?php

namespace Beartropy\Tables\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

trait StateHandler
{
    public bool $handle_state = false;

    public string $handler_prefix = '';

    /**
     * Enable or disable persisting column visibility to the database.
     *
     * @return void
     */
    public function useStateHandler(bool $bool)
    {
        $this->handle_state = $bool;
    }

    /**
     * Set a prefix for the state handler key to differentiate tables.
     *
     * @return void
     */
    public function setHandlerPrefix(string $string)
    {
        $this->handler_prefix = $string;
    }

    /**
     * Save the current column visibility state to the database.
     *
     * @return void
     */
    public function saveTableState()
    {
        if ($this->handle_state) {
            try {
                DB::table('yat_user_table_config')->updateOrInsert(
                    ['user_id' => Auth::user()->id, 'table' => $this->handler_prefix.static::class],
                    ['configuration' => json_encode($this->columns->pluck('isVisible', 'key'))]);

                $this->dispatch('tableStateSaved', true);
                $this->column_toggle_dd_status = false;
            } catch (\Throwable $th) {
                $this->dispatch('tableStateSaved', false);
            }
        }
    }

    /**
     * Restore column visibility state from the database.
     *
     * @return void
     */
    public function setTableState()
    {
        if (! $this->yat_is_mobile && $this->handle_state) {
            $state = DB::table('yat_user_table_config')->where(['user_id' => Auth::user()->id, 'table' => $this->handler_prefix.static::class])->first()?->configuration ?? false;

            if ($state) {
                $state = json_decode($state, true);
                foreach ($state as $key => $isVisible) {
                    $column = $this->columns->where('key', $key)->first();
                    if ($column) {
                        $column->isVisible = $isVisible;
                    }
                }
            }
        }
    }
}
