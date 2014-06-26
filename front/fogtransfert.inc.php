<?php

/** fogtransfert.inc.php
**	Fonctions pour FOG Transfert
**/

function getConfiguration()
{
	global $DB;
	
	$query = "SELECT * FROM glpi_plugin_fogtransfert_config";
	$result = $DB->query($query);
	
	if($DB->numrows($result) > 0)
	{
		$i = 0;
		while($row = $DB->fetch_assoc($result)) 
		{
			if(!empty($row['fog_address']))
			{
				$config['fog_address'] = $row['fog_address'];
			}
			else
			{
				$config['fog_address'] = "";
			}
			
			if(!empty($row['user_db_fog']))
			{
				$config['user_db_fog'] = $row['user_db_fog'];
			}
			else
			{
				$config['user_db_fog'] = "";
			}

			if(!empty($row['pass_db_fog']))
			{
				$config['pass_db_fog'] = $row['pass_db_fog'];
			}
			else
			{
				$config['pass_db_fog'] = "";
			}
			
			if(!empty($row['name_db_fog']))
			{
				$config['name_db_fog'] = $row['name_db_fog'];
			}
			else
			{
				$config['name_db_fog'] = "";
			}
			
			$retour[$i] = $config;
			$i++;
		}
	}
	else
	{
		$retour = "";
	}
	
	return $retour;
}

function setConfiguration($fog_address, $user_db_fog, $pass_db_fog, $name_db_fog)
{
	global $DB;

	if(($fog_address != null) && ($user_db_fog != null) && ($pass_db_fog != null) && ($name_db_fog != null))
	{
		$query = "INSERT INTO glpi.glpi_plugin_fogtransfert_config VALUES ('".$fog_address."', '".$user_db_fog."', '".$pass_db_fog."', '".$name_db_fog."')";
		$DB->query($query) or die($DB->error());
		redirection("index.php");
		
		return true;
	}
	else
	{
		redirection("index.php?fog_parameters_set_champs");
		
		return false;
	}
}

function UpdateConfiguration($fog_address, $user_db_fog, $pass_db_fog, $name_db_fog)
{
	global $DB;

	if(($fog_address != null) && ($user_db_fog != null) && ($pass_db_fog != null) && ($name_db_fog != null))
	{
		$query = "UPDATE glpi.glpi_plugin_fogtransfert_config SET fog_address='".$fog_address."', user_db_fog='".$user_db_fog."', pass_db_fog='".$pass_db_fog."', name_db_fog='".$name_db_fog."' LIMIT 1";
		$DB->query($query) or die($DB->error());
		redirection("index.php");
		
		return true;
	}
	else
	{
		redirection("index.php?fog_parameters_update_champs");
		
		return false;
	}
}

function showForm($fog_address,$user_db_fog,$pass_db_fog,$name_db_fog,$update)
{
	if(!$fog_address and !$user_db_fog and !$pass_db_fog and !$name_db_fog and !$update)
	{
		if(isset($_GET['fog_parameters_set_champs']))
		{
			echo '<span style="color:#CC0011;">Tous les paramètres de connexion à la base de données de FOG n\'ont pas été renseignés !</span><br><br>'."\n";
		}
		echo '<div class="showForm">
			<form action="index.php" method="get">
				<input type="hidden" name="fog_parameters_set" value="true">
				<table border="0">
				<tr>
					<td>Adresse du serveur FOG :</td><td><input type="text" name="fog_address" placeholder="localhost"></td>
				</tr>
				<tr>
					<td>Nom d\'utilisateur de la base de données de FOG : </td><td> <input type="text" name="user_db_fog"></td>
				</tr>
				<tr>			
					<td>Mot de passe de la base de données de FOG :</td><td> <input type="password" name="pass_db_fog"></td>
				</tr>
				<tr>
					<td>Base de données de FOG :</td><td> <input type="text" name="name_db_fog" placeholder="fog"><br></td>
				</tr>
				<tr>
				<td><br><input type="submit" value="Enregistrer les paramètres de connexion !"></td>
				</tr>
				</table>
			</form>
		</div>'."\n";
	}
	else
	{
		echo '<h1 class="initbis">Paramètres de connexion à la base de données FOG actuels</h1><br>'."\n";
		if(isset($_GET['fog_parameters_update_champs']))
		{
			echo '<span style="color:#CC0011;">Tous les paramètres de connexion à la base de données de FOG n\'ont pas été renseignés !</span><br><br>'."\n";
		}
		echo '<div class="showForm">
			<form action="index.php" method="get">
			<input type="hidden" name="fog_parameters_update" value="true">
			<table border="0">
			<tr>
				<td>Adresse du serveur FOG :</td><td><input type="text" name="fog_address" value="'.$fog_address.'"></td>
			</tr>
			<tr>
				<td>Nom d\'utilisateur de la base de données de FOG : </td><td> <input type="text" name="user_db_fog" value="'.$user_db_fog.'"></td>
			</tr>
			<tr>			
				<td>Mot de passe de la base de données de FOG :</td><td> <input type="password" name="pass_db_fog" value="'.$pass_db_fog.'"></td>
			</tr>
			<tr>
				<td>Base de données de FOG :</td><td> <input type="text" name="name_db_fog" value="'.$name_db_fog.'"><br></td>
			</tr>
			<tr>
			<td><br><input type="submit" value="Mettre à jour les paramètres de connexion !"></td>
			</tr>
			</table>
			</form>
		</div>'."\n";
	}
}

function testFOGconnect($arrayFOG)
{
	$mysqli_fog = new mysqli($arrayFOG['0']['fog_address'],$arrayFOG['0']['user_db_fog'],$arrayFOG['0']['pass_db_fog'],$arrayFOG['0']['name_db_fog']);
	if($mysqli_fog->connect_error)
	{
		fogtransfert_style("Echec lors de la connexion à la base de données de FOG !");
		echo '<b>Erreur détaillée retournée :</b> <span class="mysql_error_green">"'.$mysqli_fog->connect_error.'"</span><br><br>
		Cette erreur peut-être due à :<br><br> 
		<ul class="listconseil">
			<li class="spacer">De mauvais paramètres de connexion</li>
			<li class="spacer">Serveur FOG éteint ou injoignable</li>
			<li class="spacer">Serveur MySQL FOG qui n\'écoute qu\'en local et pas sur l\'interface réseau</li>
			<ul class="listconseil">
				<li class="spacer">Résolution : <code>netstat -nlpta | grep mysql</code></li>
				<li class="spacer">Le serveur MySQL de FOG doit écouter sur 0.0.0.0:3306 et non pas 127.0.0.1:3306</li>
				<li class="spacer">Éditer le fichier <code>/etc/mysql/my.cnf</code> ligne 47 ; <code>bind-address =&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;0.0.0.0</code></li>
			</ul>
		</ul><br>'."\n";
		showForm($arrayFOG['0']['fog_address'],$arrayFOG['0']['user_db_fog'],$arrayFOG['0']['pass_db_fog'],$arrayFOG['0']['name_db_fog'],$fog_parameters_true);
		return false;
	}
	else
	{
		return true;
	}
}

function getAbsolutePath()
{
	return str_replace("plugins/fogtransfert/front/index.php", "", $_SERVER['SCRIPT_FILENAME']);
}
	
function getHttpPath()
{
    $temp = explode("/", $_SERVER['HTTP_REFERER']);
    $ref = "";
    foreach($temp as $value)
	{
        if($value != "front")
		{
			$ref .= $value."/";
		}
        else
		{
			break;
		}
	}
	
    return $ref;
}

function fogtransfert_style($titre)
{
	echo '<link rel="stylesheet" href="fogtransfert.css?v='.time().'" type="text/css">
	<script type="text/javascript" src="fogtransfert.js?v='.time().'"></script>
	
	<h1><img src="fog-logo.png" alt="FOG">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<sup>Transfert</sup></h1><br><br>'."\n";
	
	if($titre != null)
	{
		echo '<h1 class="init">'.$titre.'</h1>'."\n";
	}
}

function redirection($url)
{
   die('<meta http-equiv="refresh" content="0; URL='.$url.'">');
}
?>
