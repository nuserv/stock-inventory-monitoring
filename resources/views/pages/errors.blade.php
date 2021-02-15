
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="{{asset('favicon.ico')}}" type="image/x-icon" />
        <link rel="shortcut icon" href="{{asset('favicon.ico')}}" type="image/x-icon" />
        <title>Server Error</title>
        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .code {
                font-size: 26px;
                padding: 0 15px 0 15px;
                text-align: center;
            }

            .message {
                font-size: 18px;
                text-align: center;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if((new \Jenssegers\Agent\Agent())->isDesktop())
                <div class="code">
                    <p>The system currently works only on Google Chrome. Download it <a href="https://support.google.com/chrome/answer/95346">here</a>. </p>
                    <p>We will keep you posted once it works on other browsers. Thank you.</p>
                    <a href="https://support.google.com/chrome/answer/95346"><img src="{{asset('idsi.png')}}" height="100"></a>
                </div>
            @endif
            @if(!(new \Jenssegers\Agent\Agent())->isDesktop())
                <div class="code">
                    <p style="font-size: 75%;">{{(new \Jenssegers\Agent\Agent())->isMobile() ? 'Mobile':(new \Jenssegers\Agent\Agent())->device()}} browser is not supported.</p>
                    <p style="font-size: 75%;">Use or download Google Chrome on your desktop to access this site.</p>
                    <a href="#"><img src="{{asset('idsi.png')}}" height="100"></a>
                </div>
            @endif
        </div>
    </body>
</html>
