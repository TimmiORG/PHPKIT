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


if(isset($_REQUEST['mode']) && $_REQUEST['mode']=='delete')
	{
	if(!adminaccess('fdelete'))
		return pkEvent('access_forbidden');
	
	if(isset($_REQUEST['editcat']) && intval($_REQUEST['editcat'])>0)
		{
		$editcat=intval($_REQUEST['editcat']);
		$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'confirm';
		
		$forumcat=$SQL->fetch_array($SQL->query("SELECT forumcat_name FROM ".pkSQLTAB_FORUM_CATEGORY." WHERE forumcat_id='".$editcat."'"));
		
		if(isset($_POST['confirm']) && $_POST['confirm']=='true' && $ACTION==$_POST['delete'] && $editcat)
			{
			$sqlcommand='';
			$sqlcommand2='';			
			
			$getthreads=$SQL->query("SELECT forumthread_id FROM ".pkSQLTAB_FORUM_THREAD." WHERE forumthread_catid='".$editcat."'");
			while($threads=$SQL->fetch_array($getthreads))
				{
				if($sqlcommand)
					$sqlcommand.=" OR forumpost_threadid='".$threads[0]."'";
				else
					$sqlcommand=" forumpost_threadid='".$threads[0]."'";
				
				if($sqlcommand2)
					$sqlcommand2.=" OR forumnotify_threadid='".$threads[0]."'";
				else
					$sqlcommand2=" forumnotify_threadid='".$threads[0]."'";
				}
			
			if($sqlcommand)
				$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_POST." WHERE ".$sqlcommand);
			
			if($sqlcoammnd2) 
				$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_NOTIFY." WHERE ".$sqlcommand2);
			
			$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_THREAD." WHERE forumthread_catid='".$editcat."'");
			$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_CATEGORY." WHERE forumcat_id='".$editcat."'");
			
			eval("\$site_body.= \"".pkTpl("forum/delcat_done")."\";");
			return;
			}
		elseif($ACTION!='confirm' || $_POST['confirm']=='false')
			{
			pkHeaderLocation('forumcategory','','editcat='.$editcat);
			} 
		else
			{
			$form_action=pkLink('forumcategory','delete');
			
			eval("\$site_body.= \"".pkTpl("forum/delcat")."\";");
			return;
			}
		}
	else
		{
		pkHeaderLocation('forumcategory');
		}
	}


if(!adminaccess('fedit')) 
	return pkEvent('access_forbidden');
	

if(isset($_REQUEST['todo']))
	$todo=$_REQUEST['todo'];

if(isset($_REQUEST['editcat']) && intval($_REQUEST['editcat'])>0)
	$editcat=intval($_REQUEST['editcat']);
elseif(isset($_REQUEST['editcat']) && $_REQUEST['editcat']=='new')	
	$editcat='new';
else
	unset($editcat);

if(isset($_REQUEST['subcat']) && intval($_REQUEST['subcat'])>0)
	$subcat=intval($_REQUEST['subcat']);

if(isset($_POST['action']))
	$ACTION=$_POST['action'];
else
	$ACTION='view';

if($ACTION==$_POST['edit'] && $subcat>0) 
 	{
	$editcat=$subcat;
	$todo=0;
	}


unset($list_hash);


if($ACTION==$_POST['cancel'])
	{
	pkHeaderLocation('forumcategory');
	}

if(isset($subcat) && ($ACTION==$_POST['up'] || $ACTION==$_POST['down'])) 
	{
	if($ACTION==$_POST['up']) 
		$SQL->query("UPDATE ".pkSQLTAB_FORUM_CATEGORY." SET forumcat_order=forumcat_order-1 WHERE forumcat_id='".$subcat."'");
	elseif($ACTION==$_POST['down'])
		$SQL->query("UPDATE ".pkSQLTAB_FORUM_CATEGORY." SET forumcat_order=forumcat_order+1 WHERE forumcat_id='".$subcat."'");
		
	
	pkHeaderLocation('forumcategory','','editcat='.$editcat.'&subcat='.$subcat.'&todo=1');	
	}


