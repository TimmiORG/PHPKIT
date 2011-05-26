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


if(!adminaccess('images'))
	return pkEvent('access_forbidden');


$entries=(isset($_REQUEST['entries']) && intval($_REQUEST['entries'])>0) ? intval($_REQUEST['entries']) : 0;
$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';


if(isset($_POST['cancel']) && $ACTION==$_POST['cancel']) 
	{
	pkHeaderLocation('mediaarchive');
	}
	
	
if(isset($_POST['delete']) && $ACTION==$_POST['delete']) 
	{
	@unlink("../content/images/".basename($_POST['filename']));
	pkHeaderLocation('mediaarchive','','entries='.$entries);
	}

if(isset($_REQUEST['upload']))
	{
	if($ACTION==$_POST['upload'] && $_FILES['image_file']['tmp_name']!='')
		{
		$UPLOAD=new UPLOAD();
		$uploadreturned=$UPLOAD->images($_FILES['image_file'],'../'.$config['image_archive'],$_POST['image_name']);
		
		if($uploadreturned[0]==TRUE)
			$file_name=str_replace('../','',$uploadreturned[1]);
		else
			eval("\$upload_info= \"".pkTpl("images_upload_error")."\";");
		
		eval("\$upload_info= \"".pkTpl("images_upload_info")."\";");
		}
	
	
	$max_file_size=(ini_get('upload_max_filesize')*1024*1024);
	$max_execution_time=ini_get('max_execution_time');
	$max_filesize=FileSizeExt('','B',$max_file_size);
	
	eval("\$site_body.= \"".pkTpl("images_upload")."\";");
	return;
	}


$epp=20;
	
if($_REQUEST['dir']!='') 
	$dir=$_REQUEST['dir'];
	
if(!strstr($dir,$config['image_archive']) || $dir=='') 
	$dir='../'.$config['image_archive'];
	
$dirpath=str_replace("../","",$dir);
$dirlist='';
$filelist='';
$count=0;
$width=1;
	
	
$a=opendir($dir);
while($datei=readdir($a))
	{
	if(is_dir($dir.'/'.$datei) && $datei!='.' && $datei!='..')
		eval("\$dirlist.= \"".pkTpl("images_dirlist")."\";");
	elseif(pkFileCheck($dir.'/'.$datei) && $datei!='.' && $datei!='..')
		{
		$filepath=$dirpath.'/'.$datei;
			
		eval("\$dirlist.= \"".pkTpl("images_filelist")."\";");
		}
	
	$ext=strtolower($datei);
	
	if(substr($ext,-3)=='gif' || substr($ext,-3)=='png' || substr($ext,-3)=='jpg' || substr($ext,-4)=='jpeg')
		{
		$count++;

		if($count>$entries && $count<($entries+$epp+1))
			{
			if($width==6)
				{
				eval("\$show_images.= \"".pkTpl("images_row_break")."\";");
				$width=1;
				}
				
			$link_popup=pkLink('popup','','img='.rawurlencode($filepath));
			
			eval("\$show_images.= \"".pkTpl("images_cell")."\";");
			$width++;
			}
		}
	
	}
closedir($a);	
	
$cs=6-$width;
	
	
if($cs>0)
	{
	eval("\$show_images.= \"".pkTpl("images_row_spacer")."\";");
	}
	

$page_link=sidelinkfull($count,$epp,$entries,"include.php?path=mediaarchive","small");
	
eval("\$site_body.= \"".pkTpl("images")."\";");
?>