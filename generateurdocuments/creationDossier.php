<?php
require_once '../../main.inc.php';

if (isset($_GET['directory'])){
    $dossier = "../../../documents" . $_GET['directory'];
    echo $dossier;
    if(!file_exists($dossier)){
        if(mkdir($dossier, 0755, true)){
            echo 'le dossier a été créé.';
        }
        else{
            echo 'le dossier n\'a pas pu être créé.';
        }
    }
    else{
        echo 'le fichier existe déjà.';
    }
}


?>