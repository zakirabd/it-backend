<?php

use App\Exam;
use Illuminate\Database\Seeder;
use App\Course;
use App\Essay;
use App\Lesson;
use App\Speaking;
use Faker\Factory as Faker;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Create course
        factory(Course::class, 10)->create()->each(function ($course) {

            // Create Lesson
            factory(Lesson::class, 10)->create([
                'course_id' => $course->id
            ])->each(function ($lesson) use ($course) {

                // Create Essay
                factory(Essay::class, 3)->create([
                    'course_id' => $course->id,
                    'lesson_id' => $lesson->id
                ]);

                // Create Speaking
                factory(Speaking::class, 10)->create([
                    'course_id' => $course->id,
                    'lesson_id' => $lesson->id
                ]);

                // Create listening
                factory(\App\Listening::class, 10)->create([
                    'course_id' => $course->id,
                    'lesson_id' => $lesson->id
                ]);

                // Create exam
                factory(Exam::class, 3)->create([
                    'course_id' => $course->id,
                    'lesson_id' => $lesson->id
                ])->each(function ($exam) {
                    // Create Parent Question
                    factory(\App\Question::class, 5)->create([
                        'parent_id' => null,
                        'exam_id' => $exam->id,
                        'type' => 'parent',
                        'score' => 0,
                    ])->each(function ($parent_question) use ($exam) {
                        // Create Child question
                        $faker = Faker::create();
                        factory(\App\Question::class, 10)->create([
                            'type' => $faker->randomElement([
                                'single_choice', 'single_image_type', 'single_image_choice', 'single_text_type', 'single_sat_type',
                                'single_video_type', 'single_audio_type', 'multiple_choice', 'matching_type', 'dropdown_question_type', 'single_boolean_type'
                            ]),
                            'parent_id' => $parent_question->id,
                            'exam_id' => $exam->id
                        ])->each(function ($question) {
                            // Create answer
                            $faker = Faker::create();

                            switch ($question->type) {
                                case "single_choice":
                                case "single_image_type":
                                case "single_image_choice":
                                case "single_text_type":
                                case "single_sat_type":
                                case "single_video_type":
                                case "single_audio_type":
                                    for ($i = 0; $i <= 3; $i++) {
                                        factory(\App\Answer::class, 1)->create([
                                            'question_id' => $question->id,
                                            'is_correct' => $i == 3 ? 1 : 0
                                        ]);
                                    }
                                    break;
                                case "multiple_choice":
                                    factory(\App\Answer::class, 4)->create([
                                        'question_id' => $question->id,
                                        'is_correct' => array_rand([1, 0])
                                    ]);
                                    break;
                                case "matching_type":
                                    if ($question->sub_type == 'text_to_text') {
                                        factory(\App\Answer::class, 4)->create([
                                            'question_id' => $question->id,
                                            'is_correct' => $faker->text
                                        ]);
                                    } elseif ($question->sub_type == 'text_to_image') {
                                        factory(\App\Answer::class, 4)->create([
                                            'question_id' => $question->id,
                                            'is_correct' => 'https://image.shutterstock.com/image-photo/isolated-apples-whole-red-apple-260nw-575378506.jpg'
                                        ]);
                                    } elseif ($question->sub_type == 'image_to_image') {
                                        factory(\App\Answer::class, 4)->create([
                                            'title' => 'https://image.shutterstock.com/image-photo/isolated-apples-whole-red-apple-260nw-575378506.jpg',
                                            'question_id' => $question->id,
                                            'is_correct' => 'https://image.shutterstock.com/image-photo/isolated-apples-whole-red-apple-260nw-575378506.jpg'
                                        ]);
                                    }
                                    break;
                                case "dropdown_question_type":
                                    $questions = array(
                                        ['title' => 'My name [is,are,am] Rifat', 'answer' => 'is'],
                                        ['title' => 'I [am,is,are] from Bangladesh', 'answer' => 'am'],
                                        ['title' => 'I [am,is,age] 28 years old', 'answer' => 'am'],
                                        ['title' => 'And the lorem [am,is,are] a demo text', 'answer' => 'am'],
                                        ['title' => 'This is [an,a,the] another demo text', 'answer' => 'an'],
                                        ['title' => 'We [need,without,moment] to resolve it', 'answer' => 'need'],
                                    );
                                    foreach ($questions as $_question) {
                                        factory(\App\Answer::class, 1)->create([
                                            'title' => $_question['title'],
                                            'is_correct' => $_question['answer'],
                                            'question_id' => $question->id,
                                        ]);
                                    }
                                    break;
                                case "single_boolean_type":
                                    factory(\App\Answer::class, 1)->create([
                                        'title' => 'True',
                                        'question_id' => $question->id,
                                        'is_correct' => 0
                                    ]);
                                    factory(\App\Answer::class, 1)->create([
                                        'title' => 'False',
                                        'question_id' => $question->id,
                                        'is_correct' => 1
                                    ]);
                                    break;
                            }
                        });
                    });
                });

            });

        });
    }
}
