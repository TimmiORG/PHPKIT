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


if(!defined('pkFRONTEND') || pkFRONTEND!='setup')
	die('Direct access to this location is not permitted.');


#included by setup-class

#config preset
$SQL->query("REPLACE INTO ".pkSQLTAB_CONFIG."
	(id,value) VALUES ('site_frontpage','".serialize('newsblock
content&contentid=4
content&contentid=8')."')");

#forumranks
$sql='';
$array=array(1,10,35,100,150,300,500,750,1000,2000,5000);
foreach($array as $i)
	{
	$sql.=(empty($sql) ? '' : ',')."(".$i.",'".$SQL->f(pkGetLang('forumrank'.$i))."')";
	}

$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_RANK);
$SQL->query("INSERT INTO ".pkSQLTAB_FORUM_RANK."
	(forumrank_post, forumrank_title)
	VALUES ".$sql);

#smilies
$sql='';
foreach(pkCfgData('smilies') as $i=>$data)
	{
	$sql.=(empty($sql) ? '' : ',')."('".$SQL->f($data['code'])."','".$SQL->f($data['path'])."','".$SQL->f(pkGetLang('smiley'.$i))."',1)";
	}

$SQL->query("DELETE FROM ".pkSQLTAB_SMILIES);
$SQL->query("INSERT INTO ".pkSQLTAB_SMILIES."
	(smilie_code, smilie_path, smilie_title, smilie_option)
	VALUES ".$sql);

#navigation
$SQL->query("DELETE FROM ".pkSQLTAB_NAVIGATION);
$SQL->query("DELETE FROM ".pkSQLTAB_NAVIGATION_CATEGORY);

#categories
$SQL->query("INSERT INTO ".pkSQLTAB_NAVIGATION_CATEGORY."
	(navigationcat_id,navigationcat_order,navigationcat_align,navigationcat_title,navigationcat_box,navigationcat_link) VALUES
	(1,1,0,'".$SQL->f(pkGetLang('setup_preset_navigationcat_home'))."','',''),
	(2,2,0,'".$SQL->f(pkGetLang('setup_preset_navigationcat_contact'))."','',''),
	(3,3,0,'".$SQL->f(pkGetLang('setup_preset_navigationcat_interactive'))."','',''),				
	(4,4,0,'".$SQL->f(pkGetLang('setup_preset_navigationcat_community'))."','navbox.community.php',''),
	(13,5,0,'','navbox.twitter.php',''),

	(6,1,1,'".$SQL->f(pkGetLang('setup_preset_navigationcat_newsflash'))."','navbox.newsflash.php',''),
	(7,2,1,'".$SQL->f(pkGetLang('setup_preset_navigationcat_style'))."','navbox.style.php',''),
	(8,3,1,'".$SQL->f(pkGetLang('setup_preset_navigationcat_poll'))."','navbox.vote.php',''),
	(5,4,1,'".$SQL->f(pkGetLang('setup_preset_navigationcat_websitestatus'))."','navbox.status.php',''),
	
	(9,1,2,'".$SQL->f(pkGetLang('setup_preset_navigationcat_home_s'))."','','?path=start'),
	(10,2,2,'".$SQL->f(pkGetLang('setup_preset_navigationcat_news'))."','','?path=news'),
	(11,3,2,'".$SQL->f(pkGetLang('setup_preset_navigationcat_forums'))."','','?path=forumsdisplay'),
	(12,4,2,'".$SQL->f(pkGetLang('setup_preset_navigationcat_contact'))."','','?path=contact')"
	);

#links
$SQL->query("INSERT INTO ".pkSQLTAB_NAVIGATION." 
	(navigation_cat,navigation_order,navigation_title,navigation_link) VALUES 
	(1,1,'".$SQL->f(pkGetLang('setup_preset_navigation_home'))."','?path=start'),
	(1,2,'".$SQL->f(pkGetLang('setup_preset_navigation_news'))."','?path=news'),
	(1,3,'".$SQL->f(pkGetLang('setup_preset_navigation_articles'))."','include.php?path=contentarchive&type=1'),
	(1,4,'".$SQL->f(pkGetLang('setup_preset_navigation_links'))."','include.php?path=contentarchive&type=3'),
	(1,5,'".$SQL->f(pkGetLang('setup_preset_navigation_downloads'))."','include.php?path=contentarchive&type=4'),
	(1,6,'".$SQL->f(pkGetLang('setup_preset_navigation_rss'))."','include.php?path=rss'),	
	(1,7,'".$SQL->f(pkGetLang('setup_preset_navigation_faq'))."','include.php?path=faq'),

	(2,1,'".$SQL->f(pkGetLang('setup_preset_navigation_contact'))."','?path=contact'),
	(2,2,'".$SQL->f(pkGetLang('setup_preset_navigation_imprint'))."','?path=content&contentid=1'),
	
	(3,1,'".$SQL->f(pkGetLang('setup_preset_navigation_forums'))."','?path=forumsdisplay'),
	(3,2,'".$SQL->f(pkGetLang('setup_preset_navigation_guestbook'))."','?path=guestbook'),
	(3,3,'".$SQL->f(pkGetLang('setup_preset_navigation_polls'))."','?path=pollarchive')"
	);

#gimme me some names to personalize the presets
$query = $SQL->query("SELECT id,value FROM ".pkSQLTAB_CONFIG." WHERE id IN ('site_name','site_url')");
while(list($key,$value) = $SQL->fetch_row($query))
	{
	$$key = @unserialize($value);
	}

list($adminnick)=$SQL->fetch_row($SQL->query("SELECT user_nick FROM ".pkSQLTAB_USER." WHERE user_id=1 LIMIT 1"));

#our brand new administrator gains a pm
$SQL->query("DELETE FROM ".pkSQLTAB_USER_PRIVATEMESSAGE);
$SQL->query("INSERT INTO ".pkSQLTAB_USER_PRIVATEMESSAGE." 
	SET im_to=1,
		im_title='".$SQL->f(pkGetLang('setup_preset_adminpm_title'))."',
		im_text='".$SQL->f(pkGetSpecialLang('setup_preset_adminpm',$adminnick))."',
		im_time='".pkTIME."',
		im_delautor=1");
	
#give them something to read
#content
$SQL->query("DELETE FROM ".pkSQLTAB_CONTENT);
$SQL->query("DELETE FROM ".pkSQLTAB_CONTENT_CATEGORY);
$SQL->query("DELETE FROM ".pkSQLTAB_CONTENT_THEME);

#categories
$SQL->query("INSERT INTO ".pkSQLTAB_CONTENT_CATEGORY."
	(contentcat_id,contentcat_name,contentcat_symbol) VALUES
	(1,'".$SQL->f($site_name)."','note-large.png'),
	(2,'".$SQL->f(pkGetLang('setup_preset_content_category_PHPKIT'))."','globe-large.png')"
	);

#some content
#1 - frontpage
#2 - news I
#3 - news II
#4 - imprint
#5 - article:summary presets
#6 - PHPKIT backlink
#7 - PHPKIT supportforums		
$SQL->query("INSERT INTO ".pkSQLTAB_CONTENT."
	(content_id,content_cat,content_autorid,content_option,content_time,content_title,content_text,content_altdat,content_comment_status) VALUES
	(1,1,1,0,".$SQL->i(pkTIME).",'".$SQL->f(pkGetLang('setup_preset_content_title_1'))."','".$SQL->f(pkGetSpecialLang('setup_preset_content_text_1',$site_url,$site_url,$site_url))."','',0),
	(2,1,1,2,".$SQL->i(pkTIME).",'".$SQL->f(pkGetLang('setup_preset_content_title_2'))."','".$SQL->f(pkGetLang('setup_preset_content_text_2'))."','".$SQL->f('http://www.loremipsum.de/')."',0),
	(3,1,1,2,".$SQL->i(pkTIME+1).",'".$SQL->f(pkGetSpecialLang('setup_preset_content_title_3',$site_name))."','".$SQL->f(pkGetSpecialLang('setup_preset_content_text_3',$site_name,$site_url))."','',0),
	(4,1,1,0,".$SQL->i(pkTIME).",'".$SQL->f(pkGetLang('setup_preset_content_title_4'))."','".$SQL->f(pkGetLang('setup_preset_content_text_4'))."','',0),
	
	(5,2,1,1,".$SQL->i(pkTIME).",'".$SQL->f(pkGetLang('setup_preset_content_title_5'))."','".$SQL->f(pkGetSpecialLang('setup_preset_content_text_5',$site_name))."','',0),
	(6,2,1,3,".$SQL->i(pkTIME).",'".$SQL->f(pkGetLang('setup_preset_content_title_6'))."','".$SQL->f(pkGetLang('setup_preset_content_text_6'))."','".$SQL->f('http://www.phpkit.com')."',0),
	(7,2,1,3,".$SQL->i(pkTIME).",'".$SQL->f(pkGetLang('setup_preset_content_title_7'))."','".$SQL->f(pkGetLang('setup_preset_content_text_7'))."','".$SQL->f('http://www.phpkit.com/de/forum')."',0),
	(8,2,1,0,".$SQL->i(pkTIME).",'".$SQL->f(pkGetLang('setup_preset_content_title_8'))."','".$SQL->f(pkGetLang('setup_preset_content_text_8'))."','',0)"
	);

#guestbook
$SQL->query("DELETE FROM ".pkSQLTAB_GUESTBOOK);
$SQL->query("INSERT INTO ".pkSQLTAB_GUESTBOOK." 
	SET gbook_id=1,
		gbook_time='".$SQL->i(pkTIME)."',
		gbook_autor='".$SQL->f(pkGetLang('setup_preset_gbook_author'))."',
		gbook_title='".$SQL->f(pkGetLang('setup_preset_gbook_title'))."',
		gbook_text='".$SQL->f(pkGetLang('setup_preset_gbook_text'))."'"
		);
		
#polls
$SQL->query("DELETE FROM ".pkSQLTAB_POLL);
$SQL->query("DELETE FROM ".pkSQLTAB_POLL_TOPIC);			
$SQL->query("DELETE FROM ".pkSQLTAB_POLL_COUNT);

$SQL->query	("INSERT INTO ".pkSQLTAB_POLL_TOPIC." 
	(votetheme_id,votetheme_multianswer,votetheme_time,votetheme_title,votetheme_description) VALUES
	(1,0,'".$SQL->i(pkTIME)."','".$SQL->f(pkGetLang('setup_preset_poll_title_1'))."','".$SQL->f(pkGetLang('setup_preset_poll_description_1'))."'),
	(2,1,'".$SQL->i(pkTIME)."','".$SQL->f(pkGetLang('setup_preset_poll_title_2'))."','".$SQL->f(pkGetLang('setup_preset_poll_description_2'))."')"
	);						

$SQL->query	("INSERT INTO ".pkSQLTAB_POLL." 
	(vote_themeid,vote_order,vote_text) VALUES
	(1,1,'".$SQL->f(pkGetLang('setup_preset_poll_text_1_1'))."'),
	(1,2,'".$SQL->f(pkGetLang('setup_preset_poll_text_1_2'))."'),				
	(1,3,'".$SQL->f(pkGetLang('setup_preset_poll_text_1_3'))."'),
	(1,4,'".$SQL->f(pkGetLang('setup_preset_poll_text_1_4'))."'),
	(1,5,'".$SQL->f(pkGetLang('setup_preset_poll_text_1_5'))."'),
	(1,6,'".$SQL->f(pkGetLang('setup_preset_poll_text_1_6'))."'),																
	
	(2,1,'".$SQL->f(pkGetLang('setup_preset_poll_text_2_1'))."'),
	(2,2,'".$SQL->f(pkGetLang('setup_preset_poll_text_2_2'))."'),
	(2,3,'".$SQL->f(pkGetLang('setup_preset_poll_text_2_3'))."'),
	(2,4,'".$SQL->f(pkGetLang('setup_preset_poll_text_2_4'))."'),
	(2,5,'".$SQL->f(pkGetLang('setup_preset_poll_text_2_5'))."'),
	(2,6,'".$SQL->f(pkGetLang('setup_preset_poll_text_2_6'))."')"
	);
	
#faq						
$SQL->query("DELETE FROM ".pkSQLTAB_FAQ);
$SQL->query("DELETE FROM ".pkSQLTAB_FAQ_CATEGORY);

$SQL->query	("INSERT INTO ".pkSQLTAB_FAQ_CATEGORY." 
	(faqcat_id,faqcat_title,faqcat_order) VALUES
	(1,'".$SQL->f(pkGetLang('setup_preset_faqcat_title_1'))."',1)"
	);

$SQL->query	("INSERT INTO ".pkSQLTAB_FAQ." 
	(faq_id,faq_order,faq_catid,faq_autorid,faq_time,faq_title,faq_answer) VALUES
	(1,1,1,1,'".$SQL->i(pkTIME)."','".$SQL->f(pkGetLang('setup_preset_faq_title_1'))."','".$SQL->f(pkGetLang('setup_preset_faq_answer_1'))."'),
	(2,2,1,1,'".$SQL->i(pkTIME)."','".$SQL->f(pkGetLang('setup_preset_faq_title_2'))."','".$SQL->f(pkGetLang('setup_preset_faq_answer_2'))."'),
	(3,3,1,1,'".$SQL->i(pkTIME)."','".$SQL->f(pkGetLang('setup_preset_faq_title_3'))."','".$SQL->f(pkGetLang('setup_preset_faq_answer_3'))."'),
	(4,4,1,1,'".$SQL->i(pkTIME)."','".$SQL->f(pkGetLang('setup_preset_faq_title_4'))."','".$SQL->f(pkGetLang('setup_preset_faq_answer_4'))."'),
	(5,5,1,1,'".$SQL->i(pkTIME)."','".$SQL->f(pkGetLang('setup_preset_faq_title_5'))."','".$SQL->f(pkGetLang('setup_preset_faq_answer_5'))."')"
	);

#forums						
$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_CATEGORY);
$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_THREAD);
$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_POST);
$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_FAVORITE);
$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_INFO);
$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_NOTIFY);


