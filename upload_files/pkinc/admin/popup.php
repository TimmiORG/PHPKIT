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


$pkDISPLAYPOPUP=true;

$modehash=array('smilies','morelinks','download','readfile','finduser','searchuser','preview','credits');
$mode=(isset($_REQUEST['mode']) && in_array($_REQUEST['mode'],$modehash)) ? $_REQUEST['mode'] : NULL;	

$window_w_size=(isset($_REQUEST['window_w_size']) && intval($_REQUEST['window_w_size'])>0) ? intval($_REQUEST['window_w_size']) : 0;
$window_h_size=(isset($_REQUEST['window_h_size']) && intval($_REQUEST['window_h_size'])>0) ? intval($_REQUEST['window_h_size']) : 0;
$img=(isset($_REQUEST['img']) && !empty($_REQUEST['img'])) ? rawurldecode($_REQUEST['img']) : NULL;
		
		
if($img)
	{
	$img_path=pkDIRROOT.$img;
	$img_wwwpath=pkDIRWWWROOT.$img;
	

	if(pkFilecheck($img_path) && ($imgsize=@getimagesize($img_path)))
		{
		if($imgsize)
			list($window_w_size,$window_h_size,$type,$htmlsize)=$imgsize;
		else
			$window_w_size=$window_h_size=0;
	
		$window_w_size+=50;
		$window_w_size=($window_w_size<800) ? 800 : $window_w_size;
				
		$window_h_size+=300;
		$window_h_size=($window_h_size<550) ? 550 : $window_h_size;

		eval("\$site_body.=\"".pkTpl("popup_image")."\";");
		}
	else
		pkEvent('not_a_valid_image');	
	}
elseif($mode=='credits')
	{
	pkLoadLang('adminevent');
	$site_body=pkGetLang('credits');
	}
elseif($mode=='download')
	{
	$option=(isset($_REQUEST['option'])) ? $_REQUEST['option'] : '';
	$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';
	
	
	if($ACTION==$_POST['upload'] || $ACTION==$_POST['save']) 
		{
		if($ACTION==$_POST['upload'])
			{
			$UPLOAD=new UPLOAD();
			$UPLOAD->images($_FILES['upload_file'],"../".$config['content_downloadpath'],'');
			}
		
		else
			rename("../".$config['content_downloadpath'].'/'.$_POST['file_name'],"../".$config['content_downloadpath'].'/'.$_POST['rename_file']);
		
		pkHeaderLocation('popup','download','window_w_size='.$window_w_size.'&window_h_size='.$window_h_size.'&option='.$option);
		}
	
	if(isset($_REQUEST['unlink']))
		{
		$file="../".$config['content_downloadpath']."/".$_REQUEST['unlink'];
		
		if(filecheck($file))
			@unlink($file);
		}
	
	$dir="../".$config['content_downloadpath'];
	if(!is_dir($dir))
		{
		echo 'DIR <b>'.$config['content_downloadpath'].'</b> not found';
		exit();
		}
	
	
	$filelist=array();
	
	$a=opendir($dir);
	while($info=readdir($a))
		{
		if($info=='.' || $info=='..' || $info=='index.php') 
			continue;
		
		$filecheck="../".$config['content_downloadpath']."/".$info;
		$filepath=$config['content_downloadpath']."/".$info;
		if(filecheck($filecheck))
			{
			$size=ceil(filesize($filecheck)/1024);
			$filelist[$info]=array('path'=>$info,'size'=>FileSizeExt($filecheck,"B"),'truesize'=>$size); 
			}
		}
	closedir($a);
	
	if(is_array($filelist))
		{
		ksort($filelist);
		foreach($filelist as $name=>$info)
			{
			$row=rowcolor($row);
			$info_path=urlencode($info['path']);
			
			eval("\$download_row.= \"".pkTpl("download_row")."\";");
			}
		
		if($_REQUEST['rename'] && filecheck("../".$config['content_downloadpath']."/".urldecode($_REQUEST['rename'])))
			{
			$file_name=$_REQUEST['rename'];
			eval("\$download_head= \"".pkTpl("download_rename")."\";");
			}
		else
			eval("\$download_head= \"".pkTpl("download_upload")."\";");
		}
	
	eval("\$site_body.= \"".pkTpl("download")."\";");		
	}
