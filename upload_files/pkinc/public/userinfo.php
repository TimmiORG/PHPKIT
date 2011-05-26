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


if(!getrights($config['member_infoshow']))
	{
	pkEvent('access_refused');
	return;
	}


pkLoadClass($BBCODE,'bbcode');
pkLoadLang('profile');
pkLoadFunc('user');

$id=(isset($_REQUEST['id']) && intval($_REQUEST['id'])>0) ? intval($_REQUEST['id']) : pkGetUservalue('id');

 
$userinfo = $SQL->fetch_assoc($SQL->query("SELECT 
	*
	FROM ".pkSQLTAB_USER." 
	WHERE user_id='".$id."' 
		AND user_activate
	LIMIT 1"));
	
if(!$userinfo['user_id'])
	{
	return pkEvent('account_not_displayable');
	}


$info_nick=$userinfo['user_nick']=pkEntities($userinfo['user_nick']);


if($userinfo['user_bd_day']!=0 && $userinfo['user_bd_month']!=0 && $userinfo['user_bd_year']!=0)
	{
	$info_birthday = $userinfo['user_bd_day'].". ";
	$info_birthday.= pkGetLang('month'.$userinfo['user_bd_month'])." ";
	$info_birthday.= $userinfo['user_bd_year']." - ".$lang['age'].": ".getAge($userinfo['user_bd_day'],$userinfo['user_bd_month'],$userinfo['user_bd_year']);
	}
else
	{
	$info_birthday = $lang['not_specified'];
	}


if($userinfo['user_sex']=="w")
	{
	$info_sex=$lang['female'];
	}
elseif($userinfo['user_sex']=="m")
	{
	$info_sex=$lang['male'];
	}
else
	{
	$info_sex=$lang['not_specified'];
	}

if($userinfo['user_country']!="")
	{
	$info_country=pkGetLang('origin_'.$userinfo['user_country']);
	}
else
	{
	$info_country=$lang['not_specified'];
	}


$info_signin=formattime($userinfo['signin'],'','date');
$info_logtime=$userinfo['logtime'] ? formattime($userinfo['logtime']) : formattime($userinfo['signin'],'','date');

if($userinfo['user_status']=="admin")
	{
	if($userinfo['user_sex']=="w")
		{
		$info_userstatus=$lang['admin_female'];
		}
	else
		{
		$info_userstatus=$lang['admin'];
		}
	}
elseif($userinfo['user_status']=="mod")
	{
	if($userinfo['user_sex']=="w")
		{
		$info_userstatus=$lang['mod_female'];
		}
	else
		{
		$info_userstatus=$lang['mod'];
		}
	}
elseif($userinfo['user_status']=="member")
	{
	if($userinfo['user_sex']=="w")
		{
		$info_userstatus=$lang['member_female'];
		}
	else
		{
		$info_userstatus=$lang['member'];
		}
	}
elseif($userinfo['user_status']=="user")
	{
	if($userinfo['user_sex']=="w")
		{
		$info_userstatus=$lang['user_female'];
		}
	else
		{
		$info_userstatus=$lang['user'];
		}
	}
elseif($userinfo['user_status']=="ban")
	{
	$info_userstatus=$lang['banned'];
	}


#user group
$info_usergroup=pkGetSpecialLang('no_usergroup',$userinfo['user_nick']);

if($userinfo['user_groupid']!=0)
	{
	$group=$SQL->fetch_array($SQL->query("SELECT usergroup_name FROM ".pkSQLTAB_USER_GROUP." WHERE usergroup_id='".$userinfo['user_groupid']."' LIMIT 1"));

	if(trim($group['usergroup_name'])!='')
		{
		$info_usergroup=pkEntities($group['usergroup_name']);
		}
	}

$info_os=(isonline($userinfo['user_id'])) ? $lang['online'] : $lang['offline'];

$userfields=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_USER_FIELDS." WHERE userid='".$userinfo['user_id']."' LIMIT 1"));
$info_extended='';

