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
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->integer('faq_category_id')->nullable();
            $table->text('title')->nullable();
            $table->longText('answer')->nullable();
            $table->integer('category_id')->nullable();
            $table->timestamps(); // includes created_at and updated_at
            $table->softDeletes(); // for deleted_at
            $table->tinyInteger('status')->default(1);
            $table->integer('sequence')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('faqs');
    }
};
