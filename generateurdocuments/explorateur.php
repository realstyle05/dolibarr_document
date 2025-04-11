<?php
require_once 'lib/generateurdocuments.lib.php';

if (isset($_GET['directory'])) {
    $directory = $_GET['directory'] . "/";
    genererExplorateur($directory);
} else {
    echo "Aucun dossier spécifié.";
}
?>