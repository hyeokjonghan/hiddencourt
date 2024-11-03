<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://vjs.zencdn.net/8.3.0/video-js.css" rel="stylesheet" />
        <title>m3u8 sample</title>


    </head>
    <body class="antialiased">
        <video
        id="my-video"
        class="video-js"
        controls
        preload="auto"
        width="640"
        height="264"
        data-setup="{}"
      >
      <source src="https://vod-cld3.gigaeyes.co.kr/V100/VMS_49001/playlist?s=%2BVBl0qiLxj1kdXxmfeIDmmgV9H4wnXoVQNDDoKEd6TVUAg9hlUIutodF25uD03lD9%2Bp0ZvX64Z2MfHNXzXxXh9yyKYU2I9Yn%2Bo5IStvxIYFbq5Gc3CHcPEeHS%2FFGa2PgxL%2BGeKCJL2F151KenRfUxrDOhJ5TBTUzfg1DPC4WpKLXY%2BAOMH4DWVXiGX1kBOsj&t=3718629f.m3u8" type="application/x-mpegURL">
        <p class="vjs-no-js">
          To view this video please enable JavaScript, and consider upgrading to a
          web browser that
          <a href="https://videojs.com/html5-video-support/" target="_blank"
            >supports HTML5 video</a
          >
        </p>
      </video>


    </body>
</html>
