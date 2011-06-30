<?php
# PHPKIT WCMS | Web Content Management System
#
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATION
#
# SIE SIND NICHT BERECHTIGT, UNRECHTM�SSIGE KOPIEN DIESER
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


#Compares PHP version against our required PHP version
if(!version_compare(PHP_VERSION,'5.2.0','>='))
	{
	die('PHPKIT WCMS requires PHP 5.2.0 or higher to run. You are currently running PHP '.PHP_VERSION .'. For further information check <a href="http://www.phpkit.com">phpkit.com</a>.');
	}

if(!defined('pkFRONTEND'))
	{
	die('Direct access to this location is not permitted.');
	}

if(!in_array(pkFRONTEND,array('admin','public','setup','captcha','style','rsimg')))
	{
	die('Direct access to this location is not permitted.');
	}

#set errror_reporting
@error_reporting(0);


#DOS blocker
$blockeragents	= array('libwww-perl');
$blockerqueries	= array('=http://','=ftp://','=https://','=ftps://','/errors.php');
$agent			= strtolower(getenv('HTTP_USER_AGENT'));
$query			= str_replace(' ','',strtolower(rawurldecode(getenv('QUERY_STRING'))));

if(str_replace($blockeragents,'#',$agent)!=$agent || str_replace($blockerqueries,'#',$query)!=$query)
	{
	header('HTTP/1.1 403 Forbidden');
	die('Request to this website terminated!');
	}
	
unset($blockers,$agent,$query,$checkagent,$checkquery);
#END DOS blocker


#set some constants
define('pkMICROTIME',		microtime());
define('pkTIME',			substr(pkMICROTIME,11,10));
define('pkTIMETODAY',		mktime(0,0,0,date('m',pkTIME),date('d',pkTIME),date('Y',pkTIME)));

define('pkCAPTCHAVARNAME',	'pkCaptcha');
define('pkCAPTCHAVERIFIED',	'pkCaptchaVerified');
define('pkCHMODDIR',		0755);
define('pkCHMODDIR_WRITE',	0777);
define('pkCHMODFILE',		0644);
define('pkDEVMODE',			true);
define('pkEXT',				'.php'); #standard php file extension
define('pkEXTTPL',			'.htm');
define('pkPHPKITSID',		@ini_get('session.auto_start') ? @ini_get('session.name') : 'PHPKITSID');
define('pkPHPVERSION',		str_replace('.','',phpversion()));

#directory and path separator
define('pkDS', DIRECTORY_SEPARATOR);
define('pkPS', PATH_SEPARATOR);


#define serval needed directories-paths
define('pkDIRINC',			pkDIRROOT.'pkinc'.pkDS);	#base source directory

define('pkDIRADMIN',		pkDIRINC.'admin'.pkDS);		#source directory - admin scripts
define('pkDIRADMINTPL',		pkDIRINC.'admintpl'.pkDS);	#admin tpl directory 
define('pkDIRCFG',			pkDIRINC.'cfg'.pkDS);		#source directory - configuration-/raw-datafiles
define('pkDIRCLASS',		pkDIRINC.'class'.pkDS);		#source directory - classes
define('pkDIRETC',			pkDIRINC.'etc'.pkDS);		#passwords
define('pkDIRFUNC',			pkDIRINC.'func'.pkDS);		#source directory - functions
define('pkDIRHTML',			pkDIRINC.'html'.pkDS);		#source directory - html bits
define('pkDIRLANG',			pkDIRINC.'lang'.pkDS);		#source directory - language packs
define('pkDIRPUBLIC',		pkDIRINC.'public'.pkDS);	#source directory - public scripts
define('pkDIRPUBLICINC',	pkDIRINC.'publicinc'.pkDS);	#source directory - public include files
define('pkDIRPUBLICTPL',	pkDIRINC.'publictpl'.pkDS);	#public tpl directory
define('pkDIRREP',			pkDIRINC.'rep'.pkDS);		#repository
define('pkDIRTEMP',			pkDIRINC.'temp'.pkDS);		#temp dir

#gdfx subdirectories
define('pkDIRGDFX',			pkDIRREP.'gdfx'.pkDS);		#source directory - fx raw file - used by gdlib
define('pkDIRGDFONTS',		pkDIRGDFX.'fonts'.pkDS);	#source directory - fx raw file - used by gdlib
define('pkDIRGDCAPTCHA',	pkDIRGDFX.'captcha'.pkDS);	#source directory - fx raw file - used by gdlib

#web paths
define('pkDIRWWWROOT',		pkFRONTEND=='admin' ? './../' : './');	#web-root used in links
define('pkDIRWWWADMIN',		pkDIRWWWROOT.'pk/');		#web dir admin directory
define('pkWWWSELF',			pkREQUESTEDFILE);			

#files
define('pkFILESQL',			pkDIRETC.'sql.php');		#dbaccess


#check register_globals and unset everything if nessesary
if(@ini_get('register_globals'))
	{
	#_REQUEST, _GET, _POST, _COOKE
	foreach($_REQUEST as $var=>$dump)
		{
		unset($$var);
		}
	}

