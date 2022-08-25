<!-- Gjort idag -->
    

<!-- TO-DO -->
    <!-- Goal algorithm -->
        <!-- if user or goal is in circle when it's created then make a smaller circle -->
        <!-- what happens if a user puts a marker on water -->

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