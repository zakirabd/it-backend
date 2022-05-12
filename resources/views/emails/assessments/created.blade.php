<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <style>
        body {
            margin-left: 20px !important;
            max-width: 1000px;
        }

        .star {
            width: 20px;
            height: 20px;

        }

        .main-section{
            display: flex;
            width: 100%;
        }
        .left{
            width: 50%;
        }
        .right{
            height: 50%;
        }
        .title{
            border-bottom: 1px solid #dddddd;
            padding-bottom: 5px;
        }
        .hide-default{
            display: none;
        }
        @media only screen and (min-width: 300px) and (max-width: 600px) {
            /*body {*/
            /*    background-color: gray;*/
            /*}*/
            .hide-sm{

                display: none!important;
            }
            .right{
                width: 100% !important;
            }
            .left{
                width: auto!important;
            }
            .hide-default{
                margin-top: 5px!important;
                display: block!important;
            }
        }

    </style>
</head>
<body>
<p>
    Hello,
<p>
<p>
    Please read the assessment report.
</p>
<p>
    Student : {{ $user->full_name }}
</p>
<p>
    Teacher : {{ $teacher->full_name }}
</p>

<div class="main-section ">
    <div class="left hide-sm">
        <p class="title hide-sm">Description</p>
        <p class="hide-sm">{!! $assessment->note !!}</p>

    </div>
    <div class="right">
        <p class="title">Rating </p>
{{--        homework --}}
                        <div>
                            @if($assessment->home_work !=0)
                                <p> Homework (Ev Tapşırığı) :
                                    <br>
                                    @for($i=1;$i<=$assessment->home_work;$i++)
                                        <img src="{{ asset('storage/images/avatars/Star.png') }}" alt="star image" class="star">
                                    @endfor
                                    @for($i=1;$i<=10 - $assessment->home_work;$i++)
                                        <img src="{{ asset('storage/images/avatars/Star_outline2.png') }}" alt="star image" class="star">
                                    @endfor

                                </p>
                            @endif
                        </div>
{{--        participation--}}
        <div>
            @if($assessment->participation !=0)
                <p> Vocabulary (Söz bazası) :
                    <br>
                    @for($i=1;$i<=$assessment->participation;$i++)
                        <img src="{{ asset('storage/images/avatars/Star.png') }}" alt="star image" class="star">
                    @endfor
                    @for($i=1;$i<=10 - $assessment->participation;$i++)
                        <img src="{{ asset('storage/images/avatars/Star_outline2.png') }}" alt="star image" class="star">
                    @endfor
                </p>
            @endif
        </div>
{{--        performance--}}
        <div>
            @if($assessment->performance !=0)
                <p> Performance (Dərsdə aktivlik):
                    <br>
                    @for($i=1;$i<=$assessment->performance;$i++)
                        <img src="{{ asset('storage/images/avatars/Star.png') }}" alt="star image" class="star">
                    @endfor
                    @for($i=1;$i<=10 - $assessment->performance;$i++)
                        <img src="{{ asset('storage/images/avatars/Star_outline2.png') }}" alt="star image" class="star">
                    @endfor
                </p>
            @endif
        </div>
{{--        reading_comprehension--}}
        <div>
            @if($assessment->reading_comprehension !=0)
                <p> Reading skills (Mətn anlama):
                    <br>

                    @for($i=1;$i<=$assessment->reading_comprehension;$i++)
                        <img src="{{ asset('storage/images/avatars/Star.png') }}" alt="star image" class="star">
                    @endfor
                    @for($i=1;$i<=10 - $assessment->reading_comprehension;$i++)
                        <img src="{{ asset('storage/images/avatars/Star_outline2.png') }}" alt="star image" class="star">
                    @endfor

                </p>
            @endif
        </div>
{{--        listening_skills--}}
        <div>
            @if($assessment->listening_skills !=0)
                <p> Listening skills (Dinləmə):
                    <br>
                    @for($i=1;$i<=$assessment->listening_skills;$i++)
                        <img src="{{ asset('storage/images/avatars/Star.png') }}" alt="star image" class="star">
                    @endfor
                    @for($i=1;$i<=10 - $assessment->listening_skills;$i++)
                        <img src="{{ asset('storage/images/avatars/Star_outline2.png') }}" alt="star image" class="star">
                    @endfor
                </p>
            @endif
        </div>
{{--        speaking_fluency--}}
        <div>
            @if($assessment->speaking_fluency !=0)
                <p> Speaking skills (Danışıq) :
                    <br>

                    @for($i=1;$i<=$assessment->speaking_fluency;$i++)
                        <img src="{{ asset('storage/images/avatars/Star.png') }}" alt="star image" class="star">
                    @endfor

                    @for($i=1;$i<=10 - $assessment->speaking_fluency;$i++)
                        <img src="{{ asset('storage/images/avatars/Star_outline2.png') }}" alt="star image" class="star">
                    @endfor
                </p>
            @endif
        </div>
{{--        writing_skills--}}
        <div>
            @if($assessment->writing_skills !=0)
                <p> Writing/Essays (Yazı/Esse):
                    <br>
                    @for($i=1;$i<=$assessment->writing_skills;$i++)
                        <img src="{{ asset('storage/images/avatars/Star.png') }}" alt="star image" class="star">
                    @endfor
                    @for($i=1;$i<=10 - $assessment->writing_skills;$i++)
                        <img src="{{ asset('storage/images/avatars/Star_outline2.png') }}" alt="star image" class="star">
                    @endfor
                </p>
            @endif
        </div>
</div>
</div>

<div class="bottom-left hide-default">
    <p class="title">Description</p>
    <p class="">{!! $assessment->note !!}</p>

</div>

<p>
    If you have any questions, please send an email to celtenglish@celt.az
    <br>
    Warm regards from lovely Baku.
<p>
    Thanks,<br>
    {{ config('app.name') }}
</p>

</body>
</html>
