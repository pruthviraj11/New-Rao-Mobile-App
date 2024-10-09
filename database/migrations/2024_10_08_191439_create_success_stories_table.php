<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('success_stories', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('short_description')->nullable();
            $table->longText('long_description')->nullable();
            $table->string('file')->nullable();
            $table->date('date');
            $table->string('candidate_name');
            $table->text('candidate_image')->nullable();
            $table->string('candidate_type')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('for_home')->default(0);
            $table->string('ratings')->nullable();
            $table->text('video_thumbnail')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('success_stories');
    }
};
