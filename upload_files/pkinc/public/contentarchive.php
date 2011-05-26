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


$letterhash=array(0=>pkGetLang('all'),"A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","0-9");
$orderhash=array('rate','rated','title','titled','dated');

$entries=(isset($_REQUEST['entries']) && intval($_REQUEST['entries'])>0) ? intval($_REQUEST['entries']) : 0;
$type=(isset($_REQUEST['type'])) ? intval($_REQUEST['type']) : ((isset($type) && ($type==3 || $type==2 || $type==4 )) ? $type : 0);
$contentid=(isset($_REQUEST['contentid']) && intval($_REQUEST['contentid'])>0) ? intval($_REQUEST['contentid']) : ((isset($contentid) && $contentid>0) ? $contentid : 0);
$catid=(isset($_REQUEST['catid']) && intval($_REQUEST['catid'])) ? intval($_REQUEST['catid']) : 0;
$themeid=(isset($_REQUEST['themeid']) &&  intval($_REQUEST['themeid'])) ? intval($_REQUEST['themeid']) : 0;
$letter=(isset($_REQUEST['letter']) && in_array($_REQUEST['letter'],$letterhash)) ? $_REQUEST['letter'] : NULL;
$order=(isset($_REQUEST['order']) && in_array($_REQUEST['order'],$orderhash)) ? $_REQUEST['order'] : NULL;


if($type==2)
	{
	$type=2;
	$content_type=$lang['news'];
	$epp=$config['content_epp2'];
 	$content_page="news";
	}
elseif($type==3) 
	{
	$type=3;
	$content_type=$lang['links'];
	$epp=$config['content_epp3'];
	$content_page="overview";
	}
elseif($type==4)
	{
	$content_type=$lang['downloads'];
	$epp=$config['content_epp4'];
	$type=4;
	$content_page="download";
	}
else
	{
	$type=1;
	$content_type=$lang['articles'];
	$epp=$config['content_epp1'];
	$content_page="articles";
	}


$epp=($epp<1) ? 12 : $epp;


$contentcat_cache=contentcats();
$contentcat_cache=$contentcat_cache[0];

$sqlcommand="WHERE ".pkSQLTAB_CONTENT.".content_status=1 AND 
	".pkSQLTAB_CONTENT.".content_option='".$type."' AND 
	".pkSQLTAB_CONTENT.".content_time<'".pkTIME."' AND 
	(".pkSQLTAB_CONTENT.".content_expire>'".pkTIME."' OR 
	 ".pkSQLTAB_CONTENT.".content_expire=0)";

if(intval($catid)>0)
	$sqlcommand.=" AND ".pkSQLTAB_CONTENT.".content_cat='".$catid."'";

if(intval($themeid)>0)
	$sqlcommand.=" AND ".pkSQLTAB_CONTENT.".content_themeid='".$themeid."'";

if(intval($contentid)>0) 
	$sqlcommand.=" AND ".pkSQLTAB_CONTENT.".content_id='".$contentid."'";


if($letter=='0-9') 
	{
	$sqlcommand.=" AND (".pkSQLTAB_CONTENT.".content_title LIKE '0%'";

	foreach(range(1,9) as $i)
		{
		$sqlcommand.=" OR ".pkSQLTAB_CONTENT.".content_title LIKE '".$i."%'";
		}
	$sqlcommand.=")";
	}
elseif(in_array($letter,$letterhash) && $letter!=pkGetLang('all'))
	$sqlcommand.=" AND ".pkSQLTAB_CONTENT.".content_title LIKE '".$SQL->f($letter)."%'";
else
	$letter=NULL;


$sqlcommand="FROM ".pkSQLTAB_CONTENT." LEFT JOIN ".pkSQLTAB_CONTENT_CATEGORY." 
	ON ".pkSQLTAB_CONTENT_CATEGORY.".contentcat_id=".pkSQLTAB_CONTENT.".content_cat ".$sqlcommand." AND 
	".sqlrights(pkSQLTAB_CONTENT_CATEGORY.".contentcat_rights");


if($order=="rate") 
	$sqlorder="ORDER by ".pkSQLTAB_CONTENT.".content_rating DESC";
