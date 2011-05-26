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


if(!adminaccess('submit'))
	return pkEvent('access_forbidden');


if(isset($_POST['action']))
	$ACTION=$_POST['action'];
else
	$ACTION='view';


if(isset($_REQUEST['contentid']))
	{
	unset($editid);
	$contentid=$_REQUEST['contentid'];
	
	
	$contentinfo=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_CONTENT_SUBMIT." WHERE content_submited_id='".$contentid."'"));
	if(intval($contentinfo['content_submited_autorid'])>0)
		{
		$userinfo=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_USER." WHERE user_id='".$contentinfo['content_submited_autorid']."'"));
		}
	
	if($ACTION!='view')
		{
		pkLoadLang('adminemail');
		
		
		if($ACTION==$_POST['save'])
			{
			$SQL->query("DELETE FROM ".pkSQLTAB_CONTENT_SUBMIT." WHERE content_submited_id='".$contentid."'");
			$SQL->query("INSERT INTO ".pkSQLTAB_CONTENT." 
				(content_cat, content_time, content_title, content_autor, content_autorid, content_text, content_status, content_option, content_altdat) VALUES 
				('".intval($contentinfo['content_submited_catid'])."',
				 '".intval($contentinfo['content_submited_time'])."',
				 '".$SQL->f($contentinfo['content_submited_title'])."',
				 '".$SQL->f($userinfo['user_nick'])."',
				 '".intval($contentinfo['content_submited_autorid'])."',
				 '".$SQL->f($contentinfo['content_submited_text'])."',
				  0,
				 '".intval($contentinfo['content_submited_type'])."',
				 '".$SQL->f($contentinfo['content_submited_altdat'])."')");

			$editid = $SQL->insert_id();
			
			
			#submited content accepted and tranfered
			$link = pkGetConfig('site_url').'/include.php?path=content&contentid='.$editid;
			
			$email_text = pkGetSpecialLang('contentsubmited_mail_body_accepted',
							$contentinfo['content_submited_autor'],
							pkGetConfig('site_name'),
							$link,
							pkGetConfig('site_name'),
							pkGetConfig('site_url')
							);
			}
		elseif($ACTION==$_POST['delete'])
			{
			#submited content declined and deleted
			$SQL->query("DELETE FROM ".pkSQLTAB_CONTENT_SUBMIT." WHERE content_submited_id='".$contentid."'");
			
			$email_text = pkGetSpecialLang('contentsubmited_mail_body_declined',
							$contentinfo['content_submited_autor'],
							pkGetConfig('site_name'),							
							pkGetConfig('site_url')
							);
			}
		

		#global title for both mails (acceptd and declined)
		$email_title = pkGetSpecialLang('contentsubmited_mail_title',
						pkGetConfig('site_name')				
						);
		
		mailsender($userinfo['user_email'],$email_title,$email_text);


		if($editid)
			{
			pkHeaderLocation('contentcompose','','contentid='.$editid);
			}

		pkHeaderLocation('contentsubmited');
		}
	else
		{
		pkLoadClass($BBCODE,'bbcode');		
		
		if($contentinfo['content_submited_type']==1)
			$content_type=$lang['article'];
		elseif($contentinfo['content_submited_type']==2)
			$content_type=$lang['news'];
		elseif($contentinfo['content_submited_type']==3)
			$content_type=$lang['link'];
		elseif($contentinfo['content_submited_type']==4)
			$content_type=$lang['download'];
		else
			$content_type='-';
		
		if(intval($userinfo['user_id'])>0)
			{
			$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
			eval("\$info_autor= \"".pkTpl("content/submited_row_autorinfo")."\";");
			}
		else
			$info_autor=pkEntities($contentinfo['content_submited_autor']);
		
		$contentinfo['content_submited_title']=pkEntities($contentinfo['content_submited_title']);
		$contentinfo['content_submited_altdat']=pkEntities($contentinfo['content_submited_altdat']);		
		$submited_text=$BBCODE->parse($contentinfo['content_submited_text'],0,1,1,0);

		
		pkEvent('thirdparty_submission_warning','warning');
		
		eval("\$site_body.= \"".pkTpl("content/submited_check")."\";");
		}
	}
else
	{
	unset($sqlcommand);
	$getcontent=$SQL->query("SELECT * FROM ".pkSQLTAB_CONTENT_SUBMIT." ORDER by content_submited_time DESC");
	while($contentinfo=$SQL->fetch_array($getcontent))
		{
		$submited_hash[]=$contentinfo;
		
		if(intval($contentinfo['content_submited_autorid'])>0)
			{
			if($sqlcommand) 
				$sqlcommand.=" OR user_id='".$contentinfo['content_submited_autorid']."'";
			else 
				$sqlcommand="SELECT user_id, user_nick FROM ".pkSQLTAB_USER." WHERE user_id='".$contentinfo['content_submited_autorid']."'";
			}
		}
	
	if($sqlcommand)
		{
		$getautor=$SQL->query($sqlcommand);
		while($userinfo=$SQL->fetch_array($getautor))
			{
			$userinfo_hash[$userinfo['user_id']]=$userinfo;
			}
		}
	
	if(is_array($submited_hash))
		{
		foreach($submited_hash as $contentinfo)
			{
			$row=rowcolor($row);
			$submited_time=formattime($contentinfo['content_submited_time']);
			$contentinfo['content_submited_title']=pkEntities($contentinfo['content_submited_title']);
			
			if($contentinfo['content_submited_type']==1)
				$content_type=$lang['article'];
			elseif($contentinfo['content_submited_type']==2)
				$content_type=$lang['news'];
			elseif($contentinfo['content_submited_type']==3)
				$content_type=$lang['link'];
			elseif($contentinfo['content_submited_type']==4) 
				$content_type=$lang['download'];
			else 
				$content_type='-';
			
			$userinfo=$userinfo_hash[$contentinfo['content_submited_autorid']];
			if(intval($userinfo['user_id'])>0)
				{
				$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
				eval("\$info_autor= \"".pkTpl("content/submited_row_autorinfo")."\";");
				}
			else
				$info_autor=pkEntities($contentinfo['content_submited_autor']);
			
			eval("\$submited_row.= \"".pkTpl("content/submited_row")."\";");
			}
		}
	
	if($submited_row=='')
		eval("\$submited_row.= \"".pkTpl("content/submited_empty")."\";");
	
	pkEvent('thirdparty_submission_warning','warning');
	
	eval("\$site_body.= \"".pkTpl("content/submited")."\";");
	}
?>
