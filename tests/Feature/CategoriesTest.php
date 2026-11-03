<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\MasterItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoriesTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_crud_pages_and_validation()
    {
        $this->get('/categories')->assertOk()->assertSee('Belum ada kategori.')->assertSee('Master Item');
        $this->get('/categories/create')->assertOk();
        $this->post('/categories', [])->assertSessionHasErrors(['name', 'code']);
        $this->post('/categories', ['name' => str_repeat('a', 256), 'code' => str_repeat('b', 256)])
            ->assertSessionHasErrors(['name', 'code']);
        $this->post('/categories', ['name' => 'Medicine', 'code' => 'MED'])
            ->assertRedirect('/categories/1');
        $this->get('/categories/1')->assertOk()->assertSee('Medicine');
        $this->get('/categories/1/edit')->assertOk()->assertSee('MED');
        $this->put('/categories/1', ['name' => 'Equipment', 'code' => 'EQ'])
            ->assertRedirect('/categories/1');
        $this->assertDatabaseHas('categories', ['id' => 1, 'name' => 'Equipment', 'code' => 'EQ']);
        $this->delete('/categories/1')->assertRedirect('/categories');
        $this->assertDatabaseMissing('categories', ['id' => 1]);
        $this->get('/categories/1')->assertNotFound();
    }

    public function test_deleting_category_removes_links_but_preserves_items()
    {
        $category = Category::create(['name' => 'Medicine', 'code' => 'MED']);
        $item = new MasterItem;
        $item->forceFill([
            'kode' => '00001', 'nama' => 'Item', 'harga_beli' => 100,
            'laba' => 10, 'supplier' => 'Supplier', 'jenis' => 'Obat',
        ])->save();
        $category->items()->attach($item);
        $this->delete(route('categories.destroy', $category))->assertRedirect('/categories');
        $this->assertDatabaseCount('categories_item', 0);
        $this->assertDatabaseHas('master_items', ['id' => $item->id]);
    }
}
