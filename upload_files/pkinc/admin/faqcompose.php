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


if(!adminaccess('faq'))
	return pkEvent('access_forbidden');


$faq_id=(isset($_REQUEST['faq_id']) && intval($_REQUEST['faq_id'])>0) ? intval($_REQUEST['faq_id']) : (isset($_REQUEST['faq_id']) && $_REQUEST['faq_id']=='new' ? 'new' : 0);
$catid=(isset($_REQUEST['catid']) && intval($_REQUEST['catid'])>0) ? intval($_REQUEST['catid']) : 0;
$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';


if(isset($_POST['delete']) && $ACTION==$_POST['delete'])
	{
	$SQL->query("DELETE FROM ".pkSQLTAB_FAQ." WHERE faq_id='".$faq_id."' LIMIT 1");
	pkHeaderLocation('faqarchive');
	}
elseif($ACTION==$_POST['save'] || intval($_POST['faq_id'])>0)
	{
	if($faq_id=='new') 
		{
		$SQL->query("INSERT INTO ".pkSQLTAB_FAQ." (faq_title) VALUES ('new')"); 
		$faq_id=$SQL->insert_id();
		}

	$SQL->query("UPDATE ".pkSQLTAB_FAQ." 
		SET faq_title='".$SQL->f($_POST['faq_title'])."',
			faq_question='".$SQL->f($_POST['faq_question'])."',
			faq_answer='".$SQL->f($_POST['content'])."',
			faq_catid='".$catid."' 
		WHERE faq_id='".$faq_id."'");
	
	pkHeaderLocation('faqarchive');
	}

if($faq_id && $faq_id!='new')
	{
	$faqinfo=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_FAQ." WHERE faq_id='".$faq_id."' LIMIT 1"));
	
	$faq_id=$faqinfo['faq_id'];
	$catid=$faqinfo['faq_catid'];
	$faq_title=pkEntities($faqinfo['faq_title']);
	$faq_question=pkEntities($faqinfo['faq_question']);
	$faq_answer=pkEntities($faqinfo['faq_answer']);
	}
else
	$faq_id='new';


$getfaqcat=$SQL->query("SELECT * FROM ".pkSQLTAB_FAQ_CATEGORY." ORDER by faqcat_title ASC");
while($faqcat=$SQL->fetch_array($getfaqcat))
	{
	$selected='';
	$faqcat['faqcat_title']=pkEntities($faqcat['faqcat_title']);

	if($catid==$faqcat['faqcat_id'])
		{
		$selected='selected';
		$faqcat_title=$faqcat['faqcat_title'];
		}	

	eval("\$faqcat_option.= \"".pkTpl("faq/faqcat_option")."\";");
	}
 	
if($catid!="")
	eval("\$faqcat_body= \"".pkTpl("faq/writefaq_form")."\";");

eval("\$site_body.= \"".pkTpl("faq/writefaq")."\";");
?>