<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;

$factory->define(\App\Question::class, function (Faker $faker) {

    $question_type = $faker->randomElement([
        'parent', 'single_choice', 'single_image_type', 'single_image_choice', 'single_text_type', 'single_sat_type',
        'single_video_type', 'single_audio_type', 'multiple_choice', 'matching_type', 'dropdown_question_type', 'single_boolean_type'
    ]);
    $question_sub_type = $faker->randomElement([
        'text_to_text', 'text_to_image', 'image_to_image'
    ]);

    $question_image = null;

    if ($question_type == 'single_image_choice' || $question_type == 'single_sat_type') {
        $question_image = 'https://blog.snappa.com/wp-content/uploads/2016/12/free-stock-photos-2.png';
    }

    $audio_file = null;

    if ($question_type == 'single_audio_type') {
        $audio_file = 'https://www.bensound.com/bensound-music/bensound-summer.mp3';
    }

    $video_link = null;

    if ($question_type == 'single_video_type') {
        $video_link = 'https://www.youtube.com/watch?v=9JkJpyqNDUI';
    }

    return [
        'title' => $faker->text(100),
        'parent_id' => $faker->randomElement([0, 1]),
        'sort_id' => 1,
        'exam_id' => 1,
        'description' => $faker->text,
        'question_description' => $faker->text,
        'type' => $question_type,
        'sub_type' => $question_type == 'matching_type' ? $question_sub_type : null,
        'score' => $faker->randomElement([5, 10, 3, 2, 15]),
        'question_image' => $question_image,
        'audio_file' => $audio_file,
        'video_link' => $video_link,
    ];
});
