<head>
    <link rel="stylesheet" type="text/css" href="lib/style.css">
    <?php
    $escaped_url = htmlspecialchars($_SERVER['PHP_SELF']);
    echo '<script>';
    echo 'var escapedUrl = "' . $escaped_url . '";';
    echo '</script>';
    ?>
</head>
<script>
	function faireExplorateur(directory, numero, element){
		if(document.getElementById('explorateur-'+numero).innerHTML == ''){
			var xhr = new XMLHttpRequest();
			xhr.open('GET', 'explorateur.php?directory='+directory, true);
			xhr.onreadystatechange = function(){
				if(xhr.readyState == 4 && xhr.status == 200){
					document.getElementById('explorateur-'+numero).innerHTML = xhr.responseText;
					element.src = 'img/moins.png';
				}
			}
			xhr.send();
		}
		else{
			document.getElementById('explorateur-'+numero).innerHTML = '';
			element.src = 'img/plus.png';
		}
	}

    function updateChemin(chemin){
        document.getElementById('chemin').value = chemin;
    }
</script>
<?php
ob_start();

include_once 'lib/generateurdocuments.lib.php';
require_once '../../main.inc.php';

//on met le compteur dans les variables de session
if (!isset($_SESSION['compteurDirectory'])) {
    $_SESSION['compteurDirectory'] = 0;
}
$compteurDirectory = &$_SESSION['compteurDirectory'];

llxHeader("", $langs->trans("GenerateurDocumentsArea"), '', '', 0, 0, '', '', '', 'mod-generateurdocuments page-index');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["fileToUpload"]) &&isset($_POST['chemin'])) {
    $target_dir = $_POST['chemin'];
    if(strpos($target_dir, "..") !== false && $target_dir[0] != "/"){
        echo "<p style='color: red;'>Désolé, vous ne pouvez pas remonter dans l'arborescence // chemin d'accès incorrect</p>";
        return;
    }
    else{
        $target_dir = "../../../documents" . $target_dir . "/";
    }
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Vérifiez si le fichier existe déjà
    if (file_exists($target_file)) {
        echo "<p style='color: red;'>Désolé, le fichier existe déjà.</p>>";
        $uploadOk = 0;
    }

    // Vérifiez la taille du fichier (10 Mo = 10 * 1024 * 1024 octets)
    if ($_FILES["fileToUpload"]["size"] > 10 * 1024 * 1024) {
        echo "Désolé, votre fichier est trop volumineux et ne peut excéder 10Mo.<br>";
        $uploadOk = 0;
    }

    // Autoriser certains formats de fichiers
    $allowedTypes = array("jpg", "png", "jpeg", "gif", "pdf", "doc", "docx", "xls", "xlsx", "txt", "ods", "odt");
    if (!in_array($fileType, $allowedTypes)) {
        echo "Désolé, format de fichier non autorisé.<br>";
        $uploadOk = 0;
    }

    // Vérifiez si $uploadOk est défini à 0 par une erreur
    if ($uploadOk == 0) {
        echo "Désolé, votre fichier n'a pas été téléchargé.<br>";
    // Si tout est ok, essayez de télécharger le fichier
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "Le fichier ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " a bien été téléchargé.<br>";
        } else {
            echo "Désolé, une erreur s'est produite lors du téléchargement de votre fichier.<br>";
        }
    }
}




echo "<h1>Banque de Documents</h1>";

//parti permettant d'ajouter un document
echo "<h2>Ajouter un document</h2>";
genererFormulaireAjoutDocument();



//parti permettant d'exporter un document
echo "<h2>Exporter un document</h2>";
genererExplorateur();


llxFooter();

ob_end_flush();
?>
<!-- <div id="popup">
   <form action="generateurdocumentsindex.php" method="post" enctype="multipart/form-data">
        <?php
            //$token = newToken();
            //echo '<input type="hidden" name="token" value="'.$token.'">';
        ?>
        <input type="hidden" id="InputNvDos" name="cheminNvDos">
        <input type="text" name="nomDossier" placeholder="entrer le nom du dossier"/><br>
        <button type="submit" value="creerDossier" name="submitnvdossier" id="buttonPopup">Créer</button>
    </form>
</div> -->
