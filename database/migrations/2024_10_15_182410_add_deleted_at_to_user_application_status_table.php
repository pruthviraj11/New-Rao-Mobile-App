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
        Schema::table('user_application_status', function (Blueprint $table) {
            $table->softDeletes(); // This will add the deleted_at column
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_application_status', function (Blueprint $table) {
            $table->dropSoftDeletes(); // This will drop the deleted_at column
        });
    }
};
