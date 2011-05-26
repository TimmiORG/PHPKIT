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


$mode=(isset($_REQUEST['mode']) && $_REQUEST['mode']=='count') ? 'count' : NULL;


if($mode=='count')
	{
	$voteids=$ENV->_cookie('vote_id');	
	
	if(intval($_REQUEST['vid'])>0) 
		$vid=intval($_REQUEST['vid']);
	else 
		unset($vid);

	if(is_array($_REQUEST['voteid']))
		$voteid=$_REQUEST['voteid'];
	else
		unset($voteid);

	if($voteids) 
		{
		$ids=explode(",",$voteids);
		foreach($ids as $i)
			{
			if($i==$vid)
				{
				unset($vid);
				unset($voteid);
				}
			}
		}


	if($voteid && $vid)
		{
		$voteinfo=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_POLL_TOPIC." WHERE votetheme_id='".$vid."' LIMIT 1"));
	
		if(getrights($voteinfo['votetheme_rights'])=='true' && $voteinfo['votetheme_status']==1)
			{
			$counter=$SQL->fetch_array($SQL->query("SELECT 
					COUNT(*) 
				FROM ".pkSQLTAB_POLL_COUNT."
				WHERE vote_rated_contid='".$vid."' AND 
					vote_rated_cat='vote' AND 
					((vote_rated_userid='".$SQL->i(pkGetUservalue('id'))."' AND 
					vote_rated_userid!=0) OR 
					vote_rated_ip='".$SQL->f($ENV->getvar('REMOTE_ADDR'))."') LIMIT 1"));

			if($counter[0]<1)
				{
				unset($sqlcommand);
			
				foreach($voteid as $i)
					{
					if($sqlcommand)
						$sqlcommand.=" OR "; 
				
					$sqlcommand.="vote_id='".intval($i)."'";
					}
			
				$SQL->query("UPDATE ".pkSQLTAB_POLL." SET vote_counter=vote_counter+1 WHERE ".$sqlcommand);
				
				$SQL->query("INSERT INTO ".pkSQLTAB_POLL_COUNT." 
					(vote_rated_contid,vote_rated_userid,vote_rated_cat,vote_rated_ip) 
					VALUES 
					('".$vid."','".$SQL->i(pkGetUservalue('id'))."','vote','".$SQL->f($ENV->getvar('REMOTE_ADDR'))."')");
					
				$votetotal=$SQL->fetch_array($SQL->query("SELECT SUM(vote_counter) FROM ".pkSQLTAB_POLL." WHERE vote_themeid='".intval($voteinfo['votetheme_id'])."'"));

				if(($votetotal[0]>=$voteinfo['votetheme_maxvotes'] && $voteinfo['votetheme_maxvotes']>0) || ($voteinfo['votetheme_expire']!=0 && $voteinfo['votetheme_expire']<=time())) 
					$SQL->query("UPDATE ".pkSQLTAB_POLL_TOPIC." SET votetheme_status=0 WHERE votetheme_id='".$vid."'");
				}

			$voteids=$voteids.",".$vid;

			$SESSION->set('svoteids',$voteids);
			$ENV->setCookie('vote_id',$voteids);			
			}
		}
	
	pkHeaderLocation('pollarchive','','vid='.$vid);
	}


pkLoadLang('poll');


$col_align = '';
$epp=10;
$entries=(isset($_REQUEST['entries']) && intval($_REQUEST['entries'])>0) ? intval($_REQUEST['entries']) : 0;
$vid=(isset($_REQUEST['vid']) && intval($_REQUEST['vid'])>0) ? intval($_REQUEST['vid']) : 0;
$poll_comments = false;
$voteids=$ENV->_cookie('vote_id');
$svoteids=$SESSION->get('svoteids');


$voting=TRUE;

if($vid>0)
	{
	$sqlcommand="AND votetheme_time<'".pkTIME."' AND ".sqlrights("votetheme_rights")." ORDER by votetheme_time DESC"; 
	$getvote="(votetheme_status=1 OR votetheme_id='".$vid."')";
	}
else
	{
	$sqlcommand="AND (votetheme_expire>'".pkTIME."' OR votetheme_expire=0) AND votetheme_time<'".pkTIME."' AND ".sqlrights("votetheme_rights")." ORDER by votetheme_time DESC"; 
	$getvote="votetheme_status=1";
	}


$getvote=$SQL->query("SELECT * FROM ".pkSQLTAB_POLL_TOPIC." WHERE ".$getvote." ".$sqlcommand);
while($voteinfo=$SQL->fetch_array($getvote))
	{
	$voteinfo['votetheme_title']=pkEntities($voteinfo['votetheme_title']);
	
	$votetotal=$SQL->fetch_array($SQL->query("SELECT sum(vote_counter) FROM ".pkSQLTAB_POLL." WHERE vote_themeid='".$voteinfo['votetheme_id']."'"));
	
	if($voteinfo['votetheme_status']!=1)
		$voting=FALSE;
	elseif(!empty($voteids) || !empty($svoteids))
		{
		if(empty($voteids))
			$voteids=$svoteids;
		
		$ids=explode(",",$voteids);
		
		foreach($ids as $i)
			{
			if($i==$voteinfo['votetheme_id']) 
				{
				$voting=FALSE;
				break;
				}
			}
		}
	elseif(pkGetUservalue('id')>0)
		{
		$counter=$SQL->fetch_array($SQL->query("SELECT 
			COUNT(*)
			FROM ".pkSQLTAB_POLL_COUNT." 
			WHERE vote_rated_contid='".$SQL->i($voteinfo['votetheme_id'])."' AND 
				vote_rated_cat='vote' AND 
				((vote_rated_userid='".$SQL->i(pkGetUservalue('id'))."' AND vote_rated_userid!=0) OR 
				vote_rated_ip='".$SQL->f($ENV->getvar('REMOTE_ADDR'))."')
			LIMIT 1"));
		
		if($counter[0]>0)
			$voting=FALSE;
		}

	if($vid>0 && $vid!=$voteinfo['votetheme_id']) 
		{
		$row=rowcolor($row); 
		
		eval("\$vote_row_other.= \"".pkTpl("vote/archiv_row_other")."\";");
		}
	else
		{
		$getvoteanswer=$SQL->query("SELECT * FROM ".pkSQLTAB_POLL." WHERE vote_themeid='".$voteinfo['votetheme_id']."' ORDER by vote_order ASC");
		while($voteanswerinfo=$SQL->fetch_array($getvoteanswer))
			{
			$rowimage=rowcolor($rowimage);
			$voteanswerinfo['vote_text']=pkEntities($voteanswerinfo['vote_text']);
			
			if($voting)
				{
				if($voteinfo['votetheme_multianswer']==1)
					$inputtype='checkbox';
				else
					$inputtype='radio';
				
				eval("\$archiv_row_vote= \"".pkTpl("vote/archiv_row_vote")."\";");
				
				if($voteinfo['votetheme_hidderesult']==1)
					eval("\$vote_row_result.= \"".pkTpl("vote/archiv_row_vote_complete")."\";");
				}
			
			if($voteinfo['votetheme_hidderesult']!=1)
				{
				if($votetotal[0]>=1)
					{
					$vote_percent=number_format((($voteanswerinfo['vote_counter']*100) / $votetotal[0]),0,",",".");
					}
				else
					{
					$vote_percent='0';
					}
				
				eval("\$vote_row_result.= \"".pkTpl("vote/archiv_row_voted")."\";");
				}
			}
		
		if($voteinfo['votetheme_hidderesult']==1 && $voting)
			{
			eval("\$vote_row_result.= \"".pkTpl("vote/archiv_hidden_msg")."\";");
			}
		
		if($voteinfo['votetheme_hidderesult']!=1)
			{
			$link_result = pkLink('pollarchive','','vid='.$voteinfo['votetheme_id']);
		
			eval("\$vote_result= \"".pkTpl("vote/vote_box_result")."\";");
			}
		
		if($voteinfo['votetheme_comment']==1)
			{
			$poll_comments = true;
			
			list($comment_count) = $SQL->fetch_row($SQL->query("SELECT COUNT(comment_id) FROM ".pkSQLTAB_COMMENT." WHERE comment_cat='vote' AND comment_subid='".$voteinfo['votetheme_id']."'"));
		
			$lang_comment = pkGetSpecialLang('comment',$comment_count);
			$link_comment = pkLink('pollarchive','','comcat=vote&vid='.$voteinfo['votetheme_id']);
			
			eval("\$vote_comment= \"".pkTpl("vote/archiv_comment")."\";");
			}
		
		if(trim($voteinfo['votetheme_description'])!='')
			{
			eval("\$vote_description= \"".pkTpl("vote/archiv_row_description")."\";");
			}
			
		if($voteinfo['votetheme_status']==0)
			$info_msg=' ('.$lang['closed'].')';
		
		if($voting)
			{
			eval("\$vote_button= \"".pkTpl("vote/archiv_submit_button")."\";");
			}
		
		
		$form_action = pkLink('pollarchive','count','vid='.$voteinfo['votetheme_id']);
		
		$col_align = $col_align=='left' ? 'right' : 'left';		
		$vname	= 'vote_row_'.$col_align;
		$$vname.= empty($$vname) ? '' : '<br />';
		
		eval("\$\$vname.= \"".pkTpl("vote/archiv_row_result")."\";");
		
		unset($vote_row_result, $vote_button, $archiv_row_vote, $vote_comment, $vote_result, $vote_description, $info_msg);
		}
	
	$voting=TRUE;
	}

if($vote_row_other!='')
	{
	eval("\$vote_row_right.= \"".pkTpl("vote/archiv_other")."\";");
	}
	


$page_headline = pkGetLang('Polls');	


if($vid)
	{
	eval("\$pagelink= \"".pkTpl("vote/archiv_link")."\";");
	eval("\$site_body.= \"".pkTpl("vote/archiv")."\";");
	
	if($poll_comments)
		{
		$subid = $vid;
		$comcat = 'vote';
		
		include(pkDIRPUBLIC.'comment'.pkEXT);
		}
	}
else
	{
	list($counter)=$SQL->fetch_row($SQL->query("SELECT COUNT(votetheme_id) FROM ".pkSQLTAB_POLL_TOPIC." WHERE votetheme_status=0 ".$sqlcommand));

	if(!$counter)
		{
		$pagelink='&nbsp;';
		}
	else
		{
		$row = '';
		$link 		= pkLink('pollarchive');
		$pagelink	= sidelinkfull($counter,$epp,$entries,$link,'sitebodysmall');
		
		$getvote = $SQL->query("SELECT votetheme_id,votetheme_title FROM ".pkSQLTAB_POLL_TOPIC." WHERE votetheme_status=0 AND votetheme_time<'".pkTIME."' AND ".sqlrights("votetheme_rights")." ORDER by votetheme_time DESC LIMIT ".$entries.",".$epp);
		while($voteinfo = $SQL->fetch_assoc($getvote))
			{
			$row=rowcolor($row);
			
			$closed_title = pkEntities($voteinfo['votetheme_title']);
			$closed_link = pkLink('pollarchive','','vid='.$voteinfo['votetheme_id']);
			
			eval("\$archiv_closed_row.= \"".pkTpl("vote/archiv_closed_row")."\";");		
			}
		
		eval("\$archiv_closed= \"".pkTpl("vote/archiv_closed")."\";");
		}
	
	eval("\$site_body.= \"".pkTpl("vote/archiv")."\";");
	}
?>