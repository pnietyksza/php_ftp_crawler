<?php

class ftpload_class{
    public function ftpload_connect($serverAdress,$login,$password)
    {
        # metoda do łączenia się z serwerm

        $connect = ftp_connect( $serverAdress ) or die( 'Nie można połączyć serwerem:' . $serverAdress );
        if( $connect ){
            $loginProccess = ftp_login( $connect, $login, $password );
            ftp_set_option( $connect, FTP_USEPASVADDRESS, false );
            ftp_pasv($connect, true);
            if($loginProccess){
                echo 'Proces logowanie przeszedł pomyślnie</br>';
            };
        }
        return $connect;

    }
    public function ftpload_main( $connect )
    {
        # głowna metoda rekurencyjna, która przeszukuje katalogi

        $listOfAll = ftp_mlsd($connect, '');
        $listOfFiles = [];
        $currentPath = ftp_pwd($connect);
        $link = 'https://ftp.eltrox.pl';
        foreach($listOfAll as $element){
            $type = $element['type'];
            $name = $element['name'];
            switch($type){
                case 'file':
                    echo '</br> To jest obecne położenie pliku:  '.$currentPath.'  a ten plik to: '.$name.'</br>';
                    $forWP = $currentPath . '/' .$element['name'];
                    $urlForPost = $link.$forWP;
                    $listOfFiles[] = $currentPath . '/' .$element['name'];
                    $dynamicPost = array(
                        'post_title'   => $element['name'],
                        'post_content' => "<a href='{$urlForPost}'>Kliknij aby pobrać firmware</a>",
                        'post_type'    => 'post',
                        'post_status' => 'publish',
                    );
                    if(post_exists($name) == null){
                        wp_insert_post($dynamicPost);
                        $parts = explode( '/', $forWP );
                        $partsReverse = array_reverse($parts);
                        $title = $element['name'];
                        $output = object;
                        $post_type = 'post';
                        $post_info = get_page_by_title($title,$output,$post_type);
                        $id = $post_info->ID;
                        $taxonomy = 'Model';
                        $terms = $partsReverse[1];
                        wp_set_object_terms($id, $terms, $taxonomy);
                        $id_2 = $post_info->ID;
                        $taxonomy_2 = 'Producent';
                        $terms_2 = $parts[2];
                        wp_set_object_terms($id_2, $terms_2, $taxonomy_2);
                        
                    }
                break;
                case 'dir':
                    ftp_chdir($connect, $name);
                    $listOfAll_2 = ftp_mlsd($connect, $name);
                    $listOfFiles = array_merge($listOfFiles, $this->ftpload_main($connect));
                    ftp_chdir($connect, '..');
                break;
                case null;
                break;
            }
        }
        return($listOfFiles);
    }
    public function ftpload_pknocking( $host, $port )
    {
        # metoda do otwarcia połączenia poprzez portknocking

        $waitTimeoutInSeconds = 1;
        fsockopen($host,$port,$errCode,$errStr,$waitTimeoutInSeconds);
    }
}


