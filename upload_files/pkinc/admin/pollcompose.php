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
	

$mode=(isset($_REQUEST['mode']) && $_REQUEST['mode']=='delete') ? 'delete' : NULL;
$voteid=(isset($_REQUEST['voteid']) && intval($_REQUEST['voteid'])>0) ? intval($_REQUEST['voteid']) : 0;
$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';
$step=(isset($_REQUEST['step']) && intval($_REQUEST['step'])==2) ? 2 : 0;


if($mode=='delete')
	{
	$voteinfo=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_POLL_TOPIC." WHERE votetheme_id='".$voteid."' LIMIT 1"));

	if(isset($_POST['action']) && $_POST['action']==$_POST['delete'] && $_POST['confirm']=='yes') 
		{
		$SQL->query("DELETE FROM ".pkSQLTAB_POLL_TOPIC." WHERE votetheme_id='".$voteid."' LIMIT 1");
		$SQL->query("DELETE FROM ".pkSQLTAB_POLL." WHERE vote_themeid='".$voteid."'");
		
		pkHeaderLocation('pollarchive');
		}
	
	if(isset($_POST['action'])) 
		{
		pkHeaderLocation('pollarchive');
		}
	
	eval("\$site_body.= \"".pkTpl("vote/delete")."\";");
	return;
	}
	

