<?php

/**
 * Class pour la partie gestion de la configuration
 */
class FogTransfertConfig extends CommonDBTM
{
	/**
	 * Récupère les informations de configuration enregistrées
	 * Retourne un tableau avec les identifiants, mot de passe et adresse du serveur FOG
	 * @global type $DB
	 * @return string
	 */
	function getConfiguration()
	{
		global $DB;
		
		$query = "SELECT * FROM glpi_plugin_fogtransfert_config";
		if ($result = $DB->query($query))
		{
			if ($DB->numrows($result) > 0)
			{
				$i = 0;
				while ($row = $DB->fetch_assoc($result)) 
					{
					/** FOG ADDRESS **/
					if (!empty($row['fog_address'])){$config['fog_address'] = $row['fog_address'];}
					else{$config['id'] = "";echo "Aucune valeur renseignée dans la base de configuration FOG";}
					
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
		}
		return $retour;
	}    
        
	/**
	 * Enregistre ou modifie une information de configuration
	 * @global type $DB
	 * @param type $id
	 * @param type $valeur
	 */
	function setConfiguration($fog_address,$user_db_fog,$pass_db_fog,$name_db_fog)
	{
		global $DB;
				
		if(($fog_address != null) && ($user_db_fog != null) && ($pass_db_fog != null) && ($name_db_fog != null))
		{
			$query = "UPDATE glpi_plugin_fogtransfert_config SET fog_address='".$fog_address."' AND user_db_fog ='".$user_db_fog."'
			AND pass_db_fog='".$pass_db_fog."' AND name_db_fog='".$name_db_fog."';";
			$DB->query($query) or die($DB->error());
		}
		else
		{
			echo "Veuillez completer tous les champs !";
		}
	}        
}
?>