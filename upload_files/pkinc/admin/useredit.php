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


if(!adminaccess('user'))
	return pkEvent('access_forbidden');

$editid=(isset($_REQUEST['editid']) && intval($_REQUEST['editid'])>0) ? intval($_REQUEST['editid']) : ((isset($_REQUEST['editid']) && $_REQUEST['editid']=='new') ? 'new' : 0);
$ACTION=isset($_POST['action']) ? $_POST['action'] : 'new';


if($editid==1 && pkGetUservalue('id')!=1)
	{
	eval("\$site_body.= \"".pkTpl("edituser_error")."\";");
	return;
	}


if(isset($_POST['user_delete']) && $_POST['user_delete']==1 && $editid!=1)
	{
	$userinfo = $SQL->fetch_assoc($SQL->query("SELECT * FROM ".pkSQLTAB_USER." WHERE user_id='".$editid."'"));
	$usernick = pkEntities($userinfo['user_nick']);
	
	$notify = isset($_POST['edit_notify']) && intval($_POST['edit_notify']) ? 1 : 0; #bool
	$notify_true = $notify ? ' checked="checked"' : '';
	$notify_false = $notify ? '' : ' checked="checked"';
	
	eval("\$site_body.= \"".pkTpl("edituser_delete_form")."\";");
	return;
	}


