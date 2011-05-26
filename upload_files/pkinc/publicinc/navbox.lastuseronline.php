<?php
# PHPKIT WCMS | Web Content Management System
#
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATION
#
# SIE SIND NICHT BERECHTIGT, UNRECHTMÄSSIGE KOPIEN DIESER
# DATEI ZU ERSTELLEN UND/ODER DIESE INFORMATIONEN ZU ENTFERNEN
#
# This file / the PHPKIT software is no freeware! For further 
# information please visit our website or contact us via email:
#
# Diese Datei / die PHPKIT Software ist keine Freeware! Für weitere
# Informationen besuchen Sie bitte unsere Website oder kontaktieren uns per E-Mail:
#
# email     : info@phpkit.com
# website   : http://www.phpkit.com
# licence   : http://www.phpkit.com/licence
# copyright : Copyright (c) 2002-2009 mxbyte gbr | http://www.mxbyte.com


if(!defined('pkFRONTEND') || pkFRONTEND!='public')
	die('Direct access to this location is not permitted.');


pkLoadHtml('default');

$boxlinks=array();


$getuserinfo=$SQL->query("SELECT
		user_id,
		user_nick
	FROM ".pkSQLTAB_USER."
	WHERE user_activate=1 ".
	(pkGetConfig('user_ghost') ? ' AND user_ghost<>1' : '')."
	ORDER by logtime DESC
	LIMIT 5");
while(list($id,$nick)=$SQL->fetch_row($getuserinfo))
	{
	$onlinestatus=isonline($id) ? 'online' : 'offline';
	$nick_cutted=pkEntities(pkStringCut($nick));
	$nick=pkEntities($nick);

	$boxlinks[]=pkHtmlImage('img_user_'.$onlinestatus).'&#160;'.
		pkHtmlLink(pkLink('userinfo','','id='.$id),$nick_cutted,'','pknidlastuseronline'.count($boxlinks),'pkcontent_a_'.$navalign,$nick);
	}

return $boxlinks;
?>