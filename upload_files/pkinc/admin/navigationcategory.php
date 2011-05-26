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


if(!adminaccess('navcat'))
	return pkEvent('access_forbidden');


$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';


if(isset($_POST['cancel']) && $ACTION==$_POST['cancel'])
	{
	pkHeaderLocation('navigationcategory');
	}
 
 
if(isset($_POST['edit']) && isset($_POST['navigation_cat']))
	$select_navcat=$_POST['navigation_cat'];
elseif(isset($_POST['select_navcat']))
	$select_navcat=$_POST['select_navcat'];
else
	unset($select_navcat);
	

if(isset($_POST['new']))
	{
	$select_navcat='new';
	}
elseif(isset($_POST['setopen']) && isset($_POST['navigation_cat']))
	{
	$SQL->query("UPDATE ".pkSQLTAB_NAVIGATION_CATEGORY." SET navigationcat_status='1' WHERE navigationcat_id='".$SQL->id($_POST['navigation_cat'])."'");
	}
elseif(isset($_POST['setup']) && isset($_POST['navigation_cat']))
	{
	$SQL->query("UPDATE ".pkSQLTAB_NAVIGATION_CATEGORY." SET navigationcat_order=navigationcat_order-1 WHERE navigationcat_id='".$SQL->id($_POST['navigation_cat'])."'");
	}
elseif(isset($_POST['settop']) && isset($_POST['navigation_cat']))
	{
	$SQL->query("UPDATE ".pkSQLTAB_NAVIGATION_CATEGORY." SET navigationcat_align='2' WHERE navigationcat_id='".$SQL->id($_POST['navigation_cat'])."'");
	}
elseif(isset($_POST['setleft']) && isset($_POST['navigation_cat']))
	{
	$SQL->query("UPDATE ".pkSQLTAB_NAVIGATION_CATEGORY." SET navigationcat_align='0' WHERE navigationcat_id='".$SQL->id($_POST['navigation_cat'])."'");
	}
elseif(isset($_POST['setright']) && isset($_POST['navigation_cat']))
	{
	$SQL->query("UPDATE ".pkSQLTAB_NAVIGATION_CATEGORY." SET navigationcat_align='1'  WHERE navigationcat_id='".$SQL->id($_POST['navigation_cat'])."'");
	}
elseif(isset($_POST['setbottom']) && isset($_POST['navigation_cat']))
	{
	$SQL->query("UPDATE ".pkSQLTAB_NAVIGATION_CATEGORY." SET navigationcat_align='3'  WHERE navigationcat_id='".$SQL->id($_POST['navigation_cat'])."'");
	}
elseif(isset($_POST['setdown']) && isset($_POST['navigation_cat']))
	{
	$SQL->query("UPDATE ".pkSQLTAB_NAVIGATION_CATEGORY." SET navigationcat_order=navigationcat_order+1 WHERE navigationcat_id='".$SQL->id($_POST['navigation_cat'])."'");
	}
elseif(isset($_POST['setclose']) && isset($_POST['navigation_cat']))
	{
	$SQL->query("UPDATE ".pkSQLTAB_NAVIGATION_CATEGORY." SET navigationcat_status='0' WHERE navigationcat_id='".$SQL->id($_POST['navigation_cat'])."'");
	}


