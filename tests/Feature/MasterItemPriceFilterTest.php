<?php

namespace Tests\Feature;

use App\Models\MasterItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterItemPriceFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_price_bounds_are_independent_and_inclusive()
    {
        foreach ([0, 1, 2000, 20000] as $price) {
            $item = new MasterItem;
            $item->forceFill(['kode' => (string) $price, 'nama' => 'Item', 'harga_beli' => $price, 'laba' => 10, 'supplier' => 'Supplier', 'jenis' => 'Obat'])->save();
        }
        foreach ([
            'hargamin=1&hargamax=2000' => [1, 2000],
            'hargamin=2000&hargamax=' => [2000, 20000],
            'hargamin=&hargamax=1' => [0, 1],
            'hargamin=0&hargamax=0' => [0],
            'hargamin=&hargamax=' => [0, 1, 2000, 20000],
            'hargamin=2&hargamax=1999' => [],
        ] as $query => $expected) {
            $response = $this->getJson('/master-items/search?kode=&nama=&'.$query)->assertOk();
            $this->assertSame($expected, array_column($response->json('data'), 'harga_beli'));
        }
        $this->getJson('/master-items/search?hargamin=2000&hargamax=1')->assertUnprocessable()->assertJsonValidationErrors('hargamax');
        $this->getJson('/master-items/search?hargamin=abc')->assertUnprocessable()->assertJsonValidationErrors('hargamin');
    }
}
