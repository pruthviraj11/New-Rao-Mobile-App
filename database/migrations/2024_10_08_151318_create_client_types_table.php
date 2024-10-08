<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_types', function (Blueprint $table) {
            $table->id(); // This creates an auto-incrementing ID field
            $table->string('name')->nullable();
            $table->string('displayname')->nullable();
            $table->string('status')->nullable();
            $table->timestamps(); // This creates created_at and updated_at fields
            $table->softDeletes(); // This creates deleted_at field
            $table->unsignedBigInteger('created_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('client_types');
    }
};
