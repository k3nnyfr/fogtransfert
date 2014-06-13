<?php

/**
 * Fonction d'installation du plugin
 * @return boolean
 */
function plugin_fogtransfert_install() 
    {
	
    global $DB;
	
	if(!TableExists("glpi_plugin_fogtransfert_config")) 
	{
	
		$query = "CREATE TABLE IF NOT EXISTS `glpi_plugin_fogtransfert_config` (
		`fog_address` varchar(255) NOT NULL,
		`user_db_fog` varchar(255) NOT NULL,
		`pass_db_fog` text NOT NULL,
		`name_db_fog` varchar(255) NOT NULL,
		KEY `fog_address` (`fog_address`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

		$DB->query($query) or die($DB->error());
    }
	
    return true ;
    }
	
function plugin_fogtransfert_uninstall() 
{
    global $DB;

    $tables = array("glpi_plugin_fogtransfert_config");

    foreach($tables as $table) 
        {$DB->query("DROP TABLE IF EXISTS `$table`;");}
    return true;
}

?>