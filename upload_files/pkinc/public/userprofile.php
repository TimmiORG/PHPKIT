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


if(!pkGetUservalue('id'))
	{
	pkEvent('access_refused');
	return;
	}


$modehash=array('avatar','delete','edit','friends','options');
$mode=(isset($_REQUEST['mode']) && in_array($_REQUEST['mode'],$modehash)) ? $_REQUEST['mode'] : NULL;


switch($mode)
	{
	case 'avatar' :
		pkLoadFunc('user');
		$user_navigation=pkUserNavigation();


		$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';

		if(($_REQUEST['upload']==1 && $config['avatar_eod']==1) || ($config['avatar_eod']!=1 && $config['avatar_eod']!=2))
			{
			pkHeaderLocation('','','event=function_disabled');
			}


		if($ACTION==$_POST['cancel']) 
			{
			pkHeaderLocation('userprofile','avatar');
			}
	
		if(isset($_REQUEST['upload']))
			{
			if($ACTION==$_POST['upload_action'] && is_uploaded_file($_FILES['upload_pic']['tmp_name']))
				{
				$path = array();
				$path = $_FILES['upload_pic'];
				$path['tmp_name'] = pkDIRTEMP.strrchr($_FILES['upload_pic']['tmp_name'],'/');
				
				if(!move_uploaded_file($_FILES['upload_pic']['tmp_name'],$path['tmp_name']))
					{
					$path['tmp_name'] = $_FILES['upload_pic']['tmp_name'];
					}
				
				if($avatar_size=getimagesize($path['tmp_name']))
					{
					if($avatar_size[2]==1)
						$ext='.gif';
					elseif($avatar_size[2]==2)
						$ext='.jpg';
					elseif($avatar_size[2]==3)
						$ext='.png';
					else
						unset($ext);
					}					
				else
					{
					unset($avatar_size);
					}
				
				if(isset($ext) && isset($avatar_size))
					{
					$filesize = filesize($path['tmp_name']);
					$filename='avauser_'.pkGetUservalue('id').$ext;
					
					
					if(($config['avatar_height']<$avatar_size[1]) || ($config['avatar_width']<$avatar_size[0]))
						$error=1;
					elseif($filesize<($config['avatar_size']*1024) && $filesize!=0)
						{
						$UPLOAD=new UPLOAD();
						$uploadreturned=$UPLOAD->images($path,$config['avatar_path'],$filename);
						
						if($uploadreturned[0]==TRUE)
							{
							unlink($_FILES['upload_pic']['tmp_name']);
							unlink($path);
							
							pkHeaderLocation('userprofile','options','setavatar='.urlencode(basename($filename)));
							}
						else
							{
							$error=4;
							}
						}
					else
						{
						$error=2;
						}
					}
				else
					{
					$error=3;
					}
				
				pkHeaderLocation('userprofile','avatar','upload&error='.$error);
				}
			else
				{
				$error=(isset($_REQUEST['error']) && intval($_REQUEST['error'])>0 && intval($_REQUEST['error'])<5) ? intval($_REQUEST['error']) : 0;

				
				if($error==1)
					eval("\$avatar_message=\"".pkTpl("getavatar_upload_error1")."\";");
				elseif($error==2)
					eval("\$avatar_message= \"".pkTpl("getavatar_upload_error2")."\";");
				elseif($error==3)
					eval("\$avatar_message= \"".pkTpl("getavatar_upload_error3")."\";");
				else
					eval("\$avatar_message= \"".pkTpl("getavatar_upload_message")."\";");
				
				eval("\$site_body.= \"".pkTpl("getavatar_upload")."\";");
				} 
			}
		else
			{
			$dir=$config['avatar_path'];
			$width=1;
			$row='odd';
			
			$a=opendir($dir);
			while($datei=readdir($a))
				{
				if(strstr($datei,"avatar") || (pkGetUservalue('status')=='member' && strstr($datei,"avamember")) || (pkGetUservalue('status')=='mod' && (strstr($datei,"avamod") || strstr($datei,"avamember"))) || (pkGetUservalue('status')=='admin' && (strstr($datei,"avaadmin") || strstr($datei,"avamod") || strstr($datei,"avamember")))) 
					{
					if($width==4)
						{
						eval("\$avatar_list.= \"".pkTpl("getavatar_rowbreak")."\";");
						$width=1;
						$row=rowcolor($row);
						}
					
					if(!$avatar_dimension=@getimagesize($dir."/".$datei))
						unset($avatar_dimension);
					
					eval("\$avatar_list.= \"".pkTpl("getavatar_list")."\";");
					$width++;
					}
				}
			closedir($a);
			
			$cs=4-$width;
			if($cs>0)
				eval("\$avatar_list.= \"".pkTpl("getavatar_lastrow")."\";");
				
			if($config['avatar_eod']==2)
				eval("\$avatar_uploadlink= \"".pkTpl("getavatar_uploadlink")."\";");
			
			eval("\$site_body.= \"".pkTpl("getavatar")."\";");
			}		
		break;
		#END case avatar
	case 'delete' :
		if(pkGetUservalue('id')==1)
			{
			pkEvent('mainadmin_account_delete');
			return;
			}

		if(!pkGetUservalue('id')>1)
			{
			pkEvent('access_refused');
			return;
			}

		if($config['user_delete']!=2 && $config['user_delete']!=1)
			{
			pkEvent('function_disabled');
			}

		if(isset($_POST['action']))
			{
			if($_POST['action']==$_POST['delete'] && $_POST['delete_confirm']=='confirmed')
				{
				if($config['user_delete']==2)
					{
					pkLoadFunc('user');
					pkUserDelete(intval(pkGetUservalue('id')));
					}
				elseif($config['user_delete']==1)
					{
					$SQL->query("UPDATE ".pkSQLTAB_USER." SET user_activate='2' WHERE user_id='".$SQL->i(pkGetUservalue('id'))."'");
					}
				
				pkHeaderLocation('','','event=account_deleted&logout=1');
				}
			
			pkHeaderLocation('userprofile');
			}
		
		if($config['user_delete']==1)
			eval("\$delete_msg= \"".pkTpl("extdelete_msg1")."\";");
		elseif($config['user_delete']==2)
			eval("\$delete_msg= \"".pkTpl("extdelete_msg2")."\";");
		
		eval("\$site_body.= \"".pkTpl("extdelete")."\";");		
		break;
		#END case delete
	case 'edit' :
		$S=&$SQL;
		
		pkLoadFunc('user');
		
		$info=array('user_id'=>0);
		$bd_option_year=$uderror=$editprofile_userfield=
		$user_email_option_1=$user_email_option_0=$event=
		$user_sex_option_1=$user_sex_option_2=$user_sex_option_0=NULL;
		
		if(pkGetUservalue('id'))
			{
			$info=$S->fetch_assoc($S->query("SELECT 
				user_id,
				user_name,
				user_pw,
				user_profillock,
				user_status,
				user_nick,
				user_pw,
				user_email,
				user_sex,
				user_hpage,
				user_aimid,
				user_yim,
				user_icqid,
				user_emailshow,
				user_country,
				user_bd_day,
				user_bd_month,
				user_bd_year,
				signin,
				user_qou,
				user_sig,
				user_hobby
				FROM ".pkSQLTAB_USER." 
				WHERE user_name='".$SQL->f(pkGetUservalue('name'))."' AND
					user_pw='".$SQL->f(pkGetUservalue('pass'))."' AND
					user_id='".$SQL->f(pkGetUservalue('id'))."'
				LIMIT 1"));
			}
		
		if(!pkGetUservalue('id') || $info['user_id']!=pkGetUservalue('id'))
			return pkEvent('access_refused');
		
		if($info['user_profillock']==1)
			return pkEvent('profile_update_disabled');
		
		
		$ACTION=isset($_POST['action']) ? $_POST['action'] : false;
		
		if(isset($_POST['save']) && $ACTION==$_POST['save']) 
			{
			$uderror=NULL;
			$ud_nick=(isset($_POST['ud_nick']) && !empty($_POST['ud_nick'])) ? trim($_POST['ud_nick']) : '';
			$ud_newpw1=(isset($_POST['ud_newpw1']) && !empty($_POST['ud_newpw1'])) ? trim($_POST['ud_newpw1']) : '';
			$ud_newpw2=(isset($_POST['ud_newpw2']) && !empty($_POST['ud_newpw2'])) ? trim($_POST['ud_newpw2']) : '';
			$ud_email=(isset($_POST['ud_email']) && !empty($_POST['ud_email'])) ? trim($_POST['ud_email']) : '';
			$cur_password=(isset($_POST['cur_password']) && !empty($_POST['cur_password'])) ? md5(trim($_POST['cur_password'])) : '';


			#verify password change
			if(!empty($ud_newpw1) && !empty($ud_newpw2))
				{
				if($cur_password!=pkGetUservalue('pass'))
					$uderror='wrong_password';
				elseif($_POST['ud_newpw1']!=$_POST['ud_newpw2'])
					$uderror='passwords_unequal';
				elseif($ud_newpw1===$ud_newpw2)
					$ud_userpw=md5($ud_newpw1);
				}

			#verify email address change
			if(!empty($ud_email) && $ud_email!=pkGetUservalue('email'))
				{
				if(!emailcheck($ud_email,1))
					{
					$uderror='email_invalid';
					$ud_email='';
					}
				else
					{
					list($check)=$SQL->fetch_row($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_USER." 
						WHERE user_email='".$SQL->f($ud_email)."' AND user_id<>".pkGetUservalue('id')." 
						LIMIT 1"));
					
					if($check)
						{
						$uderror='email_in_use';
						$ud_email='';
						}
					}
					
				if(!empty($ud_email) && $cur_password!=pkGetUservalue('pass'))
					$uderror='wrong_password';						
				}

		
			#verify usernick change
			if(!empty($ud_nick) && $ud_nick!=pkGetUservalue('nick'))
				{
				if($cur_password!=pkGetUservalue('pass'))
					$uderror='wrong_password';							
				elseif(checkusername($ud_nick))
					{
					list($check)=$S->fetch_row($S->query("SELECT COUNT(user_id) FROM ".pkSQLTAB_USER."
						WHERE user_nick='".$S->f($ud_nick)."' AND user_id<>'".$S->f(pkGetUservalue('id'))."'
						LIMIT 1"));
					
					if($check)
						{
						$uderror='nickname_in_use';
						$ud_nick='';
						}
					}
				else
					$uderror='nickname_invalid';
				}


			if($uderror)
				pkHeaderLocation('userprofile','edit','uderror='.$uderror);

			$vars=array(
				'ud_nick'=>'ud_nick',
				'ud_hpage'=>'ud_hpage',
				'ud_aimid'=>'ud_aimid',
				'ud_yim'=>'ud_yim',
				'ud_icqid'=>'ud_icqid',
				'ud_usersig'=>'ud_usersig',
				'ud_userqou'=>'ud_userqou',
				'ud_userhobby'=>'ud_userhobby',
				'ud_sex'=>'ud_sex',
				'ud_emailshow'=>'ud_emailshow',
				'ud_country'=>'ud_country',
				'user_bd_day'=>'user_bd_day',
				'user_bd_month'=>'user_bd_month',
				'user_bd_year'=>'user_bd_year'	
				);
			
			foreach($vars as $k=>$v)
				$$k=$ENV->_post($v);
			
			$ud_hpage=pkUrlCheck($ud_hpage) ? $ud_hpage : '';
		
			if($user_bd_year > (date('Y',pkTIME)-5) || $user_bd_year<=1900 || !checkdate($user_bd_month,$user_bd_day,$user_bd_year))
				$user_bd_day=$user_bd_month=$user_bd_year=0;	
		
			$S->query("UPDATE ".pkSQLTAB_USER." SET 
				uid='".pkRand()."',".
				(empty($ud_nick) ? '' : "user_nick='".$S->F($ud_nick)."',").
				(empty($ud_userpw) ? '' : "user_pw='".$S->F($ud_userpw)."',").
				(empty($ud_email) ? '' : "user_email='".$S->F($ud_email)."',")."
				user_emailshow='".$S->F(intval($ud_emailshow))."',
				user_sex='".$S->F($ud_sex)."',
				user_country='".$S->F($ud_country)."',
				user_hpage='".$S->F($ud_hpage)."',
				user_aimid='".$S->F($ud_aimid)."',
				user_yim='".$S->F($ud_yim)."',
				user_icqid='".$S->F($ud_icqid)."',
				user_sig='".$S->F($ud_usersig)."',
				user_qou='".$S->F($ud_userqou)."',
				user_hobby='".$S->F($ud_userhobby)."',
				user_bd_day='".$S->F($user_bd_day)."',
				user_bd_month='".$S->F($user_bd_month)."',
				user_bd_year='".$S->F($user_bd_year)."'
				WHERE user_id='".$SQL->i(pkGetUservalue('id'))."'"
				);
			
			$info=$S->fetch_array($S->query("SELECT 
				* 
				FROM ".pkSQLTAB_USER." 
				WHERE user_id='".$SQL->i(pkGetUservalue('id'))."'"));
		
			if(isset($_POST['profilefields']) && is_array($_POST['profilefields'])) 
				{
				$query=NULL;
				$userfield_counter=$S->fetch_array($S->query("SELECT 
					COUNT(userid) as counter 
					FROM ".pkSQLTAB_USER_FIELDS." 
					WHERE userid='".$SQL->i(pkGetUservalue('id'))."' LIMIT 1"));
				
				if($userfield_counter['counter']<1) 
					$S->query("INSERT INTO ".pkSQLTAB_USER_FIELDS." (userid) VALUES ('".$SQL->i(pkGetUservalue('id'))."')");
					
				foreach($_POST['profilefields'] as $id=>$value)
					$query.=(empty($query) ? '' : ',')."field_".intval($id)."='".$S->f($value)."'";
					
				if($query) 
					$S->query("UPDATE ".pkSQLTAB_USER_FIELDS." 
						SET ".$query." 
						WHERE userid='".$SQL->i(pkGetUservalue('id'))."'");
				}
				
			$array=array(
				'nick'=>'nick',
				'pass'=>'pw',
				'email'=>'email',
				'sex'=>'sex',
				'hpage'=>'hpage',
				'icqid'=>'icqid'
				);
				
			foreach($array as $k=>$v)
				pkSetUservalue($k,$info['user_'.$v]);
			
			#stats update
			usercount();
			newestuser();
			bdusertoday();
						
			pkHeaderLocation('userprofile','edit','event=profileupdate');
			}
		
			
		pkLoadLang('profile');


		$uderror=$ENV->_get('uderror');

		if($uderror)
			pkEvent('profileupdate_'.$uderror,false);
		
		$userfields=$S->fetch_assoc($S->query("SELECT 
			* 
			FROM ".pkSQLTAB_USER_FIELDS." 
			WHERE userid='".$SQL->i(pkGetUservalue('id'))."'
			LIMIT 1"));
		
		$result=$S->query("SELECT 
			profilefields_id, 
			profilefields_description,
			profilefields_name,
			profilefields_maxlength
			FROM ".pkSQLTAB_USER_PROFILEFIELDS."
			ORDER by profilefields_order ASC");
		while(list($fieldid,$fielddescription,$fieldname,$fieldlength)=$S->fetch_row($result))
			{
			$value=pkEntities($userfields['field_'.$fieldid]);
			$name='profilefields['.$fieldid.']';
		
			eval("\$editprofile_userfield.= \"".pkTpl("usereditprofile_userfield")."\";");
			}
		
		switch($info['user_sex'])
			{
			case 'w' :
				$user_sex_option_1='selected="selected"';
				break;
			case 'm' :
				$user_sex_option_2='selected="selected"';
				break;
			default :
				$user_sex_option_0='selected="selected"';
			}
		
		foreach(range(1,31) as $d)
			$bd_option_day.='<option value="'.$d.'"'.($info['user_bd_day']==$d ? ' selected="selected"' : '').'>'.$d.'</option>';
		
		foreach(range(1,12) as $m)
			$bd_option_month.='<option value="'.$m.'"'.($info['user_bd_month']==$m ? ' selected="selected"' : '').'>'.pkGetSpecialLang('month',$m).'</option>';
		  
		foreach(range(date('Y')-5,1900) as $y)
			$bd_option_year.='<option '.($info['user_bd_year']==$y ? ' selected="selected"' : '').'>'.$y.'</option>';
		
		
		$action_target=pkLink('userprofile','edit');
		
		$user_name=pkEntities($info['user_name']);
		$user_nick=pkEntities($info['user_nick']);
		$user_email=pkEntities($info['user_email']);
		$user_aimid=pkEntities($info['user_aimid']);
		$user_yim=pkEntities($info['user_yim']);
		$user_sig=pkEntities($info['user_sig']);
		$user_qou=pkEntities($info['user_qou']);
		$user_hobby=pkEntities($info['user_hobby']);
		$user_hpage=pkEntities($info['user_hpage']);
		
		$user_status=pkUserStatus($info['user_status']);
		$user_signin=formattime($info['signin']);
		$user_country=pkUserCountryOptionlist($info['user_country']);
		$user_icqid=intval($info['user_icqid'])>0 ? intval($info['user_icqid']) : NULL;
		$info['user_emailshow']==1 ? $user_email_option_1=' checked="checked"' : $user_email_option_0=' checked="checked"';
		
		$user_navigation=pkUsernavigation();
		
		$L_save=pkGetLang('save');
		$L_reset=pkGetLang('reset');
		$L_email_address=pkGetLang('email_address');		

		foreach(array(
				'edit_profile',
				'username',
				'username_description',
				'userstatus',
				'userstatus_description',
				'member_since',
				'account_information',
				'current_password',
				'current_password_description',
				'nickname',
				'nickname_description',
				'password',
				'password_description',
				'confirm_password',
				'confirm_password_description',
				'email_description',
				'optional_specifications',
				'show_email',
				'show_email_description',
				'sex',
				'sex_description',
				'sex_not_specified',
				'sex_male',
				'sex_female',
				'dateofbirth',
				'dateofbirth_description',
				'dateofbirth_day',
				'dateofbirth_month',
				'dateofbirth_year',
				'origin',
				'origin_description',
				'origin_ger',
				'origin_aut',
				'origin_ch',
				'origin_nl',
				'origin_oth',
				'origin_def',
				'homepage',
				'homepage_description',
				'aim_screenname',
				'aim_screenname_description',
				'yim',
				'yim_description',
				'icq',
				'icq_description',
				'signature',
				'signature_description',
				'quotation',
				'quotation_description',
				'hobbies',
				'hobbies_description'
				) as $l) {
			$v='L_editprofile_'.$l;
			$$v=pkGetLang($l);
			}
		
		eval("\$site_body.= \"".pkTpl("usereditprofile")."\";");
		unset($S);		
		break;
		#END case edit
	case 'friends' :
		pkLoadFunc('user');
		$user_navigation=pkUserNavigation();

		$add=isset($_REQUEST['add']) && intval($_REQUEST['add'])>0 ? intval($_REQUEST['add']) : 0;
		$drop=isset($_REQUEST['drop']) && intval($_REQUEST['drop'])>0 ? intval($_REQUEST['drop']) : 0;

		if($add && $add!=pkGetUservalue('id'))
			{
			if($SQL->num_rows($SQL->query("SELECT 
				*
				FROM ".pkSQLTAB_USER_FRIENDLIST."
				WHERE buddy_friendid='".$add."' AND 
					buddy_userid='".$SQL->i(pkGetUservalue('id'))."'
				LIMIT 1"))==0)
				{
				$SQL->query("INSERT INTO ".pkSQLTAB_USER_FRIENDLIST."
					(buddy_userid, buddy_friendid) VALUES ('".$SQL->i(pkGetUservalue('id'))."','".$add."')");
				}
			}
		elseif($add==pkGetUservalue('id')) 
			{
			pkEvent('buddy_addself');
			}

		if($drop)
			{
			$SQL->query("DELETE FROM ".pkSQLTAB_USER_FRIENDLIST." 
				WHERE buddy_friendid='".$drop."' AND
				buddy_userid='".$SQL->i(pkGetUservalue('id'))."'");
			}

		unset($sqlcommand);		
		$getbuddies=$SQL->query("SELECT 
				buddy_friendid
			FROM ".pkSQLTAB_USER_FRIENDLIST." 
			WHERE buddy_userid='".$SQL->i(pkGetUservalue('id'))."'
			ORDER BY buddy_userid");
		while($buddy=$SQL->fetch_array($getbuddies))
			{
			$buddy_chache[$buddy['buddy_friendid']]=$buddy;
			if($sqlcommand=='') 
				{
				$sqlcommand="SELECT 
						user_id,
						user_ghost,
						user_nick,
						user_emailshow,
						user_email,
						user_hpage,
						user_sex,
						user_imoption,
						user_icqid 
					FROM ".pkSQLTAB_USER." 
					WHERE user_id='".$buddy['buddy_friendid']."'";
				}
			else 
				{
				$sqlcommand.=" OR user_id='".$buddy['buddy_friendid']."'";
				}
			}

		if($sqlcommand!='')
			{
			$getuserinfo=$SQL->query($sqlcommand);
			while($userinfo=$SQL->fetch_array($getuserinfo)) 
				{
				$user_hash[$userinfo['user_id']]=$userinfo;
				}
	
			foreach($buddy_chache as $buddy)
				{
				if(!$userinfo=$user_hash[$buddy['buddy_friendid']])
					{
					$SQL->query("DELETE FROM ".pkSQLTAB_USER_FRIENDLIST."
						WHERE buddy_friendid='".$buddy['buddy_friendid']."' OR 
							buddy_userid='".$buddy['buddy_friendid']."'");
					continue;
				}
		
				$row=rowcolor($row);
				$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
		
				if(isonline($userinfo['user_id'])) 
					eval("\$info_os= \"".pkTpl("member_os_online")."\";");
				else 
					eval("\$info_os= \"".pkTpl("member_os_offline")."\";");
		
				eval("\$info_nick= \"".pkTpl("member_showprofil_textlink")."\";");
		
		
				if($userinfo['user_emailshow']==1)
					{
					eval("\$info_email= \"".pkTpl("member_email_textlink")."\";");
					}
				else
					{
					$info_email='&nbsp;';
					}
		
				if($userinfo['user_hpage']!="")
					{
					if(ereg('http://',$userinfo['user_hpage'])) 
						{
						$info_link=pkEntities($userinfo['user_hpage']);
						}
					else
						{
						$info_link='http://'.pkEntities($userinfo['user_hpage']);
						}
					
					eval("\$info_hpage= \"".pkTpl("member_hpage_iconlink")."\";");
					}
				else
					{
					$info_hpage='&nbsp;';
					}
				
				if($userinfo['user_sex']=='m')
					{
					eval("\$info_profile= \"".pkTpl("member_showprofil_iconlink_m")."\";");
					}
				elseif($userinfo['user_sex']=='w')
					{
					eval("\$info_profile= \"".pkTpl("member_showprofil_iconlink_w")."\";");
					}
				else
					{
					eval("\$info_profile= \"".pkTpl("member_showprofil_iconlink")."\";");
					}
					
				
				if($userinfo['user_imoption']==1)
					{
					eval("\$info_im= \"".pkTpl("member_sendim_iconlink")."\";");
					}
				else
					{
					eval("\$info_im= \"".pkTpl("member_sendim_nolink")."\";");
					}
				
				
				if($userinfo['user_icqid']!=0)
					{
					eval("\$info_icq= \"".pkTpl("member_icq_iconlink")."\";");
					}
				else
					{
					$info_icq = '&nbsp;';
					}
				
				
				eval("\$info_delete= \"".pkTpl("buddy_deletelink")."\";");
				eval("\$buddy_list.= \"".pkTpl("buddy_row")."\";");
				}
			
			eval("\$buddy_head= \"".pkTpl("buddy_head")."\";");
			}
		else
			{
			eval ("\$buddy_list= \"".pkTpl("buddy_empty")."\";");
			}
		
		eval("\$site_body.= \"".pkTpl("buddy")."\";");
		break;
		#END case friends
	case 'options' :
		$userinfo=$style_option='';
		$userinfo=$SQL->fetch_assoc($SQL->query("SELECT 
			*
			FROM ".pkSQLTAB_USER." 
			WHERE user_name='".$SQL->f(pkGetUservalue('name'))."' AND
				user_pw='".$SQL->f(pkGetUservalue('pass'))."' AND 
				user_id='".$SQL->f(pkGetUservalue('id'))."'
			LIMIT 1"));
		
		if($info['user_profillock']==1)
			return pkEvent('eventtitle_profile_update_disabled');


		pkLoadLang('profile');
		pkLoadFunc('user');

	
		if($ENV->_post_action('action','save'))
			{
			if($_POST['profil_delete']==1)
				pkHeaderLocation('userprofile','delete');
	
			$SESSION->setUservalue('sigoption',intval($_POST['new_sigoption']));
			$SESSION->setUservalue('design',intval($_POST['user_design']));
			$SESSION->setUservalue('imoption',intval($_POST['new_imoption']));
	
			$SQL->query("UPDATE ".pkSQLTAB_USER." 
				SET user_design='".$ENV->_post_id('user_design')."', 
					user_imoption='".$ENV->_post_id('new_imoption')."',
					user_imnotify='".$ENV->_post_id('new_imnotify')."',
					user_sigoption='".$ENV->_post_id('new_sigoption')."',
					user_nloption='".$ENV->_post_id('new_nloption')."',
					user_ghost='".$ENV->_post_id('ghost_option')."'
				WHERE user_id='".pkGetUservalue('id')."'");
	
			pkHeaderLocation('userprofile','options');
			}
		
		if(isset($_REQUEST['setavatar']))
			{
			if($_REQUEST['unset']==1)
				{
				if(strstr($_REQUEST['setavatar'],'avauser'))
					{
					$name=pkGetConfig('avatar_path')."/avauser_".pkGetUservalue('id').".";

					@unlink($name.'gif');
					@unlink($name.'jpg');
					@unlink($name.'png');
					}

				unset($_REQUEST['setavatar']);
				}
	
			$avatar=basename($_REQUEST['setavatar']);
			$path=pkGetConfig('avatar_path').'/'.$avatar;

			if(	(!pkFileCheck($path)) || 
				(substr($path,-4)=='.php') ||
				(strstr($avatar,'avauser') && (substr($avatar,0,8+strlen(pkGetUservalue('id')))!='avauser_'.pkGetUservalue('id') || pkGetConfig('avatar_eod')!=2)) || 
				(strstr($avatar,'avamember') && (pkGetUservalue('status')!='member' && pkGetUservalue('status')!='mod' && pkGetUservalue('status')!='admin')) || 
				(strstr($avatar,'avamod') && (pkGetUservalue('status')!='mod' && pkGetUservalue('status')!='admin')) ||
				(strstr($avatar,'avaadmin') && pkGetUservalue('status')!='admin')
				)
				$avatar='';
					
			$SQL->query("UPDATE ".pkSQLTAB_USER." 
				SET user_avatar='".$SQL->f($avatar)."' 
				WHERE user_id='".$SQL->i(pkGetUservalue('id'))."'");

			pkHeaderLocation('userprofile','options');
			}
			
		$userinfo['user_nick']=pkEntities($userinfo['user_nick']);

		if($userinfo['user_imoption']==1) 
			$im_option1=' checked';
		else
			$im_option0=' checked';

		if($userinfo['user_imnotify']==1)
			$im_imnotify1=' checked';
		else
			$im_imnotify0=' checked';

		if($userinfo['user_sigoption']==1) 
			$im_sigoption1=' checked';
		else
			$im_sigoption0='checked';

		if($userinfo['user_nloption']==1)
			$nl_option1=' checked';
		else
			$nl_option0=' checked';

		if($config['user_ghost']==1) 
			{
		  	if($userinfo['user_ghost']==1)
				$ghost_option1=' checked';
			else
				$ghost_option0=' checked';
	
			eval("\$ghost_eod= \"".pkTpl("extoption_ghost")."\";");
			}
		
		if($config['user_design']==1)
			{
			$userdesign=$userinfo['user_design'] ? $userinfo['user_design'] : pkGetConfig('site_design');
			
			$result=$SQL->query("SELECT 
					style_id,
					style_name
				FROM ".pkSQLTAB_STYLE." 
				WHERE style_user=1 OR 
					style_id='".pkGetConfig('site_style')."'
				ORDER by style_name ASC");
			while($styleinfo=$SQL->fetch_array($result))
				{
				$style_option.='<option value="'.$styleinfo['style_id'].'"'.(($userdesign==$styleinfo['style_id']) ? ' selected' : '').'>'.pkEntities($styleinfo['style_name']).'</option>';
				}
			
			if($style_option) 
				eval("\$style_option= \"".pkTpl("extoption_style")."\";");
			}
		
		if($config['avatar_eod']==1 || $config['avatar_eod']==2)
			{
			$userinfo['user_avatar']=basename($userinfo['user_avatar']);
			
			if($userinfo['user_avatar']!='' && @filecheck($config['avatar_path']."/".$userinfo['user_avatar']))
				{
				$avatar_dimension[3]=@getimagesize($config['avatar_path']."/".$userinfo['user_avatar']);
		
				eval("\$avatar_show= \"".pkTpl("user_avatar_show")."\";");
				eval("\$avatar_deselect= \"".pkTpl("extoption_avatar_deselect")."\";");
				}
			
			if($config['avatar_eod']==2)
				{
				eval("\$avatar_upload= \"".pkTpl("extoption_avatar_upload")."\";");
				}
			
			eval("\$avatar_eod= \"".pkTpl("extoption_avatar")."\";");
			}
		
		if(pkGetConfig('user_delete'))
			eval("\$extoption_delete= \"".pkTpl("extoption_delete")."\";");
		
		$user_navigation=pkUserNavigation();
		
		eval("\$site_body.=\"".pkTpl("extoption")."\";");
		break;
		#END case options
	default :
		$phpkit_status=phpkitstatus();

		$favstatus=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_FORUM_FAVORITE." WHERE forumfav_userid='".$SQL->i(pkGetUservalue('id'))."' LIMIT 1"));

		if($favstatus[0]>0)
			{
			eval ("\$profil_favorits= \"".pkTpl("profile_favorits")."\";");
			}
		
		if($config['member_gbook']==1)
			{
			$user_nav2=" &#149; "; 
			$link_userguestbook=pkLink('userguestbook');
			
			$counter=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_COMMENT." WHERE comment_cat='user' AND comment_subid='".$SQL->i(pkGetUservalue('id'))."'"));
	
			eval("\$profile_gbook= \"".pkTpl("profile_gbook")."\";");
			}
		
		unset($sqlcommand);

		$getbuddies=$SQL->query("SELECT buddy_friendid FROM ".pkSQLTAB_USER_FRIENDLIST." WHERE buddy_userid='".$SQL->i(pkGetUservalue('id'))."' ORDER BY buddy_userid");
		while($buddy=$SQL->fetch_array($getbuddies))
			{
			$buddy_cache[$buddy['buddy_friendid']]=$buddy;

			if(!$sqlcommand)
				{
				$sqlcommand="SELECT user_imoption, user_nick, user_id, user_ghost FROM ".pkSQLTAB_USER." WHERE user_id='".$buddy['buddy_friendid']."'";
				}
			else
				{
				$sqlcommand.=" OR user_id='".$buddy['buddy_friendid']."'";
				}
			}

		if(is_array($buddy_cache))
			{
			$getuserinfo=$SQL->query($sqlcommand);
			while($userinfo=$SQL->fetch_array($getuserinfo))
				{
				$user_cache[$userinfo['user_id']]=$userinfo;
				}
	
			foreach($buddy_cache as $buddy)
				{
				$row=rowcolor($row);
		
				$userinfo=$user_cache[$buddy['buddy_friendid']];
				$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
		
				if(isonline($userinfo['user_id']))
					eval("\$info_os= \"".pkTpl("member_os_online")."\";");
				else
					eval("\$info_os= \"".pkTpl("member_os_offline")."\";");
		
				if($userinfo['user_imoption']==1)
					eval("\$info_im= \"".pkTpl("member_sendim_iconlink")."\";");
				else
					eval("\$info_im= \"".pkTpl("member_sendim_nolink")."\";");
		
				eval("\$info_nick= \"".pkTpl("member_showprofil_textlink")."\";");
				eval("\$buddy_list.= \"".pkTpl("profile_buddy_row")."\";");
				}
	
			if($buddy_list!='')
				{
				eval("\$profile_info.= \"".pkTpl("profile_buddy")."\";");
				}
			}

		if(intval($imstatus_info=imstatus())>0) 
			eval("\$profile_info.= \"".pkTpl("profile_newim")."\";");

		$online_time=formattime(pkGetUservalue('logtime'));
		$usernick=pkGetUservalueF('nick');

		$sitename=pkGetConfigF('site_name');

		$link_userprofile=pkLink('userinfo');
		$link_privatemessages=pkLink('privatemessages');
		$link_userprofile_edit=pkLink('userprofile','edit');
		$link_userprofile_friends=pkLink('userprofile','friends');
		$link_userprofile_options=pkLink('userprofile','options');


		eval("\$site_body.= \"".pkTpl("profile")."\";");
		break;
		#END case default
	}
?>