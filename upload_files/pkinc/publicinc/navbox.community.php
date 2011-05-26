<?php
# PHPKIT Web Content Management System
# --------------------------------------------
# Copyright (c) 2002-2007 Gersöne & Schott GbR
#
# This file / the PHPKIT-software is no freeware!
# For further informations please vistit our website
# or contact us via email:
#
# Diese Datei / die PHPKIT-Software ist keine Freeware!
# Für weitere Information besuchen Sie bitte unsere 
# Webseite oder kontaktieren uns per E-Mail:
#
# Website : http://www.phpkit.de
# Mail    : info@phpkit.de
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATIONS
#
# SIE SIND NICHT BERECHTIGT, UNRECHTMÄSSIGE KOPIEN DIESER
# DATEI ZU ERSTELLEN UND/ODER DIESE INFORMATIONEN ZU ENTFERNEN


if(!defined('pkFRONTEND') || pkFRONTEND!='public')
	die('Direct access to this location is not permitted.');

$boxlinks=array();

#login/logout
if(!pkGetUservalue('id'))
	{
	if($config['nb_community_box']!=2)
		$boxlinks[0]=pkHtmlLink(pkLink('login'),pkGetLang('login'),'','pknidcommunity0','pkcontent_a_'.$navalign);
	else
		{
		$form_action=pkLink('','','login=1');
		$login_remove_path=pkEntities($ENV->getvar('QUERY_STRING'));

		$lang_username=pkGetLang('username');
		$lang_password=pkGetLang('password');
		$lang_bl_login=pkGetLang('bl_login');

		eval("\$boxlinks[0]=\"".pkTpl("navigation/community_loginform")."\";");

		$boxlinks[1]=pkHtmlLink(pkLink('login','lostpassword'),pkGetLang('password_lost?'),'','','pkcontent_a_'.$navalign);
		}
	}
else
	$boxlinks[0]=pkHtmlLink(pkLink('','','logout=1'),pkGetLang('logout'),'','','pkcontent_a_'.$navalign);


#memberlist
if(getrights(pkGetConfig('member_infoshow')))
	$boxlinks[2]=pkHtmlLink(pkLink('userslist'),pkGetLang('users'),'','','pkcontent_a_'.$navalign);


#register & users >> profile
if(intval(pkGetUservalue('id'))>0)
	{
	$boxlinks[3]=pkHtmlLink(pkLink('userprofile'),pkGetLang('profile'),'','','pkcontent_a_'.$navalign);
	
	if(pkGetUservalue('imoption'))
		{
		if(pkGetUserMessageCount())
			{
			$boxlinks[4]=pkHtmlLink(pkLink('privatemessages','','imid=new'),pkGetSpecialLang('private_message'),'','','pkcontent_a_'.$navalign,pkGetSpecialLang('private_message'));
			}
		else
			$boxlinks[4]=pkHtmlLink(pkLink('privatemessages'),pkGetLang('pn_center'),'','','pkcontent_a_'.$navalign,pkGetLang('write_private_message'));
		}
	
	#admin link
	if(adminaccess('adminarea'))
		$boxlinks[5]=pkHtmlLink(pkLinkAdmin(),pkGetLang('administration'),'','','pkcontent_a_'.$navalign);
	}
elseif(pkGetConfig('user_registry')!=0)
	$boxlinks[3]=pkHtmlLink(pkLink('registration'),pkGetLang('register'),'','','pkcontent_a_'.$navalign);
	

return $boxlinks;
?>