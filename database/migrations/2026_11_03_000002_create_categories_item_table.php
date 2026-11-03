<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_categories')->constrained('categories');
            $table->foreignId('id_master_item')->constrained('master_items');
            $table->timestamps();

            $table->unique(['id_categories', 'id_master_item']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories_item');
    }
};
