<?php

namespace Tests\Unit;

use Beartropy\Tables\YATBaseTable;
use Livewire\Livewire;
use Tests\TestCase;
use Workbench\App\Models\User;

class OptionsLogicTest extends TestCase
{
    /** @test */
    public function it_shows_options_by_default()
    {
        // Mocking the component or a simplified version
        $component = new class extends YATBaseTable {
            public function options() {
                return ['export' => 'Export'];
            }
        };
        
        $this->assertFalse($component->showOptionsOnlyOnRowSelect);
        // We can't easily test blade conditional rendering without a full integration test environment setup here easily
        // But we can check property state.
    }

    /** @test */
    public function it_can_enable_show_options_only_on_row_select()
    {
        $component = new class extends YATBaseTable {
             public function options() {
                return ['export' => 'Export'];
            }
        };

        $component->showOptionsOnlyOnRowSelect(true);
        $this->assertTrue($component->showOptionsOnlyOnRowSelect);
    }
}
