<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MasterItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterItemCategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_items_can_select_replace_and_clear_multiple_categories()
    {
        $first = Category::create(['name' => 'Medicine', 'code' => 'MED']);
        $second = Category::create(['name' => 'Equipment', 'code' => 'EQ']);
        $payload = ['nama' => 'Test item', 'harga_beli' => 100, 'laba' => 10, 'supplier' => 'Tokopaedi', 'jenis' => 'Obat'];
        $this->get('/master-items/form/new')->assertOk()->assertSee('Medicine')->assertSee('multipart/form-data');
        $this->post('/master-items/form/new', $payload + ['categories' => [$first->id, $second->id]])->assertRedirect('/master-items');
        $item = MasterItem::firstOrFail();
        $this->assertCount(2, $item->categories);
        $this->get('/master-items/form/edit/'.$item->id)->assertOk()->assertSee('checked');
        $this->get('/master-items/view/'.$item->kode)->assertOk()->assertSee('Medicine')->assertSee('Equipment');
        $this->get('/master-items/search')->assertJsonCount(2, 'data.0.categories');
        $this->get('/categories')->assertOk()->assertSee('Test item')->assertSee('master-items/view/'.$item->kode);
        $this->post('/master-items/form/edit/'.$item->id, $payload + ['categories' => [$second->id]])->assertRedirect('/master-items');
        $this->assertEquals([$second->id], $item->fresh()->categories->modelKeys());
        $this->post('/master-items/form/edit/'.$item->id, $payload)->assertRedirect('/master-items');
        $this->assertCount(0, $item->fresh()->categories);
    }

    public function test_invalid_category_does_not_create_item()
    {
        $this->post('/master-items/form/new', [
            'nama' => 'Test', 'harga_beli' => 100, 'laba' => 10,
            'supplier' => 'Tokopaedi', 'jenis' => 'Obat', 'categories' => [999],
        ])->assertSessionHasErrors('categories.0');
        $this->assertDatabaseCount('master_items', 0);
    }
}
