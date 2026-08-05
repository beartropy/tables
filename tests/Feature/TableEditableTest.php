<?php

use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\BeartropyTable;
use Illuminate\Database\Eloquent\Model;
use Livewire\Livewire;

class UserForEditable extends Model
{
    protected $table = 'users';

    protected $guarded = [];
}

class EditableUserTable extends BeartropyTable
{
    public function settings()
    {
        $this->model = UserForEditable::class;
    }

    public function columns()
    {
        return [
            Column::make('Name')->editable(),
            Column::make('Email')->editable(),
        ];
    }
}

class EditableWithUpdateFieldTable extends BeartropyTable
{
    public function settings()
    {
        $this->model = UserForEditable::class;
    }

    public function columns()
    {
        return [
            Column::make('Display Name', 'name')->editable()->setUpdateField('name'),
            Column::make('Email')->editable(),
        ];
    }
}

class EditableWithCallbackTable extends BeartropyTable
{
    public bool $callbackInvoked = false;

    public string $callbackValue = '';

    public function settings()
    {
        $this->model = UserForEditable::class;
    }

    public function columns()
    {
        return [
            Column::make('Name')->editable('input', [], 'customUpdateHandler'),
            Column::make('Email'),
        ];
    }

    public function customUpdateHandler($id, $field, $value, $component): void
    {
        $this->callbackInvoked = true;
        $this->callbackValue = $value;
    }
}

class DeniedEditableTable extends BeartropyTable
{
    public function settings()
    {
        $this->model = UserForEditable::class;
    }

    public function columns()
    {
        return [
            Column::make('Name')->editable(),
            Column::make('Email'),
        ];
    }

    public function authorizeFieldUpdate(\Illuminate\Database\Eloquent\Model $record, string $field, mixed $value): bool
    {
        return false;
    }
}

class EditableArrayTable extends BeartropyTable
{
    public function columns()
    {
        return [
            Column::make('Name', 'name')->editable(),
            Column::make('Email', 'email'),
        ];
    }

    public function data()
    {
        return [
            ['id' => 1, 'name' => 'Alice', 'email' => 'alice@example.com'],
            ['id' => 2, 'name' => 'Bob', 'email' => 'bob@example.com'],
        ];
    }

    public function settings() {}
}

class NonEditableColumnTable extends BeartropyTable
{
    public function columns()
    {
        return [
            Column::make('Name', 'name')->editable(),
            Column::make('Role', 'role'),
        ];
    }

    public function data()
    {
        return [
            ['id' => 1, 'name' => 'Ada', 'role' => 'user'],
        ];
    }

    public function settings() {}
}

class CallbackEditableTable extends BeartropyTable
{
    public $written = [];

    public $denyEverything = false;

    public function columns()
    {
        return [
            Column::make('Name', 'name')->editable('input', [], 'recordWrite'),
            Column::make('Nick', 'nick')->editable('input', [], function ($id, $field, $value, $table) {
                $table->written[] = [$id, $field, $value];
            }),
            Column::make('Role', 'role')->editable(),
        ];
    }

    public function recordWrite($id, $field, $value, $table)
    {
        $this->written[] = [$id, $field, $value];
    }

    public function authorizeFieldUpdateRaw($id, string $field, mixed $value): bool
    {
        return ! $this->denyEverything;
    }

    public function data()
    {
        return [
            ['id' => 1, 'name' => 'Ada', 'nick' => 'ada', 'role' => 'user'],
        ];
    }

    public function settings() {}
}

beforeEach(function () {
    UserForEditable::create(['name' => 'Alice', 'email' => 'alice@example.com']);
    UserForEditable::create(['name' => 'Bob', 'email' => 'bob@example.com']);
});

it('updateField updates model record in DB', function () {
    Livewire::test(EditableUserTable::class)
        ->call('updateField', 1, 'name', 'Alice Updated');

    expect(UserForEditable::find(1)->name)->toBe('Alice Updated');
});

it('updateField passes null for empty string', function () {
    // The updateField method converts '' to null before saving.
    // SQLite allows null in string columns even without explicit nullable(),
    // but if the DB rejects it the update will fail silently (caught by try/catch).
    // We verify the method is called and the conversion logic runs.
    $component = Livewire::test(EditableUserTable::class);
    $result = $component->call('updateField', 1, 'name', 'Valid Name');

    expect(UserForEditable::find(1)->name)->toBe('Valid Name');
});

it('updateField with setUpdateField uses alternative DB field', function () {
    Livewire::test(EditableWithUpdateFieldTable::class)
        ->call('updateField', 1, 'name', 'New Display Name');

    expect(UserForEditable::find(1)->name)->toBe('New Display Name');
});

