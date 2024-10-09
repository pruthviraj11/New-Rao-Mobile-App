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
        Schema::create('faq_categories', function (Blueprint $table) {
            $table->id(); // Auto-incrementing ID
            $table->string('name')->nullable(); // Name field
            $table->text('description')->nullable(); // Description field
            $table->timestamps(); // Created and updated timestamps
            $table->softDeletes(); // Soft delete column
            $table->tinyInteger('status')->nullable()->default(1); // Status field
            $table->string('category_id')->nullable(); // Category ID field
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faq_categories');
    }
};
