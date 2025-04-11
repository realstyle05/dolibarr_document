<script>
	function changeIsSelected(element) {
		// Remove the class 'isSelected' from all elements
		var selectedElements = document.querySelectorAll('.isSelected');
		selectedElements.forEach(function(el) {
			el.classList.remove('isSelected');
		});

		// Add the class 'isSelected' to the clicked element
		element.classList.add('isSelected');
	}

	function genererPopupAddDirectory(element, chemin) {
        var NomDossier = prompt("Entrez le nom du dossier", "Nouveau dossier");
        if (NomDossier != null) {
            var xhr = new XMLHttpRequest();
            if(chemin == "/"){
                chemin = "";
            }
            xhr.open('GET', 'creationDossier.php?directory=' + chemin + '/' + NomDossier, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    reponse = xhr.responseText;
                    alert(reponse);
                    location.reload();
                }
            }
            xhr.send();
        }
	}
</script>

<?php
/* Copyright (C) 2025		SuperAdmin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */
/**
 * \file    generateurdocuments/lib/generateurdocuments.lib.php
 * \ingroup generateurdocuments
 * \brief   Library files with common functions for GenerateurDocuments
 */

/**
 * Prepare admin pages header
 *
 * @return array<array{string,string,string}>
 */
function generateurdocumentsAdminPrepareHead()
{
	global $langs, $conf;

	// global $db;
	// $extrafields = new ExtraFields($db);
	// $extrafields->fetch_name_optionals_label('myobject');

	$langs->load("generateurdocuments@generateurdocuments");

	$h = 0;
	$head = array();

	$head[$h][0] = dol_buildpath("/generateurdocuments/admin/setup.php", 1);
	$head[$h][1] = $langs->trans("Settings");
	$head[$h][2] = 'settings';
	$h++;

	/*
	$head[$h][0] = dol_buildpath("/generateurdocuments/admin/myobject_extrafields.php", 1);
	$head[$h][1] = $langs->trans("ExtraFields");
	$nbExtrafields = is_countable($extrafields->attributes['myobject']['label']) ? count($extrafields->attributes['myobject']['label']) : 0;
	if ($nbExtrafields > 0) {
		$head[$h][1] .= ' <span class="badge">' . $nbExtrafields . '</span>';
	}
	$head[$h][2] = 'myobject_extrafields';
	$h++;
	*/

	$head[$h][0] = dol_buildpath("/generateurdocuments/admin/about.php", 1);
	$head[$h][1] = $langs->trans("About");
	$head[$h][2] = 'about';
	$h++;

	// Show more tabs from modules
	// Entries must be declared in modules descriptor with line
	//$this->tabs = array(
	//	'entity:+tabname:Title:@generateurdocuments:/generateurdocuments/mypage.php?id=__ID__'
	//); // to add new tab
	//$this->tabs = array(
	//	'entity:-tabname:Title:@generateurdocuments:/generateurdocuments/mypage.php?id=__ID__'
	//); // to remove a tab
	complete_head_from_modules($conf, $langs, null, $head, $h, 'generateurdocuments@generateurdocuments');

	complete_head_from_modules($conf, $langs, null, $head, $h, 'generateurdocuments@generateurdocuments', 'remove');

	return $head;
}

//mes variables globales

function genererFormulaireAjoutDocument()
{
	// Générer un token CSRF en utilisant la fonction newToken
    $csrf_token = newToken();
	
	echo '<form action="' .htmlspecialchars($_SERVER['PHP_SELF']). '" method="post" enctype="multipart/form-data">';
	echo '<p>Sélectionnez le fichier à importer :</p>>';
	echo '<input type="file" name="fileToUpload" id="fileToUpload"><br>';
	echo '<input type="hidden" name="token" value="'.$csrf_token.'">';
	echo '<p>Sélectionnez le chemin d\'accès :</p>';
	echo '<input type="text" name="chemin" id="chemin" value="/" style="display: none;">';
	listerDossier();
	echo '<button type="submit" value="Importer" name="submit">Importez le fichier</button>';
	echo '</form>';
}

