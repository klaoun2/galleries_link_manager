<?php
defined('GLM_PATH') or die('Hacking attempt!');

// +---------------------------------------------------------------------------+
// | Helper functions                                                          |
// +---------------------------------------------------------------------------+

function glm_is_restricted($path)
{
  $restricted = array(
    '/', '/etc', '/bin', '/sbin', '/usr', '/usr/bin', '/usr/sbin',
    '/proc', '/sys', '/dev', '/run', '/boot', '/lib', '/lib64',
  );
  return in_array(rtrim($path, '/'), $restricted);
}

function glm_list_symlinks($galleries_dir)
{
  $symlinks = array();
  if (!$galleries_dir || !is_dir($galleries_dir))
  {
    return $symlinks;
  }
  $entries = @scandir($galleries_dir);
  if ($entries === false)
  {
    return $symlinks;
  }
  foreach ($entries as $entry)
  {
    if ($entry === '.' || $entry === '..')
    {
      continue;
    }
    $full_path = $galleries_dir . '/' . $entry;
    if (is_link($full_path))
    {
      $target = readlink($full_path);
      $real   = realpath($full_path);
      $symlinks[] = array(
        'name'       => $entry,
        'target'     => $target,
        'accessible' => ($real !== false && is_dir($real)),
      );
    }
  }
  return $symlinks;
}

// +---------------------------------------------------------------------------+
// | AJAX handler                                                              |
// +---------------------------------------------------------------------------+

if (isset($_GET['ajax']))
{
  while (ob_get_level() > 0)
  {
    ob_end_clean();
  }
  header('Content-Type: application/json; charset=utf-8');

  $dir  = isset($_GET['dir']) ? $_GET['dir'] : '/';
  $real = @realpath($dir);

  if ($real === false || !is_dir($real))
  {
    echo json_encode(array('success' => false, 'error' => 'Invalid directory'));
    exit();
  }

  $items = @scandir($real);
  if ($items === false)
  {
    echo json_encode(array('success' => false, 'error' => 'Permission denied'));
    exit();
  }

  $entries = array();
  foreach ($items as $item)
  {
    if ($item === '.' || $item === '..')
    {
      continue;
    }
    $item_path = $real . '/' . $item;
    if (is_dir($item_path) && !is_link($item_path))
    {
      $entries[] = array('name' => $item, 'path' => $item_path);
    }
  }

  usort($entries, function($a, $b) { return strcasecmp($a['name'], $b['name']); });

  $parent = dirname($real);

  echo json_encode(array(
    'success' => true,
    'current' => $real,
    'parent'  => ($parent !== $real) ? $parent : null,
    'entries' => $entries,
  ));
  exit();
}

// +---------------------------------------------------------------------------+
// | Access control                                                            |
// +---------------------------------------------------------------------------+

check_status(ACCESS_ADMINISTRATOR);

$galleries_dir = realpath(PHPWG_ROOT_PATH . 'galleries');

// +---------------------------------------------------------------------------+
// | Process POST actions                                                      |
// +---------------------------------------------------------------------------+

$page_action = isset($_POST['action']) ? $_POST['action'] : '';

if (!empty($page_action))
{
  check_pwg_token();

  if ($page_action === 'create' || $page_action === 'edit')
  {
    $link_name = isset($_POST['link_name']) ? trim($_POST['link_name']) : '';
    $target    = isset($_POST['target'])    ? trim($_POST['target'])    : '';

    if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $link_name))
    {
      $page['errors'][] = l10n('glm_invalid_link_name');
    }
    elseif (empty($target))
    {
      $page['errors'][] = l10n('glm_target_required');
    }
    else
    {
      $real_target = @realpath($target);
      if ($real_target === false)
      {
        $page['errors'][] = l10n('glm_target_not_found');
      }
      elseif (!is_dir($real_target))
      {
        $page['errors'][] = l10n('glm_target_not_dir');
      }
      elseif (glm_is_restricted($real_target))
      {
        $page['errors'][] = l10n('glm_target_restricted');
      }
      else
      {
        $link_path = $galleries_dir . '/' . $link_name;

        if ($page_action === 'edit' && is_link($link_path))
        {
          @unlink($link_path);
        }

        if (!is_link($link_path) && !file_exists($link_path))
        {
          if (@symlink($real_target, $link_path))
          {
            $page['infos'][] = ($page_action === 'create')
              ? l10n('glm_link_created')
              : l10n('glm_link_updated');
          }
          else
          {
            $page['errors'][] = l10n('glm_link_failed');
          }
        }
        else
        {
          $page['errors'][] = l10n('glm_name_exists');
        }
      }
    }
  }
  elseif ($page_action === 'delete')
  {
    $link_name = isset($_POST['link_name']) ? trim($_POST['link_name']) : '';
    $link_path = $galleries_dir . '/' . $link_name;

    if (!is_link($link_path))
    {
      $page['errors'][] = l10n('glm_not_a_link');
    }
    elseif (@unlink($link_path))
    {
      $page['infos'][] = l10n('glm_link_deleted');
    }
    else
    {
      $page['errors'][] = l10n('glm_delete_failed');
    }
  }
}

