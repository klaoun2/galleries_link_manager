<?php
/*
Plugin Name: Galleries Link Manager
Version: 1.4
Description: Manage symbolic links in the Piwigo galleries directory
Plugin URI: https://piwigo.org/ext/extension_view.php?eid=1080
Author: Charles69
*/

//============= VERSIONS ============================================
/*
version 1.4 - 10/05/2026
    ajouté recommandation
    
version 1.3 - 07/05/2026
    ajouté URI

version 1.2 - 04/05/2026
    ajouté tests erreur linux

version 1.1 - 02/05/2026
    masquage des repertoires système sous linux
    détail des messages d'erreur 

version 1.0 - 27/04/2026
    Création du plugin
*/
//====================================================================



if (!defined('PHPWG_ROOT_PATH'))
{
  die('Hacking attempt!');
}

define('GLM_ID',   basename(dirname(__FILE__)));
define('GLM_PATH', PHPWG_PLUGINS_PATH . GLM_ID . '/');

add_event_handler('init',                   'glm_init');
add_event_handler('tabsheet_before_select', 'glm_add_tab');

function glm_init()
{
  global $conf;
  // Charger l'anglais comme base, puis la langue de l'utilisateur par-dessus
  load_language('plugin.lang', GLM_PATH, array('language' => 'en_UK', 'no_fallback' => true));
  load_language('plugin.lang', GLM_PATH);
  $conf['galleries_link_manager'] = isset($conf['galleries_link_manager'])
    ? safe_unserialize($conf['galleries_link_manager'])
    : array();
  if (empty($conf['galleries_link_manager']))
  {
    $conf['galleries_link_manager'] = array('browser_root' => '/');
  }
}

function glm_add_tab($sheets, $tab_id)
{
  if ($tab_id === 'site_update')
  {
    $sheets['galleries_link_manager'] = array(
      'caption' => '<span class="icon-link"></span>' . l10n('Symbolic links'),
      'url'     => get_root_url() . 'admin.php?page=plugin-' . GLM_ID,
    );
  }
  return $sheets;
}
