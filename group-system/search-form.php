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
                <form action='search.php' method='POST'>
                    <input type='text' name='groupcode' placeholder='Group code (10 char)' minlength='10' maxlength='10' required>
                    <input type='submit' value='JOIN'>
                </form>
            </article>
        </section>
    </body>
</html>