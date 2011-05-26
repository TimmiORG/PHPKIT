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


$SESSION=new pkSessionadmin;	
$SESSION->main();


pkLoadLang('admin');

if(!adminaccess('adminarea') || !$SESSION->isadminsession()) 
	pkAdminLogin();


#small admin config
define('pkWINDOWNAME_ADMINFRAME_HEADER','pkFrameHeader');
define('pkWINDOWNAME_ADMINFRAME_MAIN','pkFrameMain');
define('pkWINDOWNAME_ADMINFRAME_NAVIGATION','pkFrameNavigation');
define('pkWINDOWNAME_ADMINFRAME_HIDDEN','pkFrameHidden');

$pkDISPLAYPOPUP		= false;
$adminsite_template	= 'site';
$adminsite_css		= 'fx/default/css/main.css';

$site_body			= '';
$site_header_script	= '';
$site_body_onload	= '';
$site_refresh		= '';


$path = $ENV->_isset_request('path') ? $ENV->_request('path') : NULL;

if(!empty($path))
	{
	$path=basename($path);
	$path_filename=pkDIRADMIN.$path.pkEXT;
	
	if(filecheck($path_filename))
		{
		include($path_filename);
		
		if(!$pkDISPLAYPOPUP && $ENV->requestmethod('GET'))
			{
			$SESSION->set('admin_main_openedpath',$ENV->getvar('QUERY_STRING'));
			}
		}
	else 
		{
		pkEvent('page_not_found');
		}
	}