if($ACTION==$_POST['save'])
	{
	if($_POST['cat_delete']==1)
		{
		pkHeaderLocation('forumcategory','delete','editcat='.$editcat);
		}
	else 
		{
		if(isset($_POST['cat_mods']))
			$cat_mods=$_POST['cat_mods'];
		if(isset($_POST['cat_user']))
			$cat_user=$_POST['cat_user'];
		
		if($cat_mods[0]=="0")
			unset($cat_mods);
		elseif(is_array($cat_mods))
			$cat_mods="-".implode("-",$cat_mods)."-";

		if($cat_user[0]=="0")
			unset($cat_user);
		elseif(is_array($cat_user))
			$cat_user="-".implode("-",$cat_user)."-";
		
		
		if($editcat=='new' || $_POST['cat_duplicate']==1)
			{
			$SQL->query("INSERT INTO ".pkSQLTAB_FORUM_CATEGORY." (forumcat_name) VALUES ('".$SQL->f($_POST['cat_name'])."')");
			$editcat=$SQL->insert_id();
			}
		
		$SQL->query("UPDATE ".pkSQLTAB_FORUM_CATEGORY." 
			SET forumcat_subcat='".$SQL->f($_POST['cat_push'])."',
				forumcat_name='".$SQL->f($_POST['cat_name'])."',
				forumcat_description='".$SQL->f($_POST['cat_description'])."',
				forumcat_description_show='".$SQL->f($_POST['cat_description_show'])."',
				forumcat_option='".$SQL->f($_POST['cat_option'])."',
				forumcat_status='".$SQL->f($_POST['cat_status'])."',
				forumcat_rrights='".$SQL->f($_POST['cat_rrights'])."',
				forumcat_wrights='".$SQL->f($_POST['cat_wrights'])."',
				forumcat_trights='".$SQL->f($_POST['cat_trights'])."',
				forumcat_threads_option='".$SQL->f($_POST['cat_threads_option'])."',
				forumcat_replys='".$SQL->f($_POST['cat_replys'])."',
				forumcat_views='".$SQL->f($_POST['cat_views'])."',
				forumcat_threads='".$SQL->f($_POST['cat_threads'])."',
				forumcat_posts='".$SQL->f($_POST['cat_posts'])."',
				forumcat_mods='".$SQL->f($cat_mods)."',
				forumcat_user='".$SQL->f($cat_user)."'
			WHERE forumcat_id='".$editcat."'");
		
		pkHeaderLocation('forumcategory','','editcat='.$editcat);
		}
	}

$site_header_script.='<script src="fx/form.js" type="text/javascript"></script>';
$link_finduser=pkLink('popup','searchuser');


$getforumcat=$SQL->query("SELECT * FROM ".pkSQLTAB_FORUM_CATEGORY." ORDER by forumcat_name ASC");
while($forumcat=$SQL->fetch_array($getforumcat))
	{
	if($editcat==$forumcat['forumcat_id'])
		$select=" selected";
		
	$forumcat_name=pkEntities($forumcat['forumcat_name']);
	
	eval("\$forum_cat.= \"".pkTpl("forum/editcat_option")."\";");
	unset($select);

	if(!$editcat) 
		{
		$list_hash[$forumcat['forumcat_id']]=$forumcat;
		}
	
	if($editcat!=$forumcat['forumcat_id'])
		eval("\$forum_cats.= \"".pkTpl("forum/editcat_option")."\";");
	else 
		{
		$forumcat_description=pkEntities($forumcat['forumcat_description']);
		
		if($forumcat['forumcat_description_show']==1)
			$descrition_show1=" selected";
		else
			$descrition_show0=" selected";
		
		
		if($forumcat['forumcat_option']==1)
			$option1=" selected";
		elseif($forumcat['forumcat_option']==2)
			$option2=" selected";
		else
			$option0=" selected";
		
		
		if($forumcat['forumcat_status']==1)
			$status1=" selected";
		else
			$status0=" selected";
		
		if($forumcat['forumcat_rrights']=="guest")
			$rrights0=" selected";
		elseif($forumcat['forumcat_rrights']=="user")
			$rrights1=" selected";
		elseif($forumcat['forumcat_rrights']=="member")
			$rrights2=" selected";
		elseif($forumcat['forumcat_rrights']=="mod")
			$rrights3=" selected";
		else
			$rrights4=" selected";
		
		if($forumcat['forumcat_wrights']=="guest")
			$wrights0=" selected";
		elseif($forumcat['forumcat_wrights']=="user")
			$wrights1=" selected";
		elseif($forumcat['forumcat_wrights']=="member")
			$wrights2=" selected";
		elseif($forumcat['forumcat_wrights']=="mod")
			$wrights3=" selected";
		else
			$wrights4=" selected";
		
		if($forumcat['forumcat_trights']=="guest")
			$trights0=" selected";
		elseif($forumcat['forumcat_trights']=="user")
			$trights1=" selected";
		elseif($forumcat['forumcat_trights']=="member")
			$trights2=" selected";
		elseif($forumcat['forumcat_trights']=="mod")
			$trights3=" selected";
		else
			$trights4=" selected";
		
		if($forumcat['forumcat_threads_option']==1)
			$threads_option1=" selected";
		else
			$threads_option0=" selected";
		

		$user_optionlist=$mod_optionlist='';
	
		$getuserinfo=$SQL->query("SELECT user_id,user_nick FROM ".pkSQLTAB_USER." WHERE user_id IN (0".str_replace('-',',0',$forumcat['forumcat_mods']).")");
		while(list($userid,$usernick)=$SQL->fetch_row($getuserinfo))
			{
			$mod_optionlist.='<option value="'.$userid.'" selected="selected">'.pkEntities($usernick).'</option>';
			}

		$getuserinfo=$SQL->query("SELECT user_id,user_nick FROM ".pkSQLTAB_USER." WHERE user_id IN (0".str_replace('-',',0',$forumcat['forumcat_user']).")");
		while(list($userid,$usernick)=$SQL->fetch_row($getuserinfo))
			{
			$user_optionlist.='<option value="'.$userid.'" selected="selected">'.pkEntities($usernick).'</option>';
			}

		$push_cat='';
		
		if($forumcat['forumcat_subcat']==0)
			$catpushselect0=='selected';
		
		$getcat=$SQL->query("SELECT * FROM ".pkSQLTAB_FORUM_CATEGORY." WHERE forumcat_id!='".$forumcat['forumcat_id']."' ORDER by forumcat_name");
		while($cat=$SQL->fetch_array($getcat))
			{
			$push_cat.='<option value="'.$cat['forumcat_id'].'"';
			
			if($forumcat['forumcat_subcat']==$cat['forumcat_id'])
				$push_cat.='selected';
			
			$push_cat.='>'.pkEntities($cat['forumcat_name']).'</option>';
			}

		eval("\$edit_cat= \"".pkTpl("forum/editcat_form_edit")."\";");
		eval("\$edit_cat= \"".pkTpl("forum/editcat_form")."\";");
		}
	}

$count=0;
if(($todo==1 && $editcat!="new") or $editcat=="0")
	{
	$todo1=" checked";
	$getforumcat=$SQL->query("SELECT * FROM ".pkSQLTAB_FORUM_CATEGORY." WHERE forumcat_subcat='".$editcat."' ORDER by forumcat_order ASC");
	while($forumcat=$SQL->fetch_array($getforumcat)) 
		{
		$forumcat_name=pkEntities($forumcat['forumcat_name']);
		
		if($subcat==$forumcat['forumcat_id'])
			$select=" selected";
		else
			unset($select);
		
		$count++; 
		eval("\$move_fcat.= \"".pkTpl("forum/editcat_move_option")."\";");
		}
	
	if($count==0) 
		{
		eval("\$move_fcat.= \"".pkTpl("forum/editcat_option_notfound")."\";");
		$count++;
		}
	
	$count++;
	eval ("\$edit_cat= \"".pkTpl("forum/editcat_move")."\";");
	}
else
	{
	$todo0=' checked';
	
	if($editcat=='new')
		{
		$action_type="Kategorie erstellen";
		$forumcat_name='';
		$forumcat_description='';
		$threads_option1='selected';
		$status1='selected';
		$option1='selected';
		$rrights0='selected';
		$wrights0='selected';
		$trights0='selected';
		$descrition_show1='selected';
		$forumcat['forumcat_threads']=15;
		$forumcat['forumcat_views']=100;
		$forumcat['forumcat_posts']=15;
		$forumcat['forumcat_replys']=20;
		
		
		eval("\$edit_cat= \"".pkTpl("forum/editcat_form_new")."\";");		
		eval("\$edit_cat= \"".pkTpl("forum/editcat_form")."\";");
		}
	}

if($editcat=="0")
	$select0="selected";
elseif($editcat=="new")
	$selectnew="selected";
	
$form_action=pkLink('forumcategory');

eval("\$site_body.= \"".pkTpl("forum/editcat")."\";");

if(!$editcat)
	{
	unset($list_row);
	unset($row);
	
	if(isset($_REQUEST['order']))
		$order=$_REQUEST['order'];
	else
		unset($order);
	
	if(intval($_REQUEST['where'])>0)
		$where=$_REQUEST['where'];
	else
		unset($where);
		
	
	$ordersql='';
	$wheresql='';
	$ordername='named';
	$ordertype='type';
	$orderid='id';
	

	if($where)
		$wheresql=" WHERE forumcat_subcat='".$where."' OR forumcat_id='".$where."'";
	
	if($order=='idd')
		{
		$ordersql='forumcat_id DESC';
		$orderid='id';
		}
	elseif($order=='id')
		{
		$ordersql='forumcat_id ASC';
		$orderid='idd';
		}
	elseif($order=='named')
		{
		$ordersql='forumcat_name DESC';
		$ordername='name';
		}
	elseif($order=='name')
		{
		$ordersql='forumcat_name ASC';
		$ordername='named';
		}
	elseif($order=='typed')
		{
		$ordersql='forumcat_subcat DESC';
		$ordertype='type';
		}
	else
		{
		$ordersql='forumcat_subcat ASC';
		$ordertype='typed';
		}
	
	
	$getforumcat=$SQL->query("SELECT * FROM ".pkSQLTAB_FORUM_CATEGORY." ".$wheresql." ORDER by ".$ordersql);
	while($forumcat=$SQL->fetch_array($getforumcat)) 
		{
		$row=rowcolor($row);
		
		
		if($forumcat['forumcat_status']==1) 
			{
			$catstatus=$lang['open'];
			$catstatusimg='open';
			}
		else 
			{
			$catstatus=$lang['closed'];
			$catstatusimg='close';
			}
		
		
		$catid=$forumcat['forumcat_id'];
		
		if(trim($catname=pkEntities($forumcat['forumcat_name']))=='')
			$catname='<span class="highlight">'.$lang['no_title'].'</span>';
		
		if($forumcat['forumcat_rrights']=="guest")
			$rrights=$lang['guest'];
		elseif($forumcat['forumcat_rrights']=="user")
			$rrights=$lang['user'];
		elseif($forumcat['forumcat_rrights']=="member")
			$rrights=$lang['member'];
		elseif ($forumcat['forumcat_rrights']=="mod") 
			$rrights=$lang['mod'];
		else
			$rrights=$lang['admin'];

		if($forumcat['forumcat_wrights']=="guest")
			$wrights=$lang['guest'];
		elseif($forumcat['forumcat_wrights']=="user")
			$wrights=$lang['user'];
		elseif($forumcat['forumcat_wrights']=="member")
			$wrights=$lang['member'];
		elseif($forumcat['forumcat_wrights']=="mod")
			$wrights=$lang['mod'];
		else
			$wrights=$lang['admin'];
		
		if($forumcat['forumcat_trights']=="guest")
			$trights=$lang['guest'];
		elseif($forumcat['forumcat_trights']=="user")
			$trights=$lang['user'];
		elseif($forumcat['forumcat_trights']=="member")
			$trights=$lang['member'];
		elseif($forumcat['forumcat_trights']=="mod")
			$trights=$lang['mod'];
		else
			$trights=$lang['admin'];
		
		if($forumcat['forumcat_threads_option']==1)
			$threadoption=$lang['allowed'];
		else
			$threadoption=$lang['not_allowed'];
		
		if($forumcat['forumcat_subcat']==0)
			{
			$subcatid=$forumcat['forumcat_id'];
			$subcatname='<b>'.$lang['maincat'].'</b>';
			}
		else
			{
			$subcatid=$list_hash[$forumcat['forumcat_subcat']]['forumcat_id'];
			$subcatname=$list_hash[$forumcat['forumcat_subcat']]['forumcat_name'];
			}
		
		$catdescrition=pkEntities($forumcat['forumcat_description']);
		
		eval("\$subcat= \"".pkTpl("forum/editcat_listcat_row_subcatlink")."\";");
		eval("\$list_row.= \"".pkTpl("forum/editcat_listcat_row")."\";"); 
		}
	
	eval("\$site_body.= \"".pkTpl("forum/editcat_listcat")."\";"); 
	}
?>