function listerDossier($directory = "../../../documents"){
	$contenu = scandir($directory);
	$cheminparent = substr($directory, 18) ;
	$token = newToken();
	echo '<ul>';
	if($directory == "../../../documents"){
		echo '<li onclick="updateChemin(\'/\');changeIsSelected(this);"><img src="img/dossier.png"width="20"/>/';
		echo '<img src="img/ajouter-le-fichier.png"width="15" class="hover-image" onclick="genererPopupAddDirectory(this, \'/\')"/></li>';
	}
	// form de creation du dossier

	echo '<ul>';
	foreach($contenu as $element){
		if($element != "." && $element != ".."){
			if(is_dir($directory . '/' . $element)){
				echo '<li onclick="updateChemin(\'' . addslashes($cheminparent . '/' . $element) . '\');changeIsSelected(this);"><img src="img/dossier.png"width="20"/>';
				echo $element;
				echo '<img src="img/ajouter-le-fichier.png"width="15" class="hover-image" onclick="genererPopupAddDirectory(this, \'' . addslashes($cheminparent . '/' . $element) . '\')"/>';
				echo '</li>';
				listerDossier($directory . '/' . $element);
			}
		}
	}
	echo '</ul>';
	echo '</ul>';
}

function genererExplorateur($directory = "../../../documents/"){
    global $compteurDirectory; //pour les dossiers imbriqués
	$files = scandir($directory);
    echo '<ul>';
    foreach($files as $file){
        if($file != "." && $file != ".." && $file[0] != '.'){
            $filePath = $directory . $file;
            if(is_file($filePath)){
				$fileInfo = pathinfo($filePath);
				$fileType = $fileInfo['extension'];
				switch($fileType){
					case 'pdf':
						echo '<li><img src="img/pdf.png "width="20"/>';
						echo '<a href="'.$directory.$file.'">'.$file.'</a>';
						echo '<a href="'.$directory.$file.'" download><img src="img/telecharger.png" class="PNGtelecharger"/></a></li>';
						break;
					case 'doc':
					case 'docx':
					case 'odt':
						echo '<li><img src="img/doc.png "width="20"/>';
						echo $file;
						echo '<a href="'.$directory.$file.'" download><img src="img/telecharger.png" class="PNGtelecharger"/></a></li>';
						break;
					case 'jpg':
					case 'jpeg':
					case 'png':
					case 'gif':
						echo '<li><img src="img/image.png "width="20"/>';
						echo '<a href="'.$directory.$file.'">'.$file.'</a>';
						echo '<a href="'.$directory.$file.'" download><img src="img/telecharger.png" class="PNGtelecharger"/></a></li>';
						break;
					case 'txt':
						echo '<li><img src="img/txt.png "width="20"/>';
						echo '<a href="'.$directory.$file.'">'.$file.'</a>';
						echo '<a href="'.$directory.$file.'" download><img src="img/telecharger.png" class="PNGtelecharger"/></a></li>';
						break;
					case 'xls':
					case 'xlsx':
					case 'ods':
						echo '<li><img src="img/xls.png "width="20"/>';
						echo $file;
						echo '<a href="'.$directory.$file.'" download><img src="img/telecharger.png" class="PNGtelecharger"/></a></li>';
						break;
					default:
						echo '<li>';
						echo $file;
						echo '<a href="'.$directory.$file.'" download><img src="img/telecharger.png" class="PNGtelecharger"/></a></li>';
						break;
				}
            }
			else{
				echo '<li><img src="img/plus.png" class="PNGplus" onclick="faireExplorateur(\'' . addslashes($directory.$file) . '\', \''.$compteurDirectory.'\', this)"/>';
				echo '<img src="img/dossier.png"width="20"/>'.$file;
				//genererExplorateur($filePath . '/');
				echo '</li>';
				echo '<div id="explorateur-'.$compteurDirectory.'"></div>';
				$compteurDirectory++;
			}
        }
    }
    echo '</ul>';
}

?>
