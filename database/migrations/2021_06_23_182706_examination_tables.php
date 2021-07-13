<?php

use App\Helpers\Constants;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ExaminationTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * Table to model Designations. Eg: Assistant Professor
         */
        Schema::create('designations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('short_name', Constants::TITLE_SHORT_LENGTH)->unique();
            $table->string('name', Constants::TITLE_LENGTH);
            $table->timestamps();
        });

        /**
         * Table to model Faculty.
         */
        Schema::create('faculties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('employee_id', Constants::REGDNO_LENGTH)->unique();
            $table->string('surname', Constants::TITLE_LENGTH);
            $table->string('given_name', Constants::TITLE_LENGTH);
            $table->string('email', Constants::EMAIL_LENGTH)->unique();
            $table->string('phone', Constants::PHONE_LENGTH)->unique();
            $table->enum('gender', ['FEMALE', 'MALE','OTHER']);
            $table->timestamps();
            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id', 'f_faculties_department_id')
                ->references('id')
                ->on('departments')
                ->onDelete('cascade');
            $table->unsignedBigInteger('designation_id');
            $table->foreign('designation_id', 'f_faculties_designation_id')
                ->references('id')
                ->on('designations')
                ->onDelete('cascade');
        });

        /**
         * Table to model Student.
         */
        Schema::create('students', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('regdno', Constants::REGDNO_LENGTH)->unique();
            $table->string('surname', Constants::TITLE_LENGTH);
            $table->string('given_name', Constants::TITLE_LENGTH);
            $table->string('email', Constants::EMAIL_LENGTH)->unique();
            $table->string('phone', Constants::PHONE_LENGTH)->unique()->nullable();
            $table->date('dob')->nullable();
            $table->year('join_year');
            $table->enum('social_category', ['OC', 'BC-A', 'BC-B', 'BC-C', 'BC-D', 'BC-E', 'EWS', 'SC', 'ST', 'OTHER']);
            $table->enum('admission_category', ['CONVENER', 'MANAGEMENT', 'SPOT', 'OTHER']);
            $table->enum('residency', ['HOSTEL', 'DAYS'])->nullable();
            $table->enum('gender', ['FEMALE', 'MALE','OTHER']);
            $table->boolean('is_lateral_entry')->default(false);
            $table->timestamps();
            $table->unsignedBigInteger('specialization_id');
            $table->foreign('specialization_id', 'f_students_specialization_id')
                ->references('id')
                ->on('specializations')
                ->onDelete('cascade');
            $table->unsignedBigInteger('regulation_id');
            $table->foreign('regulation_id', 'f_students_regulation_id')
                ->references('id')
                ->on('regulations')
                ->onDelete('cascade');
        });

        /**
         * Table to model AcademicClass. Eg: 2020-21-R18UG-5-Sem-CE
         */
        Schema::create('academic_classes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', Constants::TITLE_LENGTH);
            $table->year('academic_year');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedBigInteger('semester_id');
            $table->unsignedBigInteger('specialization_id');
            $table->foreign('semester_id', 'f_academic_classes_semester_id')
                ->references('id')
                ->on('semesters')
                ->onDelete('cascade');
            $table->foreign('specialization_id', 'f_academic_classes_specialization_id')
                ->references('id')
                ->on('specializations')
                ->onDelete('cascade');
            $table->timestamps();
            $table->unique(['academic_year', 'semester_id', 'specialization_id'], 'u_academic_classes');
        });

        /**
         * Table to model AcademicClassSection. Eg: 2020-21-R18UG-5-Sem-CE-A
         */
        Schema::create('academic_class_sections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('academic_class_id');
            $table->string('section', 1);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
            $table->foreign('academic_class_id', 'f_academic_class_sections_academic_class_id')
                ->references('id')
                ->on('academic_classes')
                ->onDelete('cascade');
            $table->unique(['academic_class_id', 'section']);
        });

        /**
         * Table to model Exam. Eg: R18UG-Regular-Exams-of-Nov-2020
         */
        Schema::create('exams', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('semester_id');
            $table->string('short_name', Constants::TITLE_SHORT_LENGTH)->unique();
            $table->string('name', Constants::TITLE_LENGTH);
            $table->year('academic_year');
            $table->enum('status', ['ACTIVE', 'CLOSED', 'CANCELLED'])->default('ACTIVE');
            // $table->date('start_date');
            // $table->date('end_date');
            $table->enum('exam_category', ['REGULAR', 'SUPPLEMENTARY', 'MIDTERM']);
            $table->foreign('semester_id', 'f_exams_semester_id')
                ->references('id')
                ->on('semesters')
                ->onDelete('cascade');
            $table->timestamps();
        });

        /**
         * Table to model ExamSchedule.
         */
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('specialization_id');
            $table->unsignedBigInteger('subject_id');
            $table->dateTime('schedule_date');
            $table->dateTime('exam_date')->nullable();
            $table->foreign('exam_id', 'f_exam_schedules_exam_id')
                ->references('id')
                ->on('exams')
                ->onDelete('cascade');
            $table->foreign('specialization_id', 'f_exam_schedules_specialization_id')
                ->references('id')
                ->on('specializations')
                ->onDelete('cascade');
            $table->foreign('subject_id', 'f_exam_schedules_subject_id')
                ->references('id')
                ->on('subjects')
                ->onDelete('cascade');
            $table->timestamps();
            $table->unique(['exam_id', 'specialization_id', 'subject_id']);
        });

        /**
         * Table to model ExamRegistrationMarks.
         */
        Schema::create('exam_registrations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('exam_schedule_id');
            $table->unsignedBigInteger('student_id');
            $table->boolean('attended_exam')->nullable();
            $table->unsignedBigInteger('marks')->nullable();
            $table->foreign('exam_schedule_id', 'f_exam_registrations_exam_schedule_id')
                ->references('id')
                ->on('exam_schedules')
                ->onDelete('cascade');
            $table->foreign('student_id', 'f_exam_registations_student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');
            $table->timestamps();
            $table->unique(['exam_schedule_id', 'student_id']);
        });

        /**
         * Table to model Internal Examination marks
         */
        Schema::create('internal_exam_marks', function(Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('internal_marks');
            $table->foreign('student_id', 'f_internal_exam_marks_student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');
            $table->foreign('subject_id', 'f_internal_exam_marks_subject_id')
                ->references('id')
                ->on('subjects')
                ->onDelete('cascade');
            $table->unique(['student_id', 'subject_id']);
        });

        /**
         * Table to model End Examination marks
         */
        Schema::create('end_exam_marks', function(Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('end_exam_marks');
            $table->foreign('exam_id', 'f_end_exam_marks_exam_id')
                ->references('id')
                ->on('exams')
                ->onDelete('cascade');
            $table->foreign('student_id', 'f_end_exam_marks_student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');
            $table->foreign('subject_id', 'f_end_exam_marks_subject_id')
                ->references('id')
                ->on('subjects')
                ->onDelete('cascade');
            $table->unique(['exam_id', 'student_id', 'subject_id']);
        });

        /**
         * Table to model StatsGrade.
         */
        Schema::create('stats_grades', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('internal_marks');
            $table->unsignedBigInteger('end_exam_marks');
            $table->string('grade', 2);
            $table->boolean('passed');
            $table->foreign('exam_id', 'f_stats_grades_exam_id')
                ->references('id')
                ->on('exams')
                ->onDelete('cascade');
            $table->foreign('student_id', 'f_stats_grades_student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');
            $table->foreign('subject_id', 'f_stats_grades_subject_id')
                ->references('id')
                ->on('subjects')
                ->onDelete('cascade');
            $table->timestamps();
            $table->unique(['exam_id', 'student_id', 'subject_id']);
        });

        /**
         * Table to model StatsGPA.
         */
        Schema::create('stats_gpas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('exam_id');
            $table->unsignedBigInteger('student_id');
            $table->unsignedDouble('sgpa');
            $table->unsignedDouble('cgpa');
            $table->unsignedDouble('semester_credits');
            $table->unsignedDouble('cumulative_credits');
            $table->foreign('student_id', 'f_stats_gpas_student_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');
            $table->foreign('exam_id', 'f_stats_gpas_exam_id')
                ->references('id')
                ->on('exams')
                ->onDelete('cascade');
            $table->timestamps();
            $table->unique(['exam_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('faculties', function(Blueprint $table) {
            $table->dropForeign('f_faculties_department_id');
            $table->dropForeign('f_faculties_designation_id');
        });
        
        Schema::table('students', function(Blueprint $table) {
            $table->dropForeign('f_students_specialization_id');
            $table->dropForeign('f_students_regulation_id');
        });
        
        Schema::table('academic_classes', function(Blueprint $table) {
            $table->dropForeign('f_academic_classes_semester_id');
            $table->dropForeign('f_academic_classes_specialization_id');
        });
        
        Schema::table('academic_class_sections', function(Blueprint $table) {
            $table->dropForeign('f_academic_class_sections_academic_class_id');
        });

        Schema::table('exams', function(Blueprint $table) {
            $table->dropForeign('f_exams_semester_id');
        });

        Schema::table('exam_schedules', function(Blueprint $table) {
            $table->dropForeign('f_exam_schedules_exam_id');
            $table->dropForeign('f_exam_schedules_subject_id');
        });

        Schema::table('exam_registrations', function(Blueprint $table) {
            $table->dropForeign('f_exam_registrations_exam_schedule_id');
            $table->dropForeign('f_exam_registrations_student_id');
        });

        Schema::table('stats_grades', function(Blueprint $table) {
            $table->dropForeign('f_stats_grades_exam_id');
            $table->dropForeign('f_stats_grades_student_id');
            $table->dropForeign('f_stats_grades_subject_id');
        });

        Schema::table('stats_gpas', function(Blueprint $table) {
            $table->dropForeign('f_stats_gpas_exam_id');
            $table->dropForeign('f_stats_gpas_student_id');
        });
        
        Schema::dropIfExists('stats_gpas');
        Schema::dropIfExists('stats_grades');
        Schema::dropIfExists('exam_registrations');
        Schema::dropIfExists('exam_schedules');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('faculties');
        Schema::dropIfExists('academic_class_sections');
        Schema::dropIfExists('academic_classes');
        Schema::dropIfExists('students');
        Schema::dropIfExists('designations');
    }
}