// +---------------------------------------------------------------------------+
// | Build data for template                                                   |
// +---------------------------------------------------------------------------+

$symlinks = glm_list_symlinks($galleries_dir);

$piwigo_real  = realpath(PHPWG_ROOT_PATH);
$browser_start = $piwigo_real ? dirname($piwigo_real) : '/';
if (!$browser_start || $browser_start === $piwigo_real)
{
  $browser_start = '/';
}

// +---------------------------------------------------------------------------+
// | Tabsheet                                                                  |
// +---------------------------------------------------------------------------+

include_once(PHPWG_ROOT_PATH . 'admin/include/tabsheet.class.php');
$my_base_url = get_root_url() . 'admin.php?page=';

$tabsheet = new tabsheet();
$tabsheet->set_id('site_update');
$tabsheet->select('galleries_link_manager');
$tabsheet->assign();

// +---------------------------------------------------------------------------+
// | Template assignment                                                       |
// +---------------------------------------------------------------------------+

$template->assign(array(
  'GLM_SYMLINKS'      => $symlinks,
  'GLM_GALLERIES_DIR' => $galleries_dir,
  'GLM_FORM_ACTION'   => get_root_url() . 'admin.php?page=plugin-' . GLM_ID,
  'PWG_TOKEN'         => get_pwg_token(),

  // Pre-translated labels used directly in HTML
  'GLM_L_TITLE'       => l10n('glm_title'),
  'GLM_L_COL_NAME'    => l10n('glm_col_name'),
  'GLM_L_COL_TARGET'  => l10n('glm_col_target'),
  'GLM_L_COL_STATUS'  => l10n('glm_col_status'),
  'GLM_L_COL_ACTIONS' => l10n('glm_col_actions'),
  'GLM_L_OK'          => l10n('glm_status_ok'),
  'GLM_L_BROKEN'      => l10n('glm_status_broken'),
  'GLM_L_NO_LINKS'    => l10n('glm_no_links'),
  'GLM_L_EDIT'        => l10n('glm_edit'),
  'GLM_L_DELETE'      => l10n('glm_delete'),
  'GLM_L_NEW_TITLE'   => l10n('glm_new_title'),
  'GLM_L_EDIT_TITLE'  => l10n('glm_edit_title'),
  'GLM_L_NAME_LBL'    => l10n('glm_name_label'),
  'GLM_L_TARGET_LBL'  => l10n('glm_target_label'),
  'GLM_L_BROWSE'      => l10n('glm_browse'),
  'GLM_L_CREATE'      => l10n('glm_create'),
  'GLM_L_UPDATE'      => l10n('glm_update'),
  'GLM_L_CANCEL'      => l10n('glm_cancel'),
  'GLM_L_BR_TITLE'    => l10n('glm_browser_title'),
  'GLM_L_BR_SELECT'   => l10n('glm_browser_select'),
  'GLM_L_BR_LOADING'  => l10n('glm_browser_loading'),
  'GLM_L_GALLERIES'   => l10n('glm_galleries_dir'),

  // All JS config pre-serialised as JSON — avoids escaping issues in template
  'GLM_JS_CONFIG'     => json_encode(array(
    'ajaxUrl'      => get_root_url() . 'admin.php?page=plugin-' . GLM_ID . '&ajax=browse_dir&dir=',
    'browserStart' => $browser_start,
    'labels'       => array(
      'empty'         => l10n('glm_browser_empty'),
      'error'         => l10n('glm_browser_error'),
      'newTitle'      => l10n('glm_new_title'),
      'editTitle'     => l10n('glm_edit_title'),
      'create'        => l10n('glm_create'),
      'update'        => l10n('glm_update'),
      'confirmDelete' => l10n('glm_confirm_delete'),
    ),
  ), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP),
));

$template->set_filename('glm_admin', GLM_PATH . 'template/admin.tpl');
$template->assign_var_from_handle('ADMIN_CONTENT', 'glm_admin');
