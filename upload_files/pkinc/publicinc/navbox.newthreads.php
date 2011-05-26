<?php
# PHPKIT Web Content Management System
# --------------------------------------------
# Copyright (c) 2002-2007 Gersöne & Schott GbR
#
# This file / the PHPKIT-software is no freeware!
# For further informations please vistit our website
# or contact us via email:
#
# Diese Datei / die PHPKIT-Software ist keine Freeware!
# Für weitere Information besuchen Sie bitte unsere 
# Webseite oder kontaktieren uns per E-Mail:
#
# Website : http://www.phpkit.de
# Mail    : info@phpkit.de
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATIONS
#
# SIE SIND NICHT BERECHTIGT, UNRECHTMÄSSIGE KOPIEN DIESER
# DATEI ZU ERSTELLEN UND/ODER DIESE INFORMATIONEN ZU ENTFERNEN


if(!defined('pkFRONTEND') || pkFRONTEND!='public')
	die('Direct access to this location is not permitted.');


$boxlinks=array();


$result=$SQL->query("SELECT 
	t.forumthread_id, 
	t.forumthread_title 
	FROM ".pkSQLTAB_FORUM_THREAD." AS t
		LEFT JOIN ".pkSQLTAB_FORUM_CATEGORY." AS c ON (c.forumcat_id=t.forumthread_catid)
	WHERE
		t.forumthread_id>0 AND 
		t.forumthread_status IN(1,2) AND 
		(".sqlrights('c.forumcat_rrights')." OR
		c.forumcat_mods LIKE '%-".pkGetUservalue('id')."-%' OR 
		c.forumcat_user LIKE '%-".pkGetUservalue('id')."-%')				
	ORDER BY t.forumthread_id DESC
	LIMIT ".pkGetConfig('nb_newthreads_break'));
while(list($id,$title)=$SQL->fetch_row($result))
	{
	$title_cutted=pkEntities(pkStringCut($title,$config['nb_newthreads_scut']));
	$title=pkEntities($title);
				
	$boxlinks[]=pkHtmlLink(pkLink('forumsthread','','threadid='.$id),$title_cutted,'','pknidcurrentthreads'.count($boxlinks),'pkcontent_a_'.$navalign,$title);
	}
	
return $boxlinks;
?>