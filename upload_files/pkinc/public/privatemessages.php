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


if(!defined('pkFRONTEND') || pkFRONTEND!='public')
	die('Direct access to this location is not permitted.');


if(!intval(pkGetUservalue('id')))
	{
	pkEvent('access_refused');
	return;
	}

if(intval(pkGetUservalue('id')) && !pkGetUservalue('imoption'))
	{
	pkEvent('privatemessages_disabled',false);
	return;
	}	


$imid=(isset($_REQUEST['imid']) && intval($_REQUEST['imid'])>0) ? intval($_REQUEST['imid']) : ((isset($_REQUEST['imid']) && $_REQUEST['imid']=='new') ? 'new' : 0);
$view=(isset($_REQUEST['view']) ? (intval($_REQUEST['view'])>0 ? intval($_REQUEST['view']) : ($_REQUEST['view']=='receive' || $_REQUEST['view']=='send' ? $_REQUEST['view'] : 'all')) : 'all');


pkLoadFunc('user');
$user_navigation=pkUserNavigation();


if(intval($_REQUEST['userid'])>0)
	$_REQUEST['writeim']=1;

if(isset($_POST['action']))
	$ACTION=$_POST['action'];
elseif(isset($_REQUEST['show']))
	{
	$imid=addslashes($_REQUEST['show']);
	$ACTION=$_REQUEST['show'];
	}
else
	$ACTION='view';

if($ACTION==$_POST['cancel'])
	{
	header("location: include.php?path=privatemessages");
	exit();
	}
elseif($ACTION==$_POST['reply'] && isset($imid))
	{
	header("location: include.php?path=privatemessages&reply=".$imid."&writeim=1");
	exit();
	}
elseif($ACTION==$_POST['forward'] && isset($imid)) 
	{
	header("location: include.php?path=privatemessages&reply=".$imid."&forward=1&writeim=1");
	exit();
	}
elseif($ACTION==$_POST['next'])
	{
	header("location: include.php?path=privatemessages&imid=new");
	exit();
	}
elseif($ACTION==$_POST['delete'])
	{
	unset($sqlcommand);
	
	if(is_array($_POST['delim_received']))
		{
		foreach ($_POST['delim_received'] as $id)
			{
			if($sqlcommand)
				$sqlcommand.=" OR im_id='".intval($id)."'";
			else
				$sqlcommand="UPDATE ".pkSQLTAB_USER_PRIVATEMESSAGE." SET im_del=1 WHERE im_to='".$SQL->i(pkGetUservalue('id'))."' AND (im_id='".intval($id)."'";
			}
		
		if($sqlcommand)
			$SQL->query($sqlcommand.")");
		}
	
	unset($sqlcommand);
	
	if(is_array($_POST['delim_send']))
		{
		foreach($_POST['delim_send'] as $id)
			{
			if($sqlcommand)
				$sqlcommand.=" OR im_id='".intval($id)."'";
			else
				$sqlcommand="UPDATE ".pkSQLTAB_USER_PRIVATEMESSAGE." SET im_delautor=1 WHERE im_autor='".$SQL->i(pkGetUservalue('id'))."' AND (im_id='".intval($id)."'";
			}
		
		if($sqlcommand) 
			$SQL->query($sqlcommand.")");
		}
	
	if(isset($imid) && $_REQUEST['deltype']=='to')
		$SQL->query("UPDATE ".pkSQLTAB_USER_PRIVATEMESSAGE." SET im_del=1 WHERE im_to='".$SQL->i(pkGetUservalue('id'))."' AND im_id='".$imid."'");
	
	if(isset($imid) && $_REQUEST['deltype']=='autor')
		$SQL->query("UPDATE ".pkSQLTAB_USER_PRIVATEMESSAGE." SET im_delautor=1 WHERE im_autor='".$SQL->i(pkGetUservalue('id'))."' AND im_id='".$imid."'");
	
	pkHeaderLocation('privatemessages');
	}
	


#(auto)prune function
if(mt_rand(1,20)==7)
	{
	$SQL->query("DELETE FROM ".pkSQLTAB_USER_PRIVATEMESSAGE." WHERE (im_del=1 AND im_delautor=1) ".
		(pkGetConfig('user_pndelete') ? " OR im_time<'".(pkTIME - pkGetConfig('user_pndelete')*86400)."'" : ''));
	}


