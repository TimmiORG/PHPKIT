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


if(!adminaccess('smilies'))
	return pkEvent('access_forbidden');


$smilie_path='';

$smilieid=(isset($_REQUEST['smilieid']) && intval($_REQUEST['smilieid'])>0) ? intval($_REQUEST['smilieid']) : 0;
$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';


if(isset($_POST['cancel']) && $ACTION==$_POST['cancel']) 
	{
	pkHeaderLocation('smilies');
	}


if (isset($_POST['delete']) && $ACTION==$_POST['delete'] && $smilieid)
	{
	$SQL->query("DELETE FROM ".pkSQLTAB_SMILIES." WHERE smilie_id='".$smilieid."'");
	
	pkHeaderLocation('smilies');
	}


if(isset($_POST['save']) && $ACTION==$_POST['save'])
	{
	$sqlcommand="SELECT 
		COUNT(*) 
		FROM ".pkSQLTAB_SMILIES." 
		WHERE (smilie_code='".$SQL->f($_POST['smilie_code'])."' OR smilie_path='".$SQL->f($_POST['smilie_path'])."')";
	
	if(isset($smilieid))
		$sqlcommand.=" AND smilie_id!='".$smilieid."'";
	
	$sqlcommand.=" LIMIT 1";
	$counter=$SQL->fetch_array($SQL->query($sqlcommand));
	
	
	if(is_uploaded_file($_FILES['smilie_upload']['tmp_name']))
		{
		$UPLOAD=new UPLOAD();
		$uploadreturned=$UPLOAD->images($_FILES['smilie_upload'],'../images/smilies','');
		if($uploadreturned[0]==TRUE) 
			$smilie_path=str_replace("../","",$uploadreturned[1]);
		}
	
	if(empty($smilie_path)) 
		$smilie_path=$_POST['smilie_path'];
	
	$smilie_code=$_POST['smilie_code'];
	$smilie_title=$_POST['smilie_title'];
	$smilie_option=$_POST['smilie_option'];
	
	
	if($counter[0]==0 && !empty($smilie_code) && !empty($smilie_path))
		{
		if(!$smilieid)
			{
			$SQL->query("INSERT INTO ".pkSQLTAB_SMILIES." (smilie_code) VALUES ('".$SQL->f($smilie_code)."')");
			$smilieid=$SQL->insert_id();
			}
		
		$SQL->query("UPDATE ".pkSQLTAB_SMILIES." 
			SET smilie_code='".$SQL->f($smilie_code)."',
				smilie_path='".$SQL->f($smilie_path)."',
				smilie_title='".$SQL->f($smilie_title)."',
				smilie_option='".$SQL->i($smilie_option)."'
			WHERE smilie_id='".$smilieid."'");
		
		pkHeaderLocation('smilies');
		}
	
	if(!$smilieid)
		eval("\$error_msg= \"".pkTpl("smilies_error_msg")."\";");
	
	$ACTION='create';
	}


if($ACTION==$_POST['create'] || $_GET['action']=='create' || (($ACTION==$_POST['edit'] || $_GET['action']=='edit') && isset($smilieid))) 
	{
	if($ACTION==$_POST['edit'] or $_GET['action']=='edit')
		{
		$smilies=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_SMILIES." WHERE smilie_id='".$smilieid."'"));
			
		if($smilies['smilie_option']==1)
			$option1=" selected";
		else
			$option0=' selected';
		}
	else
		{
		if(isset($_GET['smilie_path'])) 
			$smilie_path='images/smilies/'.$_GET['smilie_path'];
			
		$smilies['smilie_code']=$smilie_code;
		$smilies['smilie_path']=$smilie_path;
		$smilies['smilie_title']=$smilie_title;
		
		if($smilie_option==1)
			$option1=" selected";
		else
			$option0=" selected";
		}
		
	$smilies['smilie_code']=pkEntities($smilies['smilie_code']);
	$smilies['smilie_path']=pkEntities($smilies['smilie_path']);
	$smilies['smilie_title']=pkEntities($smilies['smilie_title']);

	eval("\$site_body.= \"".pkTpl("smilies_editform")."\";");
	}
else
	{
	$getsmilies=$SQL->query("SELECT * FROM ".pkSQLTAB_SMILIES." ORDER by smilie_option DESC");
	while($smilies=$SQL->fetch_array($getsmilies))
		{
		$smilies_cache[$smilies['smilie_path']]=$smilies;
		}
		
		
	if($ACTION=='showdir')
		{
		$a=opendir("../".$config['smilie_dir']);
		while($info=readdir($a))
			{
			$infocheck="../".$config['smilie_dir']."/".$info;
			$checkpath=$config['smilie_dir']."/".$info;
				
			if(filecheck($infocheck))
				{
				$imgsize=@getimagesize($infocheck);
				if($imgsize[2]==1 || $imgsize[2]==2 || $imgsize[2]==3)
					{
					$row=rowcolor($row);
					
					if($smilies_cache[$checkpath]!="") 
						{
						$smilie_info=$smilies_cache[$checkpath]['smilie_id'];
							
						eval("\$smilie_info= \"".pkTpl("smilies_info1")."\";");
						}
					else
						eval("\$smilie_info= \"".pkTpl("smilies_info0")."\";");
					
					eval("\$smilies_dirrow.= \"".pkTpl("smilies_dirrow")."\";");
					}
				}
			}
			
		closedir($a);
		
		eval("\$site_body.= \"".pkTpl("smilies_dir")."\";");
		}
	else
		{
		if(is_array($smilies_cache))
			{
			foreach($smilies_cache as $smilies)
				{
				$row=rowcolor($row);
				
				if($smilies['smilie_option']==1)
					{
					$smilies_option=$lang['direct'];
					}
				else
					{
					$smilies_option=$lang['pop_up'];
					}
					
				if(filecheck("../".$smilies['smilie_path']))
					$smilie_path="../".$smilies['smilie_path'];
				else
					$smilie_path=$smilies['smilie_path'];

				$smilie_path=pkEntities($smilie_path);				
				$smilies['smilie_title']=pkEntities($smilies['smilie_title']);
				$smilies['smilie_code']=pkEntities($smilies['smilie_code']);
					
				eval("\$smilies_row.= \"".pkTpl("smilies_row")."\";");
				}
			}
			
		eval("\$site_body.= \"".pkTpl("smilies")."\";");
		}
	}
?>