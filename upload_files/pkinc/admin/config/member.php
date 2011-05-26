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
	$user_namemin = $ENV->_post('user_namemin');
	$user_namemin = intval($user_namemin)>0 && intval($user_namemin)<=50 ? intval($user_namemin) : 1;
	
	$user_namemax = $ENV->_post('user_namemax');
	$user_namemax = intval($user_namemax)>$user_namemin && intval($user_namemax)<=50 ? intval($user_namemax) : 50;
	
	$member_infoshow = $ENV->_post('member_infoshow');
	$member_infoshow = in_array($member_infoshow,$userstatus_hash) ? $member_infoshow : $userstatus_hash[0];
	
	
	#set the save values
	$save_values['user_namemin'] = $user_namemin; #validated above
	$save_values['user_namemax'] = $user_namemax; #validated above
	
	$save_values['text_ubb']		= $ENV->_post_ibool('text_ubb');
	$save_values['text_smilies']	= $ENV->_post_ibool('text_smilies');
	$save_values['text_images']		= $ENV->_post_ibool('text_images');
	
	$save_values['member_infoshow']	= $member_infoshow; #validated above
	$save_values['member_epp']		= $ENV->_post_id('member_epp');
	$save_values['member_gbook']	= $ENV->_post_ibool('member_gbook');
	$save_values['member_mailer']	= $ENV->_post_ibool('member_mailer');

	$save_values['user_delete']		= $ENV->_post_ibool('user_delete');
	$save_values['user_ghost']		= $ENV->_post_ibool('user_ghost');
	$save_values['user_design']		= $ENV->_post_ibool('user_design');
	$save_values['user_pndelete']	= $ENV->_post_id('user_pndelete');
	
	return; #dont forget this
	}


$member_showinfo1 = pkGetConfig('member_infoshow') == 'admin' ? $_selected : '';
$member_showinfo2 = pkGetConfig('member_infoshow') == 'mod' ? $_selected : '';
$member_showinfo3 = pkGetConfig('member_infoshow') == 'member' ? $_selected : '';
$member_showinfo4 = pkGetConfig('member_infoshow') == 'user' ? $_selected : '';
$member_showinfo0 = pkGetConfig('member_infoshow') == 'guest' ? $_selected : '';

$info_delete1 = pkGetConfig('user_delete')==1 ? $_selected : '';
$info_delete2 = pkGetConfig('user_delete')==2 ? $_selected : '';
$info_delete0 = $info_delete1 || $info_delete2 ? '' : $_selected;

$info_ubb1 = pkGetConfig('text_ubb')==1 ? $_checked : '';
$info_ubb0 = $info_ubb1 ? '' : $_checked;

$info_smilies1 = pkGetConfig('text_smilies')==1 ? $_checked : '';
$info_smilies0 = $info_smilies1 ? '' : $_checked;

$info_images1 = pkGetConfig('text_images') ? $_checked : '';
$info_images0 = $info_images1 ? '' : $_checked;

$info_gbook1 = pkGetConfig('member_gbook') ? $_checked : '';
$info_gbook0 = $info_gbook1 ? '' : $_checked;

$info_mailer1 = pkGetConfig('member_mailer') ? $_checked : '';
$info_mailer0 = $info_mailer1 ? '' : $_checked;		

$info_ghost1 = pkGetConfig('user_ghost') ? $_checked : '';
$info_ghost0 = $info_ghost1 ? '' : $_checked;

$info_design1 = pkGetConfig('user_design') ? $_checked : '';
$info_design0 = $info_design1 ? '' : $_checked;

$user_pndelete = pkGetConfigF('user_pndelete');
?>