if($imid) 
	{
	if($imid=='new')
		$sqlcommand="im_to='".$SQL->i(pkGetUservalue('id'))."' AND im_view=0 ORDER BY im_id DESC";
	else
		$sqlcommand="im_id='".$imid."' AND (im_to='".$SQL->i(pkGetUservalue('id'))."' OR im_autor='".$SQL->i(pkGetUservalue('id'))."')";


	if($iminfo=$SQL->fetch_assoc($SQL->query("SELECT * FROM ".pkSQLTAB_USER_PRIVATEMESSAGE." WHERE ".$sqlcommand." LIMIT 1")))
		{
		pkLoadClass($BBCODE,'bbcode');
		
		
		if($iminfo['im_to']!=pkGetUservalue('id')) 
			$userid=$iminfo['im_to'];
		else
			{
			$userid=$iminfo['im_autor'];
			
			if($iminfo['im_view']!=1) 
				$SQL->query("UPDATE ".pkSQLTAB_USER_PRIVATEMESSAGE." SET im_view=1, im_viewtime='".pkTIME."' WHERE im_id='".$iminfo['im_id']."'");
		
			unset($imstatus_info);
			
			if(intval($imstatus_info=imstatus())>0)
				eval("\$im_nextnew= \"".pkTpl("imcenter_show_nextnew")."\";");
			
			eval("\$im_option= \"".pkTpl("imcenter_show_received_option")."\";");	
			}
		
		if($userid>0)
			$userinfo=$SQL->fetch_array($SQL->query("SELECT user_id, user_nick FROM ".pkSQLTAB_USER." WHERE user_id='".$userid."' LIMIT 1"));
		else
			$userinfo='system';
		
		
		
		if($userinfo!='system') 
			{
			$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
			eval("\$im_autor= \"".pkTpl("member_showprofil_textlink")."\";");
			}
		else
			$im_autor=$lang['system_message'];
			
		
		$im_time=formattime($iminfo['im_time']);
		$im_title=pkEntities($iminfo['im_title']);
		$im_text=$BBCODE->parse($iminfo['im_text'],0,$config['text_ubb'],$config['text_smilies'],$config['text_images'],1,pkGetConfig('user_imageresize'),pkGetConfig('user_textwrap'));
		
		if($im_option=='')
			{
			eval("\$im_option= \"".pkTpl("imcenter_show_delete_option")."\";");
			}
		
		$lang_pn_message_fromto = $iminfo['im_to']==$userid ? $lang['receiver'] : $lang['sender'];
		$lang_pn_message_title = $iminfo['im_to']==$userid ? $lang['private_message_sent'] : $lang['private_message_received'];
		
		eval("\$site_body.= \"".pkTpl("imcenter_show")."\";");
		}
	else
		{
		pkEvent('privatemessage_not_found');
		}
	}
