<?php
# PHPKIT WCMS - Web Content Management System
# --------------------------------------------
# Copyright (c) 2002-2008 Gersöne & Schott GbR
#
# This file / the PHPKIT-software is no freeware!
# For further informations please vistit our website
# or contact us via email:
#
# Diese Datei / die PHPKIT-Software ist keine Freeware!
# Für weitere Information besuchen Sie bitte unsere 
# Website oder kontaktieren uns per E-Mail:
#
# Website : http://www.phpkit.de
# Mail    : info@phpkit.de
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATION
#
# SIE SIND NICHT BERECHTIGT, UNRECHTMÄSSIGE KOPIEN DIESER
# DATEI ZU ERSTELLEN UND/ODER DIESE INFORMATIONEN ZU ENTFERNEN


if(!defined('pkFRONTEND') || pkFRONTEND!='admin')
	die('Direct access to this location is not permitted.');


if(!adminaccess('avatar'))
	return pkEvent('access_forbidden');


if(isset($_REQUEST['avatargroups']))
	$avatargroups=$_REQUEST['avatargroups'];

if(isset($_REQUEST['avatartype']))
	$avatartype=$_REQUEST['avatartype'];

$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';


if(isset($_POST['cancel']) && $ACTION==$_POST['cancel'])
	{
	pkHeaderLocation('useravatar');
	}


if(isset($_POST['delete']) && isset($_POST['delete_avatar']) && $ACTION==$_POST['delete'] && is_array($_POST['delete_avatar'])) 
	{
	foreach($_POST['delete_avatar'] as $f)
		@unlink ('../'.$config['avatar_path'].'/'.basename($f));
	
	pkHeaderLocation('useravatar','','avatartype='.$avatartype);
	}


if(isset($_POST['upload']) && $ACTION==$_POST['upload']) 
	{
	$avatar_size=getimagesize($_FILES['avatar_file']['tmp_name']);
	
	unset($skip);
	
	if($avatar_size[2]==1)
		$extension='gif';
	elseif($avatar_size[2]==2)
		$extension='jpg';
	elseif($avatar_size[2]==3)
		$extension='png';
	else 
		$skip='TRUE';
  
  	if($skip!='TRUE') 
		{
		$filename=$_POST['avatar_type'].'_'.time().'.'.$extension;
		$UPLOAD=new UPLOAD();
		$uploadreturned=$UPLOAD->images($_FILES['avatar_file'],'../'.$config['avatar_path'],$filename);
		
		if($uploadreturned[0]==TRUE) 
			$file_name=$uploadreturned[1];
		}
	
	pkHeaderLocation('useravatar','','avatartype='.$_POST['avatar_type']);
	}


if($avatargroups=="upload")
	eval("\$avatar_show= \"".pkTpl("avatar_upload")."\";");
elseif($avatargroups!="" or $avatartype!="") 
	{
	if($avatargroups=="user") 
		$avatartype="avauser";
	elseif($avatargroups=="member")
		$avatartype="avamember";
	elseif($avatargroups=="mod")
		$avatartype="avamod";
	elseif($avatargroups=="admin")
		$avatartype="avaadmin";
	elseif($avatargroups=="basic")
		$avatartype="avatar";
	elseif($avatargroups=="all")
		$avatartype="ava";
	
	
	$dir='../'.$config['avatar_path'];
	$width=1;
	$a=opendir($dir);
	while($datei=readdir($a))
		{
		unset($id);
		if(strstr($datei,$avatartype))
			{
			$row=rowcolor($row);
			
			if($width==7) 
				{
				$avatar_list.='</tr><tr>';
				$width=1;
				}  
			
			$i=@getimagesize($dir."/".$datei);
			$avatar_list.='<th class="'.$row.'" valign="top"><img border="0" vspace="2" hspace="2" '.$i[3].' alt="'.$datei.'" src="'.$dir.'/'.$datei.'" />';
			
			
			if(strstr($datei,"avauser_"))
				{
				$id=str_replace("avauser_","",$datei);
				$id=explode(".",$id);
				
				$avatar_list.='<br /><span class="small"><a target="_blank" href="'.pkLink('useredit','','editid='.$id[0]).'">Benutzerprofil</a></span>';
				}
			
			$avatar_list.='<br /><input class="checkbox" type="checkbox" name="delete_avatar[]" value="'.$datei.'" /></th>'; 
			$width++;
			}
		}
	
	closedir($a);
	
	
	if($avatar_list!='')
		{
		$cs=7-$width;
		
		if($cs > 0 )
			$avatar_list.='<td colspan="$cs"></td>';
		
		eval("\$avatar_delete= \"".pkTpl("avatar_delete")."\";");
		}
	
	else
		eval("\$avatar_list= \"".pkTpl("avatar_empty")."\";");
	
	
	if($avatartype=="avauser")
		$avatar_group="User -";
	elseif($avatartype=="avamember")
		$avatar_group="Mitglieder -";
	elseif($avatartype=="avamod")
		$avatar_group="Moderatoren -";
	elseif($avatartype=="avaadmin")
		$avatar_group="Administratoren -";
	elseif($avatartype=="avatar")
		$avatar_group="Basis -";
	elseif($avatartype=="ava")
		$avatar_group="alle";
	
	
	eval("\$avatar_show= \"".pkTpl("avatar_show")."\";");
	}

	
eval("\$site_body.= \"".pkTpl("avatar")."\";");
?>