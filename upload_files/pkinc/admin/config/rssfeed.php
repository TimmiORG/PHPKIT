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


$mode_hash = pkCfgData('rss-types');

if($ACTION==$_POST['save'])
	{
	foreach($mode_hash as $mode)
		{
		$limit = $ENV->_post_id('rss_limit_'.$mode);
		$limit = $limit && $limit<=100 ? $limit : 10;
		
		$save_values['rss_enable_'.$mode] = $ENV->_post_ibool('rss_enable_'.$mode);
		$save_values['rss_title_'.$mode] = $ENV->_post('rss_title_'.$mode);
		$save_values['rss_limit_'.$mode] = $limit;
		}	


	$save_values['rss_page_headline'] = $ENV->_post('rss_page_headline');
	$save_values['rss_page_text'] = $ENV->_post('rss_page_text');
	return; #dont forget this
	}


$rss_feed_modes = '';

foreach($mode_hash as $mode)
	{
	$enable_vname = 'rss_enable_'.$mode;
	$enable_checkbox1 = pkGetConfig($enable_vname)==1 ? $_checked : '';
	$enable_checkbox0 = $enable_checkbox1 ? '' : $_checked;
	
	$title_vname	= 'rss_title_'.$mode;
	$title_value	= pkGetConfigF($title_vname);
	
	$limit_vname	= 'rss_limit_'.$mode;
	$limit_value	= pkGetConfigF($limit_vname);

	#lang vars
	$lkey_headline		= 'rss_config_headline_'.$mode;
	$lkey_enable_label	= 'rss_config_enable_label_'.$mode;
	$lkey_enable_desc	= 'rss_config_enable_desc_'.$mode;
	$lkey_title_label	= 'rss_config_title_label_'.$mode;
	$lkey_title_desc	= 'rss_config_title_desc_'.$mode;
	$lkey_limit_label	= 'rss_config_limit_label_'.$mode;
	$lkey_limit_desc	= 'rss_config_limit_desc_'.$mode;	
	
	eval("\$rss_feed_modes.= \"".pkTpl("config_rssfeed_modes")."\";");
	}


#global - selection page
$rss_page_headline	= pkGetConfigF('rss_page_headline');
$rss_page_text		= pkGetConfigF('rss_page_text');
?>