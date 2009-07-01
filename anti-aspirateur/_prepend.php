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

$core->blog->settings->setNameSpace('AntiAspi');
$core->addBehavior('publicBeforeDocument',array('AntiAspi','publicBeforeDocument'));
//$core->addBehavior('coreBlogConstruct',array('AntiAspi','publicBeforeDocument'));

class AntiAspi
{	
	public static function publicBeforeDocument(&$core)
	{
    global $core;

    try
		{
			//Liste blanche (par exemple si votre serveur se connecte pour des vérifications de texte)
			if ($_SERVER['REMOTE_ADDR'] === '194.242.114.101') return;
			
      $score = 0;
      $blocage = false;
			
			//On récupère le score dans la base de données
			$query = 'SELECT detect FROM dc_anti_aspi WHERE IP = "' . $core->con->escape($_SERVER['REMOTE_ADDR']) . '" LIMIT 1;';
      $rs = $core->con->select($query);
			if ( $rs->isEmpty() )
			{
        //On n'a pas l'adresse IP dans la base
        $detect['UA'] = $_SERVER['HTTP_USER_AGENT'] ;
        $detect['UA-COMPATIBLE-SCORED'] = false; 
        $detect['ALERT_EMAIL'] = false;
        
        //On insère la nouvelle IP
        $query = 'INSERT INTO `dc_anti_aspi` (`IP`,`detect`,`date`) VALUES ("' . $core->con->escape($_SERVER['REMOTE_ADDR']) . '", NULL , NOW());';      
        $rs = $core->con->execute($query);
      }
      else $detect = unserialize($rs->detect);
      
      //On fait le scoring des bots
      $score = $detect['S'] + $core->blog->settings->score_page_vue;
      
      //Pour l'user-agent, s'il change
      if ($_SERVER['USER_AGENT'] !== $detect['UA']) 
      {
        $score = $score + $core->blog->settings->score_changement_useragent;
        $detect['UA'] = $_SERVER['HTTP_USER_AGENT'] ;
      }
      
      //On regarde si c'est un user-agent "compatible" et on ne le compte qu'une seule fois
      if ($_SERVER['USER_AGENT'] === "Mozilla/4.0 (compatible;)" 
          AND $detect['UA-COMPATIBLE-SCORED'] === false) 
      {
        $score = $score + $core->blog->settings->score_useragent_generique;
        $detect['UA-COMPATIBLE-SCORED'] = true;
      }
      
      //On vérifie la durée entre 2 pages
      if ( (time() - $detect['T']) < $core->blog->settings->duree_courte ) $score = $score + $core->blog->settings->score_duree_courte;
      
      //On redirige si nécessaire
      if ( $score > $core->blog->settings->score_max ) 
      {
        //On alerte le responsable de l'accès. L'email est envoyé une seule fois
        if ($detect['ALERT_EMAIL'] === false AND $core->blog->settings->envoi_email)
        {
          $detect['ALERT_EMAIL'] = true;
          $message = 'Le plugin anti-aspirateur DotClear a bloqué la session suivante : ' . "\n"
                    . ' - User-agent : ' . $_SERVER['USER_AGENT'] . "\n"
                    . ' - score : ' . "$score\n\n" . 'Autres variables serveur : ';
          foreach ($_SERVER as $key => $value)
          {
            $message .= "\n" . ' - ' . $key . ' : ' . $value;
          }
          mail($core->blog->settings->adresse_email, 'Rapport plugin DC anti-aspirateur', $message);
        }
        
        //On bloque
        $blocage = true;
      }

      //On ajoute les infos en sessions
      $detect['T'] =  time();
      $detect['S'] = $score;

      //On met à jour la base de données
      $query = 'UPDATE dc_anti_aspi SET detect = \'' . serialize($detect) . '\' WHERE IP = "' . $core->con->escape($_SERVER['REMOTE_ADDR']) . '" LIMIT 1;';
      $core->con->execute($query);
      
      //On efface 15 IP de plus de 15 minutes
      //A affiner selon la volumétrie du site. La clause LIMIT permet de
      //diluer l'effacement des IP dans le temps. Si la base devient trop grosse,
      //Il faut modifier le délai et la limite
      $query = 'DELETE FROM dc_anti_aspi WHERE date < (NOW() - 900) LIMIT 15;';
      $core->con->execute($query);
      
      if ($blocage)
      {
        //On bloque
        header('HTTP/1.1 503 Service Unavailable');
        die('Service indisponible : votre robot est banni. Merci de le desactiver et revenez plus tard');	
      }
		}
		catch (Exception $e) {}
	}
}
?>
