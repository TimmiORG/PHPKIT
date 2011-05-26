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


$contentcat_cache=contentcats();
$contentcat_cache=$contentcat_cache[0];


if(!is_array($contentcat_cache) || empty($contentcat_cache))
	return;

$type=(isset($_REQUEST['type']) && intval($_REQUEST['type'])>0 && intval($_REQUEST['type'])<5) ? intval($_REQUEST['type']) : ((isset($type) && intval($type)>0 && intval($type)<5) ? intval($type) : 1);

switch($type)
	{	
	case 2 :
		$showcat_type=$lang['news'];
		break;
	case 3 :
		$showcat_type=$lang['links'];
		break;
	case 4 :
		$showcat_type=$lang['downloads'];
		break;
	default :
		$type=1;
		$showcat_type=$lang['article'];
		break;
	}


pkLoadClass($BBCODE,'bbcode');

	
foreach($contentcat_cache as $cats)
	{
	if(($type==1 && $cats['contentcat_type1']==1) || ($type==2 && $cats['contentcat_type2']==1) || ($type==3 && $cats['contentcat_type3']==1) || ($type==4 && $cats['contentcat_type4']==1))
		{
		if(!getrights($cats['contentcat_rights']))
			continue;

		$contentcat_name=pkEntities($cats['contentcat_name']);

		if(!empty($cats['contentcat_description']))
			$contentcat_description=$BBCODE->parse($cats['contentcat_description'],1,1,1,0);

   		if($cats['contentcat_symbol']!='blank.gif')
			{
			$catimage_dimension=@getimagesize('images/catimages/'.$cats['contentcat_symbol']);
			
			eval("\$contentcat_image= \"".pkTpl("content/showcat_image")."\";");
			}
	
		if($align=='right')
			{
			eval("\$showcat_body.= \"".pkTpl("content/showcat_row_right")."\";");

			$align='left';
			}
		else
			{
			eval("\$showcat_body.= \"".pkTpl("content/showcat_row_left")."\";");

			$align='right';
			}
			
		unset($cat_name);
		unset($catimage_dimension);
		unset($contentcat_image);
		unset($contentcat_description);
		}
	}
if($showcat_body!='')
	eval("\$site_body.= \"".pkTpl("content/showcat")."\";");
?>