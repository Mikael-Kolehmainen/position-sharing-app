<?php require './../required-files/constants.php'; ?>
<!DOCTYPE html>
<html>
    <head>
        <?php require './../required-files/head.php'; ?>
        <!--        <link rel='icon' type='image/svg' href='./../media/'> -->
        <link href='./../styles/css/main.css' rel='stylesheet' type='text/css'>
        <script src='./../js/ElementDisplay.js' defer></script>
        <script src='./../js/onclick-events.js' defer></script>
        <script src='./Camera.js' defer></script>
        <script src='./initialize-camera.js' defer></script>
        <title>Take picture</title>
    </head>
    <body>
        <section>
            <main id='camera'>
                <video id='camera-view' autoplay playsinline></video>
                <canvas id='camera-sensor'></canvas>
                <img src="" alt="" id='camera-output' style='display: none;'>
                <button id='camera-trigger'>Take a picture</button>
                <div class='bottom' id='image-options' style='display: none;'>
                    <a class='btn onclick' id='reject-image'>
                        <i class='fa-solid fa-xmark'></i>
                    </a>
                    <a class='btn onclick' id='accept-image'>
                        <i class='fa-solid fa-check'></i>
                    </a>
                </div>
            </main>
        </section>
    </body>
</html>