$SQL->query("INSERT INTO ".pkSQLTAB_FORUM_CATEGORY."
	SET forumcat_name='".$SQL->f(pkGetLang('setup_preset_forumcategory_title_1'))."',
		forumcat_description='".$SQL->f(pkGetSpecialLang('setup_preset_forumcategory_description_1',$site_name))."',
		forumcat_lastreply_autor='PHPKIT',
		forumcat_lastreply_time='".$SQL->i(pkTIME)."',
		forumcat_lastreply_threadid=1,
		forumcat_threadcount=1,
		forumcat_postcount=1");

$SQL->query("INSERT INTO ".pkSQLTAB_FORUM_THREAD."
	SET forumthread_catid=1,
		forumthread_lastreply_time='".$SQL->i(pkTIME)."',
		forumthread_lastreply_autor='PHPKIT',
		forumthread_title='".$SQL->f(pkGetSpecialLang('setup_preset_forumpost_title_1',$site_name))."',
		forumthread_autor='PHPKIT'");	

$SQL->query("INSERT INTO ".pkSQLTAB_FORUM_POST."
	SET forumpost_threadid=1,
		forumpost_time ='".$SQL->i(pkTIME)."',
		forumpost_title='".$SQL->f(pkGetSpecialLang('setup_preset_forumpost_title_1',$site_name))."',
		forumpost_text='".$SQL->f(pkGetLang('setup_preset_forumpost_text_1'))."',
		forumpost_autor='PHPKIT'");
		

/*$query = $SQL->query("SELECT * FROM ".pkSQLTAB_CONFIG);
while($array = $SQL->fetch_assoc($query))
	{
	pk::fprint($array);
	}

	
exit($bool ? 'SUCCESS' : 'FAILED');		*/
?>