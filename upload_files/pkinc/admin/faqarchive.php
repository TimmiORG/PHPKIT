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


if(!defined('pkFRONTEND') || pkFRONTEND!='admin')
	die('Direct access to this location is not permitted.');


if(!adminaccess('faq')) 
	return pkEvent('access_forbidden');
	

$catid=(isset($_REQUEST['catid']) && intval($_REQUEST['catid'])>0) ? intval($_REQUEST['catid']) : 0;


$getfaqcat=$SQL->query("SELECT * FROM ".pkSQLTAB_FAQ_CATEGORY." ORDER by faqcat_title ASC");
while($faqcat=$SQL->fetch_array($getfaqcat))
	{
	$faqcat_cache[$faqcat['faqcat_id']]=$faqcat;
	}
	
$getfaq=$SQL->query("SELECT * FROM ".pkSQLTAB_FAQ.($catid ? " WHERE faq_catid='".$catid."'" : '')." ORDER by faq_catid, faq_title");
while($faq=$SQL->fetch_array($getfaq))
	{
	$row=rowcolor($row);
	
	if(trim($faq_title=$faq['faq_title'])=='') 
		$faq_title='<font class="highlight">'.$lang['no_title'].'</font>';
	else
		$faq_title=pkEntities($faq['faq_title']);
	
	$faqcat=$faqcat_cache[$faq['faq_catid']];
	$faqcat['faqcat_title']=pkEntities($faqcat['faqcat_title']);
	
	eval("\$archiv_row.= \"".pkTpl("faq/archiv_row")."\";");
	}

eval("\$site_body.= \"".pkTpl("faq/archiv")."\";");
?>