<?php

/**
 * Fonction de définition de la version du plugin
 * @return type
 */
function plugin_version_fogtransfert() 
{
    return array('name'           => "fogtransfert",
                 'version'        => '0.1',
                 'author'         => 'Alexandre GAUVRIT',
                 'license'        => 'GPLv2+',
                 'homepage'       => 'http://www.k3nny.fr',
                 'minGlpiVersion' => '0.83');
}
	
/**
 * Fonction de vérification des prérequis
 * @return boolean
 */
function plugin_fogtransfert_check_prerequisites() 
{
    if (GLPI_VERSION >= 0.80)
        return true;
    echo "A besoin de la version 0.80 au minimum";
    return false; 
}	
	
/**
 * Fonction de vérification de la configuration initiale
 * @param type $verbose
 * @return boolean
 */
function plugin_fogtransfert_check_config($verbose=false) 
{
    if (true) 
        { // Your configuration check
        return true;
        }
    if ($verbose) 
        {
        echo 'Installed / not configured';
        }
    return false;
}

/**
 * Fonction d'initialisation du plugin
 * @global array $PLUGIN_HOOKS
 */
function plugin_init_fogtransfert() 
    {
    global $PLUGIN_HOOKS;

    $PLUGIN_HOOKS['csrf_compliant']['fogtransfert'] = true;
    $PLUGIN_HOOKS['config_page']['fogtransfert'] = 'front/config.form.php';
    Plugin::registerClass('fogtransfert', array('addtabon' => array('Computer')));
    Plugin::registerClass('fogtransfert');
    }
?>
	