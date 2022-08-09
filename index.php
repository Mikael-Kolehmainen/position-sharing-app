<!-- BUG: in geolocation.js, don't focus on user location when getting user location every 3 seconds. -->

<!-- TO-DO -->
<!-- Save amount of members in group by adding 1 to the database on creation and when someone joins the group,
    remove 1 from the database when the user leaves the page. -->
<!-- Use this data of amount of members when saving the location to database, give an unique id to each location to 
    determine which one to replace -->

<!-- Gjort idag -->
<!-- leaflet karta som visar din position -->
<!-- skickar positionsdatat till en databas -->

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