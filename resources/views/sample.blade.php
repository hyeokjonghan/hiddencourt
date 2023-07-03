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
      <source src="https://dnxhgyn7462gb.cloudfront.net/common/video/648c228065f56.m3u8" type="application/x-mpegURL">
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
