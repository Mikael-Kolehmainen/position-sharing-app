<!-- Gjort idag -->


<!-- TO-DO -->
    <!-- goal algortihm -->
        <!-- Bug: fails to draw the ghostlines correctly inbetween the arcs -->
            <!-- Reason: when the intersections are saved the line can go through for example two water entities and
            if the second one is detected before the first one then it will be saved as the first one and this
            causes problems with the for loops (they're reversed), code works fine when route is going downwards but
            doesn't work correctly when going upwards. -->
            <!-- when looping through the geojson objects the water entities go from top left to bottom right of the 
                specified location -->
            <!-- Possible solution: check if route is going upwards or downwards and change the ghostline for loop
                based on the information. -->
    
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