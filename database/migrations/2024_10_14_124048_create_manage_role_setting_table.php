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
        Schema::create('manage_role_setting', function (Blueprint $table) {
            $table->id();
            $table->string('full_access')->nullable();
            $table->string('partial_access')->nullable();
            $table->string('restriction_access')->nullable();
            $table->string('dymanager_manager')->nullable();
            $table->string('pearo')->nullable();
            $table->string('adviser')->nullable();
            $table->string('client')->nullable();
            $table->timestamps(); // Adds created_at and updated_at
            $table->softDeletes(); // Adds deleted_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manage_role_setting');
    }
};