elseif($order=="rated")
	$sqlorder="ORDER by ".pkSQLTAB_CONTENT.".content_rating ASC";
elseif($order=="title")
	$sqlorder="ORDER by ".pkSQLTAB_CONTENT.".content_title ASC";
elseif($order=="titled")
	$sqlorder="ORDER by ".pkSQLTAB_CONTENT.".content_title DESC";
elseif($order=="dated")
	$sqlorder="ORDER by ".pkSQLTAB_CONTENT.".content_time ASC";
else 
	$sqlorder="ORDER by ".pkSQLTAB_CONTENT.".content_time DESC";

$counter=$SQL->num_rows($SQL->query("SELECT ".pkSQLTAB_CONTENT.".* ".$sqlcommand));


if($counter<$entries) 
	$entries=0;


pkLoadLang('content');

if($counter==0)
	{
	eval("\$content_articles= \"".pkTpl("content/overview_notfound".$type."")."\";");
	}
else 
	{
	pkLoadClass($BBCODE,'bbcode');

	
	
	$userinfo_hash=array();
	$content_side=sidelinkfull($counter,$epp,$entries,'include.php?path=contentarchive&letter='.$letter.'&catid='.$catid.'&themeid='.$themeid.'&type='.$type.'&order='.$order,"headssmall");
	
	eval("\$content_side= \"".pkTpl("content/overview_sidelink")."\";");
	
	$getcontentinfo=$SQL->query("SELECT ".pkSQLTAB_CONTENT.".* ".$sqlcommand." ".$sqlorder." LIMIT ".$entries.",".$epp);
	while($contentinfo=$SQL->fetch_array($getcontentinfo))
		{
		if($type==1 || $type==2)
			{
			$row=rowcolor2($row);
			}
		else
			{
			$row=rowcolor($row);
			}

		if(intval($contentinfo['content_autorid']))
			{
			if(!array_key_exists($contentinfo['content_autorid'],$userinfo_hash))
				{
				$userinfo=$SQL->fetch_assoc($SQL->query("SELECT user_id, user_nick FROM ".pkSQLTAB_USER." WHERE user_id='".$contentinfo['content_autorid']."' LIMIT 1"));
				$userinfo_hash[$userinfo['user_id']]=$userinfo;
				}
			else 
				$userinfo=$userinfo_hash[$contentinfo['content_autorid']];

			
			$autorinfo=pkHtmlLink(pkLink('userinfo','','id='.$userinfo['user_id']),pkEntities($userinfo['user_nick']));
			}
		else
			$autorinfo=pkEntities($contentinfo['content_autor']);

		$contentcatinfo=$contentcat_cache[$contentinfo['content_cat']];
		$contentinfo['content_title']=pkEntities($contentinfo['content_title']);
		
		
		if(intval($contentinfo['content_themeid'])>0)
			{
			$contentthemeinfo=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_CONTENT_THEME." WHERE contenttheme_id='".$contentinfo['content_themeid']."'"));
			$contentthemeinfo['contenttheme_name']=pkEntities($contentthemeinfo['contenttheme_name']);
			
			eval("\$contenttheme_info= \"".pkTpl("content/article_theme_textlink")."\";");
			}
		
		$content_time=formattime($contentinfo['content_time'],'','date');
		$content_time_full=formattime($contentinfo['content_time']);
		
		eval("\$content_link= \"".pkTpl("content/overview_article_title")."\";");
		
		
		if($contentinfo['content_teaser']!='')
			{
			$catimage_dimension=@getimagesize($contentinfo['content_teaser']);
			eval("\$content_catimage= \"".pkTpl("content/overview_teaser")."\";");
			}
		elseif($contentcatinfo['contentcat_symbol']!='blank.gif' && $contentcatinfo['contentcat_symbol']!='')# && filecheck("images/catimages/".$contentcatinfo['contentcat_symbol']))
			{
			$catimage_dimension=@getimagesize('images/catimages/'.$contentcatinfo['contentcat_symbol']);
			
			eval("\$content_catimage= \"".pkTpl("content/cat_image_left")."\";");
			}
		

		$cut = (!$contentid || $contentid!=$contentinfo['content_id']) ? pkGetConfig('content_length'.$type) : 0;
		
		$content_headline = $BBCODE->parse($contentinfo['content_header'].' '.$contentinfo['content_text'],1,1,1,1);
		$content_headline = strip_tags($content_headline);
		$content_headline = mb_substr($content_headline,0,$cut,pkGetLang('__CHARSET__'));
  
		
		if($type==1)
			{
			if($row=='odd' || $row=='even2')
				{
				eval("\$content_articles.= \"".pkTpl("content/overview_article_linkbox_left")."\";");
				}
			else
				{
				eval("\$content_articles.= \"".pkTpl("content/overview_article_linkbox_right")."\";");
				}
			}
		elseif($type==2) 
			{
			if($row=='odd' || $row=='even2')
				{
				eval("\$content_articles.= \"".pkTpl("content/overview_news_linkbox_left")."\";");
				}
			else
				{
				eval("\$content_articles.= \"".pkTpl("content/overview_news_linkbox_right")."\";");
				}
			}
		elseif($type==3)
			{
			if($contentinfo['content_comment_status']==1)
				{
				$ccounter=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_COMMENT." WHERE comment_cat='cont' AND comment_subid='".$contentinfo['content_id']."'"));
			
				eval("\$content_comment= \"".pkTpl("content/link_comment_link")."\";");
				}
			
			if($contentinfo['content_rating_status']==1)
				{
				if(intval($contentinfo['content_rating_total'])>0) 
					{
					
					$content_rating_d=number_format($contentinfo['content_rating'],2,",",".");
					$content_rating_votes=$contentinfo['content_rating_total'];
					
					eval("\$content_rating_info= \"".pkTpl("content/link_rating_info")."\";");
					}
				
				eval("\$content_rate= \"".pkTpl("content/link_rating_link")."\";");
				}
				

			$link		= pkLink('contentarchive','','type=3&contentid='.$contentinfo['content_id']);
			$link_go	= pkLink('link','','contentid='.$contentinfo['content_id'].'&link=go');
				
			
			#Links are only show in the archive
			#so we add an more link in the overview mode otherwise the text will be completly displayed
			if($contentid && $contentid==$contentinfo['content_id'])
				{
				#full
				$content_headline = $BBCODE->parse($contentinfo['content_header'].' '.$contentinfo['content_text'],$contentinfo['content_html'],$contentinfo['content_ubb'],$contentinfo['content_smilies'],1);

				$title = $contentinfo['content_title'];#was already forated above
				$CMS->site_title_set($title,true);
				}
			else
				{
				#cutted? add a "symbol for it"
				$content_headline.= strlen($content_headline)>=pkGetConfig('content_length'.$type) ? pkGetLang('cutted_text_add') : '';
				
				#cutted overview mode
				$content_headline.= ' '.pkHtmlLink($link, pkGetLang('read_more'));
				}

			eval("\$content_articles.= \"".pkTpl("content/overview_links_linkbox")."\";");
			}
		elseif($type==4)
			{
			if($contentinfo['content_rating_status']==1 && intval($contentinfo['content_rating_total'])>0)
				{
				$content_rating=number_format($contentinfo['content_rating'],2,",",".");
				
				eval("\$content_rating= \"".pkTpl("content/overview_downloads_linkbox_rating")."\";");
				}
			
			$dl=explode("\n",$contentinfo['content_altdat']);
			
			foreach($dl as $d)
				{
				$d=trim($d);
				$file_size=FileSizeExt($config['content_downloadpath'].'/'.$d,'B');
				
				if($file_size!='')
					break;
				}
			
			if($file_size=='')
				{
				$file_size=FileSizeExt('','B',$contentinfo['content_filesize']*1024);
				}
			
			if($file_size=="" || $file_size==0)
				{
				$file_size='&nbsp; - &nbsp;';
				}
			
			
			eval("\$content_articles.= \"".pkTpl("content/overview_downloads_linkbox")."\";");
			}
		
		unset($content_rating);
		unset($file_size);
		unset($content_rating_info);
		unset($contenttheme_info);
		unset($content_catimage);
		unset($content_rate);
		unset($content_rating_info);
		unset($content_comment);
		unset($ccounter);
		unset($content_comment_count);
		}
	}


