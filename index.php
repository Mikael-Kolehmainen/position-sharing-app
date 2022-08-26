<!-- Gjort idag -->
    <!-- fick målpositions algoritmen någotlunda klar, finns ett par bugar -->
    <!-- en switch som användaren kan välja att markera vatten entiteterna eller inte -->
    <!-- fixade en bug där man kunde inte flytta positionsmålen om de är "bakom" menyn -->
    <!-- fixade programfel då användaren eller positionsmålet var för nära en vatten entitet-->

<!-- TO-DO -->
    
    
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