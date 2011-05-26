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


function pkSiteException($exception=0)
	{
	global $config,$BBCODE,$LANG;

	
	$exception_key='';
	$exception_title='';
	$exception_text='';
	$lang='de';
	
	if(pkFRONTEND!='public')
		{
		header('Location: '.pkDIRWWWROOT.pkSITE.pkEXT);
		exit();
		}
		
	pkLoadLang('except');	
	
	if($exception)
		{
		$site_title=pkGetConfigF('site_name').' '.pkGetConfigF('site_title');
		}
	else
		{
		$site_title=getenv('SERVER_NAME');		
		$config=array('template_dir'=>pkDIRPUBLICTPL); 
		}

	switch($exception)
		{
		case 1 :
			$exception_key='forumsiteclosed';
			break;
		case 2 :
			$exception_key='offline';
			break;		
		case 3 :
			$exception_key='ban';
			break;
		default :
			$exception_key='nodb';
			break;
		}

	if($exception_key)
		{
		$exception_title=pkGetLang('exception_title_'.$exception_key);

		if($exception!=2)
			{
			$exception_text=pkGetLang('exception_text_'.$exception_key);
			}
		else
			{
			pkLoadClass($BBCODE,'bbcode');
			
			$exception_text=$BBCODE->parse($config['site_message'],1,1,1,1);
			}
		
		$link=$exception_key=='offline' ? pkHtmlLink(pkLinkAdmin(''),pkGetLang('administration')) : '';
		}

	eval("echo \"".pkTpl("site_exception")."\";");
	exit;
	}
?>