if($ACTION==$_POST['save'])
	{
	if($_POST['delete_cat']==1 && isset($select_navcat))
		{
		if($_POST['delete_links']==0)
			$SQL->query("DELETE FROM ".pkSQLTAB_NAVIGATION." WHERE navigation_cat='".$select_navcat."'");
		else
			$SQL->query("UPDATE ".pkSQLTAB_NAVIGATION." SET navigation_cat='".$_POST['delete_links']."' WHERE navigation_cat='".$select_navcat."'");
		
		$SQL->query("DELETE FROM ".pkSQLTAB_NAVIGATION_CATEGORY." WHERE navigationcat_id='".$select_navcat."' LIMIT 1");
		}
	else 
		{
		if($select_navcat=='new')
			{
			$SQL->query("INSERT INTO ".pkSQLTAB_NAVIGATION_CATEGORY." (navigationcat_title) VALUES ('new')");
			$select_navcat=$SQL->insert_id();
			}
		
		
		$cat_navbox=basename($_POST['cat_navbox']);

		if(!filecheck(pkDIRPUBLICINC.$cat_navbox))
			$cat_navbox='';
		
		$SQL->query("UPDATE ".pkSQLTAB_NAVIGATION_CATEGORY."
			SET navigationcat_title='".$SQL->f($_POST['cat_title'])."',
				navigationcat_align='".$SQL->f($_POST['cat_align'])."',
				navigationcat_status='".$SQL->f($_POST['cat_status'])."',
				navigationcat_rights='".$SQL->f($_POST['cat_rights'])."',
				navigationcat_template='".$SQL->f($_POST['cat_template'])."',
				navigationcat_open='".$SQL->f($_POST['cat_open'])."',
				navigationcat_showtitle='".$SQL->f($_POST['cat_showtitle'])."',
				navigationcat_box='".$SQL->f($cat_navbox)."',
				navigationcat_link='".$SQL->f($_POST['cat_link'])."',
				navigationcat_link_target='".$SQL->f($_POST['cat_link_target'])."',
				navigationcat_order='".$SQL->i($_POST['cat_order'])."'
			WHERE navigationcat_id='".$select_navcat."'");
		}
	
	pkHeaderLocation('navigationcategory');
	}
	

pkLoadLang('adminnavigation');


$getcat=$SQL->query("SELECT * FROM ".pkSQLTAB_NAVIGATION_CATEGORY." ORDER by navigationcat_order ASC");
while($cat=$SQL->fetch_array($getcat))
	{
	$navigationcat_option.='<option value="'.$cat['navigationcat_id'].'">'.pkEntities($cat['navigationcat_title']).'</option>';
	}


if($select_navcat!="")
	{
	$boxhash=array();
	$dir=pkDIRPUBLICINC;
	$a=opendir($dir);
	while($file=readdir($a))
		{
		if(substr($file,0,7) != 'navbox.')
			continue;

		$file_name=($file_name=pkGetLang($file)) ? $file_name : $file;

		$boxhash[$file_name]=$file;
		}
	
	closedir($a);
	
	
	if($select_navcat!=0)
		{
		$type=$lang['edit'];
		$info=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_NAVIGATION_CATEGORY." WHERE navigationcat_id='".$select_navcat."'"));

		if($info['navigationcat_box']=='')
			{
			$getinfo=$SQL->query("SELECT * FROM ".pkSQLTAB_NAVIGATION_CATEGORY." WHERE navigationcat_id!='".$info['navigationcat_id']."'");
			while($delinfo=$SQL->fetch_array($getinfo))
				{
				eval("\$delete_cats.= \"".pkTpl("editnavcat_delete_ext_option")."\";");
				}
			
			if($delete_cats!='')
				eval("\$delete_ext= \"".pkTpl("editnavcat_delete_ext")."\";");
			}
		
		if(is_array($boxhash))
			{
			ksort($boxhash);
			
			foreach($boxhash as $name=>$file)
				{
				$selected=($file==$info['navigationcat_box']) ? ' selected="selected"' : '';
				
				if($file=='navbox.style.php' && !pkGetConfig('user_design'))
					$name.=' ('.pkGetLang('dependency_deactivated').')';

				$navigation_box_option.='<option value="'.$file.'"'.$selected.'>'.$name.'</option>';				
				}
			
			eval("\$navigation_box=\"".pkTpl("editnavcat_box")."\";");
			}
		
		eval("\$navigation_delete=\"".pkTpl("editnavcat_delete")."\";");
		}
	else
		{
		$type=$lang['create'];
		$info['navigationcat_status']=1;
		$info['navigationcat_showtitle']=1;
		$info['navigationcat_open']=1;
		$info['navigationcat_align']=0;
		
		
		if(is_array($boxhash))
			{
			ksort($boxhash);
			
			foreach($boxhash as $name=>$file)
				$navigation_box_option.='<option value="'.$file.'">'.$name.'</option>';
			}
		
		eval("\$navigation_box= \"".pkTpl("editnavcat_box")."\";");
		}
	
	
	if($info['navigationcat_status']==1)
		$status1="selected";
	else
		$status0="selected";
	
	if($info['navigationcat_showtitle']==1)
		$show1="checked";
	else
		$show0="checked";
		
	if(!empty($info['navigationcat_link_target']))
		$target1="checked";
	else
		$target0="checked";

	if($info['navigationcat_open']==1)
		$open1="selected";
	else
		$open0="selected";
	
	if($info['navigationcat_align']==1)
		$align1="selected";
	elseif($info['navigationcat_align']==2)
		$align2="selected";
	elseif($info['navigationcat_align']==3)
		$align3="selected";
	else
		$align0="selected";
	
	if($info['navigationcat_rights']=="admin")
		$rights4="selected";
	elseif($info['navigationcat_rights']=="mod")
		$rights3="selected";
	elseif($info['navigationcat_rights']=="member")
		$rights2="selected";
	elseif($info['navigationcat_rights']=="user")
		$rights1="selected";
	else
		$rights0="selected";
	
	$info['navigationcat_title']=pkEntities($info['navigationcat_title']);
	$info['navigationcat_template']=pkEntities($info['navigationcat_template']);
	$info['navigationcat_link']=pkEntities($info['navigationcat_link']);	
	
	eval("\$navigation_show= \"".pkTpl("editnavcat_edit")."\";");
	}
else
	{
	$getcats=$SQL->query("SELECT * FROM ".pkSQLTAB_NAVIGATION_CATEGORY." ORDER by navigationcat_order ASC");
	while($cat=$SQL->fetch_array($getcats))
		{
		if($cat['navigationcat_status']==0)
			$navigation_info='('.$lang['disabled'].')';
		else 
			$navigation_info='';

		if($navigation_cat==$cat['navigationcat_id'])
			$select=" selected";
			
		$cat['navigationcat_title']=pkEntities($cat['navigationcat_title']);
		
		
		if($cat['navigationcat_align']=="1")
			{
			eval("\$navigation_right.= \"".pkTpl("editnavcat_navigation_right")."\";");
			$countr++;
			}
		elseif($cat['navigationcat_align']=="2")
			{
			eval("\$navigation_top.= \"".pkTpl("editnavcat_navigation_right")."\";");
			$countt++;
			}
		elseif($cat['navigationcat_align']=="3")
			{
			eval("\$navigation_bottom.= \"".pkTpl("editnavcat_navigation_right")."\";");
			$countb++;
			}
		else
			{
			eval("\$navigation_left.= \"".pkTpl("editnavcat_navigation_left")."\";");
			$countl++;
			}
		
		unset($select);
		}
	
	if($countl>$countr)
		$count=$countl+1;
	else
		$count=$countr+1;
	
	if($countb==1)
		$countb++;
	
	if($countt==1)
		$countt++;
	
	eval("\$navigation_show= \"".pkTpl("editnavcat_navigation")."\";");
	}

eval("\$site_body.= \"".pkTpl("editnavcat")."\";");
?>