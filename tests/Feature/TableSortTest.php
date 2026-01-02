<?php

use Beartropy\Tables\YATBaseTable;
use Beartropy\Tables\Classes\Columns\Column;
use Livewire\Livewire;
use Illuminate\Database\Eloquent\Model;

class UserForSort extends Model
{
    protected $table = 'users';
    protected $guarded = [];
}

class UserTableForSort extends YATBaseTable
{
    public function settings()
    {
        $this->model = UserForSort::class;
    }

    public function columns()
    {
        return [
            Column::make('Name')->sortable(),
            Column::make('Email'),
        ];
    }
}

beforeEach(function () {
    UserForSort::create(['name' => 'Alice', 'email' => 'alice@example.com']);
    UserForSort::create(['name' => 'Charlie', 'email' => 'charlie@example.com']);
    UserForSort::create(['name' => 'Bob', 'email' => 'bob@example.com']);
});

it('can sort by column', function () {
    expect(UserForSort::count())->toBe(3);

    $component = Livewire::test(UserTableForSort::class)
        ->assertSee('Alice')
        ->call('sortBy', 'name');

    $html = $component->html();

    // Debug output if fails
    /* if (strpos($html, 'Alice') === false) {
        dd($html);
    } */
    expect(strpos($html, 'Alice'))->toBeLessThan(strpos($html, 'Bob'));
    expect(strpos($html, 'Bob'))->toBeLessThan(strpos($html, 'Charlie'));

    $component->call('sortBy', 'name'); // Toggle direction

    $html = $component->html();
    expect(strpos($html, 'Charlie'))->toBeLessThan(strpos($html, 'Bob'));
    expect(strpos($html, 'Bob'))->toBeLessThan(strpos($html, 'Alice'));
});
