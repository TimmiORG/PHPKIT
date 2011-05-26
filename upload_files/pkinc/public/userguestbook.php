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


if($config['member_gbook']!=1)
	{
	pkEvent('function_disabled');
	return;
	}

if(getrights('user')!="true")
	{
	pkEvent('access_refused');
	return;
	}


pkLoadFunc('user');
$user_navigation=pkUserNavigation();


$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';


if($ACTION==$_POST['save'] && $_POST['save_gbwelcome']==1)
	{
	$SQL->query("UPDATE ".pkSQLTAB_USER." SET user_gbwelcome='".$SQL->f($_POST['content'])."' WHERE user_id='".$SQL->i(pkGetUservalue('id'))."'");
	
	pkHeaderLocation('userguestbook');
	}


$id=(isset($_REQUEST['id']) && intval($_REQUEST['id'])>0) ? intval($_REQUEST['id']) : pkGetUservalue('id');

$userinfo=$SQL->fetch_array($SQL->query("SELECT user_gbwelcome, user_nick, user_id FROM ".pkSQLTAB_USER." WHERE user_id='".$id."' LIMIT 1"));

if(isset($_REQUEST['edit'])) 
	{
	unset($sign_format);
	
	if($config['text_ubb']==1)
		eval("\$sign_format.= \"".pkTpl("format_text")."\";");
	
	if($config['text_smilies']==1)
		{
		$smilies=new smilies();
		$sign_format.=$smilies->getSmilies("1");
		}
	
	if($sign_format)
		eval("\$sign_format= \"".pkTpl("format_table")."\";");
	
	$userinfo['user_gbwelcome']=pkEntities($userinfo['user_gbwelcome']);
	
	eval("\$site_body.= \"".pkTpl("member_gbook_writeform")."\";");
	}

else
	{
	pkLoadClass($BBCODE,'bbcode');
	
	
	$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
	
	if($userinfo['user_sex']=="m")
		eval("\$info_profile= \"".pkTpl("member_showprofil_iconlink_m")."\";");
	elseif($userinfo['user_sex']=="w")
		eval("\$info_profile= \"".pkTpl("member_showprofil_iconlink_w")."\";");
	else
		eval("\$info_profile= \"".pkTpl("member_showprofil_iconlink")."\";");
	
	eval("\$info_nick= \"".pkTpl("member_showprofil_textlink")."\";");
	
	if($userinfo['user_gbwelcome']!='')
		{
		$pgbook_welcome=$BBCODE->parse($userinfo[0],0,$config['text_ubb'],$config['text_smilies'],pkGetConfig('user_imageresize'),pkGetConfig('user_textwrap'));
		
		eval("\$pgbook_head= \"".pkTpl("member_gbook_head")."\";");   
		}
	
	if($id==pkGetUservalue('id'))
		eval("\$pgbook_editlink.= \"".pkTpl("member_gbook_editlink")."\";");
	
	eval("\$site_body.= \"".pkTpl("member_gbook")."\";");
	
	
	$comcat="user";
	
	if(isset($id))
		$subid=$id;
	else
		$subid=$user_id;
	
	include_once(pkDIRPUBLIC.'comment'.pkEXT);
	}
?>