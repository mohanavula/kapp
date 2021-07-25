<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Reviews extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Table to model feedback on Subject, Regulation and Curriculum.
         */
        Schema::create('reviews', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->unsignedBigInteger('regulation_id')->nullable();
            $table->unsignedBigInteger('specialization_id')->nullable();
            $table->enum('category', ['regulation', 'curriculum', 'syllabus']);
            $table->string('author_email');
            $table->unsignedBigInteger('stars');
            $table->longText('review')->nullable();
            $table->timestamps();
            $table->foreign('subject_id', 'f_reviews_subject_id')
                ->references('id')
                ->on('subjects')
                ->onDelete('cascade');
            $table->foreign('regulation_id', 'f_reviews_regulation_id')
                ->references('id')
                ->on('regulations')
                ->onDelete('cascade');
            $table->foreign('specialization_id', 'f_reviews_specialization_id')
                ->references('id')
                ->on('specializations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reviews', function(Blueprint $table) {
            $table->dropForeign('f_reviews_subject_id');
            $table->dropForeign('f_reviews_regulation_id');
            $table->dropForeign('f_reviews_specialization_id');
        });
        
        Schema::dropIfExists('reviews');
    }
}
