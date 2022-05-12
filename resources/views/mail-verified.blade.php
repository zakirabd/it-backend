<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
          integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
            integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj"
            crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx"
            crossorigin="anonymous"></script>

    <style>
        body {
            padding: 5em;
            text-align: center;
        }

        h1 {
            margin-bottom: 1em;
        }

        .circle-loader {
            margin-bottom: 3.5em;
            border: 1px solid rgba(0, 0, 0, 0.2);
            border-left-color: #5cb85c;
            animation: loader-spin 1.2s infinite linear;
            position: relative;
            display: inline-block;
            vertical-align: top;
            border-radius: 50%;
            width: 7em;
            height: 7em;
        }

        .load-complete {
            -webkit-animation: none;
            animation: none;
            border-color: #5cb85c;
            transition: border 500ms ease-out;
        }

        .checkmark {
            display: none;
        }

        .checkmark.draw:after {
            animation-duration: 800ms;
            animation-timing-function: ease;
            animation-name: checkmark;
            transform: scaleX(-1) rotate(135deg);
        }

        .checkmark:after {
            opacity: 1;
            height: 3.5em;
            width: 1.75em;
            transform-origin: left top;
            border-right: 3px solid #5cb85c;
            border-top: 3px solid #5cb85c;
            content: '';
            left: 1.75em;
            top: 3.5em;
            position: absolute;
        }

        @keyframes loader-spin {
            0% {
                transform: rotate(0deg);
            }
            100% {
                transform: rotate(360deg);
            }
        }

        @keyframes checkmark {
            0% {
                height: 0;
                width: 0;
                opacity: 1;
            }
            20% {
                height: 0;
                width: 1.75em;
                opacity: 1;
            }
            40% {
                height: 3.5em;
                width: 1.75em;
                opacity: 1;
            }
            100% {
                height: 3.5em;
                width: 1.75em;
                opacity: 1;
            }
        }

    </style>
</head>
<body>
<h1>You account has been verified successfully</h1>

<div class="circle-loader">
    <div class="checkmark draw"></div>
</div>

<p><a href="{{ config('app.vue_app_url') }}" class="btn btn-success">Sign in your account</a></p>

<script>
    setTimeout(() => {
        $('.circle-loader').toggleClass('load-complete');
        $('.checkmark').toggle();
    }, 1000)

</script>

</body>
</html>
