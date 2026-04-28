<?php
/*
Plugin Name: Galleries Link Manager
Version: 1.0
Description: Manage symbolic links in the Piwigo galleries directory
Plugin URI:
Author: Charles
*/

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
  load_language('plugin.lang', GLM_PATH);
  $conf['galleries_link_manager'] = safe_unserialize($conf['galleries_link_manager']);
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
