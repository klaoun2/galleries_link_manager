{combine_css path='plugins/galleries_link_manager/css/admin.css'}

<div id="glm-wrap">

  <div class="titrePage">
    <h2>{$GLM_L_TITLE}</h2>
  </div>

  <p class="glm-info">
    <strong>{$GLM_L_GALLERIES}</strong> {$GLM_GALLERIES_DIR|escape}
  </p>

  {* ---------- Symlink list ---------- *}
  {if $GLM_SYMLINKS}
  <table class="table2">
    <thead>
      <tr>
        <th>{$GLM_L_COL_NAME}</th>
        <th>{$GLM_L_COL_TARGET}</th>
        <th>{$GLM_L_COL_STATUS}</th>
        <th>{$GLM_L_COL_ACTIONS}</th>
      </tr>
    </thead>
    <tbody>
    {foreach $GLM_SYMLINKS as $link}
      <tr>
        <td><strong>{$link.name|escape}</strong></td>
        <td><code>{$link.target|escape}</code></td>
        <td>
          {if $link.accessible}
            <span class="glm-status-ok"><span class="icon-ok"></span> {$GLM_L_OK}</span>
          {else}
            <span class="glm-status-broken"><span class="icon-cancel"></span> {$GLM_L_BROKEN}</span>
          {/if}
        </td>
        <td class="glm-actions">
          <button type="button" class="button glm-edit-btn"
            data-name="{$link.name|escape}"
            data-target="{$link.target|escape}">
            <span class="icon-pencil"></span> {$GLM_L_EDIT}
          </button>
          <form method="post" action="{$GLM_FORM_ACTION}" class="glm-delete-form">
            <input type="hidden" name="action"    value="delete">
            <input type="hidden" name="link_name" value="{$link.name|escape}">
            <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">
            <button type="submit" class="button">
              <span class="icon-trash"></span> {$GLM_L_DELETE}
            </button>
          </form>
        </td>
      </tr>
    {/foreach}
    </tbody>
  </table>
  {else}
  <p>{$GLM_L_NO_LINKS}</p>
  {/if}

  {* ---------- Create / Edit form ---------- *}
  <div style="margin-left:20px">
  <div class="glm-section">
    <h3 id="glm-form-title" style="text-align:left">{$GLM_L_NEW_TITLE}</h3>
  </div>

  <form id="glm-form" method="post" action="{$GLM_FORM_ACTION}">
    <input type="hidden" name="action"    id="glm-action"   value="create">
    <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">

    <div class="glm-form-row">
      <label for="glm-link-name">{$GLM_L_NAME_LBL}</label>
      <input type="text" id="glm-link-name" name="link_name"
             pattern="[a-zA-Z0-9_.-]+" required
             placeholder="mon_album">
    </div>

    <div class="glm-form-row">
      <label for="glm-target">{$GLM_L_TARGET_LBL}</label>
      <input type="text" id="glm-target" name="target" required
             placeholder="/volume1/photos/collection">
      <button type="button" id="glm-browse-btn" class="button">
        <span class="icon-folder-open-empty"></span> {$GLM_L_BROWSE}
      </button>
    </div>

    <div class="glm-form-row">
      <label></label>
      <button type="submit" id="glm-submit-btn" class="button bigButton">
        <span class="icon-plus-circled"></span>
        <span id="glm-submit-label">{$GLM_L_CREATE}</span>
      </button>
      <button type="button" id="glm-cancel-btn" class="button" style="display:none">
        {$GLM_L_CANCEL}
      </button>
    </div>
  </form>
  </div>{* /margin-left:20px *}

  {* ---------- Configuration ---------- *}
  <div style="margin-left:20px">
  <div class="glm-section">
    <h3 style="text-align:left">{$GLM_L_CFG_TITLE}</h3>
  </div>

  <form method="post" action="{$GLM_FORM_ACTION}">
    <input type="hidden" name="action"    value="save_config">
    <input type="hidden" name="pwg_token" value="{$PWG_TOKEN}">

    <div class="glm-form-row">
      <label for="glm-browser-root">{$GLM_L_CFG_ROOT_LBL}</label>
      <input type="text" id="glm-browser-root" name="browser_root"
             value="{$GLM_BROWSER_ROOT|escape}" placeholder="/">
    </div>
    <div class="glm-form-row">
      <label></label>
      <span class="glm-help">{$GLM_L_CFG_ROOT_HELP}</span>
    </div>

    <div class="glm-form-row">
      <label for="glm-hide-at">{$GLM_L_CFG_HIDE_AT_LBL}</label>
      <input type="checkbox" id="glm-hide-at" name="hide_at_dirs" value="1"{if $GLM_HIDE_AT_DIRS} checked{/if}>
      <span class="glm-help">{$GLM_L_CFG_HIDE_AT_HELP}</span>
    </div>

    <div class="glm-form-row">
      <label></label>
      <button type="submit" class="button bigButton">
        <span class="icon-floppy"></span> {$GLM_L_CFG_SAVE}
      </button>
    </div>
  </form>
  </div>{* /margin-left:20px *}

  {* ---------- Directory browser overlay ---------- *}
  <div id="glm-overlay" role="dialog" aria-modal="true">
    <div id="glm-browser">
      <div id="glm-browser-header">
        <span>{$GLM_L_BR_TITLE}</span>
        <button type="button" id="glm-close-btn" aria-label="Fermer">&times;</button>
      </div>
      <div id="glm-browser-path">/</div>
      <div id="glm-browser-loading">{$GLM_L_BR_LOADING}</div>
      <ul id="glm-browser-list"></ul>
      <div id="glm-browser-footer">
        <button type="button" id="glm-select-btn" class="button bigButton">
          <span class="icon-ok"></span> {$GLM_L_BR_SELECT}
        </button>
        <button type="button" id="glm-cancel2-btn" class="button">{$GLM_L_CANCEL}</button>
      </div>
    </div>
  </div>

