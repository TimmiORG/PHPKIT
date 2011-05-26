<?php
# PHPKIT WCMS | Web Content Management System
#
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATION
#
# SIE SIND NICHT BERECHTIGT, UNRECHTM�SSIGE KOPIEN DIESER
# DATEI ZU ERSTELLEN UND/ODER DIESE INFORMATIONEN ZU ENTFERNEN
#
# This file / the PHPKIT software is no freeware! For further 
# information please visit our website or contact us via email:
#
# Diese Datei / die PHPKIT Software ist keine Freeware! F�r weitere
# Informationen besuchen Sie bitte unsere Website oder kontaktieren uns per E-Mail:
#
# email     : info@phpkit.com
# website   : http://www.phpkit.com
# licence   : http://www.phpkit.com/licence
# copyright : Copyright (c) 2002-2009 mxbyte gbr | http://www.mxbyte.com


if(!defined('pkFRONTEND') || pkFRONTEND!='public')
	die('Direct access to this location is not permitted.');


#load the language file
pkLoadLang('poll');


$votechache = array();

$rowimage='';
$vote_box_row = '';
$vote_hidden_msg = '';
$vote_box_submit = '';


$svoteids = $SESSION->exists('svoteids') ? $SESSION->get('svoteids') : NULL;
$voteids = $ENV->_cookie('vote_id');


$query = $SQL->query("SELECT 
	*
	FROM ".pkSQLTAB_POLL_TOPIC."
	WHERE votetheme_status=1 AND
		(votetheme_expire>'".pkTIME."' OR votetheme_expire=0) AND 
		votetheme_time<'".pkTIME."' AND ".
		sqlrights('votetheme_rights'));
while($voteinfo = $SQL->fetch_assoc($query))
	{
	$votechache[] = $voteinfo;
	}

$rand = count($votechache);

if(!empty($votechache[0]))
{
	$votechache[] = '';
}

if($rand<2)
	{
	$voteinfo=$votechache[0];
	}
else
	{
	mt_srand(pkTIME*123456789);
	$rand = rand(1,$rand);
	$rand--;
	
	$voteinfo = $votechache[$rand];
	}
	
if($voteids=="" && $svoteids)
	{
	$voteids=$svoteids;
	}

if($voteids=="" && pkGetUservalue('id')>0)
	{
	$voted_db=$SQL->query("SELECT
			vote_rated_contid 
		FROM ".pkSQLTAB_POLL_COUNT."
		WHERE vote_rated_cat='vote' AND 
			((vote_rated_userid='".pkGetUservalue('id')."' AND
			 vote_rated_userid!=0) OR 
			 vote_rated_ip='".$SQL->f($ENV->getvar('REMOTE_ADDR'))."')");
	while($voted_num=$SQL->fetch_assoc($voted_db))
		{
		$voteids.= $voted_num['vote_rated_contid'].",";
		}
	
	if($voteids!="")
		{
		$voteids=substr($voteids, 0, -1);
		}
	}


if($voteids || $svoteids)
	{
	$ids=explode(",",$voteids);
	
	foreach($ids as $i)
		{
		if($i==$voteinfo['votetheme_id'])
			{
			$voteinfo['votetheme_title']=pkEntities($voteinfo['votetheme_title']);
			
			$votetotal=$SQL->fetch_array($SQL->query("SELECT
				SUM(vote_counter) 
				FROM ".pkSQLTAB_POLL."
				WHERE vote_themeid='".$voteinfo['votetheme_id']."'"));

			$getvoteanswer=$SQL->query("SELECT 
				*
				FROM ".pkSQLTAB_POLL." 
				WHERE vote_themeid='".$voteinfo['votetheme_id']."'
				ORDER by vote_order ASC");
			while($voteanswerinfo=$SQL->fetch_array($getvoteanswer))
				{
				$voteanswerinfo['vote_text']=pkEntities($voteanswerinfo['vote_text']);
				
				if($voteinfo['votetheme_hidderesult']==1)
					{
					eval("\$vote_box_row.=\"".pkTpl("vote/vote_box_row_voted_hidden")."\";");
					}
				else
					{
					$rowimage=rowcolor($rowimage);

					if($votetotal[0]>0)
						{
						$vote_percent=number_format((($voteanswerinfo['vote_counter']*100) / $votetotal[0]),0,",",".");
						}
					else
						{
						$vote_percent=0;
						}
					
					eval("\$vote_box_row.= \"".pkTpl("vote/vote_box_row_voted")."\";");
					}
				}
			
			if($voteinfo['votetheme_hidderesult']==1)
				{
				eval("\$vote_hidden_msg= \"".pkTpl("vote/vote_box_hidden_msg")."\";");
				}
			
			eval("\$vote_box_row.= \"".pkTpl("vote/vote_box_total_voted")."\";");
			}
		
		if($vote_box_row!='')
			{
			break;
			}
		}
	}


if($vote_box_row=='')
	{
	$inputtype = $voteinfo['votetheme_multianswer'] ? 'checkbox' : 'radio';
	$voteinfo['votetheme_title'] = pkEntities($voteinfo['votetheme_title']);

	$query = $SQL->query("SELECT
		*
		FROM ".pkSQLTAB_POLL."
		WHERE vote_themeid='".$voteinfo['votetheme_id']."'
		ORDER by vote_order ASC");
	while($voteanswerinfo = $SQL->fetch_assoc($query))
		{
		$voteanswerinfo['vote_text'] = pkEntities($voteanswerinfo['vote_text']);
		
		eval("\$vote_box_row.=\"".pkTpl("vote/vote_box_row")."\";");
		}

	eval("\$vote_box_submit=\"".pkTpl("vote/vote_box_submit")."\";");
	}





if($voteinfo['votetheme_hidderesult']!=1)
	{
	$link_result = pkLink('pollarchive','','vid='.$voteinfo['votetheme_id']);	
	
	eval("\$vote_result= \"".pkTpl("vote/vote_box_result")."\";");
	}

if($voteinfo['votetheme_comment']==1)
	{
	list($comment_count) = $SQL->fetch_row($SQL->query("SELECT COUNT(comment_id) FROM ".pkSQLTAB_COMMENT." WHERE comment_cat='vote' AND comment_subid='".$voteinfo['votetheme_id']."'"));

	$lang_comment = pkGetSpecialLang('comment',$comment_count);
	$link_comment = pkLink('pollarchive','','comcat=vote&vid='.$voteinfo['votetheme_id']);
	
	eval("\$vote_comment= \"".pkTpl("vote/vote_box_comment")."\";");
	}


$type = ($voteinfo['votetheme_id'] ? 'box' : 'nobox');

if($type=='box')
	{
	$form_action = pkLink('pollarchive','count','vid='.$voteinfo['votetheme_id']);
	}

$link_archive = pkLink('pollarchive');


eval("\$vote=\"".pkTpl("vote/vote_".$type)."\";");

return array($vote);
?>