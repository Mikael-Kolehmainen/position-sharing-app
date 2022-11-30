<?php
class CreateController extends BaseController
{
    /**
     * "index.php/create" 
     */
    public function showCreatePage()
    {
        echo "
            <title>Create</title>
        </head>
        <body class='user-page'>
            <div class='bg-image'></div>
            <section>
                <article class='box'>
                    <a href='/index.php' class='btn round'>
                        <i class='fa-solid fa-chevron-left'></i>
                    </a>
                    <h1>Create your marker</h1>
                    <form action='/index.php/map/create' method='POST'>
                        <input type='text' name='initials' placeholder='Initials (2 char)' minlength='2' maxlength='2' class='center' required onkeydown='return /[a-z0-9]/i.test(event.key)'>
                        <input type='text' name='color' placeholder='Color (HEX eg. #5BC0EB)' minlength='7' maxlength='7' class='center'>
                        <p style='text-align: center'>Default color: #FF0000</p>
                        <input type='submit' value='CREATE' name='create-group'>
                    </form>
                </article>
            </section>
        ";
    }
}