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
        Schema::create('internal_program_statuses', function (Blueprint $table) {
            $table->id(); // auto_increment ID
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('status')->nullable();
            $table->integer('order')->nullable();
            $table->integer('category_id')->nullable();
            $table->timestamps(); // created_at and updated_at
            $table->softDeletes(); // deleted_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('internal_programs');
    }
};
