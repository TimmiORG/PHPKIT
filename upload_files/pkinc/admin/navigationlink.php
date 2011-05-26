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


if(!adminaccess('navlink'))
	return pkEvent('access_forbidden');


$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';


if(isset($_POST['action']) && $ACTION==$_POST['cancel'])
	{
	pkHeaderLocation('navigationlink');
	}


if(isset($_POST['save']) && $ACTION==$_POST['save'])
	{
	if(intval($_POST['linkid'])>0 && $_POST['link_delete']==1)
		$SQL->query("DELETE FROM ".pkSQLTAB_NAVIGATION." WHERE navigation_id='".intval($_POST['linkid'])."'");

	if($_POST['linkid']=='new' || $_POST['link_copy']==1)
		{
		$SQL->query("INSERT INTO ".pkSQLTAB_NAVIGATION." (navigation_title) VALUES ('new')");
		$linkid=$SQL->insert_id();
		
		if($_POST['link_copy']==1)
			$catid=intval($_POST['link_cat']);
		else
			$catid=intval($_POST['catid']);
		}
	else 
		{
		$linkid=intval($_POST['linkid']);
		$catid=intval($_POST['link_cat']);
		}
	
	if($_POST['link_delete']!=1 || $_POST['link_delete']==1 && $_POST['link_copy']==1) 
		{
		$SQL->query("UPDATE ".pkSQLTAB_NAVIGATION." 
			SET navigation_cat='".$catid."',
				navigation_status='".$SQL->f($_POST['link_status'])."',
				navigation_title='".$SQL->f($_POST['link_title'])."',
				navigation_link='".$SQL->f($_POST['link_link'])."',
				navigation_order='".$SQL->i($_POST['link_order'])."',
				navigation_type='".$SQL->f($_POST['link_type'])."',
				navigation_option='".$SQL->f($_POST['link_option'])."',
				navigation_userstatus='".$SQL->f($_POST['link_userstatus'])."'
			WHERE navigation_id='".$linkid."'");
		}
	
	pkHeaderLocation('navigationlink');
	}

if($ACTION==$_POST['linkup'] && isset($_REQUEST['orderlink']))
	{
	$SQL->query("UPDATE ".pkSQLTAB_NAVIGATION." 
		SET navigation_order=navigation_order-1
		WHERE navigation_id='".intval($_REQUEST['orderlink'])."'");
	}
elseif($ACTION==$_POST['linkdown'] && isset($_REQUEST['orderlink']))
	{
	$SQL->query("UPDATE ".pkSQLTAB_NAVIGATION."
		SET navigation_order=navigation_order+1 
		WHERE navigation_id='".intval($_REQUEST['orderlink'])."'");
	}
elseif($ACTION==$_POST['edit'] && isset($_REQUEST['orderlink']))
	$linklink=intval($_REQUEST['orderlink']);
elseif(isset($_REQUEST['linklink'])) 
	$linklink=intval($_REQUEST['linklink']);
elseif(isset($_REQUEST['selectcat']))
	$selectcat=intval($_REQUEST['selectcat']);


unset($link_cats);
unset($move_link);

$getcat=$SQL->query("SELECT * FROM ".pkSQLTAB_NAVIGATION_CATEGORY." WHERE navigationcat_box='' ORDER by navigationcat_order ASC");
while($cat=$SQL->fetch_array($getcat))
	{
	$link_cats.='<option value="'.$cat['navigationcat_id'].'">'.pkEntities($cat['navigationcat_title']).'</option>';

	$getlink=$SQL->query("SELECT 
		* 
		FROM ".pkSQLTAB_NAVIGATION." 
		WHERE navigation_cat='".$cat['navigationcat_id']."' 
		ORDER by navigation_order ASC");
	while($link=$SQL->fetch_array($getlink))
		{
		if($link['navigation_status']==0)
			$showlink_status='('.$lang['disabled'].')';
		
		if($_REQUEST['linklink']==$link['navigation_id'])
			$select=" selected";
			
		
		$navigation_title_cutted=pkEntities(mb_substr($link['navigation_title'],0,50,'UTF-8'));
				
		eval("\$link_links.= \"".pkTpl("editnavlink_linkoption")."\";");
		unset($select);
		
		
		if($linklink==$link['navigation_id']) 
			{
			$linkinfo=$link;
			$cat_name=' '.$lang['in'].' '.pkEntities($cat['navigationcat_title']);
			$selectcat=$link['navigation_cat'];
			}
		
		if($link['navigation_cat']==$_REQUEST['ordercat']) 
			{
			if($_REQUEST['orderlink']==$link['navigation_id']) 
				$select=" selected";
			
			eval("\$order_links.= \"".pkTpl("editnavlink_order_linkoption")."\";");
			unset($select);
			$count++;
			}
		
		unset($showlink_status);
		}
	
	
	if($cat['navigationcat_id']==$selectcat)
		$cat_name=' '.$lang['in'].' '.pkEntities($cat['navigationcat_title']);
	
	if($cat['navigationcat_id']==$_REQUEST['ordercat'])
		$cat_name=pkEntities($cat['navigationcat_title']);
	
	$move_link.='<option value="'.$cat['navigationcat_id'].'"'.(($cat['navigationcat_id']==$linkinfo['navigation_cat']) ? ' selected' : '').'>'.pkEntities($cat['navigationcat_title']).'</option>';
	}


if(isset($linklink))
	{
	$action_type=$lang['edit'];
	
	if($linkinfo['navigation_type']==1)
		$type1=" selected";
	else 
		$type0=" selected";
	
	if($linkinfo['navigation_option']==1)
		$option1=" selected";
	elseif($linkinfo['navigation_option']==2)
		$option2=" selected";
	elseif($linkinfo['navigation_option']==3)
		$option3=" selected";
	elseif($linkinfo['navigation_option']==4)
		$option4=" selected";
	else
		$option0=" selected";
	
	if($linkinfo['navigation_status']==1)
		$status1=" selected";
	else
		$status0=" selected";
	
	if($linkinfo['navigation_userstatus']=="user")
		$option_s1=" selected";
	elseif($linkinfo['navigation_userstatus']=="member")
		$option_s2=" selected";
	elseif($linkinfo['navigation_userstatus']=="mod")
		$option_s3=" selected";
	elseif($linkinfo['navigation_userstatus']=="admin")
		$option_s4=" selected";
	else
		$option_s0=" selected";
		

	$linkinfo['navigation_link']=pkEntities($linkinfo['navigation_link']);
	
	eval("\$link_doedit= \"".pkTpl("editnavlink_doeditlink")."\";");
	eval("\$link_edit= \"".pkTpl("editnavlink_editlink")."\";");
	}
elseif(intval($_REQUEST['ordercat'])>0)
	{
	$action_type=$lang['order'];
	$count++;
	$ordercat=intval($_REQUEST['ordercat']);
	
	eval ("\$link_edit= \"".pkTpl("editnavlink_orderlink")."\";");
	}
elseif(isset($selectcat))
	{
	$action_type=$lang['create'];
	$linklink='new';
	
	if(intval($_GET['contentid'])>0)
		$linkinfo['navigation_link']='?path=content&contentid='.intval($_GET['contentid']);
	
	if(!empty($_GET['title']))
		$linkinfo['navigation_title']=pkEntities($_GET['title']);
	
	eval("\$link_edit=\"".pkTpl("editnavlink_editlink")."\";");
	}

eval("\$site_body.=\"".pkTpl("editnavlink")."\";");
?>