if(!@ini_get('session.use_only_cookies'))
	{
	if(@ini_set('session.use_only_cookies','1'))
		{
		if(@ini_get('session.use_trans_sid'))
			{
			@ini_set('session.use_trans_sid','0');		#PHP 5 or higher
			#@ini_set('url_rewriter.tags', '');			#before PHP 5
			}
		}
	}

#classes
$ENV=$SESSION=$BBCODE=$FORUM=$SQL=NULL;

#define global variables
$lang = array();
$LANG = &$lang;

$pkDOCMETA	= array();
$pkHTMLBITS	= array();
$pkTPLHASH	= array();
$pkCFGHASH	= array();

$pkFORUMNEWTHREADS	= array();
$pkFORUMREADEDTHREADS	= array();
$pkNAVIGATIONHIDE['left']	= 0;
$pkNAVIGATIONHIDE['right']	= 0;

$ADMINACCESS = $fx = $event = NULL;
$phpkit_status	= array();

$forumcat_info_array	= array();
$forumrank_info_array	= array();
$contentcat_info_array	= array();
$smilie_cache			= array();
$global_mods			= '';
$mod_cache				= '';


require_once(pkDIRINC.'version'.pkEXT);
require_once(pkDIRFUNC.'default'.pkEXT);

pkLoadClass($ENV,'env');

#load the sql configuration file from pkinc/etc/
if(pkDEVMODE)
	{
	include_once(pkFILESQL);
	}
else
	{
	@include_once(pkFILESQL);
	}


if(pkFRONTEND=='captcha' || pkFRONTEND=='rsimg')
	{
	return require_once(pkDIRINC.'media'.pkEXT);
	}


require_once(pkDIRFUNC.(pkFRONTEND=='public' || pkFRONTEND=='style' ? 'public' : 'admin').pkEXT);


if((!defined('pkPHPKIT_INSTALLED') || !pkPHPKIT_INSTALLED) && pkFRONTEND!='setup')
	{
	header('Location: '.pkDIRWWWROOT.'setup'.pkEXT);
	exit;
	}


pkLoadClass($SQL,'sql');
#$DB = $SQL; #you may re-enable this to support not updated scripts


switch(pkFRONTEND)
	{
	case 'setup' :
		return;
	
	case 'style' :
		require_once(pkDIRINC.'media'.pkEXT);
		return;
	}

if(!$SQL->connect()) 
	{
	pkLoadFunc('except');
	pkSiteException();
	}


#load config
$config = array();
$query = $SQL->query("SELECT id,value FROM ".pkSQLTAB_CONFIG);
while(list($key,$value) = $SQL->fetch_row($query))
	{
	$config[$key] = @unserialize($value);
	}

if(empty($config))
	{
	pkLoadFunc('except');
	pkSiteException();
	}

#force update
if(pkGetConfig('version_number') != pkPHPKIT_VERSION)
	{
	header('Location: '.pkDIRWWWROOT.'setup'.pkEXT);
	exit;
	}


$config['cookie_path']				= empty($config['cookie_path']) ? '/' : $config['cookie_path'];
$config['comment_imageresize']		= 
$config['guestbook_imageresize']	= 
$config['forum_imageresize'] 		= $config['text_imgresize'];

$config['comment_textwrap']			=
$config['guestbook_textwrap']		=
$config['forum_textwrap']			= $config['user_textwrap'];

#some more configuration values
$config['time_zone']				= 'Europe/London';	#set the time zone fix to GMT 0 to rely on - GMT +0
$config['cookie_secure']			= 0;

$config['template_dir']				= pkDIRINC.pkFRONTEND.'tpl/';
$config['image_archive']			= 'content/images';
$config['smilie_dir']				= 'images/smilies';
$config['move_logout']				= 'path=start';	#redirect on logout
$config['move_login']				= 'path=start';	#redirect on login

$config['session_expire_user']		= 1800;	#30min
$config['session_expire_admin']		= 90;	#90sec its enough to keep the session alive cause the forced refresh in the adminarea
$config['session_expire_guest']		= 600;	#10min

$config['search_min_length']		= 3;	#min search string length
$config['search_max']				= 500;	#max results displayed

$config['time_refresh']				= 10;
$config['forum_threadtitle_cut']	= 25;
$config['forum_threadautor_cut']	= 10;
$config['username_cut']				= 18;
$config['sidelinkfull_pages']		= 3;

$config['nb_community_box']			= 2;	#nb community 1=classic 2=login-form
$config['nb_newthreads_scut']		= 0;	#stringcut
$config['nb_newthreads_break']		= 5;	#limit
$config['nb_curthreads_scut']		= 0;	#stringcut
$config['nb_curthreads_break']		= 5;	#limit
$config['nb_randarticle_cur']		= 150;	#max chars
$config['nb_newarticle_cur']		= 150;	#maxchars


#use 
date_default_timezone_set(pkGetConfig('time_zone'));


pkLoadLang();																																																																																																												$config['meta_generator']=strrev('9002-2002 thgirypoc RbG etybxm - metsyS tnemganaM tnetnoC beW - SMCW TIKPHP');

require_once(pkDIRCLASS.'session'.pkEXT);
require_once(pkDIRCLASS.'session'.pkFRONTEND.pkEXT);
require_once(pkDIRINC.pkFRONTEND.pkEXT);
?>