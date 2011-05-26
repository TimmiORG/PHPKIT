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
# Diese Datei / die PHPKIT Software ist keine Freeware! F�r weitere
# Informationen besuchen Sie bitte unsere Website oder kontaktieren uns per E-Mail:
#
# email     : info@phpkit.com
# website   : http://www.phpkit.com
# licence   : http://www.phpkit.com/licence
# copyright : Copyright (c) 2002-2009 mxbyte gbr | http://www.mxbyte.com


if(!defined('pkFRONTEND') || (pkFRONTEND!='public' && pkFRONTEND!='style'))
	die('Direct access to this location is not permitted.');


#function pkTpl ( string tplname [, string tplextension ] )
#return string
function pkTpl($tpl)
	{
	global $pkTPLHASH;


	if(!isset($pkTPLHASH[$tpl]))
		{
		$comment_start	= '';
		$comment_end	= '';
	
		$path = pkDIRPUBLICTPL.$tpl.pkEXTTPL;

		#comments in the source
		if(pkGetConfig('templatename'))
			{
			$name = pkEntities($tpl);
			
			$comment_start	= "\r\n<!-- TPL START ".$name." -->\r\n";
			$comment_end	= "\r\n<!-- TPL END ".$name." -->\r\n";
			}

		if(pkDEVMODE && !pkFileCheck($path))
			{
			$pkTPLHASH[$tpl] = NULL;
			echo '<p><b>WARNING:</b> '.pkFRONTEND.' template file "<b>'.$tpl.'</b>" not found</p>';
			}
		else
			{
			$pkTPLHASH[$tpl] = $comment_start.str_replace("\"","\\\"",@file_get_contents($path)).$comment_end;
			}
		}

	return $pkTPLHASH[$tpl];
	}
#END function pkTpl