elseif($mode=='finduser')
	{
	$option=(isset($_REQUEST['option'])) ? $_REQUEST['option'] : 'default';
	
	if($option=='register')
		$option_element='notify_register_i';
	elseif($option=='submit')
		$option_element='notify_submit_i';
	elseif($option=='gbook')
		$option_element='notify_gbook_i';
	elseif($option=='forum')
		$option_element='notify_forum_i';
	elseif($option=='comment')
		$option_element='notify_comment_i';
	else
		{
		$option='default';
		$option_element='default';
		}
	
	if($_GET['search_user']!='')
		{
		$usercount=0;
		$search_result='';
		$search_user=$_GET['search_user'];
		
		$getuserinfo=$SQL->query("SELECT user_nick, user_id FROM ".pkSQLTAB_USER." 
			WHERE user_nick LIKE '%".$SQL->f($search_user)."%' ORDER by user_nick ASC LIMIT 50");
		while($userinfo=$SQL->fetch_array($getuserinfo))
			{
			$usercount++;
			$search_result.='<option value="'.$userinfo['user_id'].'">'.pkEntities($userinfo['user_nick']).'</option>';
			}
		
		
		if($search_result!='')
			{
			eval("\$search_result=\"".pkTpl("finduser_result")."\";");
			
			if($usercount==50)
				eval("\$search_result.=\"".pkTpl("finduser_notifcation")."\";");
			} 
		}
		

	$sqlcommand=$buddy_list='';		
	$getbuddies=$SQL->query("SELECT buddy_friendid FROM ".pkSQLTAB_USER_FRIENDLIST." WHERE buddy_userid='".$SQL->i(pkGetUservalue('id'))."'");
	while($buddy=$SQL->fetch_array($getbuddies))
		{
		$buddy_chache[$buddy['buddy_friendid']]=$buddy;
		$sqlcommand.=(empty($sqlcommand) ? '' : ',').$buddy['buddy_friendid'];
		}
	
	if(!empty($sqlcommand))
		{
		$getuserinfo=$SQL->query("SELECT user_nick, user_id FROM ".pkSQLTAB_USER." WHERE user_id IN(".$sqlcommand.")");
		while($userinfo=$SQL->fetch_assoc($getuserinfo))
			$buddy_list.='<option value="'.$userinfo['user_id'].'">'.pkEntities($userinfo['user_nick']).'</option>';
			
		if($buddy_list!='')
			eval("\$buddy_list=\"".pkTpl("finduser_buddylist")."\";");
		}
	
	$search_user=pkEntities($search_user);
		
	eval("\$site_body.=\"".pkTpl("finduser")."\";");	
	}
elseif($mode=='morelinks')
	{
	$link=array();
	$link['start']='start';
	$link['contact']='contact';
	$link['team']='team';
	$link['member']='userslist';
	$link['forum_main']='forumsdisplay';
	$link['forum_search']='forumsearch';
	$link['forum_team']='forumsteam';
	$link['content_overview']='contentarchive';
	$link['content_submit_1']='contentsubmit&type=1';
	$link['content_news']='news';
	$link['content_news_last']='news&contentid=new';
	$link['content_news_archive']='contentarchive&type=2';
	$link['content_news_submit']='contentsubmit&type=2';
	$link['content_links']='link';
	$link['content_links_sbumit']='contentsubmit&type=3';
	$link['content_downloads']='download';
	$link['gbook_view']='guestbook';
	$link['gbook_sign']='guestbook&mode=sign';
	$link['vote_archive']='pollarchive';
	$link['faq']='faq';

	if(isset($_REQUEST['option']) && $_REQUEST['option']=='full')
		{
		$a='?path=';
		$return_form='newlink';
		$return_element='link_link';
		$return_code='code';
		}
	else 
		{
		$a='';
		$return_form='config';
		$return_element='site_frontpage';
		$return_code='opener.document.config.site_frontpage.value + "\n" + code';
		$morelinkswindow_frontpage='';
		}
	
	
	$morelinkswindow_mycontent='';
	$getcontent=$SQL->query("SELECT
			content_id,
			content_title
		FROM ".pkSQLTAB_CONTENT."
		WHERE content_option=0 AND 
			content_status=1 
		ORDER by content_title ASC");
	while($content=$SQL->fetch_array($getcontent))
		{
		$content_title=pkEntities($content['content_title']);
		$content_link=$a.'content&contentid='.$content['content_id'];
		
		eval("\$morelinkswindow_mycontent.= \"".pkTpl("morelinkswindow_mycontent")."\";");
		}

	if($a!='')
		{
		foreach($link as $k=>$v) 
			{
			$link[$k]=$a.$v;
			}
		
		eval("\$morelinkswindow_frontpage= \"".pkTpl("morelinkswindow_frontpage")."\";");
		eval("\$morelinkswindow_misc= \"".pkTpl("morelinkswindow_misc")."\";");
		}
	
	eval("\$site_body.= \"".pkTpl("morelinkswindow")."\";");	
	}
elseif($mode=='preview')
	{
	if(!isset($_POST['previewtext']))
		{
		$site_body_onload=' onLoad="getText(); submitText();"';
		
		eval("\$site_body.= \"".pkTpl("preview_pre")."\";");
		}
	else 
		{
		pkLoadClass($BBCODE,'bbcode');
		
		$site_refresh='<base href="'.pkGetConfig('site_url').'/">';		
		$text=$BBCODE->parse($_POST['previewtext'],1,1,1,1,0);
		
		eval("\$site_body.=\"".pkTpl("preview")."\";");
		}	
	}
elseif($mode=='readfile')
	{
	$optionhash=array('faq','content');
	$option=(isset($_REQUEST['option']) && in_array($_REQUEST['option'], $optionhash)) ? $_REQUEST['option'] : NULL;

	if($option=='faq')
		{
		$return_form='faqcat';
		$return_element='content';
		}
	elseif($option=='content')
		{
		$return_form='myform';
		$return_element='content';
		}
	
	if(isset($_POST['action']) && $_POST['action']==$_POST['read_in'])
		{
		if(filecheck($_FILES['readfile']['tmp_name']))
			$showtext=pkEntities(implode('',(file($_FILES['readfile']['tmp_name']))));
		
		eval("\$showtext=\"".pkTpl("readfilewindow_showtext")."\";");
		}
	
	eval("\$site_body.=\"".pkTpl("readfilewindow")."\";");	
	}
elseif($mode=='smilies')
	{
	$smilies=new smilies();
	$format_smilies=$smilies->getSmilies('0',1);
	eval("\$site_body.=\"".pkTpl("smiliewindow")."\";");
	}
elseif($mode=='searchuser')
	{
	pkLoadLang('profile');
	$optionlist=$searchstr=$result='';

	if($ENV->_post_action('search'))
		{
		if($searchstr=$ENV->_post('searchstr'))
			{
			$result=$SQL->query("SELECT user_id,user_nick FROM ".pkSQLTAB_USER." WHERE 
				user_nick LIKE '%".$SQL->f($searchstr)."%' OR user_name LIKE '%".$SQL->f($searchstr)."%'
				ORDER BY user_nick ASC
				LIMIT 50");
			}
		}
	else
		{
		$result=$SQL->query("SELECT u.user_id,u.user_nick FROM ".pkSQLTAB_USER_FRIENDLIST." AS b
			LEFT JOIN ".pkSQLTAB_USER." AS u ON (u.user_id=b.buddy_friendid)
			WHERE b.buddy_userid=".$SQL->i(pkGetUservalue('id'))."
			ORDER BY user_nick ASC");
		}

	if($result)
		while(list($id,$nick)=$SQL->fetch_row($result))
			$optionlist.='<option value="'.$id.'">'.pkEntities($nick).'</option>';

	$form_action=pkLink('popup','searchuser');
	$L_search_users=pkGetLang('search_users');
	$L_select_users=pkGetLang('select_users');
	$L_add=pkGetLang('add');
	$L_search=pkGetLang('search');
	$searchstr=pkEntities($searchstr);

	$site_header_script.='<script src="fx/form.js" type="text/javascript"></script>';

	eval("\$site_body.=\"".pkTpl('popup_searchuser')."\";");
	}
else
	{
	pkEvent('page_not_found');
	return;
	}

$site_header_script.='<script src="fx/popup.js" type="text/javascript"></script>';

if(empty($site_body_onload))
	$site_body_onload=' onLoad="pkWinOnTop(); pkWinResize(\''.$window_w_size.'\',\''.$window_h_size.'\');"';		
?>