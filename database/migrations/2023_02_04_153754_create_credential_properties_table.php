<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCredentialPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credential_properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credential_id')
                  ->constrained()
                  ->cascadeOnUpdate()
                  ->cascadeOnDelete();
            $table->foreignId('platform_type_id')
                  ->constrained()
                  ->cascadeOnUpdate()
                  ->restrictOnDelete();
            $table->text('answer')->nullable();
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
        Schema::dropIfExists('credential_properties');
    }
}
