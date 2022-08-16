<!-- Gjort idag -->
    <!-- Användaren kan tillägga ett positionsmål på kartan för alla användare i gruppen (visuella delen) -->
    <!-- Användaren kan förkasta valet av positionsmålet -->

<!-- TO-DO -->
    <!-- Goal algorithm -->
        <!-- The user can choose to put a goal down (line on the map) -->
            <!-- no other users can do it when there's an active goal -->
        <!-- Tell others to slow down if a user gets behind -->

        <!-- a button which allows the user to put a line on the map that other users can see --> <!-- 1 -->
            <!-- css --> <!-- 1 -->
            <!-- js --> <!-- 2 -->
                <!-- show the active goal to other users -->
            <!-- php --> <!-- 3 -->
        <!-- disclaimer on top that there's an active goal -->
            <!-- if discalimer is clicked remove active goal -->
            <!-- css -->
            <!-- backend -->
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