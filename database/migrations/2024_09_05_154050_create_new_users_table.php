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
        Schema::create('new_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('role_id')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('avatar')->nullable()->default('users/default.png');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('remember_token')->nullable();
            $table->text('settings')->nullable();
            $table->timestamps();  // created_at and updated_at fields
            $table->bigInteger('phone_number')->nullable();
            $table->integer('country_code')->nullable();
            $table->tinyInteger('verified_for_api')->nullable();
            $table->bigInteger('verification_code')->nullable();
            $table->timestamp('code_expiry')->nullable();
            $table->integer('code_attempts')->nullable();
            $table->integer('user_category')->nullable();
            $table->string('passport_expiry')->nullable();
            $table->string('test_name')->nullable();
            $table->string('test_expiry')->nullable();
            $table->text('refresh_token')->nullable();
            $table->string('imm_no')->nullable();
            $table->string('app_no')->nullable();
            $table->string('regimm_no')->nullable();
            $table->integer('advisor_id')->nullable();
            $table->integer('advisor_user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->string('imm_id')->nullable();
            $table->string('chat_status')->nullable();
            $table->string('dob')->nullable();
            $table->bigInteger('reporting_to')->nullable();
            $table->string('device_token')->nullable();
            $table->string('uuid')->nullable();
            $table->tinyInteger('is_block_user')->nullable()->default(0);
            $table->tinyInteger('home_screen')->nullable()->default(1);
            $table->tinyInteger('profile_screen')->nullable()->default(1);
            $table->tinyInteger('consultant_screen')->nullable()->default(1);
            $table->tinyInteger('consulting_journy_screen')->nullable()->default(1);
            $table->tinyInteger('our_services_screen')->nullable()->default(1);
            $table->tinyInteger('success_stories_screen')->nullable()->default(1);
            $table->tinyInteger('faq_screen')->nullable()->default(1);
            $table->tinyInteger('need_help_screen')->nullable()->default(1);
            $table->tinyInteger('privacy_policy_screen')->nullable()->default(1);
            $table->integer('status')->nullable()->default(1);
            $table->integer('assign_to')->nullable();
            $table->timestamp('login_at')->nullable();
            $table->timestamp('logout_at')->nullable();
            $table->string('is_download')->nullable()->default('No');
            $table->timestamp('download_date')->nullable();
            $table->softDeletes();  // deleted_at field for soft deletes
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('new_users');
    }
};