if(($type==1 || $type==2) && ($row=='odd' || $row=='even2')) 
	{
	if($row=='odd')
		$row="even"; 
	else
		$row="odd2";
	
	eval("\$content_articles.= \"".pkTpl("content/overview_article_spacer_right")."\";");
	unset($row);
	}


$getcontentcatinfo=$SQL->query("SELECT * FROM ".pkSQLTAB_CONTENT_CATEGORY." WHERE ".sqlrights("contentcat_rights")." AND contentcat_type".$type."=1 ORDER by contentcat_order, contentcat_name ASC");
while($contentcatinfo=$SQL->fetch_array($getcontentcatinfo))
	{
	unset($selected);
	$contentcatinfo['contentcat_name']=pkEntities($contentcatinfo['contentcat_name']);
	
	if($contentcatinfo['contentcat_id']==$catid)
		{
		eval("\$content_cat= \"".pkTpl("content/overview_cat_link")."\";");
		$selected=" selected=\"selected\"";
		}
	elseif($content_cat=='')
		{
		$content_cat = pkGetLang('Overview');
		}
	
	eval("\$overview_cats.= \"".pkTpl("content/overview_cat_option")."\";");
	}


if(intval($catid)>0)
	{
	$getcontentthemeinfo=$SQL->query("SELECT * FROM ".pkSQLTAB_CONTENT_THEME." WHERE contenttheme_catid='".$catid."' ORDER by contenttheme_name ASC");
	while($contentthemeinfo=$SQL->fetch_array($getcontentthemeinfo))
		{
		unset($selected);
		$contentthemeinfo['contenttheme_name']=pkEntities($contentthemeinfo['contenttheme_name']);
		
		if($contentthemeinfo['contenttheme_id']==$themeid)
			{
			eval("\$content_theme= \"".pkTpl("content/overview_theme_link")."\";");
			$selected="selected";
			}
		
		eval("\$overview_themes.= \"".pkTpl("content/overview_control_theme_option")."\";");
		}
	
	if($overview_themes!='')
		eval("\$control_themes= \"".pkTpl("content/overview_control_theme")."\";");
	}


