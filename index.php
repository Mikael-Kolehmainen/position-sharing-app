<!-- Gjort idag -->
    <!-- Där rutten och sjön skär varandra detekteras -->

<!-- TO-DO -->
    <!-- Goal algorithm -->
        <!-- Write a ghost line before the actual route line, if the ghost line gets
        water infront of it then stop the ghost line there and write a new ghost line starting from that position
        and go around the lake. check if ghost line is at a distance from the lake at all times until you get to a 
        position that is closest to the goal and draw a ghost line again. -->

    <!-- Issues: -->
        <!-- reading the Geojson file is slow -->
        <!-- Possible fixes: -->
            <!-- get a smaller geojson file of a smaller area -->

    <!-- Test different scenarios and that everything works -->
        
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