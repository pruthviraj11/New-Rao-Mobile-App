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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('full_access', 255)->nullable()->change();
            $table->string('partial_access', 255)->nullable()->change();
            $table->string('restriction_access', 255)->nullable()->change();
            $table->string('dymanager_manager', 255)->nullable()->change();
            $table->string('pearo', 255)->nullable()->change();
            $table->string('adviser', 255)->nullable()->change();
            $table->string('client', 255)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            // Revert changes if needed; adjust as necessary
            $table->boolean('full_access')->nullable()->change();
            $table->boolean('partial_access')->nullable()->change();
            $table->boolean('restriction_access')->nullable()->change();
            $table->string('dymanager_manager')->nullable()->change();
            $table->string('pearo')->nullable()->change();
            $table->string('adviser')->nullable()->change();
            $table->string('client')->nullable()->change();
        });
    }
};
