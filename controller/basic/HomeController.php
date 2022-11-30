<?php
class HomeController extends BaseController
{
    /**
     * "index.php"
     */
    public function showHomePage()
    {
        echo "
            <title>Home</title>
        </head>
        <body>
            <div class='bg-image'></div>
            <section class='index-page'>
                <article>
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