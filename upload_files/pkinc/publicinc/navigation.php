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



$nid=0;

if($ENV->_isset_request('nid'))
	{
	$SESSION->get('navid')==$ENV->_request_id('nid') ? $SESSION->deset('navid') : $SESSION->set('navid',$nid=$ENV->_request_id('nid'));
	}
else
	{
	$nid=$SESSION->exists('navid') ? $SESSION->get('navid') : 0;
	}


$align_hash = array(0=>'left',1=>'right',2=>'top',3=>'bottom');
$class_hash = array(1=>'important',2=>'synonym',3=>'accent',4=>'allimportant');	#use: <em class="%s">...


$navigation_top = $navigation_bottom = $navigation_left = $navigation_right = '';
$navigation_top_cats = $navigation_topcat = $navigation_topbody = '';
$navigation_bottom_cats = $navigation_bottomcat = $navigation_bottombody = '';
$navcat_hash = $info_hash = array();


$sqlcommand = '';
$query = $SQL->query("SELECT 
		*
	FROM ".pkSQLTAB_NAVIGATION_CATEGORY." 
	WHERE navigationcat_status='1' AND ".
	sqlrights("navigationcat_rights").
	($pkNAVIGATIONHIDE['left'] ? ' AND navigationcat_align<>0' : '').
	($pkNAVIGATIONHIDE['right'] ? ' AND navigationcat_align<>1' : '')."
	ORDER by navigationcat_order ASC");
while($navcat=$SQL->fetch_assoc($query))
	{
	$navcat_hash[$navcat['navigationcat_id']]=$navcat;
	$sqlcommand.=empty($sqlcommand) ? "SELECT * FROM ".pkSQLTAB_NAVIGATION." WHERE navigation_cat IN(0,".$navcat['navigationcat_id'] : ",".$navcat['navigationcat_id'];
	}

if(!empty($sqlcommand))
	{
	$sqlcommand.=") AND navigation_status='1' ORDER by navigation_order";
	
	$query=$SQL->query($sqlcommand);
	while($info=$SQL->fetch_assoc($query))
		{
		$info_hash[$info['navigation_cat']][]=$info;
		}
	}


foreach($navcat_hash as $navcat)
	{
	$navigation_head='';
	$navigation_body='';
	#make css-id
	$navboxid='pknavboxid'.$navcat['navigationcat_id'];	
		
	
	$navalign = $align_hash[$navcat['navigationcat_align']];
	$navcatid = $navcat['navigationcat_id'];

	#box headline
	if((!empty($navcat['navigationcat_title']) && $navcat['navigationcat_showtitle']==1) || $navcat['navigationcat_open']!=1) 
		{
		$title=$navcat['navigationcat_title'];

		if($navcat['navigationcat_open']!=1 || !empty($navcat['navigationcat_link']))
			{
			$link= empty($navcat['navigationcat_link']) ? '?'.$ENV->getvar('QUERY_STRING') : $navcat['navigationcat_link'];

			if(!$navcat['navigationcat_open'])
				{
				$link.= (strpos($link,'?')===false ? '?' : '&').'nid='.$navcat['navigationcat_id'];
				}
			
			$link = pkEntities($link);

			$target=(!empty($navcat['navigationcat_link_target'])) ? pkEntities($navcat['navigationcat_link_target']) : '';
			

			$nav_headline = pkHtmlLink($link,$title,$target,'pkncid'.$navcat['navigationcat_id'],'pkcontent_hl_'.$navalign);
			}
		else
			{
			$nav_headline = $title;		
			}
		
		eval("\$navigation_head=\"".pkTpl("navigation/content_hl_".$navalign)."\";");
		}


	#navbox
	if($navcat['navigationcat_box']!='' && ($navcat['navigationcat_open']==1 || $nid==$navcat['navigationcat_id']))
		{
		$boxlinks=include(pkDIRPUBLICINC.$navcat['navigationcat_box']);
		
		if(is_array($boxlinks) && ($navcat['navigationcat_open']==1 || $nid==$navcat['navigationcat_id']))
			{
			foreach($boxlinks as $nav_link)
				{
				if(!empty($nav_link))
					{
					eval("\$navigation_body.=\"".pkTpl("navigation/content_li_".$navalign)."\";");
					}
				}
			}
			
		unset($boxlinks);
		#END navbox
		}
	#links
	elseif(array_key_exists($navcat['navigationcat_id'],$info_hash))
		{
		foreach($info_hash[$navcat['navigationcat_id']] as $info)
			{
			if(!($info['navigation_cat']==$navcat['navigationcat_id'] && getrights($info['navigation_userstatus']) && ($navcat['navigationcat_open']==1 || $nid==$navcat['navigationcat_id'])))
				continue;

			$nav_link=$info['navigation_title'];
		
			if(!empty($info['navigation_link']))
				{
				$link = pkEntities($info['navigation_link']);
				$target=$info['navigation_type'] ? '_blank' : '';
						
				$value=$info['navigation_option'] ? '<em class="'.$class_hash[$info['navigation_option']].'">'.$nav_link.'</em>' : $nav_link;

				$nav_link=pkHtmlLink($link,$value,$target,'pknid'.$info['navigation_id'],'pkcontent_a_'.$navalign);
				}
						
			if(!empty($nav_link))
				{
				eval("\$navigation_body.=\"".pkTpl("navigation/content_li_".$navalign)."\";");
				}
			}#end foreach
		}#end elseif
	#end links
			
	if($navalign=='left' || $navalign=='right')
		{
		eval("\$navigation_".$navalign.".= \"".pkTpl("navigation/content_box_".$navalign)."\";");
		}
	elseif($navalign=='top')
		{
		$navigation_top.=$navigation_head;
		
		if(!empty($navigation_body))
			{
			eval("\$navigation_sub_top.=\"".pkTpl("navigation/content_box_sub_".$navalign)."\";");
			}
		}
	elseif($navalign=='bottom')
		{
		$navigation_bottom.=$navigation_head;
		
		if(!empty($navigation_body))
			{
			eval("\$navigation_sub_bottom.=\"".pkTpl("navigation/content_box_sub_".$navalign)."\";");
			}
		}
	}#END foreach
	
#add ul's for top and bottom and
if(!empty($navigation_top))
	{
	$navigation_top = '<ul class="pkcontent_box_top">'.$navigation_top.'</ul>';
	}

if(!empty($navigation_bottom))
	{
	$navigation_bottom = '<ul class="pkcontent_box_bottom">'.$navigation_bottom.'</ul>';
	}	
?>