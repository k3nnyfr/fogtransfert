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
		echo "<h3>Attention, aucune donnée n'est renseignée dans la base de données FOG Transfert</h3><br/><br/>";
		showForm();
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

function showForm($fog_address,$user_db_fog,$pass_db_fog,$name_db_fog,$validate)
{
	echo '<form action="?connexion_fog" method="GET">
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
	<tr><td><br/><input type="submit" name="submit" value="Tester la connexion"></td></tr>
	</table>
	</form>';
}

function testFOGconnect($arrayFOG)
{
	$mysqli_fog = new mysqli($arrayFOG['0']['fog_address'],$arrayFOG['0']['user_db_fog'],$arrayFOG['0']['pass_db_fog'],$arrayFOG['0']['name_db_fog']);
	if($mysqli_fog->connect_error)
	{
		echo "<b>Echec lors de la connexion à MySQL : ".$mysqli_fog->connect_error."</b><br>";
		return false;
	}
	else
	{
		// echo "<span style=\"color: green; font-size: 10pt\">Connexion FOG Server OK!</span><br>";
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

function fogtransfert_style()
{
	echo '<link rel="stylesheet" href="fogtransfert.css" type="text/css">';
	echo '<script type="text/javascript" src="fogtransfert.js"></script>';
	
	echo '<h1><img src="fog-logo.png" alt="FOG"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<sup>Transfert</sup></h1>';
	echo '<br><br>';
}

function redirige($url)
{
   die('<meta http-equiv="refresh" content="5;URL='.$url.'">');
}



?>