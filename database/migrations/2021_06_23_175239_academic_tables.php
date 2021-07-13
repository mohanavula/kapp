<?php

use App\Helpers\Constants;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AcademicTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Table to model Program level. Eg: UG
         */
        Schema::create('program_levels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('short_name', Constants::TITLE_SHORT_LENGTH)->unique();
            $table->string('name', Constants::TITLE_LENGTH);
            $table->timestamps();
        });

        /**
         * Table to model Program. Eg: B.Tech
         */
        Schema::create('programs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('program_level_id')->index();
            $table->string('short_name', Constants::TITLE_SHORT_LENGTH)->unique();
            $table->string('name', Constants::TITLE_LENGTH);
            $table->timestamps();
            $table->foreign('program_level_id', 'f_programs_program_level_id')
                ->references('id')
                ->on('program_levels')
                ->onDelete('cascade');
        });

        /**
         * Table to model Regulation. Eg: R14UG
         */
        Schema::create('regulations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('program_id')->index();
            $table->string('short_name', Constants::TITLE_SHORT_LENGTH)->unique();
            $table->string('name', Constants::TITLE_LENGTH);
            $table->year('start_year');
            $table->year('end_year')->nullable();
            $table->unsignedBigInteger('total_semesters');
            $table->unsignedBigInteger('total_credits');
            $table->unsignedDecimal('pass_cgpa');
            $table->boolean('in_force')->default(false);
            $table->timestamps();
            $table->foreign('program_id', 'f_regulations_program_id')
                ->references('id')
                ->on('programs')
                ->onDelete('cascade');
        });

        /**
         * Table to model Semester. Eg: First Semester
         */
        Schema::create('semesters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('regulation_id')->index();
            $table->string('short_name', Constants::TITLE_SHORT_LENGTH);
            $table->string('name', Constants::TITLE_LENGTH);
            $table->unsignedBigInteger('semester_number');
            $table->boolean('in_force')->default(false);
            $table->timestamps();
            $table->unique(['regulation_id', 'semester_number']);
            $table->foreign('regulation_id', 'f_semesters_regulation_id')
                ->references('id')
                ->on('regulations')
                ->onDelete('cascade');
        });

        /**
         * Table to model Department. Eg: CE
         */
        Schema::create('departments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('short_name', Constants::TITLE_SHORT_LENGTH)->unique();
            $table->string('name', Constants::TITLE_LENGTH);
            $table->string('office_email', Constants::EMAIL_LENGTH);
            $table->string('hod_email', Constants::EMAIL_LENGTH);
            $table->timestamps();
        });

        /**
         * Table to model Subject offering type. Eg: Core
         */
        Schema::create('subject_offering_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('description', Constants::TITLE_LENGTH);
            $table->timestamps();
        });

        /**
         * Table to model Subject. Eg: Engineering Mechanics
         */
        Schema::create('subjects', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('subject_code', Constants::TITLE_SHORT_LENGTH)->unique();
            $table->string('short_name', Constants::TITLE_SHORT_LENGTH);
            $table->string('name', Constants::TITLE_LENGTH);
            $table->unsignedBigInteger('department_id');
            $table->boolean('is_theory')->default(true);
            $table->boolean('is_lab')->default(false);
            $table->boolean('is_project')->default(false);
            $table->timestamps();
            $table->foreign('department_id', 'f_subjects_department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('cascade');
        });

        /**
         * Table to model Syllabus.
         */
        Schema::create('syllabus', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('excerpt');
            $table->longText('objectives');
            $table->longText('cos');
            $table->longText('syllabus');
            $table->longText('textbooks');
            $table->longText('reference_books');
            $table->unsignedBigInteger('subject_id');
            $table->timestamps();
            $table->foreign('subject_id', 'f_syllabus_subject_id')
                ->references('id')
                ->on('subjects')
                ->onDelete('cascade');
        });
        
        /**
         * Table to model feedback on Subject. Eg. Reviews, ratings, resources
         */
        Schema::create('subject_feedback', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('category', ['review', 'rating', 'resource']);
            $table->string('author_email');
            $table->longText('data');
            $table->unsignedBigInteger('subject_id');
            $table->timestamps();
            $table->foreign('subject_id', 'f_subject_feedback_subject_id')
                ->references('id')
                ->on('subjects')
                ->onDelete('cascade');
        });


        /**
         * Table to model Specialization (Branch). Eg: Civil Engineering
         */
        Schema::create('specializations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('short_name', Constants::TITLE_SHORT_LENGTH)->unique();
            $table->string('name', Constants::TITLE_LENGTH);
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('program_id');
            $table->boolean('in_force')->default(false);
            $table->timestamps();
            $table->foreign('department_id', 'f_specializations_department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('cascade');
            $table->foreign('program_id', 'f_specializations_program_id')
                ->references('id')
                ->on('programs')
                ->onDelete('cascade');
        });

        /**
         * Table to model subject category. Eg: PCC
         */
        Schema::create('subject_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('regulation_id');
            $table->string('short_name');
            $table->unique(['regulation_id', 'short_name']);
            $table->string('name');
            $table->timestamps();
            $table->foreign('regulation_id', 'f_instruction_schemes_regulation_id')
                ->references('id')
                ->on('regulations')
                ->onDelete('cascade');
        });

        /**
         * Table to model curriculum. This is master table
         */
        Schema::create('curricula', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('specialization_id');
            $table->unsignedBigInteger('semester_id');
            $table->unsignedBigInteger('subject_category_id');
            $table->unsignedBigInteger('subject_offering_type_id');
            $table->unsignedInteger('lectures');
            $table->unsignedInteger('tutorials');
            $table->unsignedInteger('practicals');
            $table->unsignedInteger('internal_exam_marks');
            $table->unsignedInteger('end_exam_marks');
            $table->unsignedInteger('credits');
            $table->unsignedBigInteger('sequence_number');
            $table->timestamps();
            $table->foreign('specialization_id', 'f_curricula_specialization_id')
                ->references('id')
                ->on('specializations')
                ->onDelete('cascade');
            $table->foreign('subject_offering_type_id', 'f_curricula_subject_offering_type_id')
                ->references('id')
                ->on('subject_offering_types')
                ->onDelete('cascade');
            $table->foreign('subject_category_id', 'f_curricula_subject_category_id')
                ->references('id')
                ->on('subject_categories')
                ->onDelete('cascade');
            $table->foreign('semester_id', 'f_curricula_semester_id')
                ->references('id')
                ->on('semesters')
                ->onDelete('cascade');
        });

        /**
         * Table to model curriculum. This is join table b/w curriculum and subjects
         */
        Schema::create('curriculum_subject', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('curriculum_id');
            $table->unsignedBigInteger('subject_id');
            $table->timestamps();
            $table->unique(['curriculum_id', 'subject_id'], 'u_curriculum_id_subject_id');
            $table->foreign('curriculum_id', 'f_curriculum_subjects_curriculum_id')
                ->references('id')
                ->on('curricula')
                ->onDelete('cascade');
            $table->foreign('subject_id', 'f_curriculum_subjects_subject_id')
                ->references('id')
                ->on('subjects')
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
        Schema::table('syllabus', function(Blueprint $table) {
            $table->dropForeign('f_syllabus_subject_id');
        });
        
        Schema::table('subject_reviews', function(Blueprint $table) {
            $table->dropForeign('f_subject_reviews_subject_id');
        });

        Schema::table('instruction_scheme_subject', function (Blueprint $table) {
            $table->dropForeign('f_instruction_scheme_subject_instruction_scheme_id');
            $table->dropForeign('f_instruction_scheme_subject_subject_id');
        });
        
        Schema::table('instruction_schemes', function (Blueprint $table) {
            $table->dropForeign('f_instruction_schemes_subject_offering_type_id');
            $table->dropForeign('f_instruction_schemes_subject_category_id');
            $table->dropForeign('f_instruction_schemes_semester_id');
        });

        Schema::table('specializations', function (Blueprint $table) {
            $table->dropForeign('f_specializations_department_id');
            $table->dropForeign('f_specializations_program_id');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropForeign('f_subjects_department_id');
        });

        Schema::table('semesters', function (Blueprint $table) {
            $table->dropForeign('f_semesters_regulation_id');
        });

        Schema::table('regulations', function (Blueprint $table) {
            $table->dropForeign('f_regulations_program_id');
        });
        
        Schema::table('programs', function (Blueprint $table) {
            $table->dropForeign('f_programs_program_level_id');
        });
        
        Schema::dropIfExists('syllabus');
        Schema::dropIfExists('subject_reviews');
        Schema::dropIfExists('instruction_scheme_subject');
        Schema::dropIfExists('instruction_schemes');
        Schema::dropIfExists('subject_catgories');
        Schema::dropIfExists('specializations');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('subject_offering_types');
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('regulations');
        Schema::dropIfExists('programs');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('program_levels');
    }
}