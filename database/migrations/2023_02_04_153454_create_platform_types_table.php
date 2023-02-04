<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlatformTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platform_types', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->foreignId('platform_id')
                  ->constrained()
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->foreignId('type_id')
                  ->constrained()
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('platform_types');
    }
}
