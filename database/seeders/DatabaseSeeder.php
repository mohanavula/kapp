<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /**
         * table: program_levels
         */
        $ug_id = DB::table('program_levels')->insertGetId(
            ['short_name' => 'UG', 'name' => 'Undergraduate']
        );

        $pg_id = DB::table('program_levels')->insertGetId(
            ['short_name' => 'PG', 'name' => 'Postgraduate']
        );
        
        /**
         * table: programs
         */
        $btech_id = DB::table('programs')->insertGetId(
            ['program_level_id' => $ug_id, 'short_name' => 'BTech', 'name' => 'Bachelor of Technology']
        );

        $mtech_id = DB::table('programs')->insertGetId(
            ['program_level_id' => $pg_id, 'short_name' => 'MTech', 'name' => 'Master of Technology']
        );

        /**
         * table: regulations
         */
        $r15ug_id = DB::table('regulations')->insertGetId([
            'program_id' => $btech_id,
            'short_name' => 'R15UG',
            'name' => 'Regulations for UG Programs in Engineering (2015)',
            'start_year' => 2015,
            'total_semesters' => 8,
            'total_credits' => 160,
            'pass_cgpa' => 4.5,
            'in_force' => false
        ]);

        $r15pg_id = DB::table('regulations')->insertGetId([
            'program_id' => $mtech_id,
            'short_name' => 'R15PG',
            'name' => 'Regulations for PG Programs in Engineering (2015)',
            'start_year' => 2015,
            'total_semesters' => 4,
            'total_credits' => 60,
            'pass_cgpa' => 4.5,
            'in_force' => false
        ]);

        $r18ug_id = DB::table('regulations')->insertGetId([
            'program_id' => $btech_id,
            'short_name' => 'R18UG',
            'name' => 'Regulations for UG Programs in Engineering (2018)',
            'start_year' => 2018,
            'total_semesters' => 8,
            'total_credits' => 160,
            'pass_cgpa' => 4.5,
            'in_force' => true
        ]);

        $r18pg_id = DB::table('regulations')->insertGetId([
            'program_id' => $mtech_id,
            'short_name' => 'R18PG',
            'name' => 'Regulations for PG Programs in Engineering (2018)',
            'start_year' => 2018,
            'total_semesters' => 4,
            'total_credits' => 60,
            'pass_cgpa' => 4.5,
            'in_force' => true
        ]);

        /**
         * table: semesters
         */
        
         // R15UG - B.Tech
        $data = [
            ['1 Sem', 'First Semester', 1, false],
            ['2 Sem', 'Second Semester', 2, false],
            ['3 Sem', 'Third Semester', 3, false],
            ['4 Sem', 'Fourth Semester', 4, false],
            ['5 Sem', 'Fifth Semester', 5, false],
            ['6 Sem', 'Sixth Semester', 6, false],
            ['7 Sem', 'Seventh Semester', 7, false],
            ['8 Sem', 'Eighth Semester', 8, false],
        ];

        foreach($data as $d) {
            DB::table('semesters')->insert([
                'regulation_id' => $r15ug_id,
                'short_name' => $d[0],
                'name' => $d[1],
                'semester_number' => $d[2],
                'in_force' => $d[3],
            ]);
        };

        
        // R18UG - B.Tech
        $data = [
            ['1 Sem', 'First Semester', 1, false],
            ['2 Sem', 'Second Semester', 2, false],
            ['3 Sem', 'Third Semester', 3, false],
            ['4 Sem', 'Fourth Semester', 4, false],
            ['5 Sem', 'Fifth Semester', 5, true],
            ['6 Sem', 'Sixth Semester', 6, true],
            ['7 Sem', 'Seventh Semester', 7, true],
            ['8 Sem', 'Eighth Semester', 8, true],
        ];

        foreach($data as $d) {
            DB::table('semesters')->insert([
                'regulation_id' => $r18ug_id,
                'short_name' => $d[0],
                'name' => $d[1],
                'semester_number' => $d[2],
                'in_force' => $d[3],
            ]);
        };

        // R15PG - M.Tech
        $data = [
            ['1 Sem', 'First Semester', 1, false],
            ['2 Sem', 'Second Semester', 2, false],
            ['3 Sem', 'Third Semester', 3, false],
            ['4 Sem', 'Fourth Semester', 4, false],
        ];

        foreach($data as $d) {
            DB::table('semesters')->insert([
                'regulation_id' => $r15pg_id,
                'short_name' => $d[0],
                'name' => $d[1],
                'semester_number' => $d[2],
                'in_force' => $d[3],
            ]);
        };

        // R18PG - M.Tech
        $data = [
            ['1 Sem', 'First Semester', 1, true],
            ['2 Sem', 'Second Semester', 2, true],
            ['3 Sem', 'Third Semester', 3, true],
            ['4 Sem', 'Fourth Semester', 4, true],
        ];
        foreach($data as $d) {
            DB::table('semesters')->insert([
                'regulation_id' => $r18pg_id,
                'short_name' => $d[0],
                'name' => $d[1],
                'semester_number' => $d[2],
                'in_force' => $d[3],
            ]);
        };

        /**
         * table: departments
         */
        $data = [
            ['CED', 'Civil Engineering Department', 'office.ce@ksrmce.ac.in', 'hod.ce@ksrmce.ac.in'],
            ['EEED', 'Electrical and Electronics Engineering Department', 'office.eee@ksrmce.ac.in', 'hod.eee@ksrmce.ac.in'],
            ['ECED', 'Electronics and Communications Engineering Department', 'office.ece@ksrmce.ac.in', 'hod.ece@ksrmce.ac.in'],
            ['MED', 'Mechanical Engineering Department', 'office.me@ksrmce.ac.in', 'hod.me@ksrmce.ac.in'],
            ['CSED', 'Computer Science and Engineering Department', 'office.cse@ksrmce.ac.in', 'hod.cse@ksrmce.ac.in'],
            ['CRI', 'Center for Research and Innovation', 'cri@ksrmce.ac.in', 'cri@ksrmce.ac.in'],
            ['HSD', 'Humanities and Sciences Department', 'office.hs@ksrmce.ac.in', 'hod.hs@ksrmce.ac.in']
        ];

        foreach($data as $d) {
            DB::table('departments')->insert([
                'short_name' => $d[0],
                'name' => $d[1],
                'office_email' => $d[2],
                'hod_email' => $d[3],
            ]);
        };

        /**
         * table: subject_offering_types
         */

        $data = [
            'CORE',
            'ELECTIVE',
            'AUDIT',
            'MANDATORY',
            'SKILL',
        ];

        foreach($data as $d) {
            DB::table('subject_offering_types')->insert([
                'description' => $d,
            ]);
        }


        /**
         * table: subjects
         */
        

        /**
         * table: specializations
         */
        $data = [
            ['CE', 'Civil Engineering', 'CED', 'BTech', true],
            ['EEE', 'Electrical and Electronics Engineering', 'EEED', 'BTech', true],
            ['ECE', 'Electronics and Comminications Engineering', 'ECED', 'BTech', true],
            ['ME', 'Mechanical Engineering', 'MED', 'BTech', true],
            ['CSE', 'Computer Science and Engineering', 'MED', 'BTech', true],
        ];
        $dept = '';
        $dept_id = '';
        $prog = '';
        $prog_id = '';
        foreach($data as $d) {
            if ($dept != $d[2]) {
                $dept_id = DB::table('departments')->where('short_name', '=', $d[2])->get()[0]->id;
            };
            if ($prog != $d[3]) {
                $prog_id = DB::table('programs')->where('short_name', '=', $d[3])->get()[0]->id;
            };
            DB::table('specializations')->insert([
                'short_name' => $d[0],
                'name' => $d[1],
                'department_id' => $dept_id,
                'program_id' => $prog_id,
                'in_force' => $d[4],
            ]);
        };

        
        /**
         * table: subject_categories
         */
        $data = [
            ['R15UG', 'BS', 'Basic sciences'],
            ['R15UG', 'ED', 'Engineering design'],
            ['R15UG', 'HS', 'Humanities and social sciences'],
            ['R15UG', 'PJ', 'Professional major'],
            ['R15UG', 'PN', 'Professional minor'],
            ['R18UG', 'BSC', 'Basic Sciences'],
            ['R18UG', 'ESC', 'Engineering Sciences'],
            ['R18UG', 'HSMS', 'Humanities and social sciences'],
            ['R18UG', 'PCC', 'Professional Core'],
            ['R18UG', 'PEC', 'Professional elective'],
            ['R18UG', 'OEC', 'Open elective'],
            ['R18UG', 'MC', 'Mandatory'],
            ['R18UG', 'PROJ', 'Project']
        ];
        $r = '';
        $r_id ='';
        foreach($data as $d) {
            if ($r != $d[0]) {
                $r = $d[0];
                $r_id = DB::table('regulations')->where('short_name', '=', $d[0])->get()[0]->id;
            }
            DB::table('subject_categories')->insert([
                'regulation_id' => $r_id,
                'short_name' => $d[1],
                'name' => $d[2],
            ]);
        };

        /**
         * table: subjects
         */
        $subjects = Excel::toCollection(null, storage_path('subjects.csv'))[0];
        $dept = '';
        $dept_id = '';
        foreach($subjects->splice(1) as $s) {
            if ($dept != $s[3]) {
                $dept = $s[3];
                $dept_id = DB::table('departments')->where('short_name', '=', $s[3])->get()[0]->id;
            }
            DB::table('subjects')->insert([
                'subject_code' => $s[0],
                'short_name' => $s[1],
                'name' => $s[2],
                'department_id' => $dept_id,
                'is_theory' => $s[4],
                'is_lab' => $s[5],
                'is_project' => $s[6],
            ]);
        }

        /**
         * table: curricula
         */
        $curriculum = Excel::toCollection(null, storage_path('curriculum.csv'))[0];
        $specialization = '';
        $specialization_id = '';
        $semester = '';
        $semester_id ='';
        $subject_categoty = '';
        $subject_categoty_id = '';
        $subject_offering_type = '';
        $subject_offering_type_id = '';
        $regulation = '';
        $r_id = '';
 
        foreach ($curriculum->splice(1) as $c) {
            try {
                if ($regulation != $c[1]) {
                    $regulation = $c[1];
                    $r_id = DB::table('regulations')->where('short_name', '=', $c[1])->get()[0]->id;
                }

                if ($specialization != $c[0]) {
                    $specialization = $c[0];
                    $specialization_id = DB::table('specializations')->where('short_name', '=', $c[0])->get()[0]->id;
                }
                if ($semester != $c[2]) {
                    $semester = $c[2];
                    $semester_id = DB::table('semesters')->where('regulation_id', '=', $r_id)->where('semester_number', '=', $c[2])->get()[0]->id;
                }

                if ($subject_categoty != $c[3]) {
                    $subject_categoty = $c[3];
                    $subject_categoty_id = DB::table('subject_categories')->where('regulation_id', '=', $r_id)->where('short_name', '=', $c[3])->get()[0]->id;
                }

                if ($subject_offering_type != $c[4]) {
                    $subject_offering_type = $c[4];
                    $subject_offering_type_id = DB::table('subject_offering_types')->where('description', '=', $c[4])->get()[0]->id;
                }

                DB::table('curricula')->insert([
                    'specialization_id' => $specialization_id,
                    'semester_id' => $semester_id,
                    'subject_category_id' => $subject_categoty_id,
                    'subject_offering_type_id' => $subject_offering_type_id,
                    'lectures' => $c[5],
                    'tutorials' => $c[6],
                    'practicals' => $c[7],
                    'internal_exam_marks' => $c[8],
                    'end_exam_marks' => $c[9],
                    'credits' => $c[10],
                    'sequence_number' => $c[11],
                ]);
            } catch (Exception $e) {
                echo $c[0] . "\n";
            }
        }

        /**
         * join table: curriculum_subject
         */

        $curriculum_subject = Excel::toCollection(null, storage_path('curriculum-subjects.csv'))[0];
        $specialization = '';
        $specialization_id = '';
        $semester = '';
        $semester_id ='';
        $regulation = '';
        $r_id = '';
        foreach($curriculum_subject->splice(1) as $cs) {
            if ($regulation != $cs[1]) {
                $regulation = $cs[1];
                $r_id = DB::table('regulations')->where('short_name', '=', $cs[1])->get()[0]->id;
            }
            if ($specialization != $cs[0]) {
                $specialization = $cs[0];
                $specialization_id = DB::table('specializations')->where('short_name', '=', $cs[0])->get()[0]->id;
            }
            if ($semester != $cs[2]) {
                $semester = $cs[2];
                $semester_id = DB::table('semesters')->where('regulation_id', '=', $r_id)->where('semester_number', '=', $cs[2])->get()[0]->id;
            }

            $curriculum_id = DB::table('curricula')->where('specialization_id', '=', $specialization_id)
                ->where('semester_id', '=', $semester_id)
                ->where('sequence_number', '=', $cs[3])
                ->get()[0]->id;
            $subject_id = DB::table('subjects')->where('subject_code', '=', $cs[4])->get()[0]->id;

            DB::table('curriculum_subject')->insert([
                'curriculum_id' => $curriculum_id,
                'subject_id' => $subject_id,
            ]);
        }
    }
}
