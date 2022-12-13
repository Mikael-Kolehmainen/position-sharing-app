<?php

namespace controller\basic;

class HomeController
{
    /**
     * "index.php"
     */
    public function showHomePage()
    {
        echo "
            <script src='/js/home/marker-preview.js' defer></script>
            <title>Home</title>
        </head>
        <body>
            <div class='bg-image'></div>
            <section class='home-page'>
                <article>
                    <h1></h1>
                    <h2>Create or join a group</h2>
                    <a href='index.php/create' class='btn' title='Create'>
                        <i class='fa-solid fa-plus'></i>
                    </a>
                    <a href='index.php/search' class='btn' title='Search'>
                        <i class='fa-solid fa-magnifying-glass'></i>
                    </a>
                </article>
            </section>
        ";
    }
}