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
	{
	die('Direct access to this location is not permitted.');
	}
	
$content_types = array('content','article','news','links','download');

$contentid = isset($_REQUEST['contentid']) && intval($_REQUEST['contentid'])>0 ? intval($_REQUEST['contentid']) : 0;
$step = isset($_REQUEST['step']) && intval($_REQUEST['step'])>0 && intval($_REQUEST['step'])<=4 ? intval($_REQUEST['step']) : 1;
$ACTION = isset($_POST['action']) ? $_POST['action'] : 'view';

#remove unaccessible content types
foreach($content_types as $key=>$value)
	{
	if(!adminaccess($value))
		{
		unset($content_types[$key]);
		}
	}

#no content types left? that means the user does not have rights to edit contents
if(empty($content_types))
	{
	return pkEvent('access_forbidden');
	}


$contentinfo = $SQL->fetch_assoc($SQL->query("SELECT * FROM ".pkSQLTAB_CONTENT." WHERE content_id='".$contentid."'"));

if(!$contentinfo['content_id'])
	{
	$contentinfo = array('content_id' => 'new');
	}


if($ACTION==$_POST['back'] && $step>1)
	{
	$step--;
	pkHeaderLocation('contentcompose','','step='.$step.'&type='.intval($_POST['type']).'&catid='.intval($_POST['catid']).'&contentid='.$contentinfo['content_id']);
	}

if($ACTION==$_POST['cancel'] || $ACTION==$_POST['back'])
	{
	pkHeaderLocation('contentarchive');
	}


if($step==4) 
	{
	#check permissions
	if(!isset($content_types[$contentinfo['content_option']]))
		{
		return pkEvent('access_forbidden');
		}
	
	
	if($ACTION==$_POST['finish']) 
		{
		if(is_array($_POST['content_related']) && $_POST['content_related'][0]!= -1)
			$content_related='-'.implode('-',$_POST['content_related']).'-';
		else
			unset($content_related);
		
		if(adminaccess('contfree'))
			$alterstatus=", content_status='".$_POST['cont_status']."'";
		else
			unset($alterstatus);
		
		
		$SQL->query("UPDATE ".pkSQLTAB_CONTENT." 
			SET content_teaser='".$SQL->f($_POST['cont_teaser'])."',
				content_related='".$SQL->f($content_related)."' ".
				$alterstatus." 
			WHERE content_id='".intval($_POST['contentid'])."'");

		if(intval($_POST['selectcat'])>0) 
			pkHeaderLocation('navigationlink','','selectcat='.intval($_POST['selectcat']).'&contentid='.intval($_POST['contentid']).'&title='.urlencode($contentinfo['content_title']));
		
		pkHeaderLocation('contentarchive');
		}
	
	$contentid=$contentinfo['content_id'];
	$cont_title=$contentinfo['content_title'];
	$cont_teaser=$contentinfo['content_teaser'];
	$cont_related=$contentinfo['content_related'];
	
	if($contentinfo['content_status']==1)
		$status1=' checked';
	else 
		$status0=' checked';
		
	$navigationinfo=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_NAVIGATION." WHERE navigation_link LIKE '%contentid=".$contentid."%' OR navigation_title LIKE '%contentid=".$contentid."%' LIMIT 1"));
	$getcat=$SQL->query("SELECT * FROM ".pkSQLTAB_NAVIGATION_CATEGORY." WHERE navigationcat_box='' ORDER BY navigationcat_order ASC");
	while($cat=$SQL->fetch_array($getcat))
		{
		if($navigationinfo['navigation_cat']==$cat['navigationcat_id'])
			{
			eval("\$linkinfo= \"".pkTpl("content/compose_step4_linkinfo")."\";");   
			}
		
		$link_cats.='<option value="'.$cat['navigationcat_id'].'">'.$cat['navigationcat_title'].'</option>';
		}
	
	if($contentinfo['content_option']==1 || $contentinfo['content_option']==4)
		{
		$getcontentinfo=$SQL->query("SELECT content_id, content_title, content_option, content_status FROM ".pkSQLTAB_CONTENT." WHERE content_id!='".$contentid."' ORDER by content_title ASC");
		while($contentinfo=$SQL->fetch_array($getcontentinfo))
			{
			unset($selected);
			
			if(strstr($cont_related,'-'.$contentinfo['content_id'].'-'))
				{
				$related_info.='<a target="_blank" href="include.php?path=contentcompose&contentid='.$contentinfo['content_id'].'">'.$contentinfo['content_title'].'</a><br />';
				$selected=' selected';
				}
			
			$content_title=pkEntities($contentinfo['content_title']);
			
			if($contentinfo['content_option']==1)
				$content_title.=' ('.$lang['article'].')';
			elseif($contentinfo['content_option']==2)
				$content_title.=' ('.$lang['news'].')';
			elseif($contentinfo['content_option']==3)
				$content_title.=' ('.$lang['link'].')';
			elseif($contentinfo['content_option']==4) 
				$content_title.=' ('.$lang['download'].')';
			else 
				$content_title.=' ('.$lang['content'].')';
			
			if($contentinfo['content_status']!=1)
				$content_title.=' - '.$lang['disabled'];
			
			$related_option.='<option value="'.$contentinfo['content_id'].'"'.$selected.'>'.$content_title.'</option>';
			}
		
		eval("\$content_relations= \"".pkTpl("content/compose_step4_relations")."\";");
		}
	}
