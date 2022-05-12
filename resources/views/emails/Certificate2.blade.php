<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Celt Certificate </title>
    <style>

        body {
            background: linear-gradient(40deg, #e3e7ef 20%, #ece5e3 80%);

            position: relative;
        }

        .top-img {
            max-width: 850px;
            height: 450px;
            margin-left: -72px;
            margin-top: -80px;
        }

        .flag-img {
            width: 270px;
            height: 60px;
            float: right;
            margin-top: -30px;
        }

        .logo-div {
            text-align: center;
        }

        .logo {
            text-align: center;
            width: 1109px;
            height: 681px;
            /*margin-bottom: 30px!important;*/
        }

        .main-title {
            text-align: center;
            font-family: 'Myriad Pro' !important;
            font-size: 100px;
            font-weight: bold;

            color: #4859d6 !important;
            margin: 0px;
            padding: 0px;
            text-transform: uppercase;
        }

        .small_title {
            font-family: 'great_vibes';
            font-size: 108px;
            color: #495a62;
            text-align: center;
            margin-top: 30px !important;
            text-transform: lowercase;

        }

        .student_name {
            text-align: center;
            /*margin-top: 30px!important;*/
            color: #4859d6 !important;
            font-family: 'calibri';
            font-size: 125px;
            text-transform: capitalize;
            font-weight: bold;

        }

        hr.hr_line_name {
            width: 1400px;
            color: #5f6093;
        }

        .following_course {
            text-align: center !important;
            color: #131312 !important;
            font-family: "Open_Sans";
            font-size: 54px !important;
            /*margin-top: 20px!important;*/
            font-weight: lighter;

        }

        .course_name {
            text-transform: uppercase !important;
            text-align: center !important;
            font-family: 'Montserrat_Subrayada';
            font-weight: bold;
            font-size: 66px !important;
            color: #ff4f35;
        }

        .date {
            text-align: center;
            font-family: "Open Sans";
            font-size: 54px;
            /*margin-bottom: 30px;*/
            color: #1c1d1f;
            font-weight: lighter;
        }

        .company_name {
            text-align: center;
            color: #404f56;
            font-size: 58px;
            font-family: 'orbitron';
            font-weight: lighter;
            /*margin-top: 30px!important;*/
        }

        .qr_section {
            padding: 0px 20px !important;
        }

        .qr_title {
            font-family: 'calibri';
            font-size: 26px !important;
            font-weight: bold;
            color: #ff5402;
            margin-bottom: 40px !important;
            margin-left: 40px !important;
        }

        .qr_image {
            width: 250px !important;
        }

        .school_director {
            text-align: center;
            position: relative;
            font-family: 'orbitron';
            color: #48575e;
            font-size: 45px;
            font-weight: bold;
            text-transform: uppercase;
        }

        hr.hr_line {
            width: 500px;
            color: #5f6093;
        }

        .bottom-img {
            position: absolute;
            margin-top: -700px;
            margin-left: 90px;
        }
    </style>
</head>
<body>


{{--<img src="{{ public_path('/storage/files/asset/top.png')}}" alt="image" class="top-img">--}}
<img src="{{ asset('/storage/files/asset/top.png')}}" alt="image" class="top-img">

<img src="{{ asset('/storage/files/asset/Flag.png')}}" alt="image" class="flag-img">
<div class="parent-div" style="position: absolute;bottom: -28px;">
    <div class="main-content">
        <div class="logo-div">
            <img src="{{ asset('/storage/files/asset/logo.png')}}" alt="star image" class="logo">
        </div>

        <p class="main-title" style="font-size: 166px;"> Certificate </p>
        <p class="main-title"> of Achievement </p>
        <p class="small_title" style="text-transform: lowercase;color: #495a62"> This Is To Proudly Certify That </p>
        <p class="student_name" > {{ $name  }} </p>
        <hr class="hr_line_name" >
        <p class="following_course" style="margin-bottom: 1px">Has Completed The Following
            Course </p>
        <p class="course_name" style="margin-bottom: 1px"> {{ $course  }} </p>
        <p class="course_name" style="text-transform: uppercase;margin-bottom:1px;"> With a
            Score of {{ $marks  }}  </p>
        <p class="date" style="margin-bottom: 1px;"><span
                style="font-weight: normal">On</span> {{ $exam_date  }}</p>
        <p class="company_name" > {{ $company_name  }}</p>
    </div>
    <div class="qr_section">
        
        <p class="qr_title"> Verify here </p>

        <div class="qr-background" style="margin-top: 20px;">
            <img src="{{ $qr_path }}" alt="image" class="qr_image">
        </div>
        
    </div>
    <div class="school_director">
        <hr class="hr_line">
        <p> School Director </p>
    </div>
    <div style="text-align: right;position: relative">
        <img src="{{ asset('/storage/files/asset/bottom2.png')}}" alt="image" style="" class="bottom-img">

    </div>
</div>
</body>
</html>
