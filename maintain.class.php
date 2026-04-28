<?php
defined('PHPWG_ROOT_PATH') or die('Hacking attempt!');

class galleries_link_manager_maintain extends PluginMaintain
{
  function install($plugin_version, &$errors = array())
  {
    global $conf;

    if (empty($conf['galleries_link_manager']))
    {
      $browser_root = '/';
      $piwigo_real  = realpath(PHPWG_ROOT_PATH);
      if ($piwigo_real && preg_match('#^(/volume\d+)#', $piwigo_real, $m))
      {
        $browser_root = $m[1];
      }
      $default = array('browser_root' => $browser_root, 'hide_at_dirs' => true);
      conf_update_param('galleries_link_manager', $default, true);
      $conf['galleries_link_manager'] = $default;
    }
  }

  function activate($plugin_version, &$errors = array())
  {
    $this->install($plugin_version, $errors);
  }

  function uninstall()
  {
    conf_delete_param('galleries_link_manager');
  }
}
