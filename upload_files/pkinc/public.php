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


#check for banned IPs
if(!ipcheck($ENV->getvar('REMOTE_ADDR')))
	{
	pkLoadFunc('except');
	pkSiteException(3);
	}


$SQL->sqlerrorreport(pkDEVMODE);

$SESSION = new pkSessionpublic;	
$SESSION->main();

#load & init PHPKIT CMS object
require_once(pkDIRCLASS.'pkcms'.pkEXT);

$CMS = pkCMS::_instance();
$CMS->site_title_affix(pkGetConfig('site_title_prefix'),pkGetConfig('site_title'),pkGetConfig('site_title_suffix'));#site title presets


pkLoadHtml();
pkLoadLang('public');


$contentid = 0;
$adview = $site = $site_width = $site_header_script = $site_body = $site_body_onload =
$navigation_top = $navigation_left = $navigation_right = $navigation_bottom = $navigation_sub_top = $navigation_sub_bottom =
$site_refresh = $path = $file = $src = '';

$pkDISPLAYPOPUP = false;
$pkDISPLAYPRINT = false;


if(($config['site_eod']!=1 || ($config['forum_eod']!=1 && $config['forum_standalone']==1)) && pkGetUservalue('status')!='admin') 
	{
	pkLoadFunc('except');
	pkSiteException(($config['forum_eod']!=1 && $config['forum_standalone']==1) ? 1:2);
	}


#fetch style informations
$sqlwhere = $sqlorder='';

if(pkGetConfig('user_design') && $ENV->_isset_get('setstyleid'))
	{
	pkSetUservalue('design',$ENV->_get_id('setstyleid'));
	}

if(pkGetConfig('user_design') && pkGetUservalue('design') && pkGetUservalue('design')!=pkGetConfig('site_style'))
	{
	$sqlwhere = "(style_id='".pkGetUservalue('design')." AND style_user=1') OR ";
	$sqlorder = " ORDER BY style_id ".(pkGetUservalue('design')<pkGetConfig('site_style') ? 'ASC' : 'DESC');
	}

#load the style
list($styleid,$tplpackid) = $SQL->fetch_row($SQL->query("SELECT
		style_id,
		style_template
	FROM ".pkSQLTAB_STYLE."
	WHERE ".$sqlwhere."
		style_id='".pkGetConfig('site_style')."' ".
	$sqlorder." 
	LIMIT 1"));

#load the required templateset
if($tplpackid!= -1)
	{
	$query = $SQL->query("SELECT
		template_name,
		template_value
	FROM ".pkSQLTAB_TEMPLATE."
	WHERE template_packid='".intval($tplpackid)."'");
	while(list($name,$value)=$SQL->fetch_row($query))
		{
		$pkTPLHASH[$name] = str_replace("\"","\\\"",$value);
		}
	}
	
#set the style ID
$CMS->site_styleid_set($styleid);	

unset($sqlwhere,$sqlorder,$name,$value,$tplpackid,$styleid);
#END style informations


if(isset($_REQUEST['event']) && !isset($event))
	{
	$event = $_REQUEST['event'];
	}


if($event)
	{
	pkEvent($event);
	}
else
	{
	$path = $ENV->_isset_get('path') ? $ENV->_get('path') : '';
	$path = empty($path) ? 'start' : trim($path);

	switch($path)
		{
		case 'index' :
		case 'index.php' :
			$path='start';
			break;
			
		case 'content/articles.php' :
			$path='article';
			break;
			
		case 'content/links.php' :
			$path='link';
			break;
			
		case 'guestbook/viewgb.php' :
			$path='guestbook';
			break;
			
		case 'content/overview.php' :
			$path='contentarchive';
			break;
			
		case 'forumthread' :
		case 'forum/showthread.php' :
			$path='forumsthread';
			break;
			
		case 'forum/index.php' :
		case 'forum/main.php' :
			$path='forumsdisplay';
			break;
			
		default :
			$path = basename($path);
			$path = substr($path,-4)==pkEXT ? substr($path,0,-4) : $path;
			break;
		}


	if(preg_match("/^([a-z0-9.])/i",$path) && pkFileCheck(pkDIRPUBLIC.$path.pkEXT))
		{
		ob_start();

		include(pkDIRPUBLIC.$path.pkEXT);

		$site_body.=ob_get_contents();
		ob_end_clean();
		}
	else
		{
		pkEvent('page_not_found');
		}
	}


#head/meta
$site_design	= pkSiteStyle();
$site_metatags	= pkSiteMetatags();
$site_title		= $CMS->site_title_get();	#meta title tag

$site_name		= pkGetConfigF('site_name'); #header
$site_slogan	= pkGetConfigF('site_slogan'); #header

#branding
$site_link_home	= pkGetConfigF('site_frontpage_link');
$site_link_home	= empty($site_link_home) ? pkLink('start') : $site_link_home;

#branding
$link_brandinglogo_overlay = pkGetHtml('img_blank');
$logo_imgalt = pkGetSpecialLang('sitelogo',pkGetConfigF('site_name'));

#copyright & optionals
$site_copyright = pkGetConfig('site_copy');
$site_closure 	= pkGetConfig('site_closure');
$site_date = pkTimeFormat(pkTIME,'datelong');

#upate pagecounter
pkPublicCalendarUpdate();


if($pkDISPLAYPRINT)
	{
	eval("echo \"".pkTpl("blank")."\";");
	exit;
	}

if($pkDISPLAYPOPUP)
	{
	eval("echo \"".pkTpl("popup")."\";");
	exit;
	}

if(pkGetConfig('site_adview'))
	{
	pkLoadClass($admanage,'admanage');
	$adview = $admanage->get();
	}
	

#creates the navigation
include(pkDIRPUBLICINC.'navigation'.pkEXT);


#footer
$lang_parsertime_stopped = pkGetSpecialLang('parsertime_stopped',pkParserTime());
$site_adminlogin = adminaccess('adminarea') ? pkHtmlLink(pkLinkAdmin(),pkGetLang('administration')) : '';


#breadcrumbnavigation
#spacer for further extension
$navigation_breadcrumb = '';	#not yet in use (pk 1.6.4)

#empty navcols / adview / etc.
$hide = ' style="display:none;"';
$displaycontent_top				= empty($navigation_top) ? $hide : '';
$displaycontent_sub_top			= empty($navigation_sub_top) ? $hide : '';

$displaycontent_left			= empty($navigation_left) ? $hide : '';
$displaycontent_right			= empty($navigation_right) ? $hide : '';

$displaycontent_bottom			= empty($navigation_bottom) ? $hide : '';
$displaycontent_sub_bottom		= empty($navigation_sub_bottom) ? $hide : '';

$displaycontent_breadcrumb		= empty($navigation_breadcrumb) ? $hide : '';
$displaycontent_site_copyright	= empty($site_copyright) ? $hide : '';
$displayadview					= empty($adview) ? $hide : '';


eval("\$site=\"".pkTpl("site")."\";");

#sent header
pkHeader();

echo $site;

pkPublicRefererLog();
?>