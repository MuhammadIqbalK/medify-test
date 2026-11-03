<?php

namespace Tests\Feature;

use App\Models\MasterItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MasterItemImageTest extends TestCase
{
    use RefreshDatabase;

    private function payload(): array
    {
        return ['nama' => 'Photo item', 'harga_beli' => 100, 'laba' => 10, 'supplier' => 'Tokopaedi', 'jenis' => 'Obat'];
    }

    public function test_upload_preserve_and_replace_photo()
    {
        Storage::fake('public');
        $this->post('/master-items/form/new', $this->payload() + ['image' => UploadedFile::fake()->image('photo.jpg')])->assertRedirect('/master-items');
        $item = MasterItem::firstOrFail();
        $original = $item->image_url;
        Storage::disk('public')->assertExists(substr($original, strlen('/storage/')));
        $this->get('/master-items/view/'.$item->kode)->assertOk()->assertSee($original);
        $this->get('/master-items/form/edit/'.$item->id)->assertOk()->assertSee($original);
        $this->get('/master-items/search')->assertJsonPath('data.0.image_url', $original);
        $this->post('/master-items/form/edit/'.$item->id, $this->payload())->assertRedirect('/master-items');
        $this->assertSame($original, $item->fresh()->image_url);
        $this->post('/master-items/form/edit/'.$item->id, $this->payload() + ['image' => UploadedFile::fake()->image('new.png')])->assertRedirect('/master-items');
        $replacement = $item->fresh()->image_url;
        $this->assertNotSame($original, $replacement);
        Storage::disk('public')->assertMissing(substr($original, strlen('/storage/')));
        Storage::disk('public')->assertExists(substr($replacement, strlen('/storage/')));
    }

    public function test_invalid_and_oversized_images_are_rejected()
    {
        Storage::fake('public');
        foreach ([UploadedFile::fake()->create('bad.txt', 1, 'text/plain'), UploadedFile::fake()->image('large.jpg')->size(2049)] as $file) {
            $this->post('/master-items/form/new', $this->payload() + ['image' => $file])->assertSessionHasErrors('image');
        }
        $this->assertDatabaseCount('master_items', 0);
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }
}
