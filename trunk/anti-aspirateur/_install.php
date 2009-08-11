<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
#
# This file is part of Dotclear 2.
#
# Copyright (c) 2009 Maxime Varinard vaisonet.com
# Licensed under the GPL version 2.0 license.
# See LICENSE file or
# http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
#
# -- END LICENSE BLOCK ------------------------------------
if (!defined('DC_CONTEXT_ADMIN')) { return; }
$core->blog->settings->setNameSpace('AntiAspi');

$version = '0.6';
/*vérification de version, à mettre en place sur la prochaine mise à jour
$version = $core->plugins->moduleInfo('Anti aspirateur','version'); 
if (version_compare($core->getVersion('Anti aspirateur'),$version,'>=')) {
	return;
}*/
 
// Procédure d'installation
//On ajoute les données de configuration
$core->blog->settings->put('envoi_email',false,'boolean', 'Doit on envoyer un email lors du blocage de la session ?', true, true);
$core->blog->settings->put('adresse_email',__('adresse@domaine.fr'),'string', 'Adresse email de r&eacute;ception du rapport', true, true);
$core->blog->settings->put('score_page_vue',1,'integer', 'Nombre de point par page vue', true, true);
$core->blog->settings->put('score_changement_useragent',50,'integer',  'Nombre de point si l\'user-agent change au cours de la session', true, true);
$core->blog->settings->put('score_useragent_generique',50,'integer',  'Nombre de point si l\'user-agent est g&eacute;n&eacute;rique ("Mozilla/4.0 (compatible;)")', true, true);
$core->blog->settings->put('score_duree_courte',50,'integer', 'Nombre de point si la fr&eacute;quence entre 2 pages est trop courte', true, true);
$core->blog->settings->put('duree_courte',2,'integer', 'Dur&eacute;e pour consid&eacute;rer une duree comme courte', true, true);
$core->blog->settings->put('score_max',280,'integer', 'Score au dela duquel la session sera bloqu&eacute;e', true, true); 

//On crée la table supplémentaire
$s = new dbStruct($core->con,$core->prefix);
$s->anti_aspi
	->IP		('varchar',	15,	false)
	->detect		('text',	0,	true)
	->date	('timestamp',	0,	false)
	;
$s->anti_aspi->unique('uk_anti_aspi','IP');
$si = new dbStruct($core->con,$core->prefix);
$changes = $si->synchronize($s);

$core->setVersion('Anti aspirateur',$version);
return true;
?>
