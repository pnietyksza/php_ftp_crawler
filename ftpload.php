<?php
/**
 * Plugin Name: FTP load
 */


add_action('admin_menu', 'test_plugin_setup_menu');
function test_plugin_setup_menu(){
    add_menu_page( 'FTP load Page', 'FTP load', 'manage_options', 'ftp-load', 'ftp_init' );
}
add_shortcode("testowo", "testowo_init");
function testowo_init(){

    $args = array(
        'post_type' => 'pods_cpt_people', // the post type
        'tax_query' => array(
            array(
                'taxonomy' => '', // the custom vocabulary
                'field'    => 'slug',                 
                'terms'    => array('the-slug'),      // provide the term slugs
            ),
        ),
    );
}

function ftp_init(){
    echo "<h4>Wtyczka do skanowania serwerów ftp i ewentualnego dodawania wpisów z linkami do plików.</br>
    Aby rozpocząć proces kilknij poniżej
    </br></h3>";
    ?>
        <form action="admin.php?page=ftp-load" method="post">
            <input type="submit" name="akcja" value="Zaaktualizuj produkty" />
        </form>
    </br>
    <?php

require_once 'library/ftpload-lib.php' ;

    if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['akcja'])){

        //dane logowania
        $serverAdress = '';
        $login = '';
        $password = '';

        //obiekt klasy
        $ftpload_obj = new ftpload_class;

        

        //otwarcie portów adresu publicznego
        $port1 = 00003;
        $port2 = 00002;
        $port3 = 00001;
        $ftpload_obj->ftpload_pknocking($serverAdress, $port1);
        $ftpload_obj->ftpload_pknocking($serverAdress, $port2);
        $ftpload_obj->ftpload_pknocking($serverAdress, $port3);


        //metoda zwracajaca polaczenie
        $connect  = $ftpload_obj->ftpload_connect($serverAdress,$login,$password);

        /*
        przeszukiwanie konkretnego folderu musi być poprzedzone funkcją ftp_chdir($polaczenie, $katalog);
        */
        ftp_chdir($connect,'/exampledir');
        $ftpload_obj->ftpload_main($connect);


    }
}















































































