<?php

use Illuminate\Database\Seeder;
use App\Company;
use App\CourseAssign;
use App\User;
use App\StudentGroup;
use App\Exam;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create super admin
        $this->call(UserSeeder::class);

        // Create admin staff
        factory(User::class, 10)->create([
            'role' => array_rand([
                'chief_auditor',
                'auditor',
                'content_manager',
                'teacher_manager',
                'accountant',
            ])
        ]);

        // Create courses
        $this->call(CourseSeeder::class);

        // student group
        factory(StudentGroup::class, 5)->create();

        // Create Company with Company head
        factory(Company::class, 10)->create()->each(function ($company) {

            // Create teacher
            factory(User::class, 5)->create([
                'role' => 'teacher',
                'company_id' => $company->id
            ]);

            // Course Assign to company
            factory(CourseAssign::class, 5)->create([
                'companie_id' => $company->id,
                'course_id' => \App\Course::all()->random()->id
            ]);

            // Create Student
            factory(User::class, 50)->create([
                'role' => 'student',
                'company_id' => $company->id
            ])->each(function ($student) use ($company) {

                // Assign course with student
                factory(\App\CourseUser::class, 1)->create([
                    'course_id' => CourseAssign::where('companie_id', $company->id)->inRandomOrder()->first()->course_id,
                    'user_id' => $student->id
                ]);

                // assign teachers with student
                factory(\App\TeacherEnroll::class, array_rand([1, 2]))->create([
                    'student_id' => $student->id,
                    'teacher_id' => User::where('role', 'teacher')->where('company_id', $company->id)->inRandomOrder()->first()->id,
                ]);
            });
        });

    }
}
