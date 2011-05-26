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
# licence   : http://www.phpkit.com/licence/phpkit
# copyright : Copyright (c) 2002-2009 mxbyte gbr | http://www.mxbyte.com


if(!defined('pkFRONTEND') || pkFRONTEND!='admin')
	die('Direct access to this location is not permitted.');


if(!adminaccess('user'))
	return pkEvent('access_forbidden');


$epp=20;
$modehash   = array('canceled','activate','unused');
$statushash = array('admin','mod','member','user','ban');

$mode    = (isset($_REQUEST['mode']) && in_array($_REQUEST['mode'],$modehash)) ? $_REQUEST['mode'] : '';
$entries = (isset($_REQUEST['entries']) && intval($_REQUEST['entries'])>0) ? intval($_REQUEST['entries']) : 0;
$svalue  = (isset($_REQUEST['svalue'])) ? trim($_REQUEST['svalue']) : '';
$ACTION  = (isset($_POST['action']) && !empty($_POST['action'])) ? $_POST['action'] : 'view';

$totalmax   = $entries+$epp;
$member_row = '';


#user marked to delete
if($mode=='canceled')
	{
	if($ACTION==$_POST['save'] && ($_POST['todelete_option']==1 || $_POST['todelete_option']==2) && is_array($_POST['user_delete']))
		{
		$useridhash=array();
		
				
		foreach($_POST['user_delete'] as $id)
			{
			$id=intval($id);
			
			if($id<=1)
				continue;
			
			$useridhash[]=$id;
			}

		if(intval($_POST['todelete_option'])==1)
			$SQL->query("UPDATE ".pkSQLTAB_USER." SET user_activate='1' WHERE user_id IN(0,".implode(',',$useridhash).") AND user_activate=2");
		else
			{
			pkLoadFunc('user');
			
			foreach($useridhash as $id)
				pkUserDelete($id);
			}

		usercount();
		newestuser();
		bdusertoday();		
			
		pkHeaderLocation('userslist','canceled');
		}


	$gettodelete=$SQL->query("SELECT 
			user_id,
			user_name,
			user_email,
			signin 
		FROM ".pkSQLTAB_USER."
		WHERE user_activate=2
		ORDER by user_nick ASC");
	while($userinfo=$SQL->fetch_array($gettodelete))
		{
		$row=rowcolor($row);
		
		$userinfo['user_name']=pkEntities($userinfo['user_name']);
		$info_signin=formattime($userinfo['signin']);
		
		eval("\$delete_row.= \"".pkTpl("delete_row")."\";");
		}
		
	if($delete_row!="")
		eval("\$delete_row.= \"".pkTpl("delete_option1")."\";");
	else
		eval("\$delete_row.= \"".pkTpl("delete_option2")."\";");
		
	eval("\$site_body.= \"".pkTpl("delete")."\";");
	return;
	}


#user to activate
if($mode=='activate')
	{
	if(isset($_POST['activate']) && $ACTION==$_POST['activate'])
		{
		if(is_array($_POST['user_activate']))
			{
			$sqlcommand='';
			
			foreach($_POST['user_activate'] as $i) 
				{
				if($sqlcommand)
					$sqlcommand.=" OR user_id='".intval($i)."'";
				else 
					$sqlcommand="SELECT user_name, uid, user_id, user_email FROM ".pkSQLTAB_USER." WHERE user_id='".intval($i)."'";
				}
			
			if($sqlcommand)
				{
				pkLoadLang('adminemail');

				
				$getuserinfo=$SQL->query($sqlcommand);
				
				$sqlcommand='';
				while($userinfo=$SQL->fetch_assoc($getuserinfo))
					{
					$mail_link = pkGetConfig('site_url').'/include.php?user='.urlencode($userinfo['user_name']).'&uid='.$userinfo['uid'].'&relog=1';
					
					$mail_title = pkGetSpecialLang('user_activate_mail_title',pkGetConfig('site_name'));
					$mail_text = pkGetSpecialLang('user_activate_mail_text',
									$userinfo['user_name'],
									pkGetConfig('site_name'),
									$mail_link,
									pkGetConfig('site_name'),
									pkGetConfig('site_url')
									);
					
					
					if(mailsender($userinfo['user_email'],$mail_title,$mail_text))
						{
						if($sqlcommand)
							$sqlcommand.=" OR user_id='".$userinfo['user_id']."'";
						else
							$sqlcommand="UPDATE ".pkSQLTAB_USER." SET user_activate=1 WHERE user_id='".$userinfo['user_id']."'";
						}
					unset($mail_text);
					unset($mail_title);
					}
				
				if($sqlcommand)
					$SQL->query($sqlcommand);
				}
			}
			
		usercount();
		newestuser();
		bdusertoday();
		
		pkHeaderLocation('userslist','activate');
		}
	
	
	$gettoactivate=$SQL->query("SELECT
			user_id,
			user_name,
			user_email,
			signin
		FROM ".pkSQLTAB_USER."
		WHERE user_activate=0
		ORDER by user_nick ASC");
	while($userinfo=$SQL->fetch_array($gettoactivate))
		{
		$row=rowcolor($row);
		
		$userinfo['user_name']=pkEntities($userinfo['user_name']);
		$info_signin=formattime($userinfo['signin']);
		
		eval("\$activate_row.= \"".pkTpl("activate_row")."\";");
		}
	
	if($activate_row!="")
		eval("\$activate_row.= \"".pkTpl("activate_option1")."\";");
	else
		eval("\$activate_row.= \"".pkTpl("activate_option2")."\";");
	
	
	eval("\$site_body.= \"".pkTpl("activate")."\";");	
	
	return;
	}
	
	
#unused useraccounts
if($mode=='unused')
	{
	if(isset($_POST['delete']) && $ACTION==$_POST['delete'] && is_array($_POST['del_unused']))
		{
		pkLoadFunc('user');		
		
		foreach($_POST['del_unused'] as $id)
			{
			if(!intval($id)>1)
				continue;
			
			pkUserDelete(intval($id));
			}
		
		usercount();
		newestuser();
		bdusertoday();
		
		pkHeaderLocation('userslist','unused');
		}
	
	unset($notused_row);
	
	$getnotused=$SQL->query("SELECT 
			user_id,
			user_name,
			user_nick,
			signin,
			user_email
		FROM ".pkSQLTAB_USER."
		WHERE logtime=0
		ORDER by signin ASC");
	while($userinfo=$SQL->fetch_array($getnotused))
		{
		$row=rowcolor($row);

		$userinfo['user_name']=pkEntities($userinfo['user_name']);
		$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
		$userinfo['user_email']=pkEntities($userinfo['user_email']);
						
		$info_signin=formattime($userinfo['signin']);
		$info_notused_since=number_format((pkTIME-$userinfo['signin'])/(3600*24),0,",",".");
		
		eval("\$notused_row.= \"".pkTpl("notused_row")."\";");
		}
	
	
	if($notused_row)
		eval("\$notused_row.= \"".pkTpl("notused_option1")."\";");
	else
		eval("\$notused_row.= \"".pkTpl("notused_option2")."\";");
	
	eval("\$site_body.= \"".pkTpl("notused")."\";");

	return;
	}

#memberlisting
if(($_REQUEST['soption']=='' || $_REQUEST['soption']=='usernick' || $_REQUEST['soption']=='all') && !empty($svalue))
	{
	$searchstr=" WHERE u.user_nick LIKE '%".$SQL->f($svalue)."%' OR 
						u.user_name LIKE '%".$SQL->f($svalue)."%' OR
						u.user_email LIKE '%".$SQL->f($svalue)."%'";
	
	$select0=' selected="selected"';
	}
elseif($_REQUEST['soption']=='lastvisit' && $svalue > 0)
	{
	$timesearch=pkTIME-86400*intval($svalue);
	$searchstr=" WHERE u.logtime>'".$timesearch."'";
	$select1=' selected="selected"';
	}
elseif($_REQUEST['soption']=='ilastvisit' && $svalue > 0) 
	{
	$timesearch=pkTIME-86400*intval($svalue);
	$searchstr=" WHERE u.logtime<'".$timesearch."'";
	$select2=' selected="selected"';
	} 
elseif($_REQUEST['soption']=='id' && intval($svalue)>0)
	{
	$searchstr=" WHERE u.user_id='".intval($svalue)."'";
	$select3=' selected="selected"';
	}
elseif($_REQUEST['soption']=='usergroup')
	{
	$searchstr=" WHERE u.user_groupid>0";
		
	if(!empty($svalue))
		{
		$searchstr.= " AND (u.user_nick LIKE '%".$SQL->f($svalue)."%' OR
							u.user_name LIKE '%".$SQL->f($svalue)."%' OR
							u.user_email LIKE '%".$SQL->f($svalue)."%' OR
							g.usergroup_name LIKE '%".$SQL->f($svalue)."%')";
		}
	$selectusergroup = ' selected="selected"';	
	}
elseif(in_array($_REQUEST['soption'],$statushash))
	{
	#search by user status
	$searchstr = " WHERE u.user_status='".$SQL->f($_REQUEST['soption'])."'";
	
	if(!empty($svalue))
		{
		$searchstr.= " AND (user_nick LIKE '%".$SQL->f($svalue)."%' OR 
							user_name LIKE '%".$SQL->f($svalue)."%' OR
							user_email LIKE '%".$SQL->f($svalue)."%')";
		}
	
	$select = 'select'.pkEntities($_REQUEST['soption']);
	$$select = ' selected="selected"';
	}
else 
	{
	unset($soption);
	unset($svalue);
	}


$counter = $count = $SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_USER." AS u"));


