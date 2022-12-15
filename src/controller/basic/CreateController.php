<?php

namespace controller\basic;

class CreateController
{
    /**
     * "index.php/create"
     */
    public function showCreatePage()
    {
        echo "
            <script src='/js/home/marker-preview.js' defer></script>
            <title>Create</title>
        </head>
        <body class='create-page'>
            <div class='bg-image'></div>
            <section>
                <article class='box'>
                    <a href='/index.php' class='btn round'>
                        <i class='fa-solid fa-chevron-left'></i>
                    </a>
                    <h1>Create your marker</h1>
                    <form action='/index.php/map/create' method='POST'>";
        $this->showMarkerCreation();
        echo "
                        <input type='submit' value='CREATE' name='create-group'>
                    </form>
                </article>
            </section>
        ";
    }

    public static function showMarkerCreation()
    {
        echo "
            <input type='text' name='initials' id='marker-initials' placeholder='Initials (2 char)' minlength='2' maxlength='2' class='center' required onkeydown='return /[a-z0-9]/i.test(event.key)'>
            <input type='text' name='color' id='marker-color' placeholder='Color (Name or HEX)' minlength='3' maxlength='20' class='center'>
            <p style='text-align: center'>Default color: red / #FF0000</p>
            <div class='marker-preview'>
                <h2>Preview:</h2>
                <div class='marker' id='marker-preview-color'>
                    <p id='marker-preview-initials'></p>
                </div>
            </div>
        ";
    }
}