</div>{* #glm-wrap *}

{literal}
<script>
(function () {
  var cfg = {/literal}{$GLM_JS_CONFIG}{literal};

  /* ---- state ---- */
  var currentDir = cfg.browserRoot || '/';

  /* ---- DOM refs ---- */
  var overlay    = document.getElementById('glm-overlay');
  var pathEl     = document.getElementById('glm-browser-path');
  var listEl     = document.getElementById('glm-browser-list');
  var loadingEl  = document.getElementById('glm-browser-loading');
  var formTitle  = document.getElementById('glm-form-title');
  var actionInp  = document.getElementById('glm-action');
  var nameInp    = document.getElementById('glm-link-name');
  var targetInp  = document.getElementById('glm-target');
  var submitLbl  = document.getElementById('glm-submit-label');
  var cancelBtn  = document.getElementById('glm-cancel-btn');

  /* ---- helpers ---- */
  function esc(s) {
    return String(s)
      .replace(/&/g,'&amp;').replace(/"/g,'&quot;')
      .replace(/</g,'&lt;').replace(/>/g,'&gt;');
  }

  /* ---- browser navigation ---- */
  function loadDir(dir) {
    currentDir = dir;
    pathEl.textContent = dir;
    loadingEl.style.display = 'block';
    listEl.innerHTML = '';

    fetch(cfg.ajaxUrl + encodeURIComponent(dir))
      .then(function (r) { return r.json(); })
      .then(function (data) {
        loadingEl.style.display = 'none';
        currentDir = data.current || dir;
        pathEl.textContent = currentDir;

        if (!data.success) {
          listEl.innerHTML = '<li class="glm-msg">' + cfg.labels.error + ': ' + esc(data.error || '') + '</li>';
          return;
        }

        var html = '';
        if (data.parent) {
          html += '<li><a href="#" class="glm-dir-up" data-path="' + esc(data.parent) + '">&#x2191; ..</a></li>';
        }
        (data.entries || []).forEach(function (e) {
          html += '<li><a href="#" class="glm-dir" data-path="' + esc(e.path) + '">&#x1F4C1; ' + esc(e.name) + '</a></li>';
        });
        if (!html) {
          html = '<li class="glm-msg">' + cfg.labels.empty + '</li>';
        }
        listEl.innerHTML = html;

        listEl.querySelectorAll('a[data-path]').forEach(function (el) {
          el.addEventListener('click', function (ev) {
            ev.preventDefault();
            loadDir(this.getAttribute('data-path'));
          });
        });
      })
      .catch(function (err) {
        loadingEl.style.display = 'none';
        listEl.innerHTML = '<li class="glm-msg">' + cfg.labels.error + ': ' + esc(err.message) + '</li>';
      });
  }

  function openBrowser() {
    var cur = targetInp.value.trim();
    loadDir(cur || cfg.browserRoot || '/');
    overlay.classList.add('open');
  }

  function closeBrowser() {
    overlay.classList.remove('open');
  }

  /* ---- browser events ---- */
  document.getElementById('glm-browse-btn').addEventListener('click', openBrowser);
  document.getElementById('glm-close-btn').addEventListener('click', closeBrowser);
  document.getElementById('glm-cancel2-btn').addEventListener('click', closeBrowser);
  document.getElementById('glm-select-btn').addEventListener('click', function () {
    targetInp.value = currentDir;
    closeBrowser();
  });
  overlay.addEventListener('click', function (e) {
    if (e.target === overlay) closeBrowser();
  });

  /* ---- edit / cancel ---- */
  document.querySelectorAll('.glm-edit-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var name   = this.getAttribute('data-name');
      var target = this.getAttribute('data-target');

      formTitle.textContent   = cfg.labels.editTitle + ' — ' + name;
      actionInp.value         = 'edit';
      nameInp.value           = name;
      nameInp.readOnly        = true;
      targetInp.value         = target;
      submitLbl.textContent   = cfg.labels.update;
      cancelBtn.style.display = '';

      document.getElementById('glm-form').scrollIntoView({ behavior: 'smooth' });
    });
  });

  cancelBtn.addEventListener('click', function () {
    formTitle.textContent   = cfg.labels.newTitle;
    actionInp.value         = 'create';
    nameInp.value           = '';
    nameInp.readOnly        = false;
    targetInp.value         = '';
    submitLbl.textContent   = cfg.labels.create;
    cancelBtn.style.display = 'none';
  });

  /* ---- delete confirmation ---- */
  document.querySelectorAll('.glm-delete-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
      if (!window.confirm(cfg.labels.confirmDelete)) {
        e.preventDefault();
      }
    });
  });
})();
</script>
{/literal}
