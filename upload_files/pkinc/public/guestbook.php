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


$modehash=array('delete','edit','print','sign');
$mode=(isset($_REQUEST['mode']) && in_array($_REQUEST['mode'],$modehash)) ? $_REQUEST['mode'] : NULL;


switch($mode)
	{
	case 'delete' :
		if(!adminaccess('gbdelete'))
			{
			pkEvent('access_refused');
			return;
			}
		
		
		$id=(isset($_REQUEST['id']) && intval($_REQUEST['id'])>0) ? intval($_REQUEST['id']) : 0;
		$ACTION=(isset($_POST['action'])) ? $_REQUEST['action'] : 'view';
			
			
		if($ACTION!='view' || !isset($id)) 
			{
			if($ACTION==$_POST['Yes'])
				{
				$SQL->query("DELETE FROM ".pkSQLTAB_GUESTBOOK." WHERE gbook_id='".$id."' LIMIT 1");
				$SQL->query("DELETE FROM ".pkSQLTAB_COMMENT." WHERE comment_cat='gb' and comment_subid='".$id."'");
				}
			
			pkHeaderLocation('guestbook');
			}
			
		pkLoadClass($BBCODE,'bbcode');
		pkLoadFunc('user');		
	
		$gbookinfo=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_GUESTBOOK." WHERE gbook_id='".$id."' LIMIT 1"));
		$row='odd';
		
		if(intval($gbookinfo['gbook_userid'])>0)
			{
			$userinfo=$SQL->fetch_array($SQL->query("SELECT * 
				FROM ".pkSQLTAB_USER." 
				WHERE user_id='".$gbookinfo['gbook_userid']."'
				LIMIT 1"));
		
		
			$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
		
			eval("\$gbook_autor= \"".pkTpl("member_showprofil_textlink")."\";");
				
				
			if(intval($userinfo['expire'])>0 && ($config['user_ghost']!=1 || ($config['user_ghost']==1 && $userinfo['user_ghost']!=1))) 
				eval("\$info_os= \"".pkTpl("member_os_online")."\";");
			else
				eval("\$info_os= \"".pkTpl("member_os_offline")."\";");
		
			if($userinfo['user_emailshow']==1)
				eval("\$gbook_email= \"".pkTpl("member_email_iconlink")."\";");
				
			if(intval($userinfo['user_icqid'])>0)
				eval("\$gbook_icq= \"".pkTpl("member_icq_iconlink")."\";");
				
			if($userinfo['user_hpage']!="")
				{
				if(ereg("http://",$userinfo['user_hpage']))
					$info_link=pkEntities($userinfo['user_hpage']);
				else
					{
					$info_link="http://".pkEntities($userinfo['user_hpage']);
					}
					
				eval("\$gbook_hpage= \"".pkTpl("member_hpage_iconlink")."\";");
				}
				

			$info_sig=pkUserSignature($userinfo['user_sig']); 
			}
		else
			{
			$gbook_autor=$userinfo['user_nick']=pkEntities($gbookinfo['gbook_autor']);
		
				
			if(trim($gbookinfo['gbook_email'])!='')
				{
				$userinfo['user_email']=pkEntities($gbookinfo['gbook_email']);
				eval("\$gbook_email= \"".pkTpl("member_email_iconlink")."\";");
				}
				
			if(trim($gbookinfo['gbook_hpage'])!='')
				{
				if(ereg("http://",$gbookinfo['gbook_hpage']))
					$info_link=pkEntities($gbookinfo['gbook_hpage']);
				else
					$info_link="http://".pkEntities($gbookinfo['gbook_hpage']);
				
				eval("\$gbook_hpage= \"".pkTpl("member_hpage_iconlink")."\";");
				}
				
			if(intval($gbookinfo['gbook_icqnr'])>0)
				{
				$userinfo['user_icqid']=intval($gbookinfo['gbook_icqnr']);
				
				eval("\$gbook_icq= \"".pkTpl("member_icq_iconlink")."\";");
				}
				
			eval("\$info_os= \"".pkTpl("guest_os_icon")."\";");
			}
			
		$gbook_title=pkEntities($gbookinfo['gbook_title']);
		$gbook_text=$BBCODE->parse($gbookinfo['gbook_text'],0,$config['gbook_ubb'],$config['gbook_smilies'],$config['gbook_images'],1,pkGetConfig('guestbook_imageresize'),pkGetConfig('guestbook_textwrap'));
		$gbook_time=formattime($gbookinfo['gbook_time']);
			
			
		if($gbookinfo['gbook_ip']==0)
			eval("\$gbook_ip= \"".pkTpl("guestbook/gbook_ipno_iconlink")."\";");
		else
			eval("\$gbook_ip= \"".pkTpl("guestbook/gbook_ip_iconlink")."\";");
		
		eval("\$gbook_admin= \"".pkTpl("guestbook/gbook_admin_iconlink")."\";");
			
			
		if($config['gbook_commenteod']==1)
			{
			list($ccounter)=$SQL->fetch_row($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_COMMENT." WHERE comment_cat='gb' and comment_subid='".$gbookinfo['gbook_id']."'"));
				
			$gbook_comment = pkGetSpecialLang('comment',$ccounter);
			
			eval("\$gbook_comment= \"".pkTpl("guestbook/viewgb_comment_textlink")."\";");
			}
			
		
		eval("\$delete_row= \"".pkTpl("guestbook/viewgb_row")."\";");
		eval("\$site_body.= \"".pkTpl("guestbook/delgb")."\";");	
		break;
		#END case delete
	case 'edit' :
		if(!adminaccess('gbedit'))
			{
			pkEvent('access_refused');
			return;
			}
		
		$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';
		$id=(isset($_REQUEST['id']) && intval($_REQUEST['id'])>0) ? intval($_REQUEST['id']) : 0;
		
		
		if($id && $ACTION!='view') 
			{
			if(isset($_POST['save']) && $ACTION==$_POST['save']) 
				{
				$SQL->query("UPDATE ".pkSQLTAB_GUESTBOOK."
					SET gbook_autor='".$SQL->f($_POST['gbook_autor'])."',
						gbook_title='".$SQL->f($_POST['gbook_title'])."',
						gbook_email='".$SQL->f($_POST['gbook_email'])."',
						gbook_hpage='".$SQL->f($_POST['gbook_hpage'])."',
						gbook_text='".$SQL->f($_POST['gbook_text'])."',
						gbook_icqnr='".$SQL->f($_POST['gbook_icqnr'])."' 
					WHERE gbook_id='".$id."'");
				}
			
			pkHeaderlocation('guestbook');
			} 
		
		
		$gbookinfo=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_GUESTBOOK." WHERE gbook_id='".$id."' LIMIT 1"));

		$link_administration_useredit=pkLinkAdmin('','','goto='.urlencode('path=useredit&editid='.$gbookinfo['gbook_userid']));
			
		if(intval($gbookinfo['gbook_userid'])>0) 
			eval("\$autor_message= \"".pkTpl("guestbook/editgb_autormsg")."\";");
				
		if(intval($gbookinfo['gbook_icqnr'])>0)
			$gbook_icq=intval($gbookinfo['gbook_icqnr']);
			
			
		$gbookinfo['gbook_autor']=pkEntities($gbookinfo['gbook_autor']);
		$gbookinfo['gbook_title']=pkEntities($gbookinfo['gbook_title']);
		$gbookinfo['gbook_email']=pkEntities($gbookinfo['gbook_email']);
		$gbookinfo['gbook_hpage']=pkEntities($gbookinfo['gbook_hpage']);		
		$gbook_text=pkEntities($gbookinfo['gbook_text']);
				
				
		eval("\$site_body.= \"".pkTpl("guestbook/editgb")."\";");
		break;
		#END case edit
	case 'print' :
		pkLoadClass($BBCODE,'bbcode');
	
		$pkDISPLAYPRINT=true;
		$id=(isset($_REQUEST['id']) && intval($_REQUEST['id'])>0) ? intval($_REQUEST['id']) : 0;
		
		$gbookinfo=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_GUESTBOOK." WHERE gbook_id='".$id."' LIMIT 1"));
		
		$gbook_title=pkEntities($gbookinfo['gbook_title']);
		$gbook_autor=pkEntities($gbookinfo['gbook_autor']);
		$gbook_text=$BBCODE->parse($gbookinfo['gbook_text'],0,$config['gbook_ubb'],$config['gbook_smilies'],$config['gbook_images'],1,pkGetConfig('guestbook_imageresize'),pkGetConfig('guestbook_textwrap'));
		$gbook_time=formattime($gbookinfo['gbook_time']);
		$print_time=formattime();
		
		eval("\$site_body.= \"".pkTpl("guestbook/print")."\";");
		break;
		#END case print
	case 'sign' :
		if($config['gbook_eod']!=1) 
			{
			pkEvent('function_disabled');
			return;
			}
		
		$error=0;
		
		$ACTION=(isset($_REQUEST['action'])) ? $_REQUEST['action'] : 'view';
		
		
		if((isset($_POST['save']) && $ACTION==$_POST['save']) || (isset($_POST['preview']) && $ACTION==$_POST['preview']))
			{
			$floodcontrol=pkTIME-($config['gbook_floodctrl']*60);
			$infocount=$SQL->fetch_array($SQL->query("SELECT 
				COUNT(*) 
				FROM ".pkSQLTAB_GUESTBOOK." 
				WHERE gbook_ip='".$SQL->f($ENV->getvar('REMOTE_ADDR'))."' AND 
					gbook_userid='".$SQL->i(pkGetUservalue('id'))."' AND
					gbook_time>'".$floodcontrol."' 
				LIMIT 1"));
		
			if(!pkCaptchaCodeValid($ENV->_post(pkCAPTCHAVARNAME)))
				$error=7;
			elseif((isset($_POST['content']) && empty($_POST['content'])) || 
				(isset($_POST['gbook_title']) && empty($_POST['gbook_title'])) || 
				(isset($_POST['gbook_autor']) && empty($_POST['gbook_autor'])))
				$error=1;
			elseif(strlen(trim($_POST['content']))>$config['gbook_maxchars'])
				{
				$error=2;
				$charcount=strlen($_POST['content']);
				}
			elseif($infocount[0]>0)
				$error=3;
			elseif(!checkusername($_POST['gbook_autor'],1))
				$error=4;
			elseif(($_POST['gbook_notify']==1 || $_POST['gbook_email']!='') && !emailcheck($_POST['gbook_email'],1)) 
				$error=5;
			else
				{
				if($ACTION==$_POST['save'])
					{
					if($SQL->query("INSERT INTO ".pkSQLTAB_GUESTBOOK." 
						(gbook_autor, gbook_title, gbook_email, gbook_icqnr, gbook_time, gbook_text, gbook_check, gbook_hpage, gbook_ip, gbook_userid, gbook_notify) VALUES 
						('".$SQL->f($_POST['gbook_autor'])."',
						 '".$SQL->f($_POST['gbook_title'])."',
						 '".$SQL->f($_POST['gbook_email'])."',
						 '".$SQL->f($_POST['gbook_icqnr'])."',
						 '".pkTIME."',
						 '".$SQL->f($_POST['content'])."',
						 '".$SQL->i($_POST['gbook_check'])."',
						 '".$SQL->f($_POST['gbook_hpage'])."',
						 '".$SQL->f($ENV->getvar('REMOTE_ADDR'))."',
						 '".$SQL->i(pkGetUservalue('id'))."',
						 '".$SQL->i($_POST['gbook_notify'])."')")) 
						 {
						 pkLoadLang('email');
						 
						 $gbid=$SQL->insert_id();
						 
						 $autor = $_POST['gbook_autor'];
						 $link= pkLinkMail('guestbook','','gbid='.$gbid);
						 
						 $mail_title = $config['site_name'].' - '.pkGetLang('new_gbentry').': '.$_POST['gbook_title'];
						 $mail_text = pkGetSpecialLang('guestbook_notify_mail_text',
										pkGetConfig('site_name'),
										$autor,
										$link
						 				);
						 
						 notifymail('gbook',$mail_title,$mail_text);
						 
						 $pn_title = $lang['new_gbentry'].': '.$_POST['gbook_title'];
						 $pn_text = pkGetSpecialLang('new_gbentry_pn_notify',$autor,$gbid);
						 
						 notifyim('gbook',$pn_title,$pn_text);
						 
						 pkHeaderlocation('','','event=guestbook');
						 }
						
					$error=6;
					}
				elseif($ACTION==$_POST['preview'])
					{
					pkLoadClass($BBCODE,'bbcode');
					
					
					$counter=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_GUESTBOOK.""));
					$gbook_number=$counter[0]+1;
					
					if($_POST['gbook_autor']!='') 
						$gbook_autor=pkEntities($_POST['gbook_autor']);
					else
						$gbook_autor=pkGetUservalueF('nick');
					
					if($_POST['gbook_check']==1 && $_POST['gbook_email']!='')
						{
						$userinfo['user_email']=pkEntities($_POST['gbook_email']);
						
						eval("\$gbook_email= \"".pkTpl("member_email_iconlink")."\";");
						}
					else 
						$gbook_email=' ';
					
					if($_POST['gbook_hpage']!='')
						{
						if(eregi("http://",$_POST['gbook_hpage']))
							$info_link=pkEntities($_POST['gbook_hpage']);
						else
							$info_link="http://".pkEntities($_POST['gbook_hpage']);
						
						eval("\$gbook_hpage= \"".pkTpl("member_hpage_iconlink")."\";");
						}
					else
						$gbook_hpage=' ';
					
					if($_POST['gbook_icqnr']>0)
						{
						$userinfo['user_icqid']=$_POST['gbook_icqnr'];
						
						eval("\$gbook_icq= \"".pkTpl("member_icq_iconlink")."\";");
						}
					else
						$gbook_icq=' ';
					
					
					$gbook_title=pkEntities($_POST['gbook_title']);
					$gbook_text=$BBCODE->parse($_POST['content'],0,$config['gbook_ubb'],$config['gbook_smilies'],$config['gbook_images'],1,pkGetConfig('guestbook_imageresize'),pkGetConfig('guestbook_textwrap'));
					$gbook_time=formattime();
					
					eval("\$site_body.= \"".pkTpl("guestbook/signgb_preview")."\";");
					}
				}
			}
		
		if($error>0) 
			eval("\$sign_message= \"".pkTpl("guestbook/signgb_error".$error)."\";");
		else
			eval("\$sign_message= \"".pkTpl("guestbook/signgb_message")."\";");
		
		if($_POST['gbook_autor']!='')
			$gbook_autor=pkEntities($_POST['gbook_autor']);
		else
			$gbook_autor=pkGetUservalueF('nick');
		
		if($_POST['gbook_email']!='')
			$gbook_email=pkEntities($_POST['gbook_email']);
		else
			$gbook_email=pkGetUservalueF('email');
		
		if($_POST['gbook_check']==1)
			$check1="checked";
		elseif($ACTION=='view')
			$check1="checked";
		
		
		if($config['gbook_commenteod']==1)
			{
			if($_POST['gbook_notify']==1)
				$check2="checked";
			
			eval("\$sign_comment= \"".pkTpl("guestbook/signgb_comment")."\";");
			}
		
		
		if(isset($_POST['gbook_hpage']) && !empty($_POST['gbook_hpage']))
			$gbook_hpage=pkEntities(trim($_POST['gbook_hpage']));
		elseif(($gbook_hpage=pkGetUservalue('hpage')) && !empty($gbook_hpage))
			$gbook_hpage=pkGetUservalueF('hpage');
		else
			$gbook_hpage='';
		
		if(intval($_POST['gbook_icqnr'])>0)
			$gbook_icqnr=intval($_POST['gbook_icqnr']);
		elseif(intval(pkGetUservalue('icqid'))>0)
			$gbook_icqnr=intval(pkGetUservalue('icqid'));
			
		if(trim($_POST['gbook_title'])!='')
			$gbook_title=pkEntities($_POST['gbook_title']);
		
		if(trim($_POST['content'])!='')
			$gbook_text=pkEntities($_POST['content']);
		
		unset($sign_format);
		
		if($config['gbook_ubb']==1)
			eval("\$sign_format= \"".pkTpl("format_text")."\";");
		
		if($config['gbook_smilies']==1)
			{
			$smilies=new smilies();
			$sign_format.=$smilies->getSmilies("1");
			}
		
		if($sign_format)
			eval("\$sign_format= \"".pkTpl("format_table")."\";");
			
		
		$captcha=pkCaptchaField(NULL,2);		
		
		eval("\$site_body.= \"".pkTpl("guestbook/signgb")."\";");	
		break;
		#END case sign	
	default :
		if(!$config['gbook_eod']==1)
			{
			pkEvent('function_disabled');
			return;
			}	


		pkLoadClass($BBCODE,'bbcode');
		pkLoadFunc('user');
		pkLoadLang('guestbook');
		
		
		if(isset($_REQUEST['entries']) && intval($_REQUEST['entries'])>0)
			$entries=intval($_REQUEST['entries']);
		else
			$entries=0;
		
		
		if($path=='guestbook')
			{
			$counter=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_GUESTBOOK));
			$sidelink=sidelinkfull($counter[0], $config['gbook_epp'], $entries, "include.php?path=guestbook","small");
			
			eval("\$gbook_sidelink= \"".pkTpl("guestbook/viewgb_sidelink")."\";");
			}
		
		$gbook_number=$counter[0]-$entries;
		
		if(intval($_REQUEST['gbid']>0)) 
			$sqlcommand=" WHERE gbook_id='".intval($_REQUEST['gbid'])."'";
		else 
			unset($sqlcommand);
		
		$getinfo=$SQL->query("SELECT * FROM ".pkSQLTAB_GUESTBOOK." ".$sqlcommand." 
			ORDER BY gbook_time DESC LIMIT ".$entries.",".$config['gbook_epp']);
		unset($sqlcommand);
		
		while($gbookinfo=$SQL->fetch_array($getinfo))
			{
			$gbookinfo_hash[]=$gbookinfo;
			if($gbookinfo['gbook_userid']!=0)
				{
				if($sqlcommand)
					{
					$sqlcommand.=" OR user_id='".$gbookinfo['gbook_userid']."'";
					}
				else
					{
					$sqlcommand="SELECT * FROM ".pkSQLTAB_USER." WHERE user_id='".$gbookinfo['gbook_userid']."'";
					}
				}
			}
		
		
		if($sqlcommand)
			{
			$getuserinfo=$SQL->query($sqlcommand);
			while($userinfo=$SQL->fetch_array($getuserinfo)) 
				{
				$userinfo_cache[$userinfo['user_id']]=$userinfo;
				}
			}
		
		if(is_array($gbookinfo_hash))
			{
			foreach($gbookinfo_hash as $gbookinfo)
				{
				$row=rowcolor($row);
				
				if($gbookinfo['gbook_userid']!=0 && $userinfo_cache[$gbookinfo['gbook_userid']]!='') 
					{
					$userinfo=$userinfo_cache[$gbookinfo['gbook_userid']];
					$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
					
					eval("\$gbook_autor= \"".pkTpl("member_showprofil_textlink")."\";");
					
					if(isonline($userinfo['user_id'])) 
						eval("\$info_os= \"".pkTpl("member_os_online")."\";");
					else
						eval("\$info_os= \"".pkTpl("member_os_offline")."\";");
					
					
					if($userinfo['user_emailshow']==1)
						{
						if($config['member_mailer']==1)
							eval("\$gbook_email= \"".pkTpl("member_email_iconlink2")."\";");
						else
							eval("\$gbook_email= \"".pkTpl("member_email_iconlink")."\";");
						}
					
					if(intval($userinfo['user_icqid'])>0)
						eval("\$gbook_icq= \"".pkTpl("member_icq_iconlink")."\";");
					
					if($userinfo['user_hpage']!='')
						{
						if(ereg("http://",$userinfo['user_hpage']))
							$info_link=pkEntities($userinfo['user_hpage']);
						else
							$info_link="http://".pkEntities($userinfo['user_hpage']);
						
						eval("\$gbook_hpage= \"".pkTpl("member_hpage_iconlink")."\";");
						}
					
					$info_sig=pkUserSignature($userinfo['user_sig']); 						
					}
				else
					{
					$gbook_autor=$userinfo['user_nick']=pkEntities($gbookinfo['gbook_autor']);
					
					if($gbookinfo['gbook_check']==1 && $gbookinfo['gbook_email']!='')
						{
						$userinfo['user_email']=pkEntities($gbookinfo['gbook_email']);
						eval("\$gbook_email= \"".pkTpl("member_email_iconlink")."\";");
						}
					
					if($gbookinfo['gbook_hpage']!='')
						{
						if(ereg("http://",$gbookinfo['gbook_hpage'])) 
							$info_link=pkEntities($gbookinfo['gbook_hpage']);
						else
							$info_link="http://".pkEntities($gbookinfo['gbook_hpage']);
						
						eval("\$gbook_hpage= \"".pkTpl("member_hpage_iconlink")."\";");
						}
					
					if(intval($gbookinfo['gbook_icqnr'])>0)
						{
						$userinfo['user_icqid']=intval($gbookinfo['gbook_icqnr']);
						
						eval("\$gbook_icq= \"".pkTpl("member_icq_iconlink")."\";");
						}
					
					eval("\$info_os= \"".pkTpl("guest_os_icon")."\";");
					}
				
				$gbook_title=pkEntities($gbookinfo['gbook_title']);
				$gbook_text=$BBCODE->parse($gbookinfo['gbook_text'],0,$config['gbook_ubb'],$config['gbook_smilies'],$config['gbook_images'],1,pkGetConfig('guestbook_imageresize'),pkGetConfig('guestbook_textwrap'));
				$gbook_time=formattime($gbookinfo['gbook_time']);
		
				if(adminaccess('gbedit') || adminaccess('gbdelete'))
					{
					if($gbookinfo['gbook_ip']==0)
						eval("\$gbook_ip= \"".pkTpl("guestbook/gbook_ipno_iconlink")."\";");
					else
						eval("\$gbook_ip= \"".pkTpl("guestbook/gbook_ip_iconlink")."\";");
					
					
					if(adminaccess('gbedit'))
						eval("\$gbook_admin= \"".pkTpl("guestbook/gbook_admin_iconlink")."\";");
					
					if(adminaccess('gbdelete'))
						eval("\$gbook_admin.= \"".pkTpl("guestbook/gbook_admin_iconlink2")."\";");
					}
				
				if($config['gbook_commenteod']==1) 
					{
					list($ccounter)=$SQL->fetch_row($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_COMMENT." WHERE comment_cat='gb' and comment_subid='".$gbookinfo['gbook_id']."'"));
			
					$gbook_comment=pkGetSpecialLang('comment',$ccounter);
								
					eval("\$gbook_comment= \"".pkTpl("guestbook/viewgb_comment_textlink")."\";");
					}
				
		
				eval("\$gbook_row.= \"".pkTpl("guestbook/viewgb_row")."\";");
				$gbook_number--;
		
				unset($gbook_email);
				unset($gbook_hpage);
				unset($gbook_icq);
				unset($gbook_admin);
				unset($gbook_ip);
				unset($gbook_comment);
				unset($info_sig);
				}
			}
			
		#set site title
		$guestbook_title = pkGetConfigF('gbook_title');
		$guestbook_title = empty($guestbook_title) ? pkGetLang('guestbook_page_title') : $guestbook_title;
		$CMS->site_title_set($guestbook_title,true);

		#welcome text
		$guestbook_welcome_text = pkGetConfig('gbook_welcome');
		$guestbook_welcome_text = empty($guestbook_welcome_text) ? pkGetLang('guestbook_welcome_text') : $BBCODE->parse($guestbook_welcome_text,1,1,1,1);

		$gbook_welcome = '';

		if(!empty($guestbook_welcome_text))
			{
			eval("\$gbook_welcome=\"".pkTpl("guestbook/viewgb_welcome")."\";");
			}
		
		 
		eval("\$site_body.= \"".pkTpl("guestbook/viewgb")."\";");
		break;
		#END default
	}
?>