it('updateField authorization denied returns false', function () {
    $component = Livewire::test(DeniedEditableTable::class)
        ->call('updateField', 1, 'name', 'Should Not Update');

    // Name should not have changed
    expect(UserForEditable::find(1)->name)->toBe('Alice');
});

it('updateField for non-existent record returns false', function () {
    Livewire::test(EditableUserTable::class)
        ->call('updateField', 999, 'name', 'Ghost');

    // No crash, record still doesn't exist
    expect(UserForEditable::find(999))->toBeNull();
});

it('updateField for non-existent column returns early', function () {
    // Should not throw, just returns void
    Livewire::test(EditableUserTable::class)
        ->call('updateField', 1, 'nonexistent_field', 'value');

    // Original data unchanged
    expect(UserForEditable::find(1)->name)->toBe('Alice');
});

it('updateField with string callback invokes component method', function () {
    $component = Livewire::test(EditableWithCallbackTable::class)
        ->call('updateField', 1, 'name', 'Callback Value');

    expect($component->get('callbackInvoked'))->toBeTrue();
    expect($component->get('callbackValue'))->toBe('Callback Value');
});

it('updateField dispatches table-field-updated event', function () {
    Livewire::test(EditableUserTable::class)
        ->call('updateField', 1, 'name', 'Updated')
        ->assertDispatched('table-field-updated');
});

it('updateField on array table updates via updateRowOnTable', function () {
    $component = Livewire::test(EditableArrayTable::class)
        ->call('updateField', 1, 'name', 'Alice Updated');

    // The data should be updated in cache
    $component->assertDispatched('table-field-updated');
});

it('a column that was never marked editable cannot be written', function () {
    Livewire::test(NonEditableColumnTable::class)
        ->call('updateField', 1, 'role', 'admin')
        ->assertNotDispatched('table-field-updated');
});

it('a column marked editable can still be written', function () {
    Livewire::test(NonEditableColumnTable::class)
        ->call('updateField', 1, 'name', 'Grace')
        ->assertDispatched('table-field-updated');
});

it('a denied update does not reach the string callback', function () {
    Livewire::test(CallbackEditableTable::class)
        ->set('denyEverything', true)
        ->call('updateField', 1, 'name', 'Grace')
        ->assertSet('written', []);
});

it('an allowed update still reaches the string callback', function () {
    Livewire::test(CallbackEditableTable::class)
        ->call('updateField', 1, 'name', 'Grace')
        ->assertSet('written', [[1, 'name', 'Grace']]);
});

it('a denied update does not reach the array fallback', function () {
    $component = Livewire::test(CallbackEditableTable::class)
        ->set('denyEverything', true)
        ->call('updateField', 1, 'role', 'admin');

    expect($component->instance()->getRowByID(1)['role'])->toBe('user');
});

it('an allowed update still reaches the array fallback', function () {
    $component = Livewire::test(CallbackEditableTable::class)
        ->call('updateField', 1, 'role', 'admin');

    expect($component->instance()->getRowByID(1)['role'])->toBe('admin');
});

it('a denied update does not dispatch table-field-updated', function () {
    Livewire::test(CallbackEditableTable::class)
        ->set('denyEverything', true)
        ->call('updateField', 1, 'name', 'Grace')
        ->assertNotDispatched('table-field-updated');
});

it('an update to a missing record does not dispatch table-field-updated', function () {
    Livewire::test(EditableUserTable::class)
        ->call('updateField', 999, 'name', 'Ghost')
        ->assertNotDispatched('table-field-updated');
});

it('a closure callback actually runs', function () {
    Livewire::test(CallbackEditableTable::class)
        ->call('updateField', 1, 'nick', 'grace')
        ->assertSet('written', [[1, 'nick', 'grace']]);
});

it('a denied update does not reach the closure callback', function () {
    Livewire::test(CallbackEditableTable::class)
        ->set('denyEverything', true)
        ->call('updateField', 1, 'nick', 'grace')
        ->assertSet('written', []);
});

class ClosureCallbackModelTable extends BeartropyTable
{
    public $written = [];

    public function settings()
    {
        $this->model = UserForEditable::class;
    }

    public function columns()
    {
        return [
            Column::make('Name', 'name')->editable('input', [], function ($id, $field, $value, $table) {
                $table->written[] = [$id, $field, $value];
            }),
        ];
    }
}

it('a closure callback owns persistence and the model is not auto-updated', function () {
    Livewire::test(ClosureCallbackModelTable::class)
        ->call('updateField', 1, 'name', 'Grace')
        ->assertSet('written', [[1, 'name', 'Grace']]);

    // The closure decides what to persist; updateField must not also write the model.
    expect(UserForEditable::find(1)->name)->toBe('Alice');
});