elseif($step==3)
	{
	$cont_cat		= intval($_POST['catid']);
	$cont_type		= isset($_REQUEST['type']) && intval($_REQUEST['type'])>=0 && intval($_REQUEST['type'])<=4 ? intval($_REQUEST['type']) : $contentinfo['content_option'];
	
	
	$cont_title		= addslashes($_POST['cont_title']);
	$cont_text		= addslashes($_POST['content']);
	$cont_altdat	= addslashes($_POST['cont_altdat']);
	
	$cont_time 		= pkMkTime($ENV->_post_id('cont_time_h'),$ENV->_post_id('cont_time_mm'),0,$ENV->_post_id('cont_time_m'),$ENV->_post_id('cont_time_d'),$ENV->_post_id('cont_time_y'));
	$cont_expire	= pkMkTime($ENV->_post_id('cont_expire_h'),$ENV->_post_id('cont_expire_mm'),0,$ENV->_post_id('cont_expire_m'),$ENV->_post_id('cont_expire_d'),$ENV->_post_id('cont_expire_y'));
	

	#check permissions
	if(!isset($content_types[$cont_type]))
		{
		return pkEvent('access_forbidden');		
		}

		
	if($ACTION==$_POST['save'] || $ACTION==$_POST['next'])
		{
		if($_POST['cont_delete']==1)
			{
			pkHeaderLocation('contentarchive','delete','contentid='.intval($_POST['contentid']));
			}
		
		$cont_autor=empty($_POST['cont_autor']) ? pkGetUservalue('nick') : $_POST['cont_autor'];
		
		$userinfo=$SQL->fetch_array($SQL->query("SELECT user_id,user_nick FROM ".pkSQLTAB_USER." WHERE user_nick='".$SQL->f($cont_autor)."'"));
	
		if($userinfo['user_id']>0)
			{
			$cont_autorid=$userinfo['user_id'];
			$cont_autor=$userinfo['user_nick'];
			}			
		else
			{
			$cont_autorid=pkGetUservalue('id');
			$cont_autor=pkGetUservalue('nick');
			}		
		

		if($cont_time<0 || $_POST['reset_time']==1)
			$cont_time=pkTIME;
		
		if($cont_expire<0) 
			$cont_expire=0;
		
		if(adminaccess('contfree'))
			$alterstatus=", content_status='".intval($_POST['cont_status'])."'";
		else
			unset($alterstatus);
		
		if(!empty($_POST['newtheme_title']) && $cont_cat>0 && adminaccess('contentcat'))
			{
			$SQL->query("INSERT INTO ".pkSQLTAB_CONTENT_THEME." (contenttheme_catid, contenttheme_name) VALUES ('".$cont_cat."','".$SQL->f($_POST['newtheme_title'])."')");
			$cont_themeid=$SQL->insert_id();
			}
		else
			{
			$cont_themeid=intval($_POST['cont_themeid']);
			}
		
		if($_POST['contentid']=='new' || $_POST['cont_duplicate']==1)
			{
			$SQL->query("INSERT INTO ".pkSQLTAB_CONTENT." (content_title) VALUES ('new')");
			$contentid=$SQL->insert_id();
			}
		else
			$contentid=intval($_POST['contentid']);
		
		if($_POST['reset_counter']==1)
			{
			$sqlcommand=",content_views=0, content_rating=0, content_rating_total=0";
			}
		else
			{
			$sqlcommand='';
			}
		
		$SQL->query("UPDATE ".pkSQLTAB_CONTENT." 
			SET content_time='".$cont_time."',
				content_expire='".$cont_expire."',
				content_title='".$cont_title."',
				content_autor='".$SQL->f($cont_autor)."',
				content_autorid='".$cont_autorid."',
				content_cat='".$cont_cat."',
				content_option='".$cont_type."',
				content_text='".$cont_text."',
				content_altdat='".$cont_altdat."',
				content_html='".intval($_POST['cont_html'])."',
				content_ubb='".intval($_POST['cont_ubb'])."',
				content_smilies='".intval($_POST['cont_smilies'])."',
				content_rating_status='".intval($_POST['cont_rating'])."',
				content_comment_status='".intval($_POST['cont_comment'])."',
				content_themeid='".$cont_themeid."',
				content_filesize='".intval($_POST['cont_filesize'])."' ".
					$alterstatus." ".
					$sqlcommand." 
			WHERE content_id='".$contentid."'");
   
		$to=($ACTION==$_POST['next']) ? 4 : 3;
		
		pkHeaderLocation('contentcompose','','step='.$to.'&contentid='.$contentid);
		}

	if(isset($_REQUEST['type']) && intval($_REQUEST['type'])>=0 && intval($_REQUEST['type'])<=4)
		{
		$type=intval($_REQUEST['type']);
		}
	else
		{
		$type=$contentinfo['content_option'];		
		}

	$contentid=(isset($_REQUEST['contentid']) && intval($_REQUEST['contentid'])>0) ? intval($_REQUEST['contentid']) : ((isset($_REQUEST['contentid']) && $_REQUEST['contentid']=='new')? 'new' : 0);	
	$catid=(isset($_REQUEST['catid']) && intval($_REQUEST['catid'])>0) ? intval($_REQUEST['catid']) : $contentinfo['content_cat'];
	
	
	if($type<0 || $type>4)
		pkHeaderLocation('contentcompose','','step=1&contentid='.$contentid);
	
	if(!$catid)
		pkHeaderLocation('contentcompose','','step=2&contentid='.$contentid);
		
		
	if($contentinfo['content_time']>0)
		$time=$contentinfo['content_time'];
	else
		$time=pkTIME;
	
	$content_time_d=date("d",$time);
	$content_time_m=date("m",$time);
	$content_time_y=date("Y",$time);
	$content_time_h=date("H",$time);
	$content_time_mm=date("i",$time);
	
	
	if($contentinfo!='' && $contentinfo['content_id']!='new')
		{
		$cont_autor=pkEntities($contentinfo['content_autor']);
		$cont_title=pkEntities($contentinfo['content_title']);
		$content_text=pkEntities($contentinfo['content_text']);
		$content_altdat=pkEntities($contentinfo['content_altdat']);
		
		if($contentinfo['content_html']==1)
			$option1=" checked";

		if($contentinfo['content_ubb']==1)
			$option2=" checked";

		if($contentinfo['content_smilies']==1)
			$option3=" checked";

		if($contentinfo['content_rating_status']==1)
			$option4=" checked";
		
		if($contentinfo['content_comment_status']==1)
			$option5=" checked";
		
		if($contentinfo['content_status']==1)
			$option6=" checked";
		
		if($contentinfo['content_expire']>0)
			{
			$time=$contentinfo['content_expire'];
			
			$content_expire_d=date("d",$time);
			$content_expire_m=date("m",$time);
			$content_expire_y=date("Y",$time);
			$content_expire_h=date("H",$time);
			$content_expire_mm=date("i",$time);
			}
		
		eval("\$ext_option= \"".pkTpl("content/compose_step3_extoption")."\";");
		}
	else
		{
		$cont_autor=pkGetUservalueF('nick');
		$option1="";
		$option2=" checked";
		$option3=" checked";
		$option4=" checked";
		$option5=" checked";
		
		if(adminaccess('contfree'))
			$option6=" checked";
		
		if($type==3)
			$content_altdat='http://';
		}
	
	$getthemes=$SQL->query("SELECT * FROM ".pkSQLTAB_CONTENT_THEME." WHERE contenttheme_catid='".$catid."' ORDER BY contenttheme_name ASC");
	while($theme=$SQL->fetch_array($getthemes))
		{
		$theme_option.='<option value="'.$theme['contenttheme_id'].'"';
		
		if($theme['contenttheme_id']==$contentinfo['content_themeid'])
			$theme_option.=' selected';
		
		$theme_option.='>'.pkEntities($theme['contenttheme_name']).'</option>';
		}
	
	
	$smilies=new smilies();
	$format_smilies=$smilies->getSmilies(1,1);
	
	$error_validity_period=($contentinfo['content_expire']<$contentinfo['content_time'] && $contentinfo['content_expire']>0) ? pkGetLangError('enddate_earlier_then_start') : '';

	
	eval("\$compose_bbcode= \"".pkTpl("format_text")."\";");  
	eval("\$content_body= \"".pkTpl("content/compose_step3_".$type."")."\";");
	}
elseif($step==2)
	{
	if(isset($_POST['content_cat']) && intval($_POST['content_cat'])>0)
		pkHeaderLocation('contentcompose','','step=3&type='.intval($_POST['content_type']).'&catid='.intval($_POST['content_cat']).'&contentid='.$contentinfo['content_id']);

	if($ACTION!='view' && intval($_POST['content_cat'])<=0)
		pkHeaderLocation('contentcompose','','step=2&type='.intval($_POST['content_type']).'&catid='.intval($_POST['content_cat']).'&contentid='.$contentinfo['content_id']);

	
	unset($cat_option);
	
	$content_type=(isset($_REQUEST['type']) && intval($_REQUEST['type'])>=0 && intval($_REQUEST['type'])<=4) ? intval($_REQUEST['type']) : $contentinfo['content_option'];

	#check permissions
	if(!isset($content_types[$content_type]))
		{
		return pkEvent('access_forbidden');
		}	

	
	$selecttype=' contentcat_type'.$content_type.'=1';
	
	$getcat=$SQL->query("SELECT contentcat_id, contentcat_name FROM ".pkSQLTAB_CONTENT_CATEGORY." WHERE ".$selecttype." ORDER by contentcat_name ASC");
	while($cat=$SQL->fetch_array($getcat))
		{
		$cat_option.='<option value="'.$cat['contentcat_id'].'"'.(($cat['contentcat_id']==$contentinfo['content_cat']) ? ' selected' : '').'>'.pkEntities($cat['contentcat_name']).'</option>';
		}
	
	if(!$cat_option) 
		{
		eval("\$step2_info= \"".pkTpl("content/compose_step2_info")."\";");
		}
	}
else 
	{
	#STEP 1 - content type selection
	if(isset($_REQUEST['content_type']) && intval($_REQUEST['content_type'])<=4 && intval($_REQUEST['content_type'])>=0)
		{
		pkHeaderLocation('contentcompose','','step=2&contentid='.$contentinfo['content_id'].'&type='.intval($_REQUEST['content_type']));
		}


	pkLoadLang('content');	

	$options = '';
	
	foreach($content_types as $key=>$value)
		{
		$options.= '<option value="'.$key.'"'.(intval($contentinfo['content_option'])===$key ? ' selected="selected"' :'').'>'.pkGetLang('content_compose_'.$value).'</option>';
		}
	}#END else
	
if($step==4)
	{
	eval("\$nav_button= \"".pkTpl("content/compose_navigation_finishbutton")."\";"); 
	}
else
	{
	eval ("\$nav_button= \"".pkTpl("content/compose_navigation_nextbutton")."\";");
	}

	
eval("\$compose_navigation= \"".pkTpl("content/compose_navigation")."\";");
eval("\$site_body.= \"".pkTpl("content/compose_step".$step."")."\";");
?>