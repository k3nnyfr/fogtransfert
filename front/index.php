<?php
include_once("fogtransfert.inc.php");
// Absolute PATH pour les lib GLPI
define("GLPI_ROOT", getAbsolutePath());
include(GLPI_ROOT."inc/includes.php");

Session::checkCentralAccess();

if(isset($_GET['fog_address']) && isset($_GET['user_db_fog']) && isset($_GET['pass_db_fog']) && isset($_GET['name_db_fog']))
{
	$fog_address = $_GET['fog_address'];
	$user_db_fog = $_GET['user_db_fog'];
	$pass_db_fog = $_GET['pass_db_fog'];
	$name_db_fog = $_GET['name_db_fog'];
	
	if(!isset($_GET['fog_parameters_update']))
	{
		setConfiguration($fog_address, $user_db_fog, $pass_db_fog, $name_db_fog);
	}
	else 
	{
		UpdateConfiguration($fog_address, $user_db_fog, $pass_db_fog, $name_db_fog);
	}
}
else
{
	$config = getConfiguration();
	if($config != null)
	{
		HTML::header('FOG Transfert plugin GLPI');
		
		if(testFOGconnect($config))
		{
			// Connexion à la base de données de FOG
			$mysqli_fog = new mysqli($config['0']['fog_address'],$config['0']['user_db_fog'],$config['0']['pass_db_fog'],$config['0']['name_db_fog']);
			if($mysqli_fog->connect_error)
			{
				fogtransfert_style("Connexion fog impossible");
				echo "Connexion fog impossible<br>";				
			}
			
			if(isset($_GET['fog_add_hosts']))
			{
				if(isset($_GET['checkbox']))
				{
					fogtransfert_style("Exportation vers FOG");
					$checkbox = $_GET['checkbox'];
					echo '<div id="box" class="green">
					<br>&nbsp; &nbsp;<b>Viennent d\'être exportés vers FOG ('.sizeof($checkbox).')</b><br><br>
					<div id="contenu_green">
					<table border="0">'."\n";
					for($i = 0; $i < sizeof($checkbox); $i++)
					{
						$explode = explode('||', $checkbox[$i]);
						$name = $explode[0];
						$mac = $explode[1];
						$requete_ajout_host_fog = "INSERT INTO hosts VALUES ('', '".substr($name, 0, 16)."', '".substr($name, 0, 16)." importé depuis GLPI le ".date("d/m/Y à H:i:s", time())."', '', '0', '0', '".date("Y-m-d H:i:s", time())."', '0000-00-00 00:00:00', 'fog', '".$mac."', '', '', '', '', '', '', '', '', '')";
						$mysqli_fog->query($requete_ajout_host_fog);
						if($mysqli_fog == true)
						{
							echo "<tr>"."\n";
							echo '<td width="175">'.$name.'</td><td>adresse MAC '.strtoupper($mac).'</td>';
							echo "</tr>"."\n";
						}
					}
					echo '</table>
					<br>
					<input type="submit" onclick="location.replace(\'index.php\');" value="OK c\'est parfait !">
					</div>
					</div>';
				}
				else
				{
					redirection("index.php");
				}
			}
			else
			{
				fogtransfert_style("FOG Transfert - Transfert depuis GLPI vers FOG");
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
				
				// Compteurs
				$compteur_orange = 0;
				$compteur_red = 0;
				for($i = 0; $i < sizeof($glpi); $i++)
				{
					if(array_search(substr($glpi[$i]['name'], 0, 16), $fog_hostName) !== array_search($glpi[$i]['mac'], $fog_hostMAC))
					{
						$compteur_orange = $compteur_orange + 1;
					}
				}
				
				for($i = 0; $i < sizeof($glpi); $i++)
				{
					if(!array_search(substr($glpi[$i]['name'], 0, 16), $fog_hostName) and !in_array($glpi[$i]['mac'], $fog_hostMAC))
					{
						$compteur_red = $compteur_red + 1;
					}
				}
				$compteur_green = sizeof($glpi)-$compteur_orange-$compteur_red;
				// Compteurs
				
				if(sizeof($glpi) < 1)
				{
					echo '<div id="box" class="grey">
					<br>&nbsp; &nbsp;<b>Actuellement dans FOG ('.sizeof($fog_hostName).')</b>&nbsp; &nbsp;<a href="#contenu_grey" onclick="contenu_grey()" class="lien_afficher_masquer">Afficher/Masquer</a><br><br>
					<div id="contenu_grey" style="display:block;">
					<table border="0">'."\n";
					for($i = 0; $i < sizeof($fog_hostName); $i++)
					{
						echo "<tr>"."\n";
						echo '<td width="175">'.$fog_hostName[$i].'</td><td>adresse MAC '.strtoupper($fog_hostMAC[$i]).'<br></td>'."\n";
						echo "</tr>"."\n";
					}
					echo '</table>
					</div>
					</div>'."\n";
				}
				else
				{
					echo '<div id="box" class="green">
					<br>&nbsp; &nbsp;<b>Déjà présents dans FOG ('.$compteur_green.')</b>&nbsp; &nbsp;<a href="#contenu_green" onclick="contenu_green()" class="lien_afficher_masquer">Afficher/Masquer</a><br><br>
					<div id="contenu_green" style="display:none;">
					<table border="0">'."\n";
					for($i = 0; $i < sizeof($glpi); $i++)
					{
						if(array_search(substr($glpi[$i]['name'], 0, 16), $fog_hostName) and in_array($glpi[$i]['mac'], $fog_hostMAC))
						{
							echo "<tr>"."\n";
							echo '<td width="175">'.substr($glpi[$i]['name'], 0, 16).'</td><td>adresse MAC '.strtoupper($glpi[$i]['mac']).'<br></td>'."\n";
							echo "</tr>"."\n";
						}
					}
					if($compteur_orange > 0)
					{
						$display_orange = "block";
					}
					else
					{
						$display_orange = "none";
					}
					echo '</table>
					</div>
					</div>
					<div id="box" class="orange">
					<br>&nbsp; &nbsp;<b>Requièrent votre attention ('.$compteur_orange.')</b>&nbsp; &nbsp;<a href="#contenu_orange" onclick="contenu_orange()" class="lien_afficher_masquer">Afficher/Masquer</a><br><br>
					<div id="contenu_orange" style="display:'.$display_orange.';">'."\n";
					for($i = 0; $i < sizeof($glpi); $i++)
					{
						if(array_search(substr($glpi[$i]['name'], 0, 16), $fog_hostName) !== array_search($glpi[$i]['mac'], $fog_hostMAC))
						{
							if(array_search(substr($glpi[$i]['name'], 0, 16), $fog_hostName) == null)
							{
								echo 'L\'adresse MAC '.strtoupper($glpi[$i]['mac']).' a été trouvée mais n\'est pas liée à '.substr($glpi[$i]['name'], 0, 16).'<br>'."\n";
							}
							elseif(array_search($glpi[$i]['mac'], $fog_hostMAC) == null)
							{
								echo substr($glpi[$i]['name'], 0, 16).' a été trouvé mais n\'est pas lié à l\'adresse MAC '.strtoupper($glpi[$i]['mac']).'<br>'."\n";
							}
							else
							{
								echo substr($glpi[$i]['name'], 0, 16).', adresse MAC '.$glpi[$i]['mac'].') - Erreur inconnue<br>'."\n";
							}
						}
					}
					if($compteur_red > 0)
					{
						$display_red = "block";
					}
					else
					{
						$display_red = "none";
					}
					echo '</div>
					</div>
					<div id="box" class="red">
					<br>&nbsp; &nbsp;<b>Pouvant être ajoutés à FOG ('.$compteur_red.')</b>&nbsp; &nbsp;<a href="#contenu_red" onclick="contenu_red()" class="lien_afficher_masquer">Afficher/Masquer</a><br><br>
					<div id="contenu_red" style="display:'.$display_red.';">'."\n";
					if($compteur_red > 0)
					{
						echo 'Les PCs suivants n\'ont pas été trouvés dans FOG, sélectionnez quels PCs vous souhaitez ajouter :<br><br>
						<form action="index.php" method="get">
						<input type="hidden" name="fog_add_hosts" value="true">
						<table border="0">
						<tr>
						<td><input type="checkbox" id="checkboxes" onclick="check_all_checkboxes(this)"> Sélectionner tous</td>
						</tr>
						<tr>
						<td>&nbsp;</td>
						</tr>'."\n";
					}
					for($i = 0; $i < sizeof($glpi); $i++)
					{
						if(!array_search(substr($glpi[$i]['name'], 0, 16), $fog_hostName) and !in_array($glpi[$i]['mac'], $fog_hostMAC))
						{
							echo "<tr>"."\n";
							echo '<td width="175"><input type="checkbox" name="checkbox[]" value="'.$glpi[$i]['name'].'||'.$glpi[$i]['mac'].'"> '.$glpi[$i]['name'].'</td><td>adresse MAC '.strtoupper($glpi[$i]['mac']).'</td>'."\n";
							echo "</tr>"."\n";
						}
					}
					if($compteur_red > 0)
					{
						echo '</table>
						<br><input type="submit" value="Ajouter sélectionné(s)">
						</form>'."\n";
					}
					echo '</div>
					</div>'."\n";
				}
			}
		}
	}
	else
	{
		// Mode d'installation du plugin
		HTML::header('FOGTransfert plugin GLPI - Installation');
		fogtransfert_style("Initialisation des paramètres de connexion à la base de données de FOG");
		// echo "<h3>Attention, aucune donnée n'est renseignée dans la base de données FOG Transfert</h3>"."\n";
		showForm();
	}
}

HTML::footer();
?>
