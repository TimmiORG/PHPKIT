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


if(!adminaccess('user')) 
	return pkEvent('access_forbidden');


$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';
$id=(isset($_REQUEST['id']) && intval($_REQUEST['id'])>0) ? intval($_REQUEST['id']) : ((isset($_REQUEST['id']) && $_REQUEST['id']=='new')? 'new' : 0);

 
if(isset($_POST['cancel']) && $ACTION==$_POST['cancel'])
	pkHeaderLocation('userprofilefields');
	
 

if(isset($_POST['delete']) && $ACTION==$_POST['delete'])
	{
	if(isset($_POST['drop_confirm']) && $_POST['drop_confirm']==1 && intval($_POST['id'])>0) 
		{
		list($order)=$SQL->fetch_row($SQL->query("SELECT 
				profilefields_order 
			FROM ".pkSQLTAB_USER_PROFILEFIELDS."
			WHERE profilefields_id='".intval($_POST['id'])."'
			LIMIT 1"));

		$SQL->query("DELETE FROM ".pkSQLTAB_USER_PROFILEFIELDS." WHERE profilefields_id='".intval($_POST['id'])."'");
		$SQL->query("ALTER TABLE ".pkSQLTAB_USER_FIELDS." DROP field_".intval($_POST['id']));		
		$SQL->query("UPDATE ".pkSQLTAB_USER_PROFILEFIELDS."
			SET profilefields_order=profilefields_order-1
			WHERE profilefields_order>'".$order."'");
		} 
	
	pkHeaderLocation('userprofilefields');
	}


if(isset($_POST['save']) && $ACTION==$_POST['save'] && isset($_POST['id']))
	{
	if($id && $id=='new')
		{
		$SQL->query("INSERT INTO ".pkSQLTAB_USER_PROFILEFIELDS." (profilefields_name) VALUES ('new')");
		$id=$SQL->insert_id();
		$SQL->query("ALTER TABLE ".pkSQLTAB_USER_FIELDS." ADD field_".$id." varchar(250) NOT NULL");
		}
		

	$field_maxlength=(intval($_POST['field_maxlength'])<1 || intval($_POST['field_maxlength'])>250) ? 250 : intval($_POST['field_maxlength']);
	
	if($id && $id>0)
		{
		$SQL->query("UPDATE ".pkSQLTAB_USER_PROFILEFIELDS." 
			SET profilefields_name='".$SQL->f($_POST['field_name'])."',
				profilefields_description='".$SQL->f($_POST['field_description'])."',
				profilefields_maxlength='".intval($field_maxlength)."',
				profilefields_order='".intval($_POST['field_order'])."'
			WHERE profilefields_id='".$id."'");
		}
	
	pkHeaderLocation('userprofilefields');
	}


if(isset($_GET['action']) && $id && $_GET['action']=='drop' && $id>0)
	{
	$profilefield=$SQL->fetch_array($SQL->query("SELECT 
		*
		FROM ".pkSQLTAB_USER_PROFILEFIELDS."
		WHERE profilefields_id='".intval($id)."' LIMIT 1"));
	
	$fieldname=pkEntities($profilefield['profilefields_name']);
	
	eval("\$site_body.= \"".pkTpl("profilefields_drop")."\";");
	return;
	}


if((isset($_GET['action']) && $_GET['action']=='edit' && $id>0) || ($id && $id=='new'))
	{
	if($id && $id!='new') 
		{
		$profilefield=$SQL->fetch_array($SQL->query("SELECT
			*
			FROM ".pkSQLTAB_USER_PROFILEFIELDS." 
			WHERE profilefields_id='".intval($id)."' LIMIT 1"));
		
		$fieldid=$profilefield['profilefields_id'];
		$fieldname=pkEntities($profilefield['profilefields_name']);
		$fielddescription=pkEntities($profilefield['profilefields_description']);
		
		$fieldmaxlength=$profilefield['profilefields_maxlength'];
		$fieldorder=$profilefield['profilefields_order'];
		}
	else
		{
		$fieldid='new';
		$fieldname='';
		$fielddescription='';
		$fieldmaxlength=250;
		list($fieldorder)=$SQL->fetch_row($SQL->query("SELECT (MAX(profilefields_order)+1) FROM ".pkSQLTAB_USER_PROFILEFIELDS));
		$fieldorder=$fieldorder ? $fieldorder : 1;
		}
	
	eval("\$site_body.= \"".pkTpl("profilefields_form")."\";");
	return;
	}


$getprofilefields=$SQL->query("SELECT * FROM ".pkSQLTAB_USER_PROFILEFIELDS." ORDER by profilefields_order ASC");
while($profilefields=$SQL->fetch_array($getprofilefields))
	{
	$row=rowcolor($row);
	$profilefields['profilefields_name']=pkEntities($profilefields['profilefields_name']);
	
	eval("\$profilefields_row.= \"".pkTpl("profilefields_row")."\";");
	}

eval("\$site_body.= \"".pkTpl("profilefields")."\";");
?>