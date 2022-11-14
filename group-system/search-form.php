<?php
require './../required-files/constants.php';
require './../autoloader.php';

session_start();
if (isset($_SESSION[UNIQUEID])) {
    $user = new User();
    $user->uniqueId = $_SESSION[UNIQUEID];
    $user->remove();
    unset($_SESSION[UNIQUEID]);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <?php require './../required-files/head.php'; ?>
        <link href='./../styles/css/main.css' rel='stylesheet' type='text/css'>
        <link rel='icon' type='image/svg' href='./../media/'>
        <title>Join group</title>
    </head>
    <body class='search-page'>
        <div class='bg-image'></div>
        <section>
            <article class='box'>
                <a href='./../index.php' class='btn round'>
                    <i class='fa-solid fa-chevron-left'></i>
                </a>
                <h1>Join group</h1>
                <form action='./../map-system/active.php' method='POST'>
                    <input type='text' name='groupcode' placeholder='Group code (3 char)' minlength='3' maxlength='3' class='center' required>
                    <input type='text' name='initials' placeholder='Initials (2 char)' minlength='2' maxlength='2' class='center' required onkeydown='return /[a-z0-9]/i.test(event.key)'>
                    <input type='text' name='color' placeholder='Color (HEX eg. #5BC0EB)' minlength='7' maxlength='7' class='center'>
                    <p style='text-align: center'>Default color: #FF0000</p>
                    <input type='submit' value='JOIN' name='search-group'>
                </form>
            </article>
        </section>
    </body>
</html>