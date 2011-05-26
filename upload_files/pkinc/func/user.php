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


if(!defined('pkFRONTEND'))
	die('Direct access to this location is not permitted.');


function pkUserDelete($id)
	{
	$id=intval($id);
	
	if($id<=1)
		return false;
	
	global $SQL;

	$SQL->query("DELETE FROM ".pkSQLTAB_USER." WHERE user_id='".$id."'");
	$SQL->query("DELETE FROM ".pkSQLTAB_USER_PRIVATEMESSAGE." WHERE im_to='".$id."'");
	$SQL->query("DELETE FROM ".pkSQLTAB_USER_FRIENDLIST." WHERE buddy_userid='".$id."' OR buddy_friendid='".$id."'");
	$SQL->query("DELETE FROM ".pkSQLTAB_USER_FIELDS." WHERE userid='".$id."'");
	$SQL->query("DELETE FROM ".pkSQLTAB_COMMENT." WHERE comment_cat='user' AND comment_subid='".$id."'");
	$SQL->query("DELETE FROM ".pkSQLTAB_POLL_COUNT." WHERE vote_rated_userid='".$id."'");
	$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_NOTIFY." WHERE forumnotify_userid='".$id."'");
	$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_FAVORITE." WHERE forumfav_userid='".$id."'");
	
	#stats update
	usercount();
	newestuser();
	bdusertoday();
	}


function pkUserSex($sex)
	{
	switch($sex)
		{
		case 'w' : 
			return pkGetLang('female');
		case 'm' : 
			return pkGetLang('male');
		default : 
			return pkGetLang('not_specified');
		}
	}


function pkUserStatus($status,$sex=NULL)
	{
	if($status=='admin')
		return '<b>'.pkGetLang($sex=='w' ? 'admin_female' : 'admin').'</b>';

	if($status=='mod')
		return '<i>'.pkGetLang($sex=='w' ? 'mod_female' : 'mod').'</i>';

	if($status=='member')
		return pkGetLang($sex=='w' ? 'member_female' : 'member');

	if($status=='user')
		return pkGetLang($sex=='w' ? 'user_female' : 'user');

	return pkGetLang('guest');
	}

	
function pkUserCountryOptionlist($selected='def')
	{
	$countries=array(
		'ger',
		'aut',
		'ch',
		'nl',
		'oth',
		'def'
		);
			
	$optionlist='';
	foreach($countries as $key)
		{
		$optionlist.='<option value="'.$key.'"'.
			($key==$selected ? ' selected="selected"' : '').
			'>'.pkGetLang('origin_'.$key).'</option>';
		}
	
	return $optionlist;	
	}


function pkUserNavigation()
	{
	return pkGetUservalue('id') ? '<a class="heads" href="'.pkLink('userprofile').'">'.pkGetLang('private_area').'</a> &#187;' : NULL;
	}


function pkUserProfilelink($id,$nick,$status=NULL,$cut=false,$class='')
	{
	$nick_cutted=pkEntities(pkStringCut($nick));
	$nick=pkEntities($nick);
	
	if($status=='admin') $nick='<b>'.$nick.'</b>';
	elseif($status=='mod') $nick='<i>'.$nick.'</i>';
	
	return pkHtmlLink(pkLink('userinfo','','id='.$id),$nick_cutted,NULL,NULL,NULL,$nick);
	}


function pkUserProfileIconlink($id,$nick,$status=NULL,$sex=NULL)
	{
#	$nick=pkEntities($nick);
	if($sex=='w')
		{
		$img=pkGetHtml('img_gender_female');
		$alt=pkGetLang('female');
		}
	elseif($sex=='m')
		{
		$img=pkGetHtml('img_gender_male');
		$alt=pkGetLang('male');
		}		
	else
		{
		$img=pkGetHtml('img_gender');		
		$alt='';
		}

	return '<a href="'.pkLink('userinfo','','id='.$id).'"><img border="0" alt="'.$alt.'" src="'.$img.'" /></a>';
	}


function pkUserHomepageIconlink($id,$nick,$hompage)
	{
	return '<a target="_blank" href="'.pkLink('links','','url='.urlencode($hompage)).'" title="'.pkGetSpecialLang('visitit_users_homepage',pkEntities($nick)).'"><img border="0" alt="'.pkGetLang('homepage').'" src="'.pkGetHtml('img_homepage').'" /></a>';
	}
	
function pkUserPmIconlink($id,$nick)
	{
	return '<a href="'.pkLink('privatemessage','','userid='.$id.'&writeim=1').'" title="'.pkGetSpecialLang('send_pm_to',pkEntities($nick)).'"><img border="0" alt="'.pkGetLang('pm').'" src="'.pkGetHtml('img_pm').'" /></a>';
	}

function pkUserEmailIconlink($id,$nick,$email)
	{
	$link=pkGetConfig('member_mailer') ? pkLink('mailer','','userid='.$id) : 'mailto:'.pkEntities($email);
	return '<a href="'.$link.'" title="'.pkGetSpecialLang('send_email_to',pkEntities($nick)).'"><img border="0" alt="'.pkGetLang('email').'" src="'.pkGetHtml('img_email').'" /></a>';
	}

function pkUserEmailLink($id,$nick,$email)
	{
	$link=pkGetConfig('member_mailer') ? pkLink('mailer','','userid='.$id) : 'mailto:'.pkEntities($email);
	return '<a href="'.$link.'">'.pkGetSpecialLang('send_email_to',pkEntities($nick)).'</a>';
	}
	
function pkUserAvatar($id,$file=NULL)
	{
	return pkGetConfig('avatar_eod') && !empty($file) ? pkHtmlImg(pkDIRWWWROOT.pkGetConfig('avatar_path').'/'.$file) : NULL;
	}

function pkUserEncodePassword($password)
	{
	return md5($password);
	} 

function pkUserOnline($id)
	{
	if(!intval($id)>0) 
		return false;
	
	$status=phpkitstatus();
	
	if(!empty($status['online_user'][$id]['expire']) && $status['online_user'][$id]['expire']>0)
		{
		if(pkGetConfig('user_ghost')==1 && $status['online_user'][$id]['user_ghost']==1)
			return false;
		
		return true;
		}

	return false;
	}
	
function pkUserSignature($usersignature,$tpluse=1)
	{
	global $BBCODE;

	if(empty($usersignature) || !pkGetUservalue('sigoption'))
		return '';


	pkLoadClass($BBCODE,'bbcode');
	
	$info_sig=$BBCODE->parse($usersignature,0,pkGetConfig('text_ubb'),pkGetConfig('text_smilies'),pkGetConfig('text_images'),1,pkGetConfig('user_imgresize'),pkGetConfig('user_textwrap'));

	if(!$tpluse)
		return $info_sig;

	eval("\$usersignature=\"".pkTpl("member_signatur")."\";");
	
	return $usersignature;
	}
?>