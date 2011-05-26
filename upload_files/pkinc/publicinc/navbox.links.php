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

$query=$SQL->query("SELECT 
		".pkSQLTAB_CONTENT.".content_id,
		".pkSQLTAB_CONTENT.".content_title
	FROM ".pkSQLTAB_CONTENT."
		LEFT JOIN ".pkSQLTAB_CONTENT_CATEGORY." ON ".pkSQLTAB_CONTENT_CATEGORY.".contentcat_id=".pkSQLTAB_CONTENT.".content_cat
	WHERE ".pkSQLTAB_CONTENT.".content_option=3 AND 
		".pkSQLTAB_CONTENT.".content_status=1 AND 
		(".pkSQLTAB_CONTENT.".content_expire>'".pkTIME."' OR 
		".pkSQLTAB_CONTENT.".content_expire='0') AND 
		".pkSQLTAB_CONTENT.".content_time<'".pkTIME."' AND 
		".sqlrights(pkSQLTAB_CONTENT_CATEGORY.'.contentcat_rights')." 
	ORDER by ".pkSQLTAB_CONTENT.".content_time DESC
	LIMIT 5");
while(list($id,$title)=$SQL->fetch_row($query))
	{
	$title_cutted=pkEntities(pkStringCut($title));
	$title=pkEntities($title);

	$boxlinks[]=pkHtmlLink(pkLink('contentarchive','','type=3&contentid='.$id),$title_cutted,'','pknidlinks'.count($boxlinks),'pkcontent_a_'.$navalign,$title);
	}

return $boxlinks;
?>