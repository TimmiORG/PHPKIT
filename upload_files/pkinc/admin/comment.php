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


if(!adminaccess('comment'))
	return pkEvent('access_forbidden');


$ACTION=(isset($_POST['action']) && !empty($_POST['action'])) ? $_POST['action'] : 'view';

$editid=(isset($_REQUEST['editid']) && intval($_REQUEST['editid'])>0) ? intval($_REQUEST['editid']) : 0;
$deleteid=(isset($_REQUEST['deleteid']) && intval($_REQUEST['deleteid'])>0) ? intval($_REQUEST['deleteid']) : 0;

$entries=(isset($_REQUEST['entries']) && intval($_REQUEST['entries'])>0) ? intval($_REQUEST['entries']) : 0;


if($ACTION==$_POST['delete'] && $_POST['delete_time']!="" && $_POST['delete_time']!="-1")
	{
	if(isset($_POST['delete_time']) && $_POST['delete_time']=='all')
		$SQL->query("DELETE FROM ".pkSQLTAB_COMMENT);
	else
		{
		$delete_time=(pkTIME-(intval($_POST['delete_time'])*86400)); 
		$SQL->query("DELETE FROM ".pkSQLTAB_COMMENT." WHERE comment_time<'".$delete_time."' AND comment_cat!='user'");
		}
	
	pkHeaderLocation('comment');
	}



if($deleteid)
	{
	$SQL->query("DELETE FROM ".pkSQLTAB_COMMENT." WHERE comment_id='".$deleteid."'");

	pkHeaderLocation('comment','','entries='.$entries);	
	}

if($editid)
	{
	if($ACTION==$_POST['save'] || $ACTION==$_POST['cancel'])
		{
		if($ACTION==$_POST['save'])
			$SQL->query("UPDATE ".pkSQLTAB_COMMENT." 
				SET comment_autor='".$SQL->f($_POST['comment_autor'])."',
					comment_text='".$SQL->f($_POST['comment_text'])."'
				WHERE comment_id='".$editid."'");
		
		pkHeaderLocation('comment','','entries='.$entries);
	   }
	  
	$commentinfo=$SQL->fetch_array($SQL->query("SELECT 
			comment_id,
			comment_text,
			comment_autor
		FROM ".pkSQLTAB_COMMENT."
		WHERE comment_id='".$editid."' LIMIT 1"));
	
	
	$editid=$commentinfo['comment_id'];
	$comment_autor=pkEntities($commentinfo['comment_autor']);
	$comment_text=pkEntities($commentinfo['comment_text']);
	
	eval("\$site_body.= \"".pkTpl("comment_editform")."\";");
	}
else
	{
	pkLoadClass($BBCODE,'bbcode');

	$epp=20;
	unset($sqlcommand);

	
	
	$counter=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_COMMENT));
	$getcomments=$SQL->query("SELECT * FROM ".pkSQLTAB_COMMENT." ORDER by comment_time DESC LIMIT ".$entries.",".$epp);
	while($comment=$SQL->fetch_array($getcomments))
		{
		$comment_hash[]=$comment;
		
		if($comment['comment_userid']>0)
			{
			if($sqlcommand)
				$sqlcommand.=" OR user_id='".$comment['comment_userid']."'";
			else
				$sqlcommand="SELECT user_nick, user_id FROM ".pkSQLTAB_USER." WHERE user_id='".$comment['comment_userid']."'";
			}
		}
	
	if(is_array($comment_hash))
		{
		if($sqlcommand) 
			{
			$getuserinfo=$SQL->query($sqlcommand);
			while($userinfo=$SQL->fetch_array($getuserinfo)) 
				{
				$userinfo_hash[$userinfo['user_id']]=$userinfo;
				}
			}
		
		foreach($comment_hash as $commentinfo)
			{
			if($commentinfo['comment_userid']>0)
				$userinfo=$userinfo_hash[$commentinfo['comment_userid']];
			else
				unset($userinfo);
			
			if($userinfo['user_nick']!='')
				{
				$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
				eval("\$comment_autor=\"".pkTpl("comment_autor")."\";");
				}
			else
				$comment_autor=pkEntities($commentinfo['comment_autor']);
			
			
			$row=rowcolor($row);
			$comment_ip=$commentinfo['comment_ip'];
			$comment_time=formattime($commentinfo['comment_time']);
			$comment_text=$BBCODE->parse($commentinfo['comment_text'],0,$config['comment_bb'],$config['comment_smilies'],$config['comment_images'],1,pkGetConfig('comment_imageresize'),pkGetConfig('comment_textwrap'));
			
			if($commentinfo[comment_cat]=="vote")
				$comment_link="path=pollarchive&vid=".$commentinfo['comment_subid'];
			else
				$comment_link="path=comment";
			
			eval("\$comment_row.= \"".pkTpl("comment_row")."\";");
			}
		}
	else
		eval("\$comment_row.= \"".pkTpl("comment_empty")."\";");
	
	
	$page_link=sidelinkfull($counter[0],$epp,$entries,"include.php?path=comment");
	
	$site_script_header.='<base href="'.pkGetConfigF('site_url').'/">';
	
	eval("\$site_body.= \"".pkTpl("comment")."\";");
	}
?>