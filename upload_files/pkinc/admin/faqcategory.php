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


if(!adminaccess('faqcat'))
	return pkEvent('access_forbidden');


$catid = isset($_REQUEST['catid']) && intval($_REQUEST['catid']) ? intval($_REQUEST['catid']) : (isset($_REQUEST['catid']) && $_REQUEST['catid']=='new' ? 'new' : 0);

$newcatid=(isset($_REQUEST['newcatid']) && intval($_REQUEST['newcatid'])>0) ? intval($_REQUEST['newcatid']) : 0;
$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';


if($catid && $catid!='new' && isset($_POST['delete']) && $ACTION==$_POST['delete'])
	{
	if($newcatid)
		{
		$SQL->query("UPDATE ".pkSQLTAB_FAQ." SET faq_catid='".$newcatid."' WHERE faq_catid='".$catid."'");
		}
	else
		{
		$SQL->query("DELETE FROM ".pkSQLTAB_FAQ." WHERE faq_catid='".$catid."' LIMIT 1");
		}

	$SQL->query("DELETE FROM ".pkSQLTAB_FAQ_CATEGORY." WHERE faqcat_id='".$catid."' LIMIT 1 ");
	
	pkHeaderLocation('faqcategory');
	}


if(isset($_POST['save']) && $ACTION==$_POST['save'] && !empty($_POST['faqcat_title']))
	{
	if($catid=='new')
		{
		$SQL->query("INSERT INTO ".pkSQLTAB_FAQ_CATEGORY." (faqcat_title) VALUES ('new')");
		$catid=$SQL->insert_id(); 
		}
	
	$SQL->query("UPDATE ".pkSQLTAB_FAQ_CATEGORY." SET faqcat_title='".$SQL->f($_POST['faqcat_title'])."' WHERE faqcat_id='".$catid."'");
	
	pkHeaderLocation('faqcategory');
	}


$query = $SQL->query("SELECT * FROM ".pkSQLTAB_FAQ_CATEGORY." ORDER BY faqcat_title ASC");
while($faqcat = $SQL->fetch_assoc($query))
	{
	$faqcat['faqcat_title'] = pkEntities($faqcat['faqcat_title']);
	
	if($catid==$faqcat['faqcat_id']) 
		{
		$faqcat_title = $faqcat['faqcat_title'];
		}
		
	if($faqcat['faqcat_id']!=$catid)
		{
		eval("\$faqnewcat_option.= \"".pkTpl("faq/faqcat_option")."\";");
		}
	
	eval("\$faqcat_option.= \"".pkTpl("faq/faqcat_option")."\";");
	}

eval("\$site_body.= \"".pkTpl("faq/faqcat")."\";");
?>