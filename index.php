<!-- TO-DO -->
<!-- CHAT -->
    <!-- backend --> <!-- 3 -->
        <!-- update chat every 3 seconds so use js to show the data and ajax to get the data from the backend -->

<!-- Initials on markers and user chosen color of markers --> <!-- 2 -->
    <!-- then upload sessions to positions db and also if the user refreshes the page-->
    <!-- have the same inputs in search-form.php as in user-details.php -->

    <!-- implement default color: #FF0000 -->
<!DOCTYPE html>
<html>
    <head>
        <?php require './required-files/head.php'; ?>
        <link rel='icon' type='image/svg' href='media/'>
        <title>Home</title>
    </head>
    <body class='index-page'>
        <div class='bg-image'></div>
        <section>
            <article>
                <a href='./group-system/user-details.php' class='btn' title='Create'>
                    <i class='fa-solid fa-plus'></i>
                </a>
                <a href='./group-system/search-form.php' class='btn' title='Search'>
                    <i class='fa-solid fa-magnifying-glass'></i>
                </a>
            </article>
        </section>
    </body>
</html>