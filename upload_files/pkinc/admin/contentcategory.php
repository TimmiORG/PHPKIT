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


if(!adminaccess('contentcat'))
	return pkEvent('access_forbidden');
	

$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';
$mode=(isset($_REQUEST['mode']) && $_REQUEST['mode']=='theme') ? 'theme' : NULL;


if($ACTION==$_POST['cancel'])
	{
	pkHeaderLocation('contentcategory',$mode);
	}


if($mode=='theme')
	{
	$themeselect=(isset($_REQUEST['themeselect']) && intval($_REQUEST['themeselect'])>0) ? intval($_REQUEST['themeselect']) : ((isset($_REQUEST['themeselect']) && $_REQUEST['themeselect']=='new') ? 'new' : 0);
	$themeid=(isset($_REQUEST['themeid']) && intval($_REQUEST['themeid'])>0) ? intval($_REQUEST['themeid']) : ((isset($_REQUEST['themeid']) && $_REQUEST['themeid']=='new')? 'new' : 0);

	unset($sqlcommand);
	

	if($ACTION==$_POST['save'])
		{
		if(isset($_POST['theme_delete']) && $_POST['theme_delete']==1)
			{
			$SQL->query("DELETE FROM ".pkSQLTAB_CONTENT_THEME." WHERE contenttheme_id='".$themeid."'");
			$SQL->query("UPDATE ".pkSQLTAB_CONTENT." SET content_themeid=0 WHERE content_themeid='".$themeid."'");
			}
		
		if(isset($_POST['theme_clear']) && intval($_POST['theme_clear'])>0)
			{
			$SQL->query("UPDATE ".pkSQLTAB_CONTENT." SET content_themeid='".intval($_POST['theme_clear'])."' WHERE content_themeid='".$themeid."'");
			}
		
		if(isset($_POST['theme_catid']) && intval($_POST['theme_catid'])>0 && $themeid!='new')
			{
			$SQL->query("UPDATE ".pkSQLTAB_CONTENT." 
				SET content_cat='".intval($_POST['theme_catid'])."'
				WHERE content_themeid='".$themeid."'");
			
			$sqlcommand=", contenttheme_catid='".intval($_POST['theme_catid'])."'";
			}
		
		if($themeid=='new')
			{
			$SQL->query("INSERT INTO ".pkSQLTAB_CONTENT_THEME." (contenttheme_catid,contenttheme_name) VALUES 
				('".intval($_POST['theme_catid'])."','".$SQL->f($_POST['theme_name'])."')");
			$themeid=$SQL->insert_id();
			}
		
		if($themeid && $themeid>0)
			{
			$SQL->query("UPDATE ".pkSQLTAB_CONTENT_THEME."
				SET contenttheme_name='".$SQL->f($_POST['theme_name'])."' ".
					$sqlcommand." 
				WHERE contenttheme_id='".$themeid."'");
			}
		}

	
	if($themeselect && $themeselect!='new')
		$themeinfo=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_CONTENT_THEME." WHERE contenttheme_id='".$themeselect."'"));


	$gettheme=$SQL->query("SELECT * FROM ".pkSQLTAB_CONTENT_THEME." ORDER by contenttheme_catid, contenttheme_name ASC");
	while($theme=$SQL->fetch_array($gettheme))
		{
		$cat=$SQL->fetch_array($SQL->query("SELECT 
			*
			FROM ".pkSQLTAB_CONTENT_CATEGORY." 
			WHERE contentcat_id='".$theme['contenttheme_catid']."'"));
		
		if($theme['contenttheme_id']!=$themeinfo['contenttheme_id'])
			{
			$theme_new.='<option value="'.$theme['contenttheme_id'].'">'.
				pkEntities($cat['contentcat_name']).' - '.pkEntities($theme['contenttheme_name']).
				'</option>';
			}
		
		$articles=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_CONTENT." WHERE content_themeid='".$theme['contenttheme_id']."'"));

		if($cat['contentcat_id'] != $catid)
			{
			if($catid!='')
				$themes.='</optgroup>';
			
			$themes.='<optgroup label="'.pkEntities($cat['contentcat_name']).'">';
			
			$catid=$cat['contentcat_id'];
			}
		
		$themes.='<option value="'.$theme['contenttheme_id'].'"';

		if($themeselect==$theme['contenttheme_id'])
			$themes.=' selected';
		
		$themes.='>'.pkEntities($theme['contenttheme_name']).' ('.$articles[0].'*)</option>';
		}
	
	if($catid!='')
		{
		$themes.='</optgroup>';
		unset($catid);
		}


	$edit_themes='';
	$themeinfo['contenttheme_name']=pkEntities($themeinfo['contenttheme_name']);
	
	if($themeselect && $themeselect=='new')
		{
		$getcat=$SQL->query("SELECT * FROM ".pkSQLTAB_CONTENT_CATEGORY." ORDER by contentcat_name ASC");
		while($cat=$SQL->fetch_array($getcat))
			{
			$new_cats.='<option value="'.$cat['contentcat_id'].'">'.pkEntities($cat['contentcat_name']).'</option>';
			}
		
		eval("\$edit_themes.= \"".pkTpl("content/thememanage_new")."\";");
		}
	elseif($themeselect)
		{
		$getcat=$SQL->query("SELECT * FROM ".pkSQLTAB_CONTENT_CATEGORY." WHERE contentcat_id!='".$themeinfo['contenttheme_catid']."' ORDER by contentcat_name ASC");
		while($cat=$SQL->fetch_array($getcat))
			{
			$new_cats.='<option value="'.$cat['contentcat_id'].'">'.pkEntities($cat['contentcat_name']).'</option>';
			}
			
		eval("\$edit_themes.= \"".pkTpl("content/thememanage_edit")."\";");
		}
	
	eval("\$site_body.= \"".pkTpl("content/thememanage")."\";");
	return;
	}


$catid=(isset($_REQUEST['catid']) && intval($_REQUEST['catid'])>0) ? intval($_REQUEST['catid']) : ((isset($_REQUEST['catid']) && $_REQUEST['catid']=='new')? 'new' : 0);


if($ACTION==$_POST['save'])
	{
	if($_POST['cat_delete']==1 && $catid!='new')
		{
		$SQL->query("DELETE FROM ".pkSQLTAB_CONTENT_CATEGORY." WHERE contentcat_id='".$catid."' LIMIT 1");
		
		if(intval($_POST['cat_delete_option'])>0)
			{
			$SQL->query("UPDATE ".pkSQLTAB_CONTENT." 
				SET content_cat='".intval($_POST['cat_delete_option'])."'
				WHERE content_cat='".$catid."'");
			$SQL->query("UPDATE ".pkSQLTAB_CONTENT_THEME." 
				SET contenttheme_catid='".intval($_POST['cat_delete_option'])."'
				WHERE contenttheme_catid='".$catid."'");
			}
		}
	else
		{
		if($catid && $catid=='new')
			{
			$SQL->query("INSERT INTO ".pkSQLTAB_CONTENT_CATEGORY." (contentcat_name) VALUES ('new')");
			$catid=$SQL->insert_id();
			}
			
		$SQL->query("UPDATE ".pkSQLTAB_CONTENT_CATEGORY." 
			SET contentcat_name='".$SQL->f($_POST['cat_name'])."',
				contentcat_symbol='".$SQL->f($_POST['cat_symbol'])."',
				contentcat_type1='".intval($_POST['cat_type1'])."',
				contentcat_type2='".intval($_POST['cat_type2'])."',
				contentcat_type3='".intval($_POST['cat_type3'])."',
				contentcat_type4='".intval($_POST['cat_type4'])."',
				contentcat_type0='".intval($_POST['cat_type0'])."',
				contentcat_rights='".$SQL->f($_POST['cat_rights'])."',
				contentcat_description='".$SQL->f($_POST['cat_description'])."'
			WHERE contentcat_id='".$catid."'");
		}
		
	pkHeaderLocation('contentcategory');
	}
elseif($ACTION==$_POST['upload'] && @is_uploaded_file($_FILES['icon_file']['tmp_name']))
	{
	if(isset($_POST['icon_name']) && !empty($_POST['icon_name']))
		{
		$icon_size=getimagesize($_FILES['icon_file']['tmp_name']);
		
		if($icon_size[2]==1)
			{
			$extension=".gif";
			}
		elseif($icon_size[2]==2)
			{
			$extension=".jpg";
			}
		elseif($icon_size[2]==3)
			{
			$extension=".png";
			}
			
		$filename=$_POST['icon_name'].$extension;
		}
	else
		$filename=$_FILES['icon_file']['name'];
		
	$UPLOAD=new UPLOAD();
	$UPLOAD->images($_FILES['icon_file'],'../images/catimages',$filename);
	
	pkHeaderLocation('contentcategory');
	}

if(isset($_REQUEST['catselect'])) 
	{
	$dir="../images/catimages";
	$width=1;
	$a=opendir($dir);
	while($datei=readdir($a))
		{
		if(strstr($datei,".gif") || strstr($datei,".jpg") || strstr($datei,".png"))
			eval("\$cat_symbols.= \"".pkTpl("content/catmanage_caticonfield")."\";");
		}
	closedir($a);
	}
	
	
$getcat=$SQL->query("SELECT 
	c.*,
	COUNT(s.content_id) AS contentcount	
	FROM ".pkSQLTAB_CONTENT_CATEGORY." AS c
		LEFT JOIN ".pkSQLTAB_CONTENT." AS s ON (s.content_cat=c.contentcat_id)
	GROUP BY c.contentcat_id
	ORDER BY c.contentcat_name ASC");
while($cat=$SQL->fetch_assoc($getcat)) 
	{
	$contentcat_name=pkEntities($cat['contentcat_name']);
	$contentcat_contentcount=$cat['contentcount'];
	
	eval("\$content_cat.= \"".pkTpl("content/catmanage_catoption")."\";");
	
		
	if($cat['contentcat_id']==$_REQUEST['catselect'])
		{
		if($cat['contentcat_type0']==1) $type0="checked";
		if($cat['contentcat_type1']==1) $type1="checked";
		if($cat['contentcat_type2']==1) $type2="checked"; 
		if($cat['contentcat_type3']==1) $type3="checked";
		if($cat['contentcat_type4']==1) $type4="checked";
	
		if($cat['contentcat_rights']=="admin") $rights4="selected";
		elseif($cat['contentcat_rights']=="mod") $rights3="selected";
		elseif($cat['contentcat_rights']=="member") $rights2="selected";
		elseif($cat['contentcat_rights']=="user") $rights1="selected";
		else $rights0="selected";
			
		$cat_name=pkEntities($cat['contentcat_name']);
		$cat_symbol=!empty($cat['contentcat_symbol']) ? pkEntities($cat['contentcat_symbol']) : 'blank.gif';
		$cat_description=pkEntities($cat['contentcat_description']);
		}
	elseif(isset($_REQUEST['catselect']))
		eval("\$edit_cats.= \"".pkTpl("content/catmanage_catoption_delete")."\";");
	}
	
if($_REQUEST['catselect']=='upload')
	{
	eval("\$edit_cat= \"".pkTpl("content/catmanage_icon_upload")."\";");
	}
elseif($_REQUEST['catselect']=='new')
	{
	$catid='new';
	$type0="checked";
	$type1="checked";
	$type2="checked";
	$type3="checked";
	$type4="checked";
	$cat_symbol="blank.gif";
	
	eval("\$edit_cat= \"".pkTpl("content/catmanage_catedit")."\";");
	} 
elseif(intval($_REQUEST['catselect'])>0)
	{
	$catid=intval($_REQUEST['catselect']);
	
	eval("\$edit_option= \"".pkTpl("content/catmanage_catedit_delete")."\";");
	eval("\$edit_cat= \"".pkTpl("content/catmanage_catedit")."\";");
	}
	
eval("\$site_body.= \"".pkTpl("content/catmanage")."\";");
?>