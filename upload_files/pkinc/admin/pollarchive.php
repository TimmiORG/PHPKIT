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


if(!adminaccess('vote'))
	return pkEvent('access_forbidden');
	

$voteid=(isset($_REQUEST['voteid']) && intval($_REQUEST['voteid'])>0) ? intval($_REQUEST['voteid']) : 0;


if($_POST['action']=='-1')
	{
	$ACTION='return';
	}
elseif(isset($_POST['action']))
	{
	$ACTION=$_POST['action'];
	}
elseif($_REQUEST['result']!='')
	{
	$ACTION='result';
	$voteid=$_REQUEST['result'];
	}
else
	{
	$ACTION='return';
	}

if($ACTION!='return' && $ACTION!='result')
	{
	if($ACTION==$_POST['delete'])
		{
		pkHeaderLocation('pollcompose','delete','voteid='.$voteid);
		}
	elseif($ACTION==$_POST['edit'])
		{
		pkHeaderLocation('pollcompose','','voteid='.$voteid);
		}
	
	$set=(isset($_POST['enable']) && $ACTION==$_POST['enable']) ? 1 : 0;

	$SQL->query("UPDATE ".pkSQLTAB_POLL_TOPIC." SET votetheme_status=".$set." WHERE votetheme_id='".$voteid."'");
	pkHeaderLocation('pollarchive');
	}

if($ACTION=='result' && $voteid)
	{
	unset($voteinfo_hash);
	$sum=0;
	
	if(isset($_GET['order']) && $_GET['order']=='count')
		{
		$sqlorder='vote_order ASC';
		$order='order';
		}
	else
		{
		$sqlorder='vote_counter DESC';
		$order='count';
		}
	
	
	$votetheme=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_POLL_TOPIC." WHERE votetheme_id='".$voteid."' LIMIT 1"));
		
	$getvoteinfo=$SQL->query("SELECT
		*
		FROM ".pkSQLTAB_POLL." 
		WHERE vote_themeid='".$voteid."'
		ORDER BY ".$sqlorder);
	while($voteinfo=$SQL->fetch_array($getvoteinfo))
		{
		$voteinfo_hash[]=$voteinfo;
		$sum+=$voteinfo['vote_counter'];
		}
	
	if(is_array($voteinfo_hash))
		{
		foreach($voteinfo_hash as $voteinfo)
			{
			$row=rowcolor($row);
			$voteinfo['vote_text']=pkEntities($voteinfo['vote_text']);
			$vote_percent=($voteinfo['vote_counter']>0) ? number_format((($voteinfo['vote_counter']*100)/$sum),0,",",".") : 0;
			
			eval("\$result_row.=\"".pkTpl("vote/archiv_result_row")."\";");
			}
		
		eval("\$result_row.= \"".pkTpl("vote/archiv_result_summary")."\";");
		}
	else
		{
		eval("\$result_row.= \"".pkTpl("vote/archiv_result_empty")."\";");
		}
	
	
	$votetheme['votetheme_title']=pkEntities($votetheme['votetheme_title']);
	
	eval("\$site_body.= \"".pkTpl("vote/archiv_result")."\";");
	return;
	}


$epp=15;
$entries=(isset($_REQUEST['entries']) && intval($_REQUEST['entries'])>0) ? intval($_REQUEST['entries']) : 0;

	
$counter=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_POLL_TOPIC));
$sidelink=sidelinkfull($counter[0],$epp,$entries,"include.php?path=pollarchive","headssmall");

$getvotetheme=$SQL->query("SELECT * FROM ".pkSQLTAB_POLL_TOPIC." ORDER by votetheme_time DESC LIMIT ".$entries.",".$epp);
while($votetheme=$SQL->fetch_array($getvotetheme))
	{
	$row=rowcolor($row);
	$summ=$SQL->fetch_array($SQL->query("SELECT SUM(vote_counter) FROM ".pkSQLTAB_POLL." WHERE vote_themeid='".$votetheme['votetheme_id']."'")); 

	$counter=($summ[0]>0) ? $summ[0] : 0;
	$expired=($votetheme['votetheme_expire']) ? ($votetheme['votetheme_expire']>pkTIME ? 0 : 1) : 0;
	
	$votetheme_title=(empty($votetheme['votetheme_title'])) ? '<span class="highlight">'.$lang['no_title'].'</span>' : pkEntities($votetheme['votetheme_title']);
	$expired_msg=$expired ? pkGetLang('validity_period_expired') : '';
	$votetheme_description=pkEntities($votetheme['votetheme_description']);
	$votetheme_maxvotes=$votetheme['votetheme_maxvotes'];

	
	if($votetheme['votetheme_rights']=='admin')
		$vote_rights=$lang['admin'];
	elseif($votetheme['votetheme_rights']=='mod')
		$vote_rights=$lang['mod'];
	elseif($votetheme['votetheme_rights']=='member')
		$vote_rights=$lang['member'];
	elseif($votetheme['votetheme_rights']=='user')
		$vote_rights=$lang['user'];
	else
		$vote_rights=$lang['guest'];
	
	
	if($votetheme['votetheme_status']==1 && !$expired)
		{
		if($votetheme['votetheme_time']>pkTIME)
			$statusimage='yellow';
		else
			$statusimage='green';
		
		eval("\$alter_status= \"".pkTpl("vote/archiv_row_deactivate")."\";");
		}
	else 
		{
		$statusimage='red';
		
		eval("\$alter_status= \"".pkTpl("vote/archiv_row_activate")."\";");
		}
	
	
	$votetheme_time=formattime($votetheme['votetheme_time']);
	
	eval("\$archiv_row.= \"".pkTpl("vote/archiv_row")."\";");
	}

eval("\$site_body.= \"".pkTpl("vote/archiv")."\";");
?>