<?php

include_once ('fogtransfert.inc.php');
// Absolute PATH pour les lib GLPI
define('GLPI_ROOT', getAbsolutePath());
include (GLPI_ROOT."inc/includes.php");


if(isset($_GET['fog_address']) && isset($_GET['user_db_fog']) && isset($_GET['pass_db_fog']) && isset($_GET['name_db_fog']))
{
	$fog_address = $_GET['fog_address'];
	$user_db_fog = $_GET['user_db_fog'];
	$pass_db_fog = $_GET['pass_db_fog'];
	$name_db_fog = $_GET['name_db_fog'];
	
	setConfiguration($fog_address,$user_db_fog,$pass_db_fog,$name_db_fog);
	header("Location: ".$_SERVER["SCRIPT_NAME"]);
}
else
{
	$config = getConfiguration();
	if($config != null)
	{
		HTML::header('FOGTransfert GLPI');
		fogtransfert_style();
		
		if(testFOGconnect($config))
		{
			// Connexion à FOG
			$mysqli_fog = new mysqli($config['0']['fog_address'],$config['0']['user_db_fog'],$config['0']['pass_db_fog'],$config['0']['name_db_fog']);
			if($mysqli_fog->connect_error)
			{
				echo "Echec lors de la connexion à MySQL : ".$mysqli_fog->connect_error;
			}
			
			if(isset($_GET['submit']))
			{
				if(!isset($_GET['checkbox']))
				{
					echo "Erreur, aucun PC selectionné<br>";
					redirige("index.php");
				}
				else
				{
					$checkbox = $_GET['checkbox'];
					for($i = 0; $i < sizeof($checkbox); $i++)
					{
						$explode = explode('||', $checkbox[$i]);
						$name = $explode[0];
						$mac = $explode[1];
						$requete_ajout_host_fog = "INSERT INTO hosts VALUES ('', '".$name."', '".$name." importé depuis GLPI le ".date("d/m/Y à H:i:s", time())."', '', '0', '0', '".date("Y-m-d H:i:s", time())."', '0000-00-00 00:00:00', 'fog', '".$mac."', '', '', '', '', '', '', '', '', '')";
						$mysqli_fog->query($requete_ajout_host_fog);
					}
					header("Location: ".$_SERVER["SCRIPT_NAME"]);
				}
			}
			else
			{
				global $DB;
				
				$requete_pcs_glpi = "SELECT glpi_computers.name, glpi_networkports.mac FROM glpi_computers, glpi_networkports WHERE glpi_computers.id = glpi_networkports.items_id AND glpi_computers.is_deleted = 0 AND glpi_networkports.mac != '' AND glpi_networkports.logical_number = '1' ORDER BY glpi_computers.name";
				$query_pcs_glpi = $DB->query($requete_pcs_glpi);
				while($pcs_glpi = $query_pcs_glpi->fetch_array(MYSQLI_ASSOC))
				{
					$glpi[] = $pcs_glpi;
				}

				$requete_pcs_fog = "SELECT hostName, hostMAC FROM hosts ORDER BY hostName";
				$query_pcs_fog = $mysqli_fog->query($requete_pcs_fog);
				while($pcs_fog = $query_pcs_fog->fetch_array(MYSQLI_ASSOC))
				{
					$fog_hostName[] = $pcs_fog['hostName'];
					$fog_hostMAC[] = $pcs_fog['hostMAC'];
				}

				echo '<div id="box" class="green">
				<br>&nbsp; &nbsp;<b>PCs déjà présents dans FOG (99)</b>&nbsp; &nbsp;<a href="#" onclick="contenu_green()" class="lien_afficher_masquer">Afficher/Masquer</a><br><br>
				<div id="contenu_green" style="display:none;">'."\n";
				for($i = 0; $i < sizeof($glpi); $i++)
				{
					if(array_search(substr($glpi[$i]['name'], 0, 16), $fog_hostName) and in_array($glpi[$i]['mac'], $fog_hostMAC))
					{
						echo 'Le PC '.substr($glpi[$i]['name'], 0, 16).' (adresse MAC '.$glpi[$i]['mac'].') est déjà présent dans FOG.<br>'."\n";
					}
				}
				echo '</div>
				</div>
				<div id="box" class="orange">
				<br>&nbsp; &nbsp;<b>PCs qui requièrent votre attention (0)</b>&nbsp; &nbsp;<a href="#" onclick="contenu_orange()" class="lien_afficher_masquer">Afficher/Masquer</a><br><br>
				<div id="contenu_orange" style="display:none;">'."\n";
				for($i = 0; $i < sizeof($glpi); $i++)
				{
					if(array_search(substr($glpi[$i]['name'], 0, 16), $fog_hostName) !== array_search($glpi[$i]['mac'], $fog_hostMAC))
					{
						if(array_search(substr($glpi[$i]['name'], 0, 16), $fog_hostName) == null)
						{
							echo 'L\'adresse MAC '.$glpi[$i]['mac'].' a été trouvée mais pas avec le PC '.substr($glpi[$i]['name'], 0, 16).'<br>'."\n";
						}
						elseif(array_search($glpi[$i]['mac'], $fog_hostMAC) == null)
						{
							echo 'Le PC '.substr($glpi[$i]['name'], 0, 16).' a été trouvé mais pas avec l\'adresse MAC '.$glpi[$i]['mac'].'<br>'."\n";
						}
						else
						{
							echo 'PC '.substr($glpi[$i]['name'], 0, 16).', adresse MAC '.$glpi[$i]['mac'].') - Erreur inconnue<br>'."\n";
						}
					}
				}
				echo '</div>
				</div>
				<div id="box" class="red">
				<br>&nbsp; &nbsp;<b>PCs pouvant être ajoutés à FOG (0)</b>&nbsp; &nbsp;<a href="#" onclick="contenu_red()" class="lien_afficher_masquer">Afficher/Masquer</a><br><br>
				<div id="contenu_red" style="display:none;">
				Les PCs suivants n\'ont pas été trouvés dans FOG, sélectionnez quels PCs vous souhaitez ajouter :<br><br>
				<form action="'.$_SERVER["SCRIPT_NAME"].'?envoi" method="get">'."\n";
				for($i = 0; $i < sizeof($glpi); $i++)
				{
					if(!array_search(substr($glpi[$i]['name'], 0, 16), $fog_hostName) and !in_array($glpi[$i]['mac'], $fog_hostMAC))
					{
						echo '<input type="checkbox" name="checkbox[]" value="'.substr($glpi[$i]['name'], 0, 16).'||'.$glpi[$i]['mac'].'"> '.substr($glpi[$i]['name'], 0, 16).' (adresse MAC '.$glpi[$i]['mac'].')<br>'."\n";
					}
				}
				echo '<br>
				<input type="submit" name="submit" value="Envoyer">
				</form>
				</div>
				</div>'."\n";
			}
		}
	}	
}

HTML::footer();  

?>
