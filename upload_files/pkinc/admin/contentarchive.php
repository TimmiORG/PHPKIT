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


if(!adminaccess('cms'))
	return pkEvent('access_forbidden');


$mode=(isset($_REQUEST['mode']) && ($_REQUEST['mode']=='revise' || $_REQUEST['mode'])=='delete') ? $_REQUEST['mode'] : false;
	

if($mode=='delete')
	{
	if(!adminaccess('contdelete'))
		return pkEvent('access_forbidden');
		
	$contentid=(isset($_REQUEST['contentid']) && intval($_REQUEST['contentid'])>0) ? intval($_REQUEST['contentid']) : 0;

	if(isset($_POST['action']))
		$ACTION=$_POST['action'];
	else
		$ACTION='view';
	
	$contentinfo=$SQL->fetch_array($SQL->query("SELECT content_title, content_id FROM ".pkSQLTAB_CONTENT." WHERE content_id='".$contentid."' LIMIT 1"));
	if($ACTION==$_POST['delete'] || $ACTION==$_POST['cancel'])
		{
		if($_POST['confirm']=='yes' && $contentinfo['content_id']) 
			$SQL->query("DELETE FROM ".pkSQLTAB_CONTENT." WHERE content_id='".$contentinfo['content_id']."'");
		
		pkHeaderLocation('contentarchive');
		}
		
	$contentinfo['content_title']=pkEntities($contentinfo['content_title']);
	
	eval("\$site_body.= \"".pkTpl("content/delete")."\";");
	return;
	}


if($mode=='revise')
	{
	if(is_array($_POST['content_id']))
		{
		unset($sqlcommand);
		
		foreach($_POST['content_id'] as $id)
			{
			$sqlcommand.=(($sqlcommand) ? ' OR ' : '')." content_id='".intval($id)."'";
			}
		
		
		if($_POST['change_to']=='del' && adminaccess('contdelete'))
			{
			$getcontent=$SQL->query("SELECT content_title, content_id FROM ".pkSQLTAB_CONTENT." WHERE ".$sqlcommand);
			while($contentinfo=$SQL->fetch_array($getcontent))
				{
				$row=rowcolor($row);
				$contentinfo['content_title']=pkEntities($contentinfo['content_title']);
				
				eval("\$historyalter_row.= \"".pkTpl("content/historyalter_row")."\";");
				}
			
			eval("\$site_body.= \"".pkTpl("content/historyalter")."\";");
			return;
			}
		elseif(adminaccess('contfree'))
			{
			if(isset($sqlcommand))
				{
				if(isset($_POST['action']))
					$ACTION=$_POST['action'];
				else
					$ACTION='view';
				
				if($_POST['change_to']=='open')
					$SQL->query("UPDATE ".pkSQLTAB_CONTENT." SET content_status=1 WHERE ".$sqlcommand);
				elseif($_POST['change_to']=='close')
					$SQL->query("UPDATE ".pkSQLTAB_CONTENT." SET content_status=0 WHERE ".$sqlcommand);
				elseif($ACTION==$_POST['delete'])
					$SQL->query("DELETE FROM ".pkSQLTAB_CONTENT." WHERE ".$sqlcommand);
				}
			}
		}
	
	pkHeaderLocation('contentarchive');
	}

	
	
if(($_REQUEST['status']==1 || $_REQUEST['status']==0) && adminaccess('contfree') && intval($_REQUEST['contentid'])>0)
 	{
	$SQL->query("UPDATE ".pkSQLTAB_CONTENT." SET content_status='".intval($_REQUEST['status'])."' WHERE content_id='".intval($_REQUEST['contentid'])."'");
	}


$epp=20;
$orderhash=array('titled','titlea','catd','cata','typed','typea','statusd','statusa','idd','ida','timed','timea','timed');
$order=(isset($_REQUEST['order']) && in_array($_REQUEST['order'],$orderhash)) ? $_REQUEST['order'] : '';

$entries=(isset($_REQUEST['entries']) && intval($_REQUEST['entries'])>0) ? intval($_REQUEST['entries']) : 0;
$searchtype=$sqlsearch='';
$searchcat=0;


if(isset($_REQUEST['action']) && ($_REQUEST['action']==$lang['search'] || $_REQUEST['action']=='search'))
	{
	$action='search';
	
	if(isset($_REQUEST['searchid']) && $_REQUEST['searchid']!='ID' && intval($_REQUEST['searchid'])>0)
		{
		$sqlsearch=" content_id='".intval($_REQUEST['searchid'])."' ";
		}
	else
		{
		if(isset($_REQUEST['searchtext']) && !empty($_REQUEST['searchtext']))
			{
			$searchtext=$_REQUEST['searchtext'];
			
			$sqlsearch.=" (content_title LIKE '%".$SQL->f($_REQUEST['searchtext'])."%' OR 
				content_text LIKE '%".$SQL->f($_REQUEST['searchtext'])."%') ";
			}
		
		if(isset($_REQUEST['searchtype']) && $_REQUEST['searchtype']!=='' && intval($_REQUEST['searchtype'])>=0 && intval($_REQUEST['searchtype'])<=4)
			{
			$searchtype=intval($_REQUEST['searchtype']);
			$var='type'.intval($_REQUEST['searchtype']);
			$$var='selected';
			
			$sqlsearch.=($sqlsearch ? ' AND ' : '')." content_option=".intval($_REQUEST['searchtype'])." ";
			}
		else
			$searchtype='';
		
		if(isset($_REQUEST['searchcat']) && intval($_REQUEST['searchcat'])>0)
			{
			$searchcat=intval($_REQUEST['searchcat']);
			$sqlsearch.=($sqlsearch ? ' AND ' : '')." content_cat='".$searchcat."' ";
			}
			
		}
	
	if($sqlsearch)
		$sqlsearch=" WHERE ".$sqlsearch;
	}