if(isset($_POST['delete']) && isset($_POST['confirm_delete']) && $ACTION==$_POST['delete'] && $_POST['confirm_delete']=='confirmed' && intval($editid)>1)
	{
	if(isset($_POST['edit_notify']) && $_POST['edit_notify']==1)
		{
		pkLoadLang('adminemail');
		
		
		$userinfo = $SQL->fetch_assoc($SQL->query("SELECT
				user_name,
				user_email 
			FROM ".pkSQLTAB_USER."
			WHERE user_id='".intval($editid)."'"));
		
		
		if(isset($_POST['confirmed_notifytext']) && !empty($_POST['confirmed_notifytext']))
			{
			$confirmed_notifytext = $_POST['confirmed_notifytext'];
			
			$notifytext = pkGetSpecialLang('user_delete_mail_text_reason',$confirmed_notifytext);
			}
		else
			{
			$notifytext = pkGetSpecialLang('user_delete_mail_text_noreason');
			}	


		$mail_title	= pkGetSpecialLang('user_delete_mail_title',
						$userinfo['user_name'],
						pkGetConfig('site_name')
						);
		
		$mail_text	= pkGetSpecialLang('user_delete_mail_text',
						$userinfo['user_name'],
						pkGetConfig('site_name'),
						$notifytext,
						pkGetConfig('site_name'),
						pkGetConfig('site_url')
						);
		
		mailsender($userinfo['user_email'],$mail_title,$mail_text);
		}
	
	
	pkLoadFunc('user');
	pkUserDelete(intval($editid));
	usercount();
	newestuser();
	bdusertoday();	
	
	
	pkHeaderLocation('userslist');
	}


if(isset($_REQUEST['writenotify']) && $_REQUEST['writenotify']==1 && intval($editid)>0) 
	{
	if($ACTION==$_POST['cancel'] || $ACTION==$_POST['send'])
		{
		if($ACTION==$_POST['send'])
			{
			pkLoadLang('adminemail');
			
			
			$userinfo = $SQL->fetch_assoc($SQL->query("SELECT 
					user_nick,
					user_name,
					user_email,
					user_id,
					uid
				FROM ".pkSQLTAB_USER."
				WHERE user_id='".intval($editid)."'
				LIMIT 1"));
			

			$notify_text = '';
			$notify_link = pkGetConfig('site_url').'/include.php?user='.urlencode($userinfo['user_name']).'&uid='.$userinfo['uid'].'&relog=1';
			
			if(trim($_POST['notify_message'])!='')
				{
				$adminname = pkGetUservalue('nick');

				$notify_text = $_POST['notify_message'];
				$notify_text = pkGetSpecialLang('user_edit_mail_textadd',
								$notify_text
								);
				}
			
			
			$notify_title	= pkGetSpecialLang('user_edit_mail_title',
								$userinfo['user_name'],
								pkGetConfig('site_name')			
								);
			
			$notify_text 	= pkGetSpecialLang('user_edit_mail_text',
								$userinfo['user_nick'],
								pkGetConfig('site_name'),
								$notify_text,
								$notify_link,
								pkGetConfig('site_name'),
								pkGetConfig('site_url')
								);
			
			
			mailsender($userinfo['user_email'],$notify_title,$notify_text);
			}
		
		pkHeaderLocation('useredit','','editid='.intval($editid));
		}
	
	
	eval("\$site_body.= \"".pkTpl("edituser_notify")."\";");
	return;
	}


if($ACTION==$_POST['save'] && trim($_POST['edit_nick'])!='' && trim($_POST['edit_name'])!='' && (trim($_POST['edit_password'])!='' || intval($_POST['editid'])>0) && emailcheck($_POST['edit_email']))
	{
	if(intval($editid)>0)
		{
		$notlike=" AND user_id!='".intval($editid)."'";
		}
		
		
	$info_name=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_USER." WHERE user_name='".$SQL->f($_POST['edit_name'])."'".$notlike));
	$info_nick=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_USER." WHERE user_nick='".$SQL->f($_POST['edit_nick'])."'".$notlike));
	$info_email=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_USER." WHERE user_email='".$SQL->f($_POST['edit_email'])."'".$notlike));

        
        $user_states = array('ban'=>0,'guest'=>1,'user'=>2,'member'=>3,'mod'=>4,'admin'=>5);
        
        if($info_name[0]==0 && $info_nick[0]==0 && $info_email[0]==0 && $user_states[pkGetUservalue('status')] >= $user_states[$_POST['edit_status']]) # Status Vergabe Fix
	
		{
		if(intval($_POST['editid'])===1) 
			$edit_status='admin';
		elseif($_POST['edit_status']=='mod' || $_POST['edit_status']=='member' || $_POST['edit_status']=='user' || $_POST['edit_status']=='ban' || $_POST['edit_status']=='admin')
			$edit_status=$_POST['edit_status'];
		else
			$edit_status='user';


		if($editid!='new' && intval($editid)>0) 
			{
			unset($set_adds);
				
			if(!empty($_POST['edit_password']))
				$set_adds=", user_pw='".md5($_POST['edit_password'])."'";
				
			if($_POST['edit_remove_avatar']==1)
				$set_adds.=", user_avatar=''";

			
			if($editid === 1)
				{
				$edit_activate = 1;
				}
			else
				{
				$edit_activate = isset($_POST['edit_activate']) && intval($_POST['edit_activate']) ? 1 : 0;
				}

			$SQL->query("UPDATE ".pkSQLTAB_USER." SET 
				user_name='".$SQL->f($_POST['edit_name'])."',
				user_nick='".$SQL->f($_POST['edit_nick'])."',
				user_email='".$SQL->f($_POST['edit_email'])."',
				user_status='".$SQL->f($edit_status)."',
				user_activate='".$edit_activate."',
				user_postdelay='".$SQL->f($_POST['edit_postdelay'])."',
				user_profillock='".$SQL->i($_POST['edit_profillock'])."',
				user_ghost='".$SQL->f($_POST['edit_ghost'])."',
				user_icqid='".$SQL->i($_POST['edit_icqid'])."',
				user_aimid='".$SQL->f($_POST['edit_aimid'])."',
				user_yim='".$SQL->f($_POST['edit_yim'])."',
				user_hpage='".$SQL->f($_POST['edit_hpage'])."',
				user_sig='".$SQL->f($_POST['edit_sig'])."',
				user_qou='".$SQL->f($_POST['edit_qou'])."',
				user_hobby='".$SQL->f($_POST['edit_hobby'])."',
				user_imoption='".$SQL->i($_POST['user_imoption'])."',
				user_groupid='".$SQL->i($_POST['edit_groupid'])."' ".
					$set_adds." 
				WHERE user_id='".$editid."'");
			}
		else 
			{
			$SQL->query("INSERT INTO ".pkSQLTAB_USER."
				SET signin='".pkTIME."',
					user_activate='".$SQL->i($_POST['edit_activate'])."',
					user_status='".$SQL->f($edit_status)."',
					user_name='".$SQL->f($_POST['edit_name'])."',
					user_nick='".$SQL->f($_POST['edit_nick'])."',
					user_email='".$SQL->f($_POST['edit_email'])."', 
					user_pw='".$SQL->f(md5($_POST['edit_password']))."',
					user_groupid='".$SQL->i($_POST['edit_groupid'])."',
					user_profillock='".$SQL->i($_POST['edit_profillock'])."',
					lastlog='".pkTIME."'");
					
			$editid=$SQL->insert_id();
			}
			
		if(isset($_POST['edit_profilefields']) && is_array($_POST['edit_profilefields'])) 
			{
			unset($sqlcommand);
			$userfield_counter=$SQL->fetch_array($SQL->query("SELECT COUNT(userid) as counter 
				FROM ".pkSQLTAB_USER_FIELDS." 
				WHERE userid='".$editid."' LIMIT 1"));
				
				
			if($userfield_counter['counter']<1)
				$SQL->query("INSERT INTO ".pkSQLTAB_USER_FIELDS." (userid) VALUES ('".$editid."')");
				
				
			foreach($_POST['edit_profilefields'] as $id=>$value)
				{
				$id=intval($id);

				if(!$id>0)
					continue;
					
				if($sqlcommand)
					$sqlcommand.=",field_".$id."='".$SQL->f($value)."'";
				else 
					$sqlcommand="UPDATE ".pkSQLTAB_USER_FIELDS." SET field_".$id."='".$SQL->f($value)."'";
				}
				
				
			if($sqlcommand)
				$SQL->query($sqlcommand." WHERE userid='".$editid."'");
			}
			
			
		if(intval($editid)===intval(pkGetUservalue('id')))
			{
			$userinfo=$SQL->fetch_assoc($SQL->query("SELECT 
					user_name,
					user_pw,
					user_nick,
					user_email,
					user_status,
					user_icqid,
					user_hpage,
					user_imoption,
					user_groupid
				FROM ".pkSQLTAB_USER."
				WHERE user_id='".$SQL->i(pkGetUservalue('id'))."'
				LIMIT 1"));
			
			$vars=array(
				'name'=>'user_name',
				'pass'=>'user_pw',
				'nick'=>'user_nick',
				'email'=>'user_email',
				'status'=>'user_status',
				'icqid'=>'user_icqid',
				'hpage'=>'user_hpage',				
				'imoption'=>'user_imoption',
				'group'=>'user_groupid'
				);	
		
			foreach($vars as $k=>$v)
				$SESSION->setUservalue($k,$userinfo[$v]);
			}
			
		usercount();
		newestuser();
		bdusertoday();			
			
		pkHeaderLocation('useredit','','editid='.$editid.(isset($_POST['edit_notify']) && $_POST['edit_notify']==1 ? '&writenotify=1' : ''));
		}
	elseif($info_name[0]!=0)
		eval("\$error_message= \"".pkTpl("edituser_error_1")."\";");
	elseif($info_nick[0]!=0)
		eval("\$error_message= \"".pkTpl("edituser_error_2")."\";");
	elseif($info_email[0]!=0)
		eval("\$error_message= \"".pkTpl("edituser_error_3")."\";");
        elseif($user_states[pkGetUservalue('status')] < $user_states[$_POST['edit_status']])
                eval("\$error_message= \"".pkTpl("edituser_error_5")."\";");
	else
		eval("\$error_message= \"".pkTpl("edituser_error_0")."\";");
		
	if($editid=='new') 
		{
		$edit_name=pkEntities($_POST['edit_name']);
		$edit_nick=pkEntities($_POST['edit_nick']);
		$edit_email=pkEntities($_POST['edit_email']);
		}
	}

if($ACTION==$_POST['save'])
	{
	if(!emailcheck($_POST['edit_email']) && $_POST['edit_nick']!="" && $_POST['edit_name']!='')
		eval("\$error_message= \"".pkTpl("edituser_error_4")."\";");
	
	if(intval($editid)>0)
		{
		$userinfo['user_postdelay']=intval($_POST['edit_postdelay']);
		$userinfo['user_icqid']=intval($_POST['edit_icqid']);
		$userinfo['user_aimid']=($_POST['edit_aimid']);
		$userinfo['user_yim']=($_POST['edit_yim']);
		$userinfo['user_hpage']=($_POST['edit_hpage']);
		$userinfo['user_groupid']=($_POST['edit_groupid']);
		$userinfo['user_sig']=($_POST['edit_sig']);
		$userinfo['user_qou']=($_POST['edit_qou']);
		$userinfo['user_hobby']=($_POST['edit_hobby']);

		$info_icqid=(intval($userinfo['user_icqid'])>0) ? intval($userinfo['user_icqid']) : '';
			
			
		if(!empty($userinfo['user_avatar'])) 
			$info_avatar=pkEntities(pkDIRWWWROOT.$config['avatar_path'].'/'.basename($userinfo['user_avatar']));
		else
			$info_avatar="fx/blank.gif";			
			
			
		if($_POST['user_ghost']==1)
			$info_ghost1="checked";
		else
			$info_ghost0="checked";
			
		$edit_type=$lang['edit'];
		$userinfo['user_aimid']=pkEntities($userinfo['user_aimid']);
		$userinfo['user_yim']=pkEntities($userinfo['user_yim']);
		$userinfo['user_hpage']=pkEntities($userinfo['user_hpage']);
		$userinfo['user_sig']=pkEntities($userinfo['user_sig']);
		$userinfo['user_qou']=pkEntities($userinfo['user_qou']);
		$userinfo['user_hobby']=pkEntities($userinfo['user_hobby']);					

			
		eval("\$edituser_full= \"".pkTpl("edituser_full")."\";");
		eval("\$edituser_delete= \"".pkTpl("edituser_delete")."\";"); 
		}
		
	$edit_name=$_POST['edit_name'];
	$edit_nick=$_POST['edit_nick'];
	$edit_email=$_POST['edit_email'];
		
	if($_POST['edit_status']=="admin") 
		$info_status4=" selected";
	elseif($_POST['edit_status']=="mod")
		$info_status3=" selected";
	elseif($_POST['edit_status']=="member")
		$info_status2=" selected";
	elseif($_POST['edit_status']=="ban")
		$info_status0=" selected";
	else
		$info_status1=" selected";


	if($_POST['edit_activate']==1)
		$info_activate=" checked";
	else
		$_POST['edit_activate']='0';

	if($_POST['user_imoption']==1)
		$info_user_imoption1=" checked";
	else
		$info_user_imoption0=" checked";

	
	if($_POST['edit_profillock']==1)
		$info_profillock=" checked";
	}
elseif(intval($editid)>0)
	{
	$userinfo=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_USER." WHERE user_id='".intval($editid)."'"));
	$userfields=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_USER_FIELDS." WHERE userid='".$userinfo['user_id']."' LIMIT 1"));
	
	$edituser_profilefields='';
	$getprofilefields=$SQL->query("SELECT * FROM ".pkSQLTAB_USER_PROFILEFIELDS." ORDER by profilefields_order ASC");
	while($profilefields=$SQL->fetch_array($getprofilefields))
		{
		$f="field_".$profilefields['profilefields_id'];
		$fieldcontent=pkEntities($userfields[$f]);
		$fieldname=pkEntities($profilefields['profilefields_name']);
		
		eval("\$edituser_profilefields.= \"".pkTpl("edituser_profilefields")."\";");
		}
		
	$editid=$userinfo['user_id'];
	$edit_name=$userinfo['user_name'];
	$edit_nick=$userinfo['user_nick'];
	$edit_email=$userinfo['user_email'];
		
	$info_icqid=(intval($userinfo['user_icqid'])>0) ? intval($userinfo['user_icqid']) : '';
		
	if($userinfo['user_status']=="admin")
		$info_status4=" selected";
	elseif($userinfo['user_status']=="mod")
		$info_status3=" selected";
	elseif($userinfo['user_status']=="member")
		$info_status2=" selected";
	elseif($userinfo['user_status']=="ban")
		$info_status0=" selected";
	else
		$info_status1=" selected";
		
	if(!empty($userinfo['user_avatar']))
		$info_avatar=pkEntities(pkDIRWWWROOT.$config['avatar_path'].'/'.basename($userinfo['user_avatar']));
	else
		$info_avatar="fx/blank.gif";
		
	if($userinfo['user_ghost']==1)
		$info_ghost1="checked";
	else
		$info_ghost0="checked";
		
	if($userinfo['user_activate']==1)
		$info_activate=" checked";
	if($userinfo['user_profillock']==1)
		$info_profillock=" checked";
		
	if($userinfo['user_imoption']==1)
		$info_user_imoption1=" checked";
	else
		$info_user_imoption0=" checked";		
	
		
	$edit_type=$lang['edit'];
	$userinfo['user_aimid']=pkEntities($userinfo['user_aimid']);
	$userinfo['user_yim']=pkEntities($userinfo['user_yim']);
	$userinfo['user_hpage']=pkEntities($userinfo['user_hpage']);
	$userinfo['user_sig']=pkEntities($userinfo['user_sig']);
	$userinfo['user_qou']=pkEntities($userinfo['user_qou']);
	$userinfo['user_hobby']=pkEntities($userinfo['user_hobby']);			
		
		
	eval("\$edituser_full= \"".pkTpl("edituser_full")."\";");
	eval("\$edituser_delete= \"".pkTpl("edituser_delete")."\";");
	}
else 
	{
	$editid='new';
	$edit_type=$lang['create'];
	$info_status1=" selected";
	
	if($_POST['edit_activate']==1 || !isset($_POST['edit_activate']))
		$info_activate=" checked";
	}
	
	
$getgroups=$SQL->query("SELECT * FROM ".pkSQLTAB_USER_GROUP." ORDER by usergroup_name ASC");
while($group=$SQL->fetch_array($getgroups))
	{
	$info_group.='<option value="'.$group['usergroup_id'].'"';
		
	if($userinfo['user_groupid']==$group['usergroup_id'])
		$info_group.=' selected';
		
	$info_group.='>'.pkEntities($group['usergroup_name']).'</option>';
	}


$edit_name=pkEntities($edit_name);		
$edit_nick=pkEntities($edit_nick);

	
$form_action=pkLink('useredit','','editid='.$editid);
	
eval("\$site_body.= \"".pkTpl("edituser")."\";");
?>