if($ACTION==$_POST['save'] || $ACTION==$_POST['next'])
	{
	$votetheme_time=formattime(pkMkTime($ENV->_post_id('vote_time_h'),$ENV->_post_id('vote_time_mm'),0,$ENV->_post_id('vote_time_m'),$ENV->_post_id('vote_time_d'),$ENV->_post_id('vote_time_y')),'','istamp');
	$votetheme_expire=pkMkTime($ENV->_post_id('vote_expire_h'),$ENV->_post_id('vote_expire_mm'),0,$ENV->_post_id('vote_expire_m'),$ENV->_post_id('vote_expire_d'),$ENV->_post_id('vote_expire_y'));

	if($votetheme_time<=0)
		$votetheme_time=pkTIME;
	
	if($votetheme_expire<=0)
		$votetheme_expire=0;
	else
		$votetheme_expire=formattime($votetheme_expire,'','stamp');
	
	if(!$voteid)
		{
		$SQL->query("INSERT INTO ".pkSQLTAB_POLL_TOPIC." (votetheme_status) VALUES (1)");
		$voteid=$SQL->insert_id();
		}

		
	$SQL->query("UPDATE ".pkSQLTAB_POLL_TOPIC." 
		SET votetheme_title='".$SQL->f($_POST['votetheme_title'])."',
			votetheme_description='".$SQL->f($_POST['votetheme_description'])."',
			votetheme_rights='".$SQL->f($_POST['votetheme_rights'])."',
			votetheme_maxvotes='".intval($_POST['votetheme_maxvotes'])."',
			votetheme_comment='".intval($_POST['votetheme_comment'])."',
			votetheme_status='".intval($_POST['votetheme_status'])."',
			votetheme_time='".intval($votetheme_time)."',
			votetheme_expire='".intval($votetheme_expire)."',
			votetheme_multianswer='".intval($_POST['votetheme_multianswer'])."',
			votetheme_hidderesult='".intval($_POST['votetheme_hidden'])."'
		WHERE votetheme_id='".$voteid."'");
	
	
	pkHeaderLocation('pollcompose','','step='.((isset($_POST['next']) && $ACTION==$_POST['next']) ? 2 : '').'&voteid='.$voteid);
	}

if($step==2)
	{
	if(isset($_POST['back']) && $ACTION==$_POST['back'])
		{
		pkHeaderLocation('pollcompose','','voteid='.$voteid);
		}

	if($voteid && $ACTION!='view' && $ACTION!=$_POST['edit'])
		{
		if($ACTION==$_POST['up'])
			$SQL->query("UPDATE ".pkSQLTAB_POLL." SET vote_order=vote_order-1 WHERE vote_id='".intval($_POST['id'])."'");
		elseif($ACTION==$_POST['down'])
			$SQL->query("UPDATE ".pkSQLTAB_POLL." SET vote_order=vote_order+1 WHERE vote_id='".intval($_POST['id'])."'");
		elseif($ACTION==$_POST['newanswer'])
			{
			if($_POST['delete_answer']==1 && isset($_POST['id'])) 
				$SQL->query("DELETE FROM ".pkSQLTAB_POLL." WHERE vote_id='".intval($_POST['id'])."'");
			else 
				{
				if($_POST['id']!="")
					{
					$SQL->query("UPDATE ".pkSQLTAB_POLL." 
						SET vote_text='".$SQL->f($_POST['vote_editanswer'])."',
							vote_order='".intval($_POST['vote_order'])."'
						WHERE vote_id='".intval($_POST['id'])."'");
						}
				else
					{
					$SQL->query("INSERT INTO ".pkSQLTAB_POLL." 
						(vote_themeid,vote_text,vote_order)
						VALUES 
						('".$voteid."','".$SQL->f($_POST['vote_newanswer'])."','".intval($_POST['vote_order'])."')");
					}
				}
			}
		
		pkHeaderLocation('pollcompose','','step=2&voteid='.$voteid);
		}
	
	if($voteid)
		{
		unset($vote_row);
		unset($vote_form);
		$order=1;
		
		$getvotes=$SQL->query("SELECT * FROM ".pkSQLTAB_POLL." WHERE vote_themeid='".$voteid."' ORDER by vote_order");
		while($voteinfo=$SQL->fetch_array($getvotes))
			{
			if($voteinfo['vote_order']==$order)
				$order++;
			
			$row=rowcolor($row);
			$voteinfo['vote_text']=pkEntities($voteinfo['vote_text']);
			
			if($ACTION==$_POST['edit'] && $voteinfo['vote_id']==$_POST['id'])
				{
				eval("\$vote_form= \"".pkTpl("vote/editvote_editanswer")."\";");
				}
			
			eval("\$vote_row.= \"".pkTpl("vote/editvote_row")."\";");
			}
		
		
		if($vote_row=="")
			{
			eval("\$vote_row.= \"".pkTpl("vote/editvote_norow")."\";");
			}
		else
			{
			eval("\$vote_head= \"".pkTpl("vote/editvote_head")."\";");
			}
		
		if($vote_form=="")
			{
			eval("\$vote_form= \"".pkTpl("vote/editvote_newanswer")."\";");
			}

		eval("\$site_body.= \"".pkTpl("vote/editvote_answers")."\";");
		}
	else
		{
		eval("\$site_body.= \"".pkTpl("vote/editvote_novote")."\";");
		}
	return;
	}
	

if($voteid)
	{
	$votetheme=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_POLL_TOPIC." WHERE votetheme_id='".$voteid."' LIMIT 1"));
	
	$votetheme_title=pkEntities($votetheme['votetheme_title']);
	$votetheme_description=pkEntities($votetheme['votetheme_description']);
	$votetheme_maxvotes=$votetheme['votetheme_maxvotes'];
	
	
	if($votetheme['votetheme_rights']=="admin")
		$rights4='selected';
	elseif($votetheme['votetheme_rights']=="mod")
		$rights3='selected';
	elseif($votetheme['votetheme_rights']=="member")
		$rights2='selected';
	elseif($votetheme['votetheme_rights']=="user")
		$rights1='selected';
	else
		$rights0='selected';
	
	
	if($votetheme['votetheme_comment']==1)
		$comment1='checked';
	
	if($votetheme['votetheme_multianswer']==1)
		$multi1='checked';
	
	if($votetheme['votetheme_hidderesult']==1)
		$hidden1='checked';
	
	if($votetheme['votetheme_status']==1)
		$status1='selected';
	else
		$status0='selected';
	
	if($votetheme['votetheme_time']>0)
		{
		$time=formattime($votetheme['votetheme_time'],'','stamp');
		
		$vote_time_d=date("d",$time);
		$vote_time_m=date("m",$time);
		$vote_time_y=date("Y",$time);
		$vote_time_h=date("H",$time);
		$vote_time_mm=date("i",$time);
		}
	
	if($votetheme['votetheme_expire']>0)
		{
		$time=formattime($votetheme['votetheme_expire'],'','stamp');
		$vote_expire_d=date("d",$time);
		$vote_expire_m=date("m",$time);
		$vote_expire_y=date("Y",$time);
		$vote_expire_h=date("H",$time);
		$vote_expire_mm=date("i",$time);
		}
	
	$error_validity_period=($votetheme['votetheme_expire']<$votetheme['votetheme_time'] && $votetheme['votetheme_expire']>0) ? pkGetLangError('enddate_earlier_then_start') : '';
	}
else 
  	{
	$rights0=" selected"; 
	$votetheme_maxvotes="0";
	$comment1=" checked";
	$status1="selected";
	
	$vote_time_d=date("d",pkTIME);
	$vote_time_m=date("m",pkTIME);
	$vote_time_y=date("Y",pkTIME);
	$vote_time_h=date("H",pkTIME);
	$vote_time_mm=date("i",pkTIME);	
	}

eval("\$site_body.= \"".pkTpl("vote/editvote")."\";");
?>