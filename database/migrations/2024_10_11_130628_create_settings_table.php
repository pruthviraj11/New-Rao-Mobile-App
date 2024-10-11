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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('full_access')->nullable();
            $table->boolean('partial_access')->nullable();
            $table->boolean('restriction_access')->nullable();
            $table->string('dymanager_manager')->nullable();
            $table->string('pearo')->nullable();
            $table->string('adviser')->nullable();
            $table->string('client')->nullable();
            $table->timestamps(); // This adds created_at and updated_at
            $table->softDeletes(); // This adds deleted_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
};
