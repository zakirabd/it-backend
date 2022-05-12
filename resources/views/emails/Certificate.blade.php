<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Celt English Certificate </title>
    <style>
        body {
            padding: 100px 100px;
            /*font-family: 'Great-Vibes', cursive;*/
        }

        .logo {
            width: 250px;
        }

        .main-title {
            font-family: 'alegreya';
            font-size: 47px;
            font-weight: bold;
            font-style: normal;
            color: rgb(43, 68, 150);
            text-decoration: none;
            text-transform: uppercase;
            line-height: 1.4;
            letter-spacing: 0.09em;
        }

        .main-content {
            font-family: 'Great-Vibes', cursive;
            text-align: center;
            /*margin-bottom: 7em;*/
        }

        .small_title {
            font-weight: normal;
            font-style: normal;
            color: rgb(239, 113, 0);
            text-decoration: none;
            font-family: 'Merriweather', serif;
            font-size: 24px;
        }

        .studnt_name {
            font-weight: 400;
            font-style: italic;
            color: rgb(43, 68, 150);
            text-decoration: none;
            font-family: 'great_vibes', cursive;
            font-size: 93px;
        }

        .course_name {
            line-height: 1.59;
            letter-spacing: 0.045em;
            font-family: 'alegreya';
            font-size: 22px;
            text-transform: uppercase;
            font-weight: bold;
            color: rgb(43, 68, 150);
        }

        .score {
            font-family: 'Alegreya', serif;
            font-size: 24px;
            color: rgb(43, 68, 150);
            font-weight: bold;
        }

        .date {
            font-weight: bold;
            font-style: normal;
            color: rgb(239, 113, 0);
            text-decoration: none;
            font-size: 20px;
            font-family: 'Open_Sans';
        }

        .on {
            font-weight: 400 !important;
        }

        .company_name {
            font-family: 'alegreya';
            font-weight: bold;
            font-style: normal;
            color: rgb(43, 68, 150);
            text-decoration: none;
            font-size: 25px;
        }

        footer {
            display: flex !important;
        }

        element.style {
            text-align: right;
        }

        .signature {
            margin-left: auto !important;
            /* background-color: red; */
            border-top: 3px solid #2b4496;
            position: relative;
            width: 250px;
            left: -44px;
        }

        .signature p {
            /*background-color: blue!important;*/
            position: absolute !important;
            /* left: -8px; */
            right: 70px !important;
        }

        /*.signature p.border_custom {*/
        /*    content: "";*/
        /*    height: 5px;*/
        /*    border-top: 3px solid #2b4496;*/
        /*    width: 245px;*/
        /*    left: 82px;*/
        /*    position: relative;*/
        /*}*/

        img.qr_code_img {
            width: 100px;
            text-align: left;
            display: inline-block;
        }

        @media only screen and (min-width: 300px) and (max-width: 700px) {

            header {
                text-align: center;
            }

            .logo {
                width: 100px;
                height: 100px;

            }

            .main-title {
                font-size: 30px !important;
            }

            .studnt_name {
                font-size: 35px;
            }

            .sm_title {
                font-size: 18px !important;
            }
        }

        @media only screen and (min-width: 701px) and (max-width: 1000px) {
            header {
                text-align: center;
            }

            .main-title {
                font-size: 40px !important;
            }

            .studnt_name {
                font-size: 40px !important;

            }
        }
    </style>
</head>
<body>
<header style="text-align: left;">
    <img src="https://c5s9m7a5.rocketcdn.me/wp-content/uploads/2019/12/Asset-1.png" alt="star image" class="logo">
</header>
<div class="main-content">
    <h1 class="main-title"> Certificate of Achievement(tsting)</h1>
    <p class="small_title sm_title "> This Is To Certify That </p>
    <h1 class="studnt_name"> {{ $name }}</h1>

    <p class="small_title sm_title ">Has Completed The Following Course</p>
    <p class="course_name">  {{ $course  }}</p>
    <p class="score">With A Score of {{ $marks }} % </p>
    <p class="date sm_title "><span style="font-weight: normal">On</span> {{ $exam_date }}</p>
    <p class="company_name">{{ $company_name }} </p>

</div>
<footer>

    <img src="{{ $qr_path }}" alt="star image" class="qr_code_img">

    <div class="small_title signature">
        <p style="text-align: center;text-transform: uppercase">School Director </p>
    </div>
</footer>
</body>
</html>
