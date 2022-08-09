<!-- BUG: in geolocation.js, don't focus on user location when getting user location every 3 seconds. -->

<!-- TO-DO -->
    <!-- Show all positions from the database on the map that are in that group (json_encode, kolla bookmark) --> <!-- 1 -->
    <!-- Fix bugs --> <!-- 2 -->
<!DOCTYPE html>
<html>
    <head>
        <?php require './required-files/head.php'; ?>
        <link rel='icon' type='image/svg' href='media/'>
        <title>Home</title>
    </head>
    <body class='index-page'>
        <?php require './required-files/header.php'; ?>
        <div class='bg-image'></div>
        <section>
            <article>
                <a href='./group-system/create.php' class='btn' title='Create'>
                    <i class='fa-solid fa-plus'></i>
                </a>
                <a href='./group-system/search-form.php' class='btn' title='Search'>
                    <i class='fa-solid fa-magnifying-glass'></i>
                </a>
            </article>
        </section>
        
        <?php require './required-files/footer.php'; ?>
    </body>
</html>