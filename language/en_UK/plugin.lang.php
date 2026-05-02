<?php
$lang['Symbolic links']          = 'Symbolic links';
$lang['glm_title']               = 'Symbolic Link Manager';
$lang['glm_galleries_dir']       = 'Galleries directory:';
$lang['glm_col_name']            = 'Name';
$lang['glm_col_target']          = 'Target';
$lang['glm_col_status']          = 'Status';
$lang['glm_col_actions']         = 'Actions';
$lang['glm_status_ok']           = 'Accessible';
$lang['glm_status_broken']       = 'Broken';
$lang['glm_no_links']            = 'No symbolic links found in the galleries directory.';
$lang['glm_edit']                = 'Edit';
$lang['glm_delete']              = 'Delete';
$lang['glm_confirm_delete']      = 'Delete this symbolic link?';
$lang['glm_new_title']           = 'Create a symbolic link';
$lang['glm_edit_title']          = 'Edit symbolic link';
$lang['glm_name_label']          = 'Link name:';
$lang['glm_target_label']        = 'Target directory:';
$lang['glm_browse']              = 'Browse…';
$lang['glm_create']              = 'Create link';
$lang['glm_update']              = 'Save';
$lang['glm_cancel']              = 'Cancel';
$lang['glm_browser_title']       = 'Choose a directory';
$lang['glm_browser_select']      = 'Select this directory';
$lang['glm_browser_loading']     = 'Loading…';
$lang['glm_browser_empty']       = 'Empty directory';
$lang['glm_browser_error']       = 'Error';

// Configuration
$lang['glm_config_title']        = 'Configuration';
$lang['glm_config_root_label']   = 'Browser root:';
$lang['glm_config_root_help']    = 'Starting directory for the file browser. Restricts navigation to this subtree (e.g. /volume1 on Synology, / for no restriction).';
$lang['glm_config_save']         = 'Save';
$lang['glm_config_hide_at_label']  = 'Hide @ directories:';
$lang['glm_config_hide_at_help']   = 'Hide directories starting with @ (e.g. @eaDir, @tmp on Synology).';
$lang['glm_config_hide_sys_label'] = 'Hide Linux system directories:';
$lang['glm_config_hide_sys_help']  = 'Hide system directories (boot, dev, etc, proc, sys, tmp…).';
$lang['glm_config_saved']        = 'Configuration saved.';
$lang['glm_config_root_required'] = 'Browser root cannot be empty.';

// Error messages
$lang['glm_invalid_link_name']   = 'Invalid link name (allowed characters: letters, digits, - _ .)';
$lang['glm_target_required']     = 'Target directory is required.';
$lang['glm_target_not_found']    = 'Target directory does not exist or is not readable.';
$lang['glm_target_not_dir']      = 'Target is not a directory.';
$lang['glm_target_restricted']   = 'This target directory is not allowed for security reasons.';
$lang['glm_name_exists']         = 'A file or link with that name already exists.';
$lang['glm_symlink_disabled']       = 'PHP symlink() function is disabled on this server (check disable_functions in php.ini).';
$lang['glm_galleries_not_writable'] = 'The galleries directory is not writable by PHP.';
$lang['glm_link_failed']            = 'Failed to create symbolic link.';
$lang['glm_not_a_link']          = 'This entry is not a symbolic link.';
$lang['glm_delete_failed']       = 'Failed to delete symbolic link.';
$lang['glm_link_created']        = 'Symbolic link created successfully.';
$lang['glm_link_updated']        = 'Symbolic link updated successfully.';
$lang['glm_link_deleted']        = 'Symbolic link deleted.';
