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


if($ACTION==$_POST['save'])
	{
	$forum_postorder = $ENV->_post('forum_postorder');
	$forum_postorder = $forum_postorder=='ASC' || $forum_postorder=='DESC' ? $forum_postorder : 'ASC';		
	
	#set the save values
	$save_values['forum_eod']			= $ENV->_post_ibool('forum_eod');
	$save_values['forum_standalone']	= $ENV->_post_id('forum_standalone');
	$save_values['forum_ubb']			= $ENV->_post_ibool('forum_ubb');
	$save_values['forum_smilies']		= $ENV->_post_ibool('forum_smilies');
	
	$save_values['forum_start']			= $ENV->_post('forum_start');
	$save_values['forum_button']		= $ENV->_post('forum_button');
	
	$save_values['forum_searcheod']		= $ENV->_post_ibool('forum_searcheod');
	$save_values['forum_showmod']		= $ENV->_post_ibool('forum_showmod');
	$save_values['forum_maxfav']		= $ENV->_post_id('forum_maxfav');
	$save_values['forum_viewreply']		= $ENV->_post_id('forum_viewreply');
	$save_values['forum_showrank']		= $ENV->_post_ibool('forum_showrank');
	$save_values['forum_postorder']		= $forum_postorder; #validated above
	$save_values['forum_structur']		= $ENV->_post_ibool('forum_structur');
	$save_values['forum_doublepost']	= $ENV->_post_ibool('forum_doublepost');
	$save_values['forum_showbd']		= $ENV->_post_ibool('forum_showbd');
	$save_values['forum_images']		= $ENV->_post_ibool('forum_images');
	
	#titles
	$save_values['forum_title_prefix'] = $ENV->_post('forum_title_prefix');
	$save_values['forum_title_suffix'] = $ENV->_post('forum_title_suffix');
	$save_values['forum_title_forumsdisplay'] = $ENV->_post('forum_title_forumsdisplay');
	
	return; #dont forget this
	}


$forum_eod1 = pkGetConfig('forum_eod')==1 ? $_checked : '';
$forum_eod0 = $forum_eod1 ? '' : $_checked;

$option_ubb1 = pkGetConfig('forum_ubb')==1 ? $_selected : '';
$option_ubb0 = $option_ubb1 ? '' : $_selected;

$option_smilies1 = pkGetConfig('forum_smilies')==1 ? $_selected : '';
$option_smilies0 = $option_smilies1 ? '' : $_selected;

$option_images1 = pkGetConfig('forum_images')==1 ? $_selected : '';
$option_images0 = $option_images1 ? '' : $_selected;

$option_searcheod1 = pkGetConfig('forum_searcheod')==1 ? $_selected : '';
$option_searcheod0 = $option_searcheod1 ? '' : $_selected;

$option_showmod1 = pkGetConfig('forum_showmod')==1 ? $_selected : '';
$option_showmod0 = $option_showmod1 ? '' : $_selected;

$option_showrank1 = pkGetConfig('forum_showrank')==1 ? $_selected : '';
$option_showrank0 = $option_showrank1 ? '' : $_selected;

$option_postorder1 = pkGetConfig('forum_postorder')=='DESC' ? $_selected : '';
$option_postorder0 = $option_postorder1 ? '' : $_selected;

$option_structur1 = pkGetConfig('forum_structur')==1 ? $_selected : '';
$option_structur0 = $option_structur1 ? '' : $_selected;

$option_doublepost1 = pkGetConfig('forum_doublepost')==1 ? $_selected : '';
$option_doublepost0 = $option_doublepost1 ? '' : $_selected;
	
$forum_showbd1 = pkGetConfig('forum_showbd')==1 ? $_checked : '';
$forum_showbd0 = $forum_showbd1 ? '' : $_checked;
	
$option_standalone2 = pkGetConfig('forum_standalone')==2 ? $_selected : '';
$option_standalone3 = pkGetConfig('forum_standalone')==3 ? $_selected : '';
$option_standalone9 = pkGetConfig('forum_standalone')==9 ? $_selected : '';
$option_standalone0 = $option_standalone2 || $option_standalone3 || $option_standalone9 ? '' : $_selected;
	
$config_forum_start		= pkGetConfigF('forum_start');
$config_forum_button	= pkGetConfigF('forum_button');

#titles
$forum_title_prefix = pkGetConfigF('forum_title_prefix');
$forum_title_suffix = pkGetConfigF('forum_title_suffix');
$forum_title_forumsdisplay = pkGetConfigF('forum_title_forumsdisplay');
?>