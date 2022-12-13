<?php

namespace controller\basic;

class SearchController
{
    /**
     * "index.php/search" 
     */
    public function showSearchPage()
    {
        echo "
            <script src='/js/home/marker-preview.js' defer></script>
            <title>Join group</title>
        </head>
        <body class='search-page'>
            <div class='bg-image'></div>
            <section>
                <article class='box'>
                    <a href='/index.php' class='btn round'>
                        <i class='fa-solid fa-chevron-left'></i>
                    </a>
                    <h1>Join group</h1>
                    <form action='/index.php/map/search' method='POST'>
                        <input type='text' name='groupcode' placeholder='Group code (3 char)' minlength='3' maxlength='3' class='center' required>";
        CreateController::showMarkerCreation();
        echo "                
                        <input type='submit' value='JOIN' name='search-group'>
                    </form>
                </article>
            </section>
        ";
    }
}