function pkPublicCalendarUpdate()
	{
	global $ENV, $SQL, $SESSION;
	
	if($SESSION->isbot())
		return false;
	
	$S=&$SQL;

	$query='';
	$ip=$S->f(getenv('REMOTE_ADDR'));
	$key='calendar';


	if($ENV->_cookie($key)==pkTIMETODAY)
		{
		return $S->query("UPDATE ".pkSQLTAB_CALENDAR." SET calender_picount=calender_picount+1 WHERE calender_date=".pkTIMETODAY);
		}

	$ENV->setCookie($key,pkTIMETODAY);


	$calendarid = $S->fetch_row($S->query("SELECT calender_id FROM ".pkSQLTAB_CALENDAR." WHERE calender_date='".pkTIMETODAY."' LIMIT 1"));

	if(!$calendarid) 
		{
		$SQL->query("DELETE FROM ".pkSQLTAB_RECORD_IP);
		$SQL->query("INSERT ".pkSQLTAB_CALENDAR." SET 
			calender_counter=1,
			calender_picount=1,
			calender_date=".pkTIMETODAY.",
			calender_versionnr='".pkGetConfig('version_number')."'");
		
		$SQL->query("INSERT ".pkSQLTAB_RECORD_IP." SET recordip='".$ip."'");
		return;
		}
	
	list($ipcount)=$S->fetch_row($S->query("SELECT COUNT(recordip) 
		FROM ".pkSQLTAB_RECORD_IP." 
		WHERE recordip='".$ip."' 
		LIMIT 1"));

	if($ipcount==0) 
		{
		$S->query("INSERT ".pkSQLTAB_RECORD_IP." SET recordip='".$ip."'");
		$query=",calender_counter=calender_counter+1";
		}

	$S->query("UPDATE ".pkSQLTAB_CALENDAR." SET 
		calender_picount=calender_picount+1 ".
		$query."
		WHERE calender_date=".pkTIMETODAY);

	bdusertoday();
	return;
	}


# void pkPublicRefererLog( void )
function pkPublicRefererLog() 
	{
	global $SQL,$ENV, $SESSION;
	
	if($SESSION->isbot())
		return false;
	
	$S=&$SQL;

	$http_referer=$ENV->getvar('HTTP_REFERER');

	if(!pkGetConfig('referer_eod') || empty($http_referer))
		return false;
	
	$referer=strtolower($http_referer);
	$filter=explode("\n",pkGetConfig('referer_filter'));
	$filter[]=pkGetConfig('site_url');

	foreach($filter as $r)
		{
		if(empty($r))
			continue;
		
		if(strstr($referer,strtolower(trim($r))))
			return false;
		}
	
	$S->query("INSERT ".pkSQLTAB_RECORD." SET
		record_referer='".$S->f($http_referer)."',
		record_time='".pkTIME."'");

	if(!pkGetConfig('referer_delete'))
		return;
	
	$record_expire=pkTIME-(intval(pkGetConfig('referer_delete'))*86400);
	$S->query("DELETE FROM ".pkSQLTAB_RECORD." WHERE record_time<'".$record_expire."'");		
	}


function pkEvent($key='',$redirect=true,$link='')
	{
	global $ENV,$site_body;	
	
	
	if(empty($key))
		return;

	pkLoadLang('event');
	pkLoadHtml('default');
	$special='';

	$link=empty($link) ? pkLink() : $link;
	$title=pkGetLang('eventtitle_'.$key);
	$msg=pkGetLang('event_'.$key);
	
	switch($key) 
		{
		case 'access_refused' :
			if(pkGetUservalue('id')>0) 
				{
				$link_logoff=pkLink('','','logout=1');
				$lang_logged_in_as=pkGetSpecialLang('logged_in_as',pkGetUservaluef('nick'));
				$lang_logoff=pkGetLang('logout');
				
				eval("\$special=\"".pkTpl("logout_small")."\";");
				}
			else 
				{
				$form_action=pkLink('','','login=1');
				$redirect_value=pkEntities(getenv('REQUEST_URI'));				
				$link_register=pkLink('register');
				$link_lostpassword=pkLink('lostpassword');			
				$L_login=pkGetLang('login');
				$L_login_username=pkGetLang('username');
				$L_login_password=pkGetLang('password');
				$L_register=pkGetLang('register');
				$L_lostpassword=pkGetLang('lost_password');
							
				eval("\$special=\"".pkTpl("login_small")."\";");
				}
			break;
		case 'logout' :
			if(pkGetUservalue('id')>0) 
				{
				$ENV->debugcookie();
				
				$link_logoff=pkLink('','','logout=1');

				$title=pkGetLang('eventtitle_logout_false');				
				$msg=pkGetLang('event_logout_false');
				}
			else
				{
				$link=pkLink('','',pkLinkUnEntities(urldecode($ENV->_request('moveto'))));
			
				pkDocmeta(pkGetSpecialHtml('__docmeta_refresh__',pkGetConfig('time_refresh'),$link));
			
				$msg=pkGetLang('event_'.$key);
				$msg.=pkGetSpecialLang('event_moving_link',$link);
				}
			break;
		case 'login' :
			$url='';
			
			if(pkGetUservalue('id'))
				{
				$moveto=urldecode($ENV->_get('moveto'));
				$moveto=strpos($moveto,'path=login')===false && strpos($moveto,'logout')===false ? $moveto : pkGetConfig('move_login');
								
				$url=pkLink('','',pkLinkUnEntities($moveto));
				pkDocmeta(pkGetSpecialHtml('__docmeta_refresh__',pkGetConfig('time_refresh'),$url));

				$msg=pkGetSpecialLang('event_'.$key,$url);
				break;
				}

			$ENV->debugcookie();
		
			$title=pkGetLang('eventtitle_login_false');
			$msg=pkGetLang('event_login_false');
			break;
		case 'guestbook' :
			$url=pkLink('guestbook');
			pkDocmeta(pkGetSpecialHtml('__docmeta_refresh__',pkGetConfig('time_refresh'),$url));
			
			$msg=pkGetSpecialLang('event_'.$key,$url);
			break;				
		case 'constribution_thank' :
			$url=$ENV->_get('moveto');
			$url=$url ? pkLinkUnEntities(urldecode($url)) : '';
			$link=pkLink('','',$url);
			pkDocmeta(pkGetSpecialHtml('__docmeta_refresh__',pkGetConfig('time_refresh'),$link));
			$msg=pkGetSpecialLang('event_'.$key,$link);
			break;
		case 'webmaster_message_sent' :
			pkDocmeta(pkGetSpecialHtml('__docmeta_refresh__',pkGetConfig('time_refresh'),pkLink()));		
			$msg=pkGetSpecialLang('event_'.$key,pkLink());
			break;
		case 'profileupdate' :
			$link=pkLink('userprofile');
			pkDocmeta(pkGetSpecialHtml('__docmeta_refresh__',pkGetConfig('time_refresh'),$link));
			$msg.=pkGetSpecialLang('event_moving_link',$link);
			break;
		case 'searchresult_limited' :
			$url=$ENV->_get('moveto');
			$url=$url ? pkLinkUnEntities(urldecode($url)) : '';
			$link=pkLink('','',$url);
			
			pkDocmeta(pkGetSpecialHtml('__docmeta_refresh__',pkGetConfig('time_refresh'),$link));
			$msg.=pkGetSpecialLang('event_moving_link',$link);
			break;
		case 'privatemessage_not_found' :
			$link=pkLink('privatemessages');
			$msg.=pkGetSpecialLang('event_moving_link',$link);
			pkDocmeta(pkGetSpecialHtml('__docmeta_refresh__',pkGetConfig('time_refresh'),$link));
			break;		
		case 'firstlogin' :
			$link=pkLink('userprofile','edit');
			pkDocmeta(pkGetSpecialHtml('__docmeta_refresh__',pkGetConfig('time_refresh'),$link));
			$msg.=pkGetSpecialLang('event_moving_link',$link);
			break;
		case 'thread_does_not_exists' :
			$link=pkLink('forumsdisplay');
			$msg.=pkGetSpecialLang('event_moving_link',$link);
			break;
		case 'password_changed' :
			$link=pkLink('login');
			$msg.=pkGetSpecialLang('event_moving_link',$link);
			break;			
		default :
			if(empty($title) || $title=='L_eventtitle_'.intval($key))
				{
				$title=pkGetLang('eventtitle_unknown_error');
				$msg=pkGetLang('event_unknown_error');
				}
			
			if($redirect)
				{
				pkDocmeta(pkGetSpecialHtml('__docmeta_refresh__',pkGetConfig('time_refresh'),$link));
				$msg.=pkGetSpecialLang('event_moving_link',$link);
				}
			break;
		}
		
		
	if($key=='page_not_found')
		{
		@header("HTTP/1.1 404 Not Found");
		}

	pkCMS::_instance()
		->site_title_set($title,true);

	eval("\$site_body.=\"".pkTpl("event")."\";");
	}


function pkSiteStyle()
	{
	$styleid = pkCMS::_instance()
		->site_styleid_get();	
	
	return '<link rel="stylesheet" href="'.pkLink('','','fx=style&id='.$styleid).'" type="text/css" />';
	}


function pkSiteMetatags()
	{
	$out='';
	$hash = array('siteurl','author','author_email','publisher','copyright','keywords','description','robots','robots_revisit','favicon');																																																																																										$hash[]='generator';

	foreach($hash as $key)
		{
		$cfg = pkGetConfigF($key=='siteurl' ? 'site_url' : 'meta_'.$key);
		
		if(empty($cfg))
			{
			continue;
			}
		
		$key = ($key=='author_email') ? 'author email' : $key;
		$key = ($key=='robots_revisit') ? 'robots-revisit' : $key;
			
		$out.= (($key!='favicon') ? '<meta name="'.$key.'" content="'.$cfg.'" />' : '<link rel="shortcut icon" href="'.$cfg.'" type="image/x-icon" />')."\r\n";
		}

	$out.= '<meta name="date" content="'.pkTimeFormat(pkTIME,'RFC822').'" />'."\r\n";
	$out.= pkGetDocmeta();
	
	
	#add custom meta tags
	$custom = pkGetConfig('meta_custom'); #no encoding here;
	$out.= trim($custom)."\r\n";
		
	
	#add rss feeds
	pkLoadLang('rss');
	
	$array = pkCfgData('rss-types');


	foreach($array as $rss)	
		{
		if(!pkGetConfig('rss_enable_'.$rss))
			{
			continue;
			}
		
		
		$link = pkLinkFull('rss',$rss);

		$title = pkGetConfigF('rss_title_'.$rss);
		$title = empty($title) ? pkGetLang('rss_title_'.$rss) : $title;
		$title = pkCMS::_instance()->site_title_temp_get($title,true);
		
		$out.= '<link rel="alternate" href="'.$link.'" title="'.$title.'" type="application/rss+xml" />'."\r\n";
		}
	
		
	return $out;
	}


function pkCaptchaCodeValid($code)
	{
	global $SESSION;
	
	$session	= $SESSION->get(pkCAPTCHAVARNAME);
	$verified	= $SESSION->get(pkCAPTCHAVERIFIED);	
	$SESSION->deset(pkCAPTCHAVARNAME);

	if(!pkGetConfig('captcha') || $verified)
		{
		return true;
		}

	$bool = (!empty($session) && strtolower($session) === strtolower($code));

	$SESSION->set(pkCAPTCHAVERIFIED,$bool);
	
	
	return $bool;
	}


#@Function:	pkCaptchaField
#@Param:	string fieldname
function pkCaptchaField($fieldname='',$colspan=0,$colspan2=0) #2nd colpsan param temporary
	{
	global $SESSION,$site_header_script;
	
	$verified = $SESSION->get(pkCAPTCHAVERIFIED);
	
	if(!pkGetConfig('captcha') || $verified)
		{
		return NULL;
		}

	
	pkLoadLang('publicform');

	$site_header_script.= '<script type="text/javascript" src="fx/form.js"></script>';
	
	$colspan = $colspan>1 ? ' colspan="'.$colspan.'"' : '';
	$colspan2 = $colspan2>1 ? ' colspan="'.$colspan2.'"' : '';
	
	$lang_captcha_code = pkGetLang('captcha_code');
	$lang_captcha_image = pkGetLang('captcha_image');
	$lang_captcha_description = pkGetLang('captcha_description');
	
	$src_captcha = pkLink('','','fx=captcha');
	$name = empty($fieldname) ? pkCAPTCHAVARNAME : $fieldname;
	$nameImage = $name.'Image';

	$captcha_field = '';
	eval("\$captcha_field=\"".pkTpl("captcha_formrow")."\";");
	
	return $captcha_field;
	}
#@END Function: pkCaptchaField
?>