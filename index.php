<!-- Gjort idag -->
    <!-- Positionsmålet kan tas bort -->
    <!-- Ritar en linje från användaren till positionsmålet -->

<!-- TO-DO -->
    <!-- Goal algorithm -->
        <!-- The user can choose to put a goal down (line on the map) -->
            <!-- no other users can do it when there's an active goal -->
        <!-- Tell others to slow down if a user gets behind -->
            <!-- save to localstorage original positions of users --> <!-- remember to clear storage -->
            <!-- then write the polyline from the original positions to the goal -->
            <!-- then compare total distance to current distance and get a percentage -->


        <!-- show the user the fastest way to the goal on the map -->
        
    <!-- Delete group function -->

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