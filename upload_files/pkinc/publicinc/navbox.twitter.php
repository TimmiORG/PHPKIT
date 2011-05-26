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

pkLoadLang('social');

$fx_base = 'fx/default/';

$twitter_baseurl	= 'http://twitter.com/'; #nothing to change here - goes without www-subdomain
$twitter_feedurl	= 'http://twitter.com/statuses/user_timeline/%s.rss'; #its a pattern
#$twitter_feedurl	= 'http://twitter.com/statuses/friends_timeline/%s.rss'; #its a pattern - friends and you

$twitter_imgalt		= '%s@twitter.com'; #its a pattern
$twitter_feed_label	= 'RSS-Feed f&uuml;r %s Tweets'; #its a pattern - special lang


#possible images in the box
$twitter_rss_icon = $fx_base.'icons/rss-tiny.png';
$twitter_images = array(
	'bird-small'		=> 'icons/twitter-bird-small.png',
	'bird-half-small'	=> 'icons/twitter-bird-half-small.png',
	'bird-logo-small'	=> 'icons/twitter-logo-small.png',	
	);


#configurable values
$twitter_account	= pkGetConfigF('social_twitter_username');
$twitter_label		= pkGetConfigF('social_twitter_label');
$twitter_label		= empty($twitter_label) ? pkGetSpecialLang('social_twitter_label_default',$twitter_account) : $twitter_label;
$twitter_image		= 'bird-half-small'; #give them something - bird, bird-half, official logo


#twitter_feed_label = '';#user config
$twitter_feed_enable = 1;


#presets
$twitter_img = $twitter_account_link = $twitter_feed_link = $twitter_acoount = $boxlinks = '';

if(!empty($twitter_account))
	{
	#base twitter link
	$twitter_account_link = $twitter_baseurl.$twitter_account;
	$twitter_account_label = pkEntities(empty($twitter_label) ? $twitter_account_link : $twitter_label);


	#an image here
	if(isset($twitter_images[$twitter_image]))
		{
		$path = $fx_base.$twitter_images[$twitter_image];
		$alt = pkEntities(sprintf($twitter_imgalt,$twitter_acoount));
		$img = pkHtmlImage($path,$alt);
		
		$twitter_img = pkHtmlLink($twitter_account_link,$img,'','','small',$twitter_account_label);
		$twitter_img.= '<br />';#Revise
		}
	
	
	#updates as rss feed
	if($twitter_feed_enable)
		{
		$link	= sprintf($twitter_feedurl,$twitter_account);
		$label	= sprintf($twitter_feed_label,$twitter_account);
		$img	= pkHtmlImage($twitter_rss_icon,$label);
		
		$twitter_feed_link = '<br />'.#Revise
			pkHtmlLink($link,$img,'','','small',$label).
			' '.#a space makes it a bit more beautiful
			pkHtmlLink($link,$label,'','','small',$label);
		}
	eval("\$boxlinks[]=\"".pkTpl("navigation/twitter")."\";");
	}

return $boxlinks;
?>