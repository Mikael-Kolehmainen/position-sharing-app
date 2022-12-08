<?php

namespace controller\basic;

class SearchController
{
    /**
     * "index.php/search" 
     */
    public function showSearchPage()
    {
        echo "<title>Join group</title>
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
                        <input type='text' name='groupcode' placeholder='Group code (3 char)' minlength='3' maxlength='3' class='center' required>
                        <input type='text' name='initials' placeholder='Initials (2 char)' minlength='2' maxlength='2' class='center' required onkeydown='return /[a-z0-9]/i.test(event.key)'>
                        <input type='text' name='color' placeholder='Color (Name or HEX)' minlength='3' maxlength='20' class='center'>
                        <p style='text-align: center'>Default color: red / #FF0000</p>
                        <input type='submit' value='JOIN' name='search-group'>
                    </form>
                </article>
            </section>";
    }
}