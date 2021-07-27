<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MarksTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('marks', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedInteger('im')->nullable();
            $table->unsignedInteger('em')->nullable();
            $table->unsignedInteger('credits')->nullable();
            $table->string('grade', 2)->nullable();
            $table->boolean('passed');
            $table->boolean('required')->default(true);
            $table->foreign('student_id', 'f_marks_student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');
            $table->foreign('subject_id', 'f_marks_subject_id')
                ->references('id')
                ->on('subjects')
                ->onDelete('cascade'); 
            $table->unique(['student_id', 'subject_id']);
        });

        Schema::create('aggregates', function(Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('semester_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedFloat('sgpa');
            $table->unsignedFloat('cgpa');
            $table->unsignedFloat('semester_credits');
            $table->unsignedFloat('cumulative_credits');
            $table->foreign('semester_id', 'f_aggregates_semester_id')
                ->references('id')
                ->on('semesters')
                ->onDelete('cascade');
            $table->foreign('student_id', 'f_aggregates_student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');
            $table->unique(['semester_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('marks', function(Blueprint $table) {
            $table->dropForeign('f_marks_student_id');
            $table->dropForeign('f_marks_subject_id');
        });

        Schema::table('aggregates', function(Blueprint $table) {
            $table->dropForeign('f_aggregates_student_id');
            $table->dropForeign('f_aggregates_semester_id');
        });

        Schema::dropIfExists('marks');
        Schema::dropIfExists('aggregates');
    }
}