foreach($letterhash as $h)
	{
	unset($a);
	
	if($content_letter_links!='')
		eval("\$content_letter_links.= \"".pkTpl("content/overview_letter_textlink_spacer")."\";");
	
	if($letter==$h || ($letter=='' && $h==pkGetLang('all')))
		{
		$a[]='<b>(';
		$a[]=')</b>';
		}
	
	eval("\$content_letter_links.= \"".pkTpl("content/overview_letter_textlink")."\";");
	}

eval("\$content_control_letter= \"".pkTpl("content/overview_control_letter")."\";");


if($type==1 && getrights($config['content_submit1'])=="true")
	{
	eval("\$submit_link= \"".pkTpl("content/overview_submitlink")."\";");
	}
elseif($type==2 && getrights($config['content_submit2'])=="true")
	{
	eval("\$submit_link= \"".pkTpl("content/overview_submitlink")."\";");
	}
elseif($type==3 && getrights($config['content_submit3'])=="true")
	{
	$content_type=$lang['links'];
	eval("\$submit_link= \"".pkTpl("content/overview_submitlink")."\";");
	}
elseif($type==4 && getrights($config['content_submit4'])=="true")
	{
	$content_type=$lang['download'];
	eval("\$submit_link= \"".pkTpl("content/overview_submitdownload")."\";");
	}

if(!$contentid)
	{
	#set site title
	$array = array(
		1 => 'articles',
		2 => 'news',
		3 => 'links',
		4 => 'downloads'
		);
	
	$vname = 'content_archive_title_'.$array[$type];

	$title = pkGetConfigF($vname);
	$title = empty($title) ? pkGetLang($vname) : $title;
	$CMS->site_title_set($title,true);
	}


eval("\$site_body.= \"".pkTpl("content/overview_control")."\";");
eval("\$site_body.= \"".pkTpl("content/overview_page".$type)."\";");
?>