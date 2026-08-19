<?php
/*
Plugin Name: Galleries Link Manager
Version: auto
Description: Manage symbolic links in the Piwigo galleries directory
Plugin URI: https://piwigo.org/ext/extension_view.php?eid=1080
Author: Charles69
*/

//============= VERSIONS ============================================
/*
version 1.5 - 11/05/2026
    arborescence dépliable des albums sur la page site_update (via prefilter)

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
add_event_handler('loc_begin_admin_page',   'glm_inject_cat_tree');

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

// +-----------------------------------------------------------------------+
// | Album tree on site_update                                             |
// +-----------------------------------------------------------------------+

function glm_inject_cat_tree()
{
  global $page, $template;

  if (!isset($page['page']) || $page['page'] !== 'site_update') return;
  if (!isset($_GET['site']) || !is_numeric($_GET['site'])) return;

  // La fonction Smarty est appelée au moment du rendu (après la synchro)
  $template->smarty->registerPlugin('function', 'glm_cat_tree_json', 'glm_smarty_cat_tree_json');
  $template->set_prefilter('update', 'glm_prefilter_cat_tree');
}

function glm_smarty_cat_tree_json($params, $smarty)
{
  if (!isset($_GET['site']) || !is_numeric($_GET['site'])) return '[]';
  $site_id = (int)$_GET['site'];
  $query = '
SELECT id, name, uppercats, global_rank
  FROM ' . CATEGORIES_TABLE . '
  WHERE site_id = ' . $site_id;
  $raw_cats = query2array($query);
  usort($raw_cats, 'global_rank_compare');
  $items = array();
  foreach ($raw_cats as $cat)
  {
    $items[] = array(
      'id'    => (int)$cat['id'],
      'name'  => $cat['name'],
      'depth' => substr_count($cat['global_rank'], '.'),
    );
  }
  return json_encode($items);
}

function glm_prefilter_cat_tree($content, &$smarty)
{
  $content = str_replace("\r\n", "\n", $content);

  // Hide the native <select> and add the tree container below it
  $old = '    <select class="categoryList" name="cat" size="10">
      {html_options options=$category_options selected=$category_options_selected}
    </select>
    </li>';

  $new = '    <style>
      #catTree { border:1px solid #ccc; height:40em; overflow-y:auto; width:100%; padding:4px; box-sizing:border-box; background:#fff; cursor:default; }
      #catTree ul { list-style:none; margin:0; padding-left:20px !important; }
      #catTree > ul { padding-left:0 !important; }
      #catTree li { line-height:1.6em; white-space:nowrap; }
      .cat-toggle { display:inline-block; width:14px; text-align:center; cursor:pointer; color:#888; user-select:none; font-size:9px; }
      .cat-label { cursor:pointer; padding:1px 5px; border-radius:3px; }
      .cat-label:hover { background:#e8f0fe; }
      .cat-label.active { background:#4a90d9; color:#fff; }
    </style>
    <select class="categoryList" name="cat" size="10" style="display:none">
      {html_options options=$category_options selected=$category_options_selected}
    </select>
    <div id="catTree"></div>
    </li>';

  $content = str_replace($old, $new, $content);

  // Append the tree JavaScript as a footer_script block
  $content .= <<<'SMARTY'

{footer_script}
var _pwgCats = {glm_cat_tree_json};
(function() {
  var $select = $('select.categoryList');
  var $container = $('#catTree');
  if (!$select.length || !$container.length || !_pwgCats.length) return;

  var roots = [], stack = [];
  _pwgCats.forEach(function(cat) {
    cat.children = [];
    while (stack.length > cat.depth) stack.pop();
    (stack.length ? stack[stack.length - 1].children : roots).push(cat);
    stack.push(cat);
  });

  function renderNodes(list) {
    var $ul = $('<ul>');
    list.forEach(function(node) {
      var $li = $('<li>');
      var hasKids = node.children.length > 0;
      var $toggle = $('<span class="cat-toggle">').html(hasKids ? '&#9658;' : '&nbsp;');
      var $label  = $('<span class="cat-label">').text(node.name).attr('data-id', node.id);
      $li.append($toggle).append($label);
      if (hasKids) {
        var $sub = renderNodes(node.children).hide();
        $li.append($sub);
        $toggle.on('click', function() {
          $sub.toggle();
          $(this).html($sub.is(':visible') ? '&#9660;' : '&#9658;');
        });
      }
      $label.on('click', function() {
        if ($(this).hasClass('active')) {
          $(this).removeClass('active');
          $select[0].selectedIndex = -1;
        } else {
          $container.find('.cat-label.active').removeClass('active');
          $(this).addClass('active');
          $select.val(node.id);
        }
      });
      $ul.append($li);
    });
    return $ul;
  }

  $container.append(renderNodes(roots));

  var presel = $select.val();
  if (presel) {
    var $active = $container.find('[data-id="' + presel + '"]').addClass('active');
    $active.parents('ul').each(function() {
      $(this).show();
      $(this).prev('.cat-toggle').html('&#9660;');
    });
  }
})();
{/footer_script}
SMARTY;

  return $content;
}
