<!-- TO-DO -->
<!-- CHAT -->
    <!-- backend --> <!-- 3 -->
        <!-- update chat every 3 seconds so use js to show the data and ajax to get the data from the backend -->

<!-- Initials on markers and user chosen color of markers --> <!-- 2 -->
    <!-- edit css in geolocation.js --> 
        <!-- for other users markers -->
        <!-- Add to stylesheetcontent variable the css, give the classname 'other-user-marker-[index]' 
        then give it the className in L.divIcon and check bookmark 4. Append and remove CSS stylesheets dynamically
        -->

    <!-- om man kommer på active.php utan att laga en avatar så skall man bli skickad till 
    search-form.php där man kan laga en avatar, kolla om man har session -->
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