elseif(isset($_REQUEST['writeim']))
	{
	if($ACTION==$_POST['send'])
		{
		pkLoadLang('error');
		
		$userinfo = array('user_id' => 0);
		
		$im_receiver=$_POST['im_receiver'];
		$im_title=$_POST['im_title'];
		$im_text=$_POST['content'];
		
		if(trim($im_receiver)!='')
			{
			$userinfo = $SQL->fetch_assoc($SQL->query("SELECT 
					user_id,
					user_imoption,
					user_imnotify,
					user_email,
					user_nick
				FROM ".pkSQLTAB_USER." 
				WHERE user_nick='".$SQL->f($im_receiver)."' LIMIT 1"));
			}
		
		if($userinfo['user_id'] && $userinfo['user_id']!=pkGetUserValue('id'))
			{
			if($userinfo['user_imoption']==1)
				{
				if(trim($im_title)!='' && trim($im_text)!='')
					{
					if($_POST['im_savemessage']==1)
						$delmessage=0;
					else
						$delmessage=1;
					
					$SQL->query("INSERT INTO ".pkSQLTAB_USER_PRIVATEMESSAGE." 
						(im_to, im_autor, im_title, im_text, im_time, im_delautor) VALUES 
						('".$userinfo['user_id']."','".$SQL->i(pkGetUservalue('id'))."','".$SQL->f($im_title)."','".$SQL->f($im_text)."',
							'".pkTIME."','".$delmessage."')");
					$newimid=$SQL->insert_id();
					
					
					pkLoadLang('email');
					
					$usernick = $userinfo['user_nick'];
					$author = pkGetUservalue('nick');
					$link = pkGetConfig('site_url').'/include.php?path=privatemessages&imid='.$newimid;
				
					$imnotify_text = pkGetSpecialLang('pncenter_mail_notify',$usernick,pkGetConfig('site_name'),$author,$im_title,$link);

					
					if($userinfo['user_imnotify']==1)
						{
						mailsender($userinfo['user_email'],$config['site_name'].': '.$lang['new_instantmessage'],$imnotify_text);
						}
					
					pkHeaderLocation('privatemessages');
					}
				else
					{
					# title or text empty
					$write_error=$lang['error_privatemessages_data_incomplete'];
					}
				}
			else
				{
				# receiver does not accept pn's
				$write_error=$lang['error_privatemessages_receiving_disabled'];
				}
			}
		elseif($userinfo['user_id'] == pkGetUserValue('id'))
			{ # cant send pns to yourself
			$write_error = $lang['error_privatemessages_no_send_self'];
			}
		else
			{
			# receiver not found
			$write_error=$lang['error_privatemessages_receiver_not_found'];
			}
		
		eval("\$write_error= \"".pkTpl("imcenter_write_error")."\";");
		}
	
	if(intval($_REQUEST['reply'])>0)
		{
		if($iminfo=$SQL->fetch_assoc($SQL->query("SELECT im_text, im_time, im_autor, im_title 
			FROM ".pkSQLTAB_USER_PRIVATEMESSAGE." 
			WHERE im_to='".$SQL->i(pkGetUservalue('id'))."' AND im_id='".$SQL->i($_REQUEST['reply'])."' LIMIT 1")))
				{
				list($iminfo_autor)=$SQL->fetch_row($SQL->query("SELECT user_nick FROM ".pkSQLTAB_USER." WHERE 
					user_id='".$iminfo['im_autor']."' LIMIT 1"));

				$iminfo_autor=$iminfo_autor;
				$iminfo_time=formattime($iminfo['im_time']);
				$iminfo_title=$iminfo['im_title'];
				$iminfo_text=pkEntities($iminfo['im_text']);
			
				if(isset($_REQUEST['forward']))
					$im_title="Fw: ".pkEntities($iminfo_title);
				else
					{
					$im_title="Re: ". pkEntities($iminfo_title);	 
					$im_autor=$iminfo_autor;
					}
				
				eval("\$iminfo_text= \"".pkTpl("imcenter_writeform_reply")."\";");
				}
			}
		
		if(intval($_REQUEST['postid'])>0 && intval($_REQUEST['threadid'])>0)
			{
			$im_title=pkGetLang('report_forumsposting_title');
			$iminfo_text='[url=include.php?path=forumsthread&threadid='.intval($_REQUEST['threadid']).'&postid='.intval($_REQUEST['postid']).']'.pkGetLang('report_forumsposting_namelink').'[/url]';
			}
		
		if(intval($_REQUEST['userid'])>0)
			{
			$userinfo=$SQL->fetch_array($SQL->query("SELECT user_nick FROM ".pkSQLTAB_USER." WHERE user_id='".intval($_REQUEST['userid'])."' LIMIT 1"));
			$im_autor=$userinfo['user_nick'];
			}
		
		if(trim($_POST['im_receiver'])=='')
			$im_receiver=pkEntities($im_autor);
		else
			$im_receiver=pkEntities($_POST['im_receiver']);
		
		if(trim($_POST['im_title'])=='')
			$im_title=$im_title;
		else 
			$im_title=pkEntities($_POST['im_title']);
		
		if(trim($_POST['content'])=='')
			$im_text=$iminfo_text;
		else
			$im_text=pkEntities($_POST['content']);
		
		if($_POST['im_savemessage']==1)
			$savemessage='checked';
		
		if($config['text_ubb']==1)
			eval("\$sign_format.= \"".pkTpl("format_text")."\";");
		
		if($config['text_smilies']==1)
			{
			$smilies=new smilies();
			$sign_format.=$smilies->getSmilies("1");
			}
		
		if($sign_format!="")
			eval("\$sign_format= \"".pkTpl("format_table")."\";");
		
		eval("\$site_body.= \"".pkTpl("imcenter_writeform")."\";");
		}
	else
		{
		pkLoadLang('profile');
		
		if($_REQUEST['order']=='time')
			{
			$order1="up";
			$order="ASC";
			}
		else
			{
			$order="DESC";
			}
		
		unset($sqlcommand);
		$counternew=0;
		$getiminfo=$SQL->query("SELECT 
			*
			FROM ".pkSQLTAB_USER_PRIVATEMESSAGE." 
			WHERE ((im_to='".$SQL->i(pkGetUservalue('id'))."' AND im_del<>1) OR 
				(im_autor='".$SQL->i(pkGetUservalue('id'))."' AND  im_delautor<>1))".
				(pkGetConfig('user_pndelete') ? " AND im_time>'".(pkTIME - pkGetConfig('user_pndelete')*86400)."'" : '')."
			ORDER by im_time ".$order);
		while($iminfo=$SQL->fetch_array($getiminfo))
			{
			if($iminfo['im_to']==pkGetUservalue('id') && $iminfo['im_del']!=1)
				{
				if($iminfo['im_view']==0)
					$counternew++;
				
				$iminfo_receive_hash[]=$iminfo;
				
				if($iminfo['im_autor']>0)
					{
					if($sqlcommand)
						$sqlcommand.=" OR user_id='".$iminfo['im_autor']."'";
					else
						$sqlcommand="SELECT * FROM ".pkSQLTAB_USER." WHERE user_id='".$SQL->i($iminfo['im_autor'])."'";
					}
				}
			
			if($iminfo['im_autor']==pkGetUservalue('id') && $iminfo['im_delautor']!=1)
				{
				$iminfo_send_hash[]=$iminfo;
				
				if($sqlcommand)
					$sqlcommand.=" OR user_id='".$iminfo['im_to']."'";
				else
					$sqlcommand="SELECT * FROM ".pkSQLTAB_USER." WHERE user_id='".$SQL->i($iminfo['im_to'])."'";
				}
			}
		
		if($sqlcommand)
			{
			$getuserinfo=$SQL->query($sqlcommand);
			while($userinfo=$SQL->fetch_array($getuserinfo))
				{
				$userinfo_hash[$userinfo['user_id']]=$userinfo;
				}
			}
		
	
		if($view!='send')
			{
			unset($receive_body);
			unset($row);
			
			
			if(is_array($iminfo_receive_hash))
				{
				foreach($iminfo_receive_hash as $iminfo)
					{
					$row=rowcolor($row);
					$iminfo_title=pkEntities(pkStringCut($iminfo['im_title'],25));
					$iminfo_time=formattime($iminfo['im_time']);
					
					if($iminfo['im_autor']>0)
						{
						$userinfo=$userinfo_hash[$iminfo['im_autor']];
						$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
						
						eval("\$iminfo_autor= \"".pkTpl("member_showprofil_textlink")."\";");
						}
					else
						$iminfo_autor=$lang['system_message'];	 
					
					if($iminfo['im_view']==1)
						{
						$im_viewtime=formattime($iminfo['im_viewtime']);
						
						eval("\$im_status= \"".pkTpl("imcenter_received_status_viewed")."\";");
						}
					
					else
						eval("\$im_status= \"".pkTpl("imcenter_received_status_new")."\";");	 
					
					eval("\$receive_body.= \"".pkTpl("imcenter_receive_row")."\";");
					}
				}
			
			if(!isset($receive_body))
				eval("\$receive_body= \"".pkTpl("imcenter_empty")."\";");
			else
				eval("\$receive_body= \"".pkTpl("imcenter_receive_body")."\";");
			
			eval("\$imcenter_receive= \"".pkTpl("imcenter_receive")."\";");
			}
		
		if($view!='receive')
			{
			unset($send_body);
			unset($row);
			
			if(is_array($iminfo_send_hash))
				{
				foreach($iminfo_send_hash as $iminfo)
					{
					$row=rowcolor($row);
					$iminfo_title=pkEntities(pkStringCut($iminfo['im_title'],25));
					$iminfo_time=formattime($iminfo['im_time']);
					$userinfo=$userinfo_hash[$iminfo['im_to']];
					$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
					
					if($iminfo['im_view']==1)
						{
						$im_viewtime=formattime($iminfo['im_viewtime']);
						
						eval("\$im_status= \"".pkTpl("imcenter_send_status_viewed")."\";");
						}
					else
						eval("\$im_status= \"".pkTpl("imcenter_send_status_new")."\";");
					
					eval("\$im_autor= \"".pkTpl("member_showprofil_textlink")."\";");	 
					eval("\$send_body.= \"".pkTpl("imcenter_send_row")."\";");
					}
				}
			if(!isset($send_body))
				eval("\$send_body= \"".pkTpl("imcenter_empty")."\";");
			else
				eval("\$send_body= \"".pkTpl("imcenter_send_body")."\";");
			
			eval("\$imcenter_send= \"".pkTpl("imcenter_send")."\";");
			}
		
		$h=array();
		
		if($view=='send') {$h[4]='<b>'; $h[5]='</b>';}
		elseif($view=='receive') {$h[2]='<b>'; $h[3]='</b>';}
		else {$h[0]='<b>'; $h[1]='</b>';}

		$countertotal=0;
		$countersend=0;
		$counterreceive=0;
		
		if(is_array($iminfo_receive_hash))
			$countertotal+=$counterreceive=count($iminfo_receive_hash);

		if(is_array($iminfo_send_hash))
			$countertotal+=$countersend=count($iminfo_send_hash);
		
		if($counternew>0)
			eval("\$imcenter_new= \"".pkTpl("imcenter_new")."\";");
		
		
		$lang_pncenter_message=pkGetLang('pncenter_message').
			pkGetSpecialLang('pncenter_message_delete',pkGetConfig('user_pndelete'));
	 
 		eval("\$site_body.= \"".pkTpl("imcenter")."\";");
		}
?>