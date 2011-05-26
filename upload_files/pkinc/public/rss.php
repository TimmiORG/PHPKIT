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


$rss_timeformat_code = 'RFC822';
$accessible_modes = pkCfgData('rss-types');
$modes = array();
$mode = false;

#see which modes are accessible
#flips the keys and values!!
foreach($accessible_modes as $key=>$value)
	{
	if(pkGetConfig('rss_enable_'.$value))
		{
		$modes[$value] = $key; #yes, this is intend - here we flip key and value
		}
	}


#Verify the given type (by mode)
if(isset($_GET['mode']) && isset($modes[$_GET['mode']]))
	{
	$mode = $_GET['mode'];
	}


if($mode!==false)	#display a feed
	{
	pkLoadLang('rss');
	pkLoadClass($BBCODE,'bbcode');
	
	$title = pkGetConfig('rss_title_'.$mode);
	$title = empty($title) ? pkGetLang('rss_title_'.$mode) : pkSpecialchars($title);
	
	#$CMS->site_title_set($title);
		
	
	#rss configuation
	$encoding			= pkGetLang('__CHARSET__');	#output charset
	$language			= pkGetLang('__LANGCODE__'); #f.e.: de-DE German/Germany or en-GB  english/Great Britan
	$generator			= 'PHPKIT WCMS - Web Content Management System';
	$items_limit		= 10; #int, number of items to display
	$fullfeed			= 0;#bool // true: show full text, false: show an excerpt
	$fullfeed_limit		= 250; #int length for the excerpts
	
	#channel items
	$rss_pubDate		= 0;
	$rss_items			= array();
	
	#channel header
	$rss_title			= pkGetConfig('site_title_prefix').$title.pkGetConfig('site_title_sufix');
	$rss_link			= pkSpecialchars(pkGetConfig('site_url'));
	$rss_description	= pkSpecialchars(pkGetConfig('site_name')).' '.pkSpecialchars(pkGetConfig('site_slogan'));
	$rss_language		= $language;
	$rss_pubDate		= '';
	$rss_lastBuildDate	= pkTimeFormat(pkTIME,$rss_timeformat_code);	#current time
	$rss_docs			= pkSpecialchars(pkGetConfig('site_url')).'/?path=rss&amp;mode='.$mode;
	$rss_generator		= pkSpecialchars($generator);
	$rss_webMaster		= pkSpecialchars(pkGetConfig('site_email'));
	$rss_webMaster_name	= pkSpecialchars(pkGetConfig('site_name')); 
	
	
	#get the content feeds
	if($mode=='forums') #forum
		{
		pkLoadClass($FORUM,'forum');

		$limit = pkGetConfig('rss_limit_forums');
		$limit = intval($limit)>0 && intval($limit)<=100 ? intval($limit) : $items_limit;
		

		$query = $SQL->query("SELECT 
				p.forumpost_id,
				p.forumpost_title,
				p.forumpost_text,
				p.forumpost_time,
				p.forumpost_bbcode,
				p.forumpost_smilies,
				t.forumthread_id,
				t.forumthread_title
			FROM ".pkSQLTAB_FORUM_POST." AS p, ".pkSQLTAB_FORUM_THREAD." AS t WHERE t.forumthread_id=p.forumpost_threadid AND
				t.forumthread_status IN(1,2) AND 
				t.forumthread_catid IN(0".implode(',',$FORUM->getCategories()).")
			ORDER BY p.forumpost_time DESC
			LIMIT ".$limit);
		while(list($id,$title,$text,$time,$format_bb,$format_smilies,$thread_id,$thread_title) = $SQL->fetch_row($query))
			{
			$format_html = 0; #disable HTML hard
			
			#compare the current publishing date against the item date, uses the item time when newer
			$rsss_pubDate = $rss_pubDate<$time ? $time : $rss_pubDate;
			
			#format the title & text
			$title 	= empty($title) ? $thread_title : $title;
			$title	= pkSpecialchars($title);
			$text	= $BBCODE->parse($text,$format_html,$format_bb,$format_smilies,1);
			
			#link to this
			$link 	= pkLinkFull('forumthread','','threadid='.$thread_id.'&postid='.$id);
			
			
			#prepare the item and stores it
			$rss_items[] = array(
				'title'			=> $title,
				'description'	=> $text,
				'pubDate'		=> pkTimeFormat($time,$rss_timeformat_code),
				'link'			=> $link,
				);	
			}#END while for SQL->fetch_row
		}#END type == forum
	else #has to stay as ELSE
		{
		$type = $modes[$mode]; #the value is the content type
		$limit = pkGetConfig('rss_limit_'.$mode);
		$limit = intval($limit)>0 && intval($limit)<=100 ? intval($limit) : $items_limit;
		
		
		$query = $SQL->query("SELECT 
				c.content_id,
				c.content_title,
				c.content_text,
				c.content_time,				
				c.content_html,
				c.content_ubb,
				c.content_smilies
			FROM ".pkSQLTAB_CONTENT." AS c
				LEFT JOIN ".pkSQLTAB_CONTENT_CATEGORY." AS cc ON cc.contentcat_id=c.content_cat 
			WHERE c.content_option=".$type." AND
				c.content_status=1 AND 
				(c.content_expire>'".pkTIME."' OR c.content_expire='0') AND 
				c.content_time<'".pkTIME."' AND 
				".sqlrights('cc.contentcat_rights')." 
			ORDER BY c.content_time DESC
			LIMIT ".$limit);
		
		while(list($id,$title,$text,$time,$format_html,$format_bb,$format_smilies) = $SQL->fetch_row($query))
			{
			#compare the current publishing date against the item date, uses the item time when newer
			$rss_pubDate = $rss_pubDate<$time ? $time : $rss_pubDate;
			
			#format the text
			$title	= pkSpecialchars($title);
			$text	= $BBCODE->parse($text,$format_html,$format_bb,$format_smilies,1);
			
			#link to this
			switch($type)
				{
				case 1: #articles
					$link 	= pkLinkFull('article','','contentid='.$id);
					break;				
				case 2: #news
					$link 	= pkLinkFull('news','','contentid='.$id);
					break;
				case 3: #links
					$link	= pkLinkFull('contentarchive','','type=3&contentid='.$id);
					break;
				case 4: #downloads
					$link	= pkLinkFull('download','','contentid='.$id);
					break;
				case 0: #contents
				default: 
					$link 	= pkLinkFull('content','','contentid='.$id);
				}#switch type (for link)
			
			
			#prepare the item and stores it
			$rss_items[] = array(
				'title'			=> $title,
				'description'	=> $text,
				'pubDate'		=> pkTimeFormat($time,$rss_timeformat_code),
				'link'			=> $link,
				);	
			}#END while for SQL->fetch_row
		}#END contents - articles, news etc			

	
	#FEED IT
	$rss_pubDate = pkTimeFormat($rss_pubDate,$rss_timeformat_code);
	
	#header - nessary to prevent failures
	header('Content-type: application/rss+xml'); #displaying may fail without this header
	
	echo '<?xml version="1.0" encoding="'.$encoding.'"?>'."\r\n";
	echo '<rss version="2.0">'."\r\n";
	echo '	<channel>'."\r\n";
	echo '		<title>'.$rss_title.'</title>'."\r\n";
	echo '		<link>'.$rss_link.'</link>'."\r\n";
	echo '		<description>'.$rss_description.'</description>'."\r\n";
	echo '		<language>'.$rss_language.'</language>'."\r\n";
	echo '		<pubDate>'.$rss_pubDate.'</pubDate>'."\r\n";
	echo '		<lastBuildDate>'.$rss_lastBuildDate.'</lastBuildDate>'."\r\n";
	echo '		<docs>'.$rss_docs.'</docs>'."\r\n";
	echo '		<generator>'.$rss_generator.'</generator>'."\r\n";
	echo '		<webMaster>'.$rss_webMaster.' ('.$rss_webMaster_name.')</webMaster>'."\r\n";
	
	
	foreach($rss_items as $item)
		{
		#relative links need to be parsed
		$item['description'] = preg_replace('#(href|src)="([^:"]*)(?:")#','$1="'.pkGetConfig('site_url').'/$2"',$item['description']);
	
		
		echo '		<item>'."\r\n";
		echo '			<title>'.$item['title'].'</title>'."\r\n";
		echo '			<description><![CDATA['.$item['description'].']]></description>'."\r\n";
		echo '			<pubDate>'.$item['pubDate'].'</pubDate>'."\r\n";
		echo '			<link>'.$item['link'].'</link>'."\r\n";
		echo '			<guid>'.$item['link'].'</guid>'."\r\n";	#guid is here the same as link: globally unique identifier
		echo '		</item>'."\r\n";
		}
	
	echo '	</channel>'."\r\n";
	echo '</rss>'."\r\n";
	exit;
	}
	

#output: overview of accessible modes
pkLoadLang('rss');
pkLoadClass($BBCODE,'bbcode');

$rss_feeds = '';

foreach($modes as $key=>$value)
	{
	$label = pkGetConfigF('rss_title_'.$key);
	$label = empty($label) ? pkGetLang($key) : $label;
	
	$path = 'fx/default/icons/rss-tiny.png'; 
	
	$img = pkHtmlImage($path,$label);
	 
	$link = pkLink('rss',$key);
	$link_full = pkLinkFull('rss',$key);	
	
	$rss_feeds.= '<dt>'.$img.' '.$label.'</dt>';
	$rss_feeds.= '<dd><a href="'.$link.'">'.$link_full.'</a></dd>';	
	}


$rss_feeds = empty($rss_feeds) ? pkGetLang('rss_feeds_unavailable') : '<dl>'.$rss_feeds.'</dl>';

$page_headline = pkGetConfigF('rss_page_headline');
$page_headline = empty($page_headline) ? pkGetLang('rss_page_headline') : $page_headline;
$CMS->site_title_set($page_headline,true);

$page_text = pkGetConfig('rss_page_text');
$page_text = empty($page_text) ? pkGetLang('rss_page_text') : $BBCODE->parse($page_text,1,1,1,1);


eval("\$site_body.=\"".pkTpl("rss")."\";");
?>