$getprofilefields=$SQL->query("SELECT * FROM ".pkSQLTAB_USER_PROFILEFIELDS." ORDER by profilefields_order ASC");
while($profilefields=$SQL->fetch_array($getprofilefields))
	{
	$f="field_".$profilefields['profilefields_id'];
	$fieldcontent=pkEntities($userfields[$f]);
	
	if(empty($fieldcontent))
		{
		$fieldcontent=$lang['not_specified'];
		}
	
	$fieldname=pkEntities($profilefields['profilefields_name']);
	
	eval("\$info_extended.= \"".pkTpl("userinfo_ext")."\";");
	}

if($userinfo['user_emailshow']==1)
	{
	$userinfo['user_email']=pkEntities($userinfo['user_email']);
	
	if($config['member_mailer']==1)
		{
		eval("\$info_email=\"".pkTpl("member_email_textlink2")."\";");
		}
	else
		{
		eval("\$info_email=\"".pkTpl("member_email_textlink")."\";");
		}
	}
else
	{
	$info_email=$lang['not_specified'];
	}

if($userinfo['user_imoption']==1)
	{
	eval("\$info_im=\"".pkTpl("member_sendim_textlink")."\";");
	}
else
	{
	$info_im=$lang['receiving_not_wanted'];
	}

if($userinfo['user_icqid']!=0)
	{
	eval("\$info_icq=\"".pkTpl("member_icq_iconlink_2")."\";");
	}
else
	{
	$info_icq=$lang['not_specified'];
	}

if(!empty($userinfo['user_aimid']))
	{
	$userinfo['user_aimid']=pkEntities($userinfo['user_aimid']);
	
	eval("\$info_aim=\"".pkTpl("member_aim_textlink")."\";");
	}
else
	{
	$info_aim=$lang['not_specified'];
	}

if(!empty($userinfo['user_yim']))
	{
	$userinfo['user_yim']=pkEntities($userinfo['user_yim']);
	
	eval("\$info_yim=\"".pkTpl("member_yim_textlink")."\";");
	}
else
	{
	$info_yim=$lang['not_specified'];
	}

if(!empty($userinfo['user_hpage']))
	{
	if(eregi("http://",$userinfo['user_hpage']))
		{
		$info_link=pkEntities($userinfo['user_hpage']);
		}
	else
		{
		$info_link="http://".pkEntities($userinfo['user_hpage']);
		}
	
	eval("\$info_hpage=\"".pkTpl("member_hpage_textlink")."\";");
	}
else
	{
	$info_hpage=$lang['not_specified'];
	}


eval("\$info_buddie=\"".pkTpl("member_buddie_textlink")."\";");



if(!empty($userinfo['user_qou']))
	{
	$info_qoute=$BBCODE->parse($userinfo['user_qou'], 0, $config['text_ubb'], $config['text_smilies'], $config['text_images'],1,pkGetConfig('user_imgresize'),pkGetConfig('user_textwrap'));
	}
else
	{
	$info_qoute=$lang['not_specified'];
	}
	
if(!empty($userinfo['user_hobby']))
	{
	$info_hobby=$BBCODE->parse($userinfo['user_hobby'], 0, $config['text_ubb'], $config['text_smilies'], $config['text_images'],1,pkGetConfig('user_imgresize'),pkGetConfig('user_textwrap'));
	}
else
	{
	$info_hobby=$lang['not_specified'];
	}


$info_sig=empty($userinfo['user_sig']) ? pkGetLang('not_specified') : pkUserSignature($userinfo['user_sig'],0);


if($config['avatar_eod']!=0)
	{
	$userinfo['user_avatar']=basename($userinfo['user_avatar']);
	
	if(!empty($userinfo['user_avatar']) && @filecheck($config['avatar_path']."/".$userinfo['user_avatar']))
		{
		$avatar_dimension[3]=@getimagesize("images/avatar/".$userinfo['user_avatar']);
		eval("\$avatar_show= \"".pkTpl("user_avatar_show")."\";");
		}
	else
		{
		$avatar_show=$lang['no_avatar_selected'];
		}
	
	eval("\$avatar_eod= \"".pkTpl("userinfo_avatar")."\";");
	}
