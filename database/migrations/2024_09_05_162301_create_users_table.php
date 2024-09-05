<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id')->nullable();
            $table->string('name', 255);
            $table->string('email', 255);
            $table->string('avatar', 255)->default('users/default.png');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password', 255);
            $table->rememberToken();
            $table->text('settings')->nullable();
            $table->unsignedBigInteger('phone_number')->nullable();
            $table->integer('country_code')->nullable();
            $table->tinyInteger('verified_for_api')->nullable();
            $table->unsignedBigInteger('verification_code')->nullable();
            $table->timestamp('code_expiry')->nullable();
            $table->integer('code_attempts')->nullable();
            $table->integer('user_category')->nullable();
            $table->string('passport_expiry', 100)->nullable();
            $table->string('test_name', 100)->nullable();
            $table->string('test_expiry', 100)->nullable();
            $table->text('refresh_token')->nullable();
            $table->string('imm_no', 100)->nullable();
            $table->string('app_no', 100)->nullable();
            $table->string('regimm_no', 100)->nullable();
            $table->integer('advisor_id')->nullable();
            $table->integer('advisor_user_id')->nullable();
            $table->string('user_name', 100)->nullable();
            $table->string('imm_id', 100)->nullable();
            $table->string('chat_status', 100)->nullable();
            $table->string('dob', 200)->nullable();
            $table->unsignedBigInteger('reporting_to')->nullable();
            $table->string('device_token', 255)->nullable();
            $table->string('uuid', 255)->nullable();
            $table->tinyInteger('is_block_user')->default(0);
            $table->tinyInteger('home_screen')->default(1);
            $table->tinyInteger('profile_screen')->default(1);
            $table->tinyInteger('consultant_screen')->default(1);
            $table->tinyInteger('consulting_journy_screen')->default(1);
            $table->tinyInteger('our_services_screen')->default(1);
            $table->tinyInteger('success_stories_screen')->default(1);
            $table->tinyInteger('faq_screen')->default(1);
            $table->tinyInteger('need_help_screen')->default(1);
            $table->tinyInteger('privacy_policy_screen')->default(1);
            $table->integer('status')->default(1);
            $table->integer('assign_to')->nullable();
            $table->timestamp('login_at')->nullable();
            $table->timestamp('logout_at')->nullable();
            $table->string('is_download', 100)->default('No');
            $table->timestamp('download_date')->nullable();
            $table->timestamps(); // created_at, updated_at


            $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
