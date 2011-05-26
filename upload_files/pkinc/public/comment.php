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


unset($event);
$comcathash = array('gb','cont','user','vote');

$comcat	= (isset($_REQUEST['comcat']) && in_array($_REQUEST['comcat'],$comcathash)) ? $_REQUEST['comcat'] : (isset($comcat) && in_array($comcat,$comcathash) ? $comcat : '');
$subid	= (isset($_REQUEST['subid']) && intval($_REQUEST['subid'])>0) ? intval($_REQUEST['subid']) : (isset($subid) && intval($subid)>0 ? intval($subid) : 0); 
$ACTION	= isset($_POST['action']) ? $_POST['action'] : 'view';


if(adminaccess('comment'))
	{
	$delcomment=(isset($_REQUEST['delcomment']) && intval($_REQUEST['delcomment'])>0) ? intval($_REQUEST['delcomment']) : 0;
	
	if($delcomment)
		{
		$SQL->query("DELETE FROM ".pkSQLTAB_COMMENT." WHERE comment_id='".$delcomment."' LIMIT 1");
		}
	}


if(isset($_POST['save']) && $ACTION==$_POST['save'])
	{
	if(($config['comment_register']==1 && !pkGetUservalue('id')) || empty($comcat))
		{
		pkHeaderLocation('','','event=access_refused');
		}
	
	
	$floodcontrol=pkTIME-($config['comment_floodctrl']*60);
	$infocount=$SQL->fetch_array($SQL->query("SELECT COUNT(*) 
		FROM ".pkSQLTAB_COMMENT."
		WHERE comment_ip='".$SQL->f($ENV->getvar('REMOTE_ADDR'))."' AND 
			comment_subid='".intval($_POST['subid'])."' AND
			comment_userid='".intval(pkGetUservalue('id'))."' AND 
			comment_time>'".$floodcontrol."'
		LIMIT 1"));

	if(!pkCaptchaCodeValid($ENV->_post(pkCAPTCHAVARNAME)))
		{
		$event='securitycode_invalid';
		}
	elseif(trim($_POST['content'])=='' || trim($_POST['comment_autor'])=='')
		{
		$event='comment_data_missing';
		}
	elseif(strlen(trim($_POST['content']))>pkGetConfig('comment_maxchars'))
		{
		$event='comment_length';
		}
	elseif($infocount[0]>0)
		{
		$event='comment_wait_loop';
		}
	elseif(!checkusername($_POST['comment_autor'],1))
		{
		$event='name_in_use';
		}
	elseif($comcat=='gb' && !pkGetConfig('gbook_commenteod'))
		{
		$event='function_disabled';
		}
	else
		{
		pkLoadLang('email');
		
		$SQL->query("INSERT INTO ".pkSQLTAB_COMMENT." 
			(comment_cat,comment_subid,comment_time,comment_autor,comment_ip,comment_text, comment_userid) 
			VALUES 
			('".$SQL->f($comcat)."',
			 '".$SQL->i($subid)."',
			 '".pkTIME."',
			 '".$SQL->f($_POST['comment_autor'])."',
			 '".$SQL->f($ENV->getvar('REMOTE_ADDR'))."',
			 '".$SQL->f($_POST['content'])."',
			 '".$SQL->i(pkGetUservalue('id'))."'
			)");


		$current_path = $ENV->getvar('QUERY_STRING');
		$link = pkLinkMail('','',$current_path);
		$autor = $_POST['comment_autor'];
		
		$mail_title = pkGetConfig('site_name').' - '.pkGetLang('new_comment');
		$mail_text = pkGetSpecialLang('comment_mail_notify',
						pkGetConfig('site_name'),
						$autor,
						$link
						);

		notifymail('comment',$mail_title,$mail_text);
		
			
		$pn_title = pkGetLang('new_comment');
		$pn_text = pkGetSpecialLang('new_comment_pn_notify',
					$autor,
					$current_path
					);
			
		notifyim('comment',$pn_title,$pn_text);


		if($comcat=='gb')
			{
			$email = $SQL->fetch_assoc($SQL->query("SELECT 
					gbook_notify,
					gbook_email,
					gbook_autor 
				FROM ".pkSQLTAB_GUESTBOOK."
				WHERE gbook_id='".$subid."'
				LIMIT 1"));
	
			if($email['gbook_notify']==1)
				{
				$link = pkLinkMail('comment','','subid='.$subid.'&comcat='.$comcat);
				$comment_text = $_POST['content'];
				
				$email_title = pkGetSpecialLang('guestbook_comment_mail_title',
								pkGetConfig('site_name')
								);

				$email_text = pkGetSpecialLang('guestbook_comment_mail_text',
								$email['gbook_autor'],
								pkGetConfig('site_name'),
								$comment_autor,
								$comment_text,
								pkGetConfig('site_name'),
								$link,
								pkGetConfig('site_name'),
								pkGetConfig('site_url')
								);								
					

				
				mailsender(mailalias($email['gbook_email'],$email['gbook_autor']),$email_title,$email_text);
				}
			}
		
		pkHeaderLocation('','','event=constribution_thank&moveto='.urlencode($ENV->getvar('QUERY_STRING')));
		}
	}


pkLoadClass($BBCODE,'bbcode');
pkLoadFunc('user');


if($comcat=="gb")
	{
	if(!pkGetConfig('gbook_commenteod'))
		{
		pkEvent('function_disabled');
		return;
		}	
	
	$getinfo=$SQL->query("SELECT * FROM ".pkSQLTAB_GUESTBOOK." WHERE gbook_id='".$SQL->f($subid)."'");
	while($gbookinfo=$SQL->fetch_array($getinfo))
		{
		$row=rowcolor($row);
		
		$userinfo=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_USER." WHERE user_id='".$SQL->i($gbookinfo['gbook_userid'])."' LIMIT 1"));
		
		if($gbookinfo['gbook_userid']>0 && $userinfo)
			{
			$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
			

			eval("\$gbook_autor= \"".pkTpl("member_showprofil_textlink")."\";");
			
			if(isonline($userinfo['user_id']))
				eval("\$info_os= \"".pkTpl("member_os_online")."\";");
			else
				eval("\$info_os= \"".pkTpl("member_os_offline")."\";");
			
			if($userinfo['user_emailshow']==1)
				eval("\$gbook_email= \"".pkTpl("member_email_iconlink")."\";");
			
			if($userinfo['user_icqid']>0)
				eval("\$gbook_icq= \"".pkTpl("member_icq_iconlink")."\";");
			
			if(trim($userinfo['user_hpage'])!='')
				{
				if(ereg("http://",$userinfo['user_hpage']))
					$info_link=pkEntities($userinfo['user_hpage']);
				else
					$info_link='http://'.pkEntities($userinfo['user_hpage']);
				
				eval("\$gbook_hpage= \"".pkTpl("member_hpage_iconlink")."\";");
				}
			
			$info_sig=pkUserSignature($userinfo['user_sig']);				
			}
		else
			{
			$userinfo['user_nick']=pkEntities($gbookinfo['gbook_autor']);
			$gbook_autor=pkEntities($gbookinfo['gbook_autor']);
			
			if($gbookinfo['gbook_check']==1 && $gbookinfo['gbook_email']!='')
				{
				$userinfo['user_email']=$gbookinfo['gbook_email'];
				
				eval("\$gbook_email= \"".pkTpl("member_email_iconlink")."\";");
				}
			
			if($gbookinfo['gbook_hpage']!='')
				{
				if(ereg('http://',$gbookinfo['gbook_hpage']))
					$info_link=pkEntities($gbookinfo['gbook_hpage']);
				else
					$info_link='http://'.pkEntities($gbookinfo['gbook_hpage']);

				eval("\$gbook_hpage= \"".pkTpl("member_hpage_iconlink")."\";");
				}
			
			if($gbookinfo['gbook_icqnr']>0)
				{
				$userinfo['user_icqid']=$gbookinfo['gbook_icqnr'];
				
				eval("\$gbook_icq= \"".pkTpl("member_icq_iconlink")."\";");
				}
			
			eval("\$info_os= \"".pkTpl("guest_os_icon")."\";");
			}
		
		$gbook_title=pkEntities($gbookinfo['gbook_title']);
		$gbook_text=$BBCODE->parse($gbookinfo['gbook_text'],0,$config['gbook_ubb'],$config['gbook_smilies'],$config['gbook_images'],1,pkGetConfig('guestbook_imageresize'),pkGetConfig('guestbook_textwrap'));
		$gbook_time=formattime($gbookinfo['gbook_time']);
		
		
		if(pkGetUservalue('admin')=='admin')
			{
			if($gbookinfo['gbook_ip']==0)
				eval("\$gbook_ip= \"".pkTpl("guestbook/gbook_ipno_iconlink")."\";");
			else
				eval("\$gbook_ip= \"".pkTpl("guestbook/gbook_ip_iconlink")."\";");
			
			eval("\$gbook_admin= \"".pkTpl("guestbook/gbook_admin_iconlink")."\";");
			}
		
		if($config['gbook_commenteod']==1)
			{
			$ccounter=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_COMMENT." WHERE comment_cat='gb' and comment_subid='".$gbookinfo['gbook_id']."'"));
			

			if($ccounter[0]>1)
				$gbook_comment=$ccounter[0].' '.$lang['comments'];
			elseif($ccounter[0]==1) 
				$gbook_comment='1 '.$lang['comment'];
			else
				$gbook_comment=$lang['no_comments'];
			
			eval("\$gbook_comment= \"".pkTpl("guestbook/viewgb_comment_textlink")."\";");
			}
		
		eval("\$gbook_row= \"".pkTpl("guestbook/viewgb_row")."\";");
		
		unset($gbook_email);
		unset($gbook_hpage);
		unset($gbook_icq);
		unset($gbook_admin);
		unset($gbook_ip);
		unset($gbook_comment);
		unset($info_sig);
		}
	
	eval("\$site_body.= \"".pkTpl("comment_gbook_body")."\";");
	}
elseif($comcat=="cont")
	{
	$info=$SQL->fetch_array($SQL->query("SELECT 
			content_option,
			content_status
		FROM ".pkSQLTAB_CONTENT." 
		WHERE content_id='".$subid."'
		LIMIT 1"));
	
	if($info['content_status']!=1) 
		{
		pkEvent('function_disabled');
		return;
		}
	else 
		{
		if($info['content_option']==1) 
			{
			$contentid=$subid;
			include(pkDIRPUBLIC.'article'.pkEXT);
			}
		elseif($info['content_option']==2)
			{
			$contentid=$subid;
			include(pkDIRPUBLIC.'news'.pkEXT);
			}
		elseif($info['content_option']==3)
			{
			$type=3;
			$contentid=$subid;
			include(pkDIRPUBLIC.'contentarchive'.pkEXT);
			}
		elseif($info['content_option']==4)
			{
			$contentid=$subid;
			include(pkDIRPUBLIC.'download'.pkEXT);			
			}
		}
	}


$comment_order="DESC";
if($comcat=="user")
	{
	$comment_title=$lang['sign'];
	$comment_type=$lang['previous_entries'];
	}
else
	{
	$comment_title=$lang['write_comment'];
	$comment_type=$lang['comments'];
	
	if($config['comment_order']=="ASC")
		$comment_order=$config['comment_order'];
	}

$sqlcommand='';
$getcomment=$SQL->query("SELECT * FROM ".pkSQLTAB_COMMENT." where comment_subid='".$SQL->f($subid)."' and comment_cat='".$SQL->f($comcat)."' ORDER BY comment_time ".$comment_order);
while($comment=$SQL->fetch_array($getcomment))
	{
	$comment_hash[$comment['comment_id']]=$comment;
	
	if($comment['comment_userid']!=0)
		{
		if($sqlcommand)
			$sqlcommand.=" OR user_id='".$comment['comment_userid']."'";
		else
			$sqlcommand="SELECT user_id, user_nick FROM ".pkSQLTAB_USER." WHERE user_id='".$comment['comment_userid']."'";
		}
	}


if($sqlcommand)
	{
	$getuserinfo=$SQL->query($sqlcommand);
	while($userinfo=$SQL->fetch_array($getuserinfo))
		$userinfo_hash[$userinfo['user_id']]=$userinfo;
	}


if(is_array($comment_hash))
	{
	unset($comment_row);
	
	if($comment_order=="DESC")
		$comment_counter=count($comment_hash);
	else
		$comment_counter=1;
	
	foreach($comment_hash as $comment)
		{
		$row=rowcolor($row);  
		
		if(adminaccess('comment'))
			{
			if($comcat=="user")
				{
				$comment_path="userguestbook";
				}
			else
				{
				$comment_path="comment&subid=$subid&comcat=$comcat";
				}
			
			eval("\$comment_option= \"".pkTpl("comment_delete_iconlink")."\";");
			
			
			if($comment['comment_ip']!="")
				{
				eval ("\$comment_option.= \"".pkTpl("comment_ip_iconlink")."\";");
				}
			else
				{
				eval ("\$comment_option.= \"".pkTpl("comment_ipno_iconlink")."\";");
				}
			}
		
		
		$userinfo=$userinfo_hash[$comment['comment_userid']];
		
		if($userinfo['user_id']>0)
			{
			$userinfo['user_nick']=pkEntities($userinfo['user_nick']);

			eval("\$info_nick= \"".pkTpl("member_showprofil_textlink")."\";");
			
			if(isonline($userinfo['user_id']))
				eval("\$comment_option.= \"".pkTpl("member_os_online")."\";");
			else
				eval("\$comment_option.= \"".pkTpl("member_os_offline")."\";");
			}
		else
			{
			eval("\$comment_option.= \"".pkTpl("guest_os_icon")."\";");
			
			$info_nick=pkEntities($comment['comment_autor']);
			}
		

		$comment_text=$BBCODE->parse($comment['comment_text'],0,pkGetConfig('comment_bb'),pkGetConfig('comment_smilies'),pkGetConfig('comment_images'),1,pkGetConfig('comment_imageresize'),pkGetConfig('comment_textwrap'));
		$comment_time=formattime($comment['comment_time']);
		
		eval("\$comment_row.= \"".pkTpl("comment_comments_row")."\";");
		
		if($comment_order=="DESC")
			$comment_counter--;
		else
			$comment_counter++;
		
		unset($comment_option);
		unset($comment_text);
		}
	
	eval("\$site_body.= \"".pkTpl("comment_comments_body")."\";");
	}


if($comcat=='user')
	{
	$comment_title="Ins G&auml;stebuch eintragen";
	$form_action=pkLink('userguestbook','','id='.$subid);
	
	
	if($config['text_ubb']==1)
		{
		eval ("\$sign_format.= \"".pkTpl("format_text")."\";");
		}
	
	if($config['text_smilies']==1)
		{
		$smilies=new smilies();
		$sign_format.=$smilies->getSmilies("1");
		}
	
	if($sign_format!="")
		eval("\$sign_format= \"".pkTpl("format_table")."\";"); 


	$usernick=pkGetUservalueF('nick');	
	
	if(pkGetUservalue('id'))
		{		
		eval("\$comment_autorinfo=\"".pkTpl("comment_writeform_autorinfo")."\";");
		}
	else
		{
		eval("\$comment_autorinfo=\"".pkTpl("comment_writeform_autorform")."\";");
		}

	
	if(isset($event))
		{
		if($event==46)
			$commentlength=strlen($_POST['content']);
		
		$site_body.='<br />';

		pkEvent($event,false);
		}
	
	$comment_text=pkEntities($_POST['content']);
	$captcha=pkCaptchaField();	
	
	eval("\$site_body.= \"".pkTpl("comment_writeform")."\";");
	}
else
	{
	if($config['comment_register']!=1 || ($config['comment_register']==1 && pkGetUservalue('id')))
		{
		if($config['comment_bb']==1)
			eval("\$sign_format.= \"".pkTpl("format_text")."\";");
		
		if($config['comment_smilies']==1)
			{
			$smilies=new smilies();
			$sign_format.=$smilies->getSmilies(1);
			}

		
		if($comcat=='vote')
			$form_action=pkLink('pollarchive','','vid='.$subid.'&comcat='.$comcat);
		else
			$form_action=pkLink('comment','','subid='.$subid.'&comcat='.$comcat);
	
		
		if($sign_format!="")
			eval("\$sign_format=\"".pkTpl("format_table")."\";"); 


		$usernick=pkGetUservalueF('nick');
		
		if(pkGetUservalue('id'))
			{
			eval("\$comment_autorinfo= \"".pkTpl("comment_writeform_autorinfo")."\";");
			}
		else
			{
			eval("\$comment_autorinfo= \"".pkTpl("comment_writeform_autorform")."\";");
			}
		
		if(isset($event))
			{
			if($event==46)
				$commentlength=strlen($_POST['content']);
			
			pkEvent($event,false);
			}
		
		$comment_text=pkEntities($_POST['content']);
		$captcha=pkCaptchaField();		
	
		eval("\$site_body.= \"".pkTpl("comment_writeform")."\";");
		}
	else
		{
		$current_path=pkEntities($ENV->getvar('QUERY_STRING'));
		
		eval("\$site_body.= \"".pkTpl("comment_autorlogin")."\";");
		}
	}
?>