$getcatinfo=$SQL->query("SELECT * FROM ".pkSQLTAB_CONTENT_CATEGORY." ORDER by contentcat_name ASC");
while($contentcat=$SQL->fetch_array($getcatinfo))
	{
	$catinfo_cache[$contentcat['contentcat_id']]=$contentcat;
	$contentcat['contentcat_name']=pkEntities($contentcat['contentcat_name']);
	
	if($searchcat==$contentcat['contentcat_id'])
		$selected='selected';
	else
		unset($selected);
	
	eval("\$option_cats.= \"".pkTpl("content/history_option_cats")."\";");
	}


$getcatinfo=$SQL->query("SELECT * FROM ".pkSQLTAB_CONTENT_THEME);
while($contenttheme=$SQL->fetch_array($getcatinfo))
	{
	$themeinfo_cache[$contenttheme['contenttheme_id']]=$contenttheme;
	}


$counter=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_CONTENT." ".$sqlsearch));
$side_link=sidelinkfull($counter[0], $epp, $entries, "include.php?path=contentarchive&searchtext=".pkEntities($searchtext)."&searchtype=".$searchtype."&searchid=".$searchid."&searchcat=".$searchcat."&action=search&order=".$order);
$order_string="&searchtext=".pkEntities($searchtext)."&searchtype=".$searchtype."&searchid=".$searchid."&searchcat=".$searchcat."&action=search&order=".$order;


$ordertitle="titlea";
$ordercat="cata";
$ordertime="timed";
$ordertype="typea";
$orderid="idd";
$orderstatus="statusa";

switch ($order) {
    case "titlea":
        $order_sql=" ORDER by content_title ASC";
	$ordertitle="titled";
	$order="titled";
        break;
    case "titled":
        $order_sql=" ORDER by content_title DESC";
	$ordertitle="titlea";
	$order="titlea";
        break;
    case "cata":
        $order_sql=" ORDER by content_cat ASC";
	$ordercat="catd";
	$order="catd";
        break;
    case "catd":
        $order_sql=" ORDER by content_cat DESC"; $ordercat="cata"; $order="cata";
        break;
    case "typea":
        $order_sql=" ORDER by content_option ASC";
	$ordertype="typed";
	$order="typed";
        break;
    case "typed":
        $order_sql=" ORDER by content_option DESC";
	$ordertype="typea";
	$order="typea";
        break;
    case "statusa":
        $order_sql=" ORDER by content_status ASC";
	$orderstatus="statusd";
	$order="statusd";
        break;
    case "statusd":
        $order_sql=" ORDER by content_status DESC";
	$orderstatus="statusa";
	$order="statusa";
        break;
    case "ida":
	$order_sql=" ORDER by content_id ASC";
	$orderid="idd";
	$order="idd";
        break;
    case "idd":
        $order_sql=" ORDER by content_id DESC";
	$orderid="ida";
	$order="ida";
        break;
    case "timea":
        $order_sql=" ORDER by content_time ASC";
	$ordertime="timed";
	$order="timed";
        break;
    case "timed":
        $order_sql=" ORDER by content_time DESC";
	$ordertime="timea";
	$order="timea";
        break; 
    default :  
        $order_sql=" ORDER by content_time DESC";
	$order="timed";     
        
}
   


$getcontentinfo=$SQL->query("SELECT * FROM ".pkSQLTAB_CONTENT.$sqlsearch.$order_sql." LIMIT ".$entries.", ".$epp);
while($contentinfo=$SQL->fetch_array($getcontentinfo))
	{
	$row=rowcolor($row);
	$catinfo=$catinfo_cache[$contentinfo['content_cat']];
	$catinfo['contentcat_name']=pkEntities($catinfo['contentcat_name']);	
	
	if($contentinfo['content_themeid']>0)
		{
		$contenttheme_info=$themeinfo_cache[$contentinfo['content_themeid']];
		$contenttheme_info=pkEntities($contenttheme_info['contenttheme_name']);
		
		eval("\$contenttheme_info= \"".pkTpl("content/history_themeinfo")."\";");
		}
	else
		unset($contenttheme_info);

	
	if(empty($contentinfo['content_title']))
		$content_title='<span class="highlight">'.$lang['no_title'].'</span>';
	else
		$content_title=pkEntities($contentinfo['content_title']);
	
	
	$content_date=formattime($contentinfo['content_time'],'','date');
	$content_time=formattime($contentinfo['content_time'],'','time');

	if($contentinfo['content_option']==0)
		$content_type=$lang['content'];
	elseif($contentinfo['content_option']==1)
		$content_type=$lang['article'];
	elseif($contentinfo['content_option']==2)
		$content_type=$lang['news'];
	elseif($contentinfo['content_option']==3)
		$content_type=$lang['link'];
	elseif($contentinfo['content_option']==4)
		$content_type=$lang['download'];

	if($contentinfo['content_status']!=1)
  		eval("\$content_status= \"".pkTpl("content/history_row_close")."\";");
	else
  		eval("\$content_status= \"".pkTpl("content/history_row_open")."\";");

	eval("\$archiv_row.= \"".pkTpl("content/history_row")."\";");
	}

eval("\$site_body.= \"".pkTpl("content/history")."\";");
?>