if(!empty($searchstr))
	{
	$counter=$SQL->fetch_array($SQL->query("SELECT 
		COUNT(*) 
		FROM ".pkSQLTAB_USER." AS u
			LEFT JOIN ".pkSQLTAB_USER_GROUP." AS g
		ON(u.user_groupid=g.usergroup_id)
		".$searchstr));
	}

$countpercent=number_format((100*$counter[0]/$count[0]),1,",",".");
$total_side=sidelinkfull($counter[0],$epp,$entries,"include.php?path=userslist&soption=".pkEntities($_REQUEST['soption'])."&svalue=".pkEntities($_REQUEST['svalue']));

$query = $SQL->query("SELECT 
		u.user_id,
		u.user_nick,
		u.user_email,
		u.user_status,
		u.user_sex,
		u.logtime,
		u.user_activate,
		u.user_groupid,
		g.usergroup_name
	FROM ".pkSQLTAB_USER." AS u
		LEFT JOIN ".pkSQLTAB_USER_GROUP." AS g ON(u.user_groupid=g.usergroup_id)
	".$searchstr."
	ORDER BY TRIM(u.user_nick) ASC
	LIMIT ".$entries.",".$epp);
while($userinfo=$SQL->fetch_assoc($query)) 
	{
	$row=rowcolor($row);
	
	if(intval($userinfo['user_id'])===1)
		$member_status=$lang['mainadmin'];
	else
		{
		if($userinfo['user_status']=='admin') 
			{
			if($userinfo['user_sex']=='w') 
				$member_status=$lang['admin_female'];
			else
				$member_status=$lang['admin'];
			}
		elseif($userinfo['user_status']=='mod')
			{
			if($userinfo['user_sex']=='w')
				$member_status=$lang['mod_female'];
			else
				$member_status=$lang['mod'];
			}
		elseif($userinfo['user_status']=='member')
			{
			if($userinfo['user_sex']=='w')
				$member_status=$lang['member_female'];
			else
				$member_status=$lang['member'];
			}
		elseif($userinfo['user_status']=='user')
			{
			if($userinfo['user_sex']=='w')
				$member_status=$lang['user_female'];
			else
				$member_status=$lang['user'];
			}
		elseif($userinfo['user_status']=='ban')
			$member_status=$lang['banned'];
		else
			$member_status='';
		}
	
	$member_group = $userinfo['user_groupid'] ? $userinfo['usergroup_name'] : '&nbsp;';
	$lastvisit = $userinfo['logtime'] ? pkTimeFormat($userinfo['logtime']) : '-';
	
	if($userinfo['user_activate']!=1)
		eval("\$user_red= \"".pkTpl("member_row_locked")."\";");
	elseif($userinfo['logtime']==0)
		eval("\$user_red= \"".pkTpl("member_row_unused")."\";");
	else
		eval("\$user_red= \"".pkTpl("member_row_standard")."\";");


	$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
	$userinfo['user_email']=pkEntities($userinfo['user_email']);
	
	eval("\$member_row.= \"".pkTpl("member_row")."\";");
	}

if($member_row=='')
	eval("\$member_row= \"".pkTpl("member_empty")."\";");

$svalue=pkEntities($svalue);
 
eval("\$site_body.= \"".pkTpl("member")."\";");
?>