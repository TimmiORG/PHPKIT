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


if(!defined('pkFRONTEND') || pkFRONTEND!='admin')
	die('Direct access to this location is not permitted.');


if(!adminaccess('config')) 
	return pkEvent('access_forbidden');


#predefine
$toplink = '';
$config_anchors = ''; #anchor links when all config links are displayed
$parsed_config_modes = ''; #storage for the form sections
$config_mode_hash = array();
$userstatus_hash = array('guest', 'user', 'member', 'mod', 'admin');
$save_values = array();


$_selected = ' selected="selected"';
$_checked = ' checked="checked"';


#get config groups
$query = $SQL->query("SELECT id,lkey,lscope FROM ".pkSQLTAB_CONFIG_GROUP." ORDER BY sorting ASC");
while($mode = $SQL->fetch_assoc($query))
	{
	$config_mode_hash[$mode['id']] = $mode;
	}

#environment
$ACTION = isset($_POST['action']) ? $_POST['action'] : 'view'; #unsave variable
$config_mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : NULL;
$config_mode = isset($config_mode_hash[$config_mode]) ? $config_mode : NULL;

#language file
pkLoadLang('adminconfig');


if(!$config_mode)
	{
	eval("\$toplink= \"".pkTpl("config_all_toplink")."\";");
	}


foreach($config_mode_hash as $mode_key=>$mode_item)
	{
	if($config_mode==$mode_key || !$config_mode)
		{
		#does this mode has an own language scope 
		if(!empty($mode_item['lscope']))
			{
			pkLoadLang($mode_item['lscope']);
			}
		
		#provide some presets for this mode
		$mode_title = pkGetLang($mode_item['lkey']);
		$mode_anchor = pkEntities($mode_key);
		

		include(pkDIRADMIN.'config/'.$mode_key.pkEXT);
		
		eval("\$parsed_config_modes.= \"".pkTpl("config_header")."\";");
		eval("\$parsed_config_modes.= \"".pkTpl("config_".$mode_key)."\";");


		if(!$config_mode) #all modes
			{
			#make top anchor link
			$config_anchors.= '<li><a href="#'.$mode_anchor.'">'.$mode_title.'</a></li>';
			
			#spacer between sections when all modes are displayed
			eval("\$parsed_config_modes.= \"".pkTpl("config_all")."\";");
			}
		}
	}#@END foreach


if($ENV->_post_action('save'))
	{
	if(!empty($save_values))
		{
		$values = '';
		
		foreach($save_values as $key=>$value)
			{
			$values.= empty($values) ? '' : ',';
			$values.= "('".$SQL->f($key)."','".$SQL->f(serialize($value))."')";
			}
		
		$SQL->query("REPLACE INTO ".pkSQLTAB_CONFIG." (id,value) VALUES ".$values);
		}
	
	pkHeaderLocation('config',$config_mode);
	}#END saving


$form_action = pkLink('config',$config_mode);

if(!$config_mode)
	{
	eval("\$site_body.= \"".pkTpl("config_all_links")."\";");
	}

eval("\$site_body.= \"".pkTpl("config")."\";");
?>