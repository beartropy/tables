<?php

use Beartropy\Tables\Classes\Columns\Column;
use Beartropy\Tables\Traits\Data;
use Beartropy\Tables\Traits\Columns;

class TestTableWithData {
    use Data, Columns;
    
    public $model = null;
    public $custom_column_id = 'id';
    
    public function columns() {
        return [
            Column::make('Name', 'name')
                ->cardTitle(function($row) {
                    return 'Title: ' . $row['name'];
                }),
            Column::make('Email', 'email')
        ];
    }
}

beforeEach(function () {
    Column::resetStaticKeys();
});

it('calculates card title from callback in transformRow', function () {
    $table = new TestTableWithData();
    // Simulate mount/setColumns
    $table->columns = collect($table->columns());
    
    $row = ['id' => 1, 'name' => 'John Doe', 'email' => 'john@example.com'];
    
    // Get fresh columns to access closures before they might be stripped (in real app)
    // Here we just use the table instance which uses Data trait
    $cardTitleCallbacks = $table->getCardTitleCallbacks();
    
    expect($cardTitleCallbacks)->toHaveKey('name');
    
    $transformed = $table->transformRow($row, [], [], [], $cardTitleCallbacks);
    
    expect($transformed)->toHaveKey('name_card_title');
    expect($transformed['name_card_title'])->toBe('Title: John Doe');
});
