<!-- Gjort idag -->

<!-- TO-DO -->
    <!-- create a start point marker for the goal algorithm -->
        <!-- an input where the user can define the distance between each start & goal point -->
        <!-- option to increase amount of waypoints for route -->
            <!-- draw the actual route so that it follows the created route -->
            <!-- a way to remove a waypoint (maybe a btn that removes previously added waypoint??) -->
    
    <!-- in the event handler instead of looking at the classname assign an id with options when creating object -->

    <!-- Create a btn where you can check map legends -->

<!-- BUGS -->
    <!-- if user removes goal it doesn't remove it from other users until refresh of site -->

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