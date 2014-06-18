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
		while ($row = $DB->fetch_assoc($result)) 
			{
			/** FOG ADDRESS **/
			if (!empty($row['fog_address'])){$config['fog_address'] = $row['fog_address'];}
			else{$config['fog_address'] = "";echo "Aucune valeur renseignée dans la base de configuration FOG";}
			
			/** USER DB FOG **/
			if (!empty($row['user_db_fog'])){$config['user_db_fog'] = $row['user_db_fog'];}
			else{$config['user_db_fog'] = "";echo "Aucune valeur renseignée dans la base de configuration FOG";}
			
			/** PASS DB FOG **/
			if (!empty($row['pass_db_fog'])){$config['pass_db_fog'] = $row['pass_db_fog'];}
			else{$config['pass_db_fog'] = "";echo "Aucune valeur renseignée dans la base de configuration FOG";}
			
			/** NAME DB FOG **/
			if (!empty($row['name_db_fog'])){$config['name_db_fog'] = $row['name_db_fog'];}
			else{$config['name_db_fog'] = "";echo "Aucune valeur renseignée dans la base de configuration FOG";}
			
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

function setConfiguration($fog_address,$user_db_fog,$pass_db_fog,$name_db_fog)
{
	global $DB;
			
	if(($fog_address != null) && ($user_db_fog != null) && ($pass_db_fog != null) && ($name_db_fog != null))
	{
		$query = "INSERT INTO glpi.glpi_plugin_fogtransfert_config VALUES ('".$fog_address."','".$user_db_fog."','".$pass_db_fog."','".$name_db_fog."')";
		$DB->query($query) or die($DB->error());
		return true;
	}
	else
	{
		echo "Veuillez completer tous les champs !";
		return false;
	}
}

function UpdateConfiguration($fog_address,$user_db_fog,$pass_db_fog,$name_db_fog)
{
	global $DB;
			
	if(($fog_address != null) && ($user_db_fog != null) && ($pass_db_fog != null) && ($name_db_fog != null))
	{
		$query = "UPDATE glpi.glpi_plugin_fogtransfert_config SET fog_address='".$fog_address."', user_db_fog='".$user_db_fog."', pass_db_fog='".$pass_db_fog."', name_db_fog='".$name_db_fog."' LIMIT 1";
		$DB->query($query) or die($DB->error());
		return true;
	}
	else
	{
		echo "Veuillez completer tous les champs !";
		return false;
	}
}

function showForm($fog_address,$user_db_fog,$pass_db_fog,$name_db_fog,$update)
{
	if(!$fog_address and !$user_db_fog and !$pass_db_fog and !$name_db_fog and !$update)
	{
		echo '<div class="showForm">
		<form action="?connexion_fog" method="GET">
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
		<tr><td><br/><input type="submit" name="submit" value="Enregistrer les parametres de connexion"></td></tr>
		</table>
		</form>
		</div>';
	}
	else
	{
		echo '<h1 class="initbis">Paramètres FOG actuels</h1>
		<div class="showForm">
		<form action="?connexion_fog" method="GET">
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
		<tr><td><br/><input type="hidden" name="fog_parameters_update" value="true"><input type="submit" name="submit" value="Mettre à jour les paramètres de connexion"></td></tr>
		</table>
		</form></div>';
	}
}

function testFOGconnect($arrayFOG)
{
	$mysqli_fog = new mysqli($arrayFOG['0']['fog_address'],$arrayFOG['0']['user_db_fog'],$arrayFOG['0']['pass_db_fog'],$arrayFOG['0']['name_db_fog']);
	if($mysqli_fog->connect_error)
	{
		fogtransfert_style("Echec lors de la connexion MySQL sur FOG !");
		//echo '<h1 class="init">Echec lors de la connexion MySQL sur FOG</h1>';
		echo '<b>Erreur détaillée retournée :</b> <span class="mysql_error_green">"'.$mysqli_fog->connect_error.'"</span><br><br>';
		echo 'Cette erreur peut-être due à :<br><br> 
		<ul class="listconseil">
			<li class="spacer">De mauvais paramètres de connexion</li>
			<li class="spacer">Serveur FOG éteint ou injoignable</li>
			<li class="spacer">Serveur MySQL FOG qui n\'écoute qu\'en local et pas sur l\'interface réseau</li>
			<ul class="listconseil">
				<li class="spacer">Résolution : <code>netstat -nlpta | grep mysql</code></li>
				<li class="spacer">Le serveur MySQL de FOG doit écouter sur 0.0.0.0:3306 et pas 127.0.0.1:3306</li>
				<li class="spacer">Éditer le fichier <code>/etc/mysql/my.cnf</code> ligne 47 ; <code>bind-address = 0.0.0.0</code></li>
			</ul>
		</ul><br>';
		showForm($arrayFOG['0']['fog_address'],$arrayFOG['0']['user_db_fog'],$arrayFOG['0']['pass_db_fog'],$arrayFOG['0']['name_db_fog'],$fog_parameters_true);
		return false;
	}
	else
	{
		return true;
	}
}

function getAbsolutePath()
    {return str_replace("plugins/fogtransfert/front/index.php", "", $_SERVER['SCRIPT_FILENAME']);}
	
function getHttpPath()
{
    $temp = explode("/",$_SERVER['HTTP_REFERER']);
    $Ref = "";
    foreach ($temp as $value)
        {
        if($value != "front"){$Ref.= $value."/";}
        else{break;}
        }
    return $Ref;
}

function fogtransfert_style($titre)
{
	echo '<link rel="stylesheet" href="fogtransfert.css" type="text/css">';
	echo '<script type="text/javascript" src="fogtransfert.js"></script>';
	
	echo '<h1><img src="fog-logo.png" alt="FOG"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<sup>Transfert</sup></h1>';
	echo '<br><br>';
	if($titre != null)
	{
		echo '<h1 class="init">'.$titre.'</h1>';
	}
}

function redirige($url)
{
   die('<meta http-equiv="refresh" content="5;URL='.$url.'">');
}



?>