else
	{
	$mode=$ENV->_get('mode');


	if($mode=='header')
		{
		$adminsite_css='fx/default/css/frame.css';

		$version=pkGetLang('version').' '.pkPHPKIT_VERSION;

		$version_check=pkGetConfig('version_check');
		$version_checked=pkGetConfig('version_checked');
		$version_lastcheck=pkGetConfig('version_lastcheck');
		
		if($version_lastcheck+86400>pkTIME && pkVersionCompare($version_checked))
			{
			$version=pkHtmlLink('http://www.phpkit.com',pkGetLang('new_version_available'),'_blank','new');
			}
		elseif($version_check && $version_lastcheck+86400<pkTIME)
			{
			$SQL->query("UPDATE ".pkSQLTAB_CONFIG." SET value='".$SQL->i(serialize(pkTIME-82800))."' WHERE id='version_lastcheck'");	#if the check fails - retry 1 hour later
						
			$site_header_script='<script type="text/javascript">
<!--
var version=\''.pkPHPKIT_VERSION.'\'; 
var latestversion=\'undefined\';
var lang_version=\''.$version.'\';
var hiddenurl=\''.pkLink('','hidden').'\';
//-->				
</script>
<script src="http://external.phpkit.de/version/check'.(pkDEVMODE ? 'dev' : '').'.js" type="text/javascript"></script>
<script src="fx/version.js" type="text/javascript"></script>
<script type="text/javascript">
<!-- 
setTimeout("pkVersioncheck();",10);
//-->
</script>';
			}


		$name_adminframe_main=pkWINDOWNAME_ADMINFRAME_MAIN;

		$link_admin=pkLink();		
		$link_main=pkLink('main');		
		$link_pm=pkLinkPublic('privatemessages','',intval(imstatus()>0) ? 'imid=new' : '');
				
		$hlink_website=pkHtmlLink(pkLinkPublic(),pkGetLang('website'),'_top');
		$hlink_logout=pkHtmlLink(pkLink('','','logout=1'),pkGetLang('do_Logout'),'_top');

		$time=pkTimeFormat(pkTIME,'extend');
		
		$lang_logged_in_as=pkGetLang('logged_in_as');
		$usernick=pkGetUservalueF('nick');

		if(adminaccess('user'))
			$userinfo=$lang_logged_in_as.pkHtmlLink(pkLink('useredit','','editid='.pkGetUservalueF('id')),$usernick,pkWINDOWNAME_ADMINFRAME_MAIN);
		else
			$userinfo=$lang_logged_in_as.$usernick;	
		
		$pmcount=pkHtmlLink($link_pm,pkGetSpecialLang('private_message'),'_blank');
	
		eval("\$site_body=\"".pkTpl('site_header')."\";");		
		}
	elseif($mode=='hidden')
		{
		$reload_header=0;
		$link_header='';

		if(pkGetConfig('version_check') && $ENV->_isset_get('versionchecked'))
			{
			$sql='';
			$versionchecked=$ENV->_get('versionchecked');

			if(!empty($versionchecked) && preg_match("/1\.[0-9]{1,}\.[0-9]{1,}([ RCPL0-9]?)/i",$versionchecked) && pkVersionCompare($versionchecked))
				{
				$sql = ",('version_checked','".$SQL->f(serialize($versionchecked))."')";
				$reload_header = 1;
				$link_header = pkLink('','header');
				}
			
			$SQL->query("REPLACE INTO ".pkSQLTAB_CONFIG." (id,value) VALUES ('version_lastcheck','".$SQL->f(serialize(pkTIME))."')".$sql);
			}
		
		
		$ctime=pkTimeFormat(pkTIME,'extend');
		$reloadtime=(60-date('s',pkTIME))*1000;

		$lang_logged_in_as=pkGetLang('logged_in_as');
		$usernick=addslashes(pkGetUservalueF('nick'));

		if(adminaccess('user'))
			{
			$userinfo = $lang_logged_in_as;
			$userinfo.= pkHtmlLink(pkLink('useredit','','editid='.pkGetUservalueF('id')),$usernick,pkWINDOWNAME_ADMINFRAME_MAIN);
			}
		else
			{
			$userinfo = $lang_logged_in_as.$usernick;	
			}
			
		$link_pm=pkLinkPublic('privatemessages','',intval(imstatus()>0) ? 'imid=new' : '');
		$pmcount=pkHtmlLink($link_pm,pkGetSpecialLang('private_message'),'_blank');
		

		eval("echo \"".pkTpl('site_hidden')."\";");
		return;
		}		
	elseif($mode=='navigation')
		{
		pkLoadHtml('admin');

		$menu_hash = array();	
		$adminsite_css='fx/default/css/navigation.css';
		
		
		if(!$ENV->_get_id('init'))
			{
			$open = explode('-',$ENV->_get('open'));
			$open = is_array($open) ? $open : array();

			$SESSION->set('admin_navigation_openlinks',$open);
			}
		else
			{
			$open=($SESSION->exists('admin_navigation_openlinks') && is_array($SESSION->get('admin_navigation_openlinks'))) ? $SESSION->get('admin_navigation_openlinks') : array();						
			}
			
		#load opened menu items
		$query = $SQL->query("SELECT 
				id,pid,sorting,lkey,lscope,target,lnkpath,lnkmode,lnkadd 
			FROM ".pkSQLTAB_ADMIN_MENU." 
			WHERE pid IN ('','".implode("','",$open)."')
			ORDER BY sorting ASC");
		while($item = $SQL->fetch_assoc($query))
			{
			$menu_hash[$item['id']] = $item;
			}
		
		pkLoadClass($TREE,'tree');
		$menu_hash = $TREE->set($menu_hash)->build('')->get(); #TREE -> set -> build -> get

		$image_spacer=pkGetHtml('image_arrow_spacer');
		$image_closed=pkGetHtml('image_arrow_right');
		$image_open=pkGetHtml('image_arrow_down');
		
		
		foreach($menu_hash as $id=>$item)
			{
			$is_open = in_array($item['id'],$open);
			
			#language scope required ?
			if(isset($item['lscope']) && !empty($item['lscope']))
				{
				pkLoadLang($item['lscope']);
				}

			#link			
			$link = empty($item['lnkpath']) && empty($item['lnkmode']) && empty($item['lnkadd']) ? '' : pkLink($item['lnkpath'], $item['lnkmode'], $item['lnkadd']);
			
			
			if(empty($link))
				{
				if($is_open)
					{
					$tmp = $open;
					$tmp = array_flip($tmp);
					unset($tmp[$item['id']]);
					$tmp = array_flip($tmp);
					
					$link = pkLink('','navigation','open='.implode('-',$tmp));
					unset($tmp);
					}
				else
					{
					$link = pkLink('','navigation','open='.$item['id'].'-'.implode('-',$open));
					}
				}
			
			if(!empty($item['pid']))
				{
				$link_class	= 'child';
				$link_img	= $image_spacer;
				}
			elseif($is_open)
				{
				$link_class	= 'parent';
				$link_img	= $image_open;
				}
			else
				{
				$link_class	= 'parent';				
				$link_img	= $image_closed;
				}

			$link_value = pkGetLang($item['lkey']);
			
			#target
			$target = strtoupper($item['target']);
			$target = in_array($target, array('MAIN','NAVIGATION')) ? $target : 'MAIN';
			$link_target = constant('pkWINDOWNAME_ADMINFRAME_'.$target);
			
			$site_body_onload='id="pknav"';
			$site_body.='<a id="sitenavigation" class="'.$link_class.'" target="'.$link_target.'" href="'.$link.'"><img border="0" alt="" src="'.$link_img.'"> '.$link_value.'</a>';
			}
		}
	else 
		{
		#check setup.php
		$unlink=false;
		if(!pkDEVMODE && pkFileCheck("../setup.php")) 
			{
			if(isset($_GET['del']) && $_GET['del']=="setup")
				$unlink=@unlink("../setup.php");

			if(!$unlink)
				{
				if(isset($_GET['del']) && $_GET['del']=="setup")
					{
					eval("\$site_body=\"".pkTpl("main_installwarning_error")."\";");
					}
				else
					{
					eval("\$site_body=\"".pkTpl("main_installwarning")."\";");
					}
				}
			}
		else
			{
			$unlink=true;
			}

		if($unlink)
			{		
			$adminsite_template = 'site_frameset';
			$mainpath = 'main';
			
			$mainquerystring = $SESSION->get('admin_main_openedpath');
			$mainquerystring = empty($mainquerystring) ? '' : pkEntities($mainquerystring);

			$name_adminframe_header		= pkWINDOWNAME_ADMINFRAME_HEADER;
			$name_adminframe_main		= pkWINDOWNAME_ADMINFRAME_MAIN;
			$name_adminframe_navigation	= pkWINDOWNAME_ADMINFRAME_NAVIGATION;
			$name_adminframe_hidden		= pkWINDOWNAME_ADMINFRAME_HIDDEN;

			$link_frame_header		= pkLink('','header');
			$link_frame_navigation	= pkLink('','navigation','init=1');
			$link_frame_main		= isset($_GET['goto']) && !empty($_GET['goto']) ? pkLink().pkEntities($_GET['goto']) : pkLink($mainpath,'','','',$mainquerystring);
			$link_frame_hidden		= pkLink('','hidden');
			}
		}	
	}


#gloals for output
$site_meta = pkAdminSiteMeta();


if(pkDEVMODE)
	{
	#color overlay in devmode - make it red like root on linux
	$site_header_script.='<link rel="stylesheet" href="fx/default/css/dev.css" type="text/css">';
	}


pkHeader();

eval("echo \"".pkTpl($adminsite_template)."\";");
?>