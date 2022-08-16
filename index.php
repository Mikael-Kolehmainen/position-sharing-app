<!-- TO-DO -->
    <!-- Goal algorithm -->
        <!-- The user can choose to put a goal down (line on the map) -->
            <!-- no other users can do it when there's an active goal -->
        <!-- Tell others to slow down if a user gets behind -->

        <!-- a button which allows the user to put a line on the map that other users can see --> <!-- 1 -->
            <!-- css --> <!-- 1 -->
            <!-- js --> <!-- 2 --> <!-- Remember: draggable: 'true' -->
                <!-- onclick add a draggable marker on the screen which the user can move -->
                <!-- then the user has to confirm the location of marker by pressing a button -->
                <!-- then it gets saved with ajax to the database -->
            <!-- php --> <!-- 3 -->
        <!-- disclaimer on top that there's an active goal -->
            <!-- css -->
            <!-- backend -->
        <!-- show the user the fastest way to the goal on the map -->

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