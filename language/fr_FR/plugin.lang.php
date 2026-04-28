<?php
$lang['Symbolic links']          = 'Liens symboliques';
$lang['glm_title']               = 'Gestion des liens symboliques';
$lang['glm_galleries_dir']       = 'Répertoire galleries :';
$lang['glm_col_name']            = 'Nom';
$lang['glm_col_target']          = 'Cible';
$lang['glm_col_status']          = 'Statut';
$lang['glm_col_actions']         = 'Actions';
$lang['glm_status_ok']           = 'Accessible';
$lang['glm_status_broken']       = 'Inaccessible';
$lang['glm_no_links']            = 'Aucun lien symbolique dans le répertoire galleries.';
$lang['glm_edit']                = 'Modifier';
$lang['glm_delete']              = 'Supprimer';
$lang['glm_confirm_delete']      = 'Supprimer ce lien symbolique ?';
$lang['glm_new_title']           = 'Créer un lien symbolique';
$lang['glm_edit_title']          = 'Modifier le lien symbolique';
$lang['glm_name_label']          = 'Nom du lien :';
$lang['glm_target_label']        = 'Répertoire cible :';
$lang['glm_browse']              = 'Parcourir…';
$lang['glm_create']              = 'Créer le lien';
$lang['glm_update']              = 'Enregistrer';
$lang['glm_cancel']              = 'Annuler';
$lang['glm_browser_title']       = 'Choisir un répertoire';
$lang['glm_browser_select']      = 'Sélectionner ce répertoire';
$lang['glm_browser_loading']     = 'Chargement…';
$lang['glm_browser_empty']       = 'Répertoire vide';
$lang['glm_browser_error']       = 'Erreur';

// Configuration
$lang['glm_config_title']        = 'Configuration';
$lang['glm_config_root_label']   = 'Racine du navigateur :';
$lang['glm_config_root_help']    = 'Répertoire de départ du navigateur de fichiers. Limite la navigation à cette arborescence (ex. /volume1 sur Synology, / pour aucune restriction).';
$lang['glm_config_save']         = 'Enregistrer';
$lang['glm_config_hide_at_label'] = 'Masquer les répertoires @ :';
$lang['glm_config_hide_at_help']  = 'Masque les répertoires commençant par @ (ex. @eaDir, @tmp sur Synology).';
$lang['glm_config_saved']        = 'Configuration enregistrée.';
$lang['glm_config_root_required'] = 'La racine du navigateur ne peut pas être vide.';

// Error messages
$lang['glm_invalid_link_name']   = 'Nom de lien invalide (caractères autorisés : lettres, chiffres, - _ .)';
$lang['glm_target_required']     = 'Le répertoire cible est requis.';
$lang['glm_target_not_found']    = 'Le répertoire cible n\'existe pas ou n\'est pas accessible.';
$lang['glm_target_not_dir']      = 'La cible n\'est pas un répertoire.';
$lang['glm_target_restricted']   = 'Ce répertoire cible n\'est pas autorisé pour des raisons de sécurité.';
$lang['glm_name_exists']         = 'Un fichier ou lien avec ce nom existe déjà.';
$lang['glm_link_failed']         = 'Échec de la création du lien symbolique. Vérifiez les droits PHP.';
$lang['glm_not_a_link']          = 'Cet élément n\'est pas un lien symbolique.';
$lang['glm_delete_failed']       = 'Échec de la suppression du lien symbolique.';
$lang['glm_link_created']        = 'Lien symbolique créé avec succès.';
$lang['glm_link_updated']        = 'Lien symbolique mis à jour avec succès.';
$lang['glm_link_deleted']        = 'Lien symbolique supprimé.';