else
	{
	eval("\$avatar_eod= \"".pkTpl("userinfo_avatar_off")."\";");
	}

if($config['forum_eod']==1)
	{
	$info_userposts=($userinfo['user_posts']+$userinfo['user_postdelay']);
	
	if($info_userposts<0)
		{
		$info_userposts=0;
		}
	
	if($info_userposts>0)
		{
		$posts_per_day=number_format(($info_userposts/(ceil((pkTIME-$userinfo['signin'])/86400))),1,",",".");
		
		$threadcount=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_FORUM_THREAD." WHERE forumthread_autorid='".$userinfo['user_id']."'"));
		$info_threadcount=$threadcount[0];
		
		$forumrank=$SQL->fetch_array($SQL->query("SELECT forumrank_title FROM ".pkSQLTAB_FORUM_RANK." WHERE forumrank_post<='".$info_userposts."' ORDER by forumrank_post DESC"));
		$forumrank=$forumrank['forumrank_title'];

		$lastpost=$SQL->fetch_array($SQL->query("SELECT 
				".pkSQLTAB_FORUM_POST.".forumpost_threadid, 
				".pkSQLTAB_FORUM_POST.".forumpost_id
			FROM ".pkSQLTAB_FORUM_POST." 
				LEFT JOIN ".pkSQLTAB_FORUM_THREAD." ON ".pkSQLTAB_FORUM_THREAD.".forumthread_id=".pkSQLTAB_FORUM_POST.".forumpost_threadid 
				LEFT JOIN ".pkSQLTAB_FORUM_CATEGORY." ON ".pkSQLTAB_FORUM_CATEGORY.".forumcat_id=".pkSQLTAB_FORUM_THREAD.".forumthread_catid
			WHERE (".sqlrights(pkSQLTAB_FORUM_CATEGORY.".forumcat_rrights")." OR 
				".pkSQLTAB_FORUM_CATEGORY.".forumcat_mods LIKE '%-".pkGetUservalue('id')."-%' OR 
				".pkSQLTAB_FORUM_CATEGORY.".forumcat_user LIKE '%-".pkGetUservalue('id')."-%') AND 
				".pkSQLTAB_FORUM_POST.".forumpost_autorid=".$userinfo['user_id']." 
			ORDER BY ".pkSQLTAB_FORUM_POST.".forumpost_time DESC 
			LIMIT 1"));
		
		
		if(!empty($lastpost[0]))
			{
			$forumthread=$SQL->fetch_array($SQL->query("SELECT forumthread_id, forumthread_title, forumthread_catid FROM ".pkSQLTAB_FORUM_THREAD." WHERE forumthread_id='".$lastpost['forumpost_threadid']."'"));
			$forumcat=$SQL->fetch_array($SQL->query("SELECT forumcat_id, forumcat_name FROM ".pkSQLTAB_FORUM_CATEGORY." WHERE forumcat_id='".$forumthread['forumthread_catid']."'"));
			
			$forumthread['forumthread_title']=pkEntities($forumthread['forumthread_title']);
			$forumcat['forumcat_name']=pkEntities($forumcat['forumcat_name']);
			
			$link_thread=pkLink('forumsthread','','threadid='.$forumthread['forumthread_id'].'&postid='.$lastpost['forumpost_id']);
			$link_category=pkLink('forumscategory','','catid='.$forumcat['forumcat_id']);

			eval("\$forumpost_info= \"".pkTpl("userinfo_foruminfo_lastthread")."\";");
			}
		else
			{
			$forumpost_info='-';
			}
		}
	else
		{
		$forumrank='-';
		$forumpost_info='-';
		$info_threadcount='-';
		}
	
	eval("\$foruminfo= \"".pkTpl("userinfo_foruminfo")."\";");
	}
else
	{
	unset($foruminfo);
	}

if($config['member_gbook']==1 && getrights("user")=="true")
	{
	eval("\$info_user_gbook= \"".pkTpl("userinfo_gbook_linkbox")."\";");
	}

$user_navigation=pkUserNavigation();

eval("\$site_body.= \"".pkTpl("userinfo")."\";");
?>