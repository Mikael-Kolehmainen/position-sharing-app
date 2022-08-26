<!-- Gjort idag -->
    <!-- fick målpositions algoritmen någotlunda klar, finns ett par bugar -->

<!-- TO-DO -->
    <!-- Goal algorithm -->
        <!-- Bugs -->
            <!-- if goal or user is too close to a water entity when the route is drawn, js throws some errors -->
    
    <!-- An option to highlight water entities -->
        <!-- onclick initialize geoJSON -->

    <!-- Issues: -->
        <!-- reading the Geojson file is slow -->
        <!-- Possible fixes: -->
            <!-- get a smaller geojson file of a smaller area -->

    <!-- Bugs -->
        <!-- can't drag marker if marker is behind bottom div -->
    
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