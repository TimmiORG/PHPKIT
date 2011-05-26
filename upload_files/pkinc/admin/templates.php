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


if(!adminaccess('templates'))
	return pkEvent('access_forbidden');
	

if(isset($_REQUEST['templatepack_id']))
	{
	if(intval($_REQUEST['templatepack_id']))
		$templatepack_id = intval($_REQUEST['templatepack_id']);
	elseif($_REQUEST['templatepack_id']=='default')
		$templatepack_id = 'default';
	else
		$templatepack_id = 0;
	}
else
	{
	if(pkGetConfig('site_style'))
		{
		$style=$SQL->fetch_assoc($SQL->query("SELECT style_template FROM ".pkSQLTAB_STYLE." WHERE style_id='".pkGetConfig('site_style')."' LIMIT 1"));
		$templatepack_id = $style['style_template'];
		$templatepack_id = $templatepack_id == -1 ? 'default' : $templatepack_id;
		}
	else
		{
		$templatepack_id = 'default';
		}
	}


$ACTION = isset($_POST['action']) ? $_POST['action'] : 'view';


if($ACTION==$_POST['cancel']) 
	{
	pkHeaderLocation('templates','','template_id='.$template_id.'&templatepack_id='.$templatepack_id);
	}


$pack_hash = array();
$template_edit_error = '';


$query = $SQL->query("SELECT * FROM ".pkSQLTAB_TEMPLATE_PACK." ORDER BY templatepack_name ASC");
while($pack = $SQL->fetch_assoc($query))
	{
	$pack_hash[$pack['templatepack_id']] = $pack;
	}

if(isset($_REQUEST['ispack']))
	{
	if(($ACTION==$_POST['save'] && $_POST['templatepack_delete']==1) || $ACTION==$_POST['delete'])
		{
		if($ACTION==$_POST['delete'])
			{
			if($_POST['confirm']=='true' && intval($templatepack_id)>0)
				{
				$SQL->query("DELETE FROM ".pkSQLTAB_TEMPLATE_PACK." WHERE templatepack_id='".$templatepack_id."' LIMIT 1");
				$SQL->query("DELETE FROM ".pkSQLTAB_TEMPLATE." WHERE template_packid='".$templatepack_id."'");
				}
			
			pkHeaderlocation('templates','','ispack=1');
			}
		else
			{
			$templatepack_name=$pack_hash[$templatepack_id]['templatepack_name'];
			
			eval("\$site_body.= \"".pkTpl("templates_pack_delete")."\";");
			}
		}
	elseif($ACTION==$_POST['save'] && trim($_POST['templatepack_name'])!='')
		{
		if($templatepack_id=='new')
			{
			$SQL->query("INSERT INTO ".pkSQLTAB_TEMPLATE_PACK." (templatepack_name) VALUES ('new')");
			$templatepack_id=$SQL->insert_id();
			}
						
		$SQL->query("UPDATE ".pkSQLTAB_TEMPLATE_PACK." 
			SET templatepack_name='".$SQL->f($_POST['templatepack_name'])."',
				templatepack_dir='".$SQL->f($_POST['templatepack_dir'])."',
				templatepack_opt='".$SQL->f($_POST['templatepack_opt'])."'
			WHERE templatepack_id='".$templatepack_id."'");
		
		pkHeaderLocation('templates','','ispack=1&templatepack_id='.$templatepack_id);
		}
	else
		{
		if($ACTION==$_POST['newpack'])
			{
			$templatepack_id='new';
			
			eval("\$pack_select= \"".pkTpl("templates_pack_new")."\";");
			}
		else
			unset($pack_select);
		
		
		if(is_array($pack_hash))
			{
			foreach($pack_hash as $pack)
				{
				$pack_select.='<option value="'.$pack['templatepack_id'].'"';
				
				if($pack['templatepack_id']==$templatepack_id) 
					$pack_select.=' selected';
				
				$pack_select.='>'.pkEntities($pack['templatepack_name']).'</option>';
				}
			}
		
		
#		if($templatepack_id!='new')
#			eval("\$templatepack_body= \"".pkTpl("templates_pack_imexport")."\";"); 
		
		
		if(intval($templatepack_id)>0 || $templatepack_id=='new')
			{
			$templatepack_name=$pack_hash[$templatepack_id]['templatepack_name'];
			$templatepack_dir=$pack_hash[$templatepack_id]['templatepack_dir'];
			
			if($pack_hash[$templatepack_id]['templatepack_opt']==1)
				$option1='checked';
			else
				$option0='checked';   
			
			
			if($templatepack_id!='new')
				eval("\$templatepack_delete= \"".pkTpl("templates_pack_deleteoption")."\";");   
			
			eval("\$templatepack_body.= \"".pkTpl("templates_pack_form")."\";");
			}
		else
			{
			if($templatepack_id=='0')
				$select0='selected';
			else
				$selectdefault="selected";
			}
		
		eval("\$site_body.= \"".pkTpl("templates_pack")."\";");
		}
	}
else
	{
	if($ACTION==$_POST['save'])
		{
		if($pack_hash[$templatepack_id]['templatepack_opt']==1)
			{
			$filename='../'.$pack_hash[$templatepack_id]['templatepack_dir'].'/'.$_POST['template_name'].pkEXTTPL;

			touch($filename);
			$fp=fopen($filename,"w");
			fwrite($fp,$_POST['template_value']);
			fclose($fp);
			}
		elseif($_POST['template_id']!='new' && !intval($_POST['template_id'])>0)
			$_POST['template_id']='new';
		
		
		if($_POST['template_id']=='new')
			{
			$count=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_TEMPLATE." WHERE template_packid='".$SQL->f($_POST['templatepack_id'])."' AND template_name='".$SQL->f($_POST['template_name'])."' LIMIT 1"));

			if($count[0]==0)
				{
				$SQL->query("INSERT INTO ".pkSQLTAB_TEMPLATE." (template_packid,template_name) VALUES ('".$SQL->f($_POST['templatepack_id'])."','".$SQL->f($_POST['template_name'])."')");
				$template_id=$SQL->insert_id();
				}
			elseif($_POST['template_overwrite']==1)
				{
				$id=$SQL->fetch_array($SQL->query("SELECT template_id FROM ".pkSQLTAB_TEMPLATE." WHERE template_packid='".$SQL->f($_POST['templatepack_id'])."' AND template_name='".$SQL->f($_POST['template_name'])."' LIMIT 1"));
				$template_id=$id[0];
				}
			else
				{
				$template_exists=1;
				$template_id='new';
				}
			}
		else
			$template_id=$_POST['template_id'];
			
		
		if(intval($template_id)>0)
			$SQL->query("UPDATE ".pkSQLTAB_TEMPLATE."
				SET template_value='".$SQL->f($_POST['template_value'])."',
					template_packid='".$SQL->f($_POST['templatepack_id'])."',
					template_name='".$SQL->f($_POST['template_name'])."'
				WHERE template_id='".$template_id."'");
			
		$_POST['edit']=$ACTION=$lang['edit'];
		}
		
	if($ACTION==$_POST['export'] && $_POST['template_id']!='')
		{
		$template_code = '';
		$template_name = 'undefined';
		
		
		if(@ini_set("url_rewriter.tags",''))
			{
			if($gettemplate=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_TEMPLATE." WHERE template_id='".$SQL->f($_POST['template_id'])."'")))
				{
				$template_code=$gettemplate['template_value'];
				$template_name=str_replace("/","-",$gettemplate['template_name']);
				}
			else
				{
				$file = basename($_POST['template_id']);
				$path = pkDIRPUBLICTPL.$file.pkEXTTPL;
	
				if(file_exists($path))
					{
					$template_code = file_get_contents($path);
					$template_name = str_replace("/","-",$_POST['template_id']);
					}
				}
				
			$content_type=(USR_BROWSER_AGENT == 'IE' || USR_BROWSER_AGENT == 'OPERA') ? 'application/octetstream':'application/octet-stream';
			$content_disposition=(USR_BROWSER_AGENT == 'IE') ? 'inline;':'attachment;';
				
				
			header('Content-Type: '.$content_type);
			header('Content-disposition: '.$content_disposition.'filename='.$template_name.pkEXTTPL);
			header('Pragma: no-cache');
			header('Expires: 0');
				
			echo $template_code;
			exit;
			}
		else 
			{
			echo "Die Datei kann nicht heruntergeladen werden";
				
			/*
			Template-Datei erzeugen
			touch($filename.".htm");
			$fp=fopen($filename.".htm","w");
			fwrite($fp,$export_value);
			fclose($fp);
			*/
			}
		}
	elseif($ACTION==$_POST['delete'] && $_POST['template_id']!='')
		{
		if($_POST['confirm']=='true')
			{
			$SQL->query("DELETE FROM ".pkSQLTAB_TEMPLATE." WHERE template_id='".$SQL->i($_POST['template_id'])."'");
				
			pkHeaderLocation('templates','','templatepack_id='.$templatepack_id);
			}
		else
			{
			$gettemplate=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_TEMPLATE." WHERE template_id='".$SQL->i($_POST['template_id'])."'"));
				
			$template_name=pkEntities($gettemplate['template_name']);
				
			if(!empty($template_name))
				{
				eval("\$site_body.= \"".pkTpl("templates_delete")."\";");
				}
			else
				{
				pkHeaderLocation('templates','','templatepack_id='.$templatepack_id);
				}
			}
		}
	elseif(($ACTION==$_POST['edit'] && (isset($_POST['template_id']) || isset($template_id))) || $ACTION==$_POST['create'] || ($ACTION==$_POST['import'] && $_FILES['template_import']['tmp_name']!='' && $_FILES['template_import']['tmp_name']!='none'))
		{
		if($template_exists==1)
			{
			$template_code=pkEntities($_POST['template_value']);
			$template_name=$_POST['template_name'];
				
			eval("\$template_edit_error.= \"".pkTpl("templates_edit_error")."\";");
			}
		elseif($ACTION==$_POST['import'])
			{
			#import a sinlge template
			$tmp_name = basename($_FILES['template_import']['tmp_name']);
			$tmp_path = pkDIRTEMP.$tmp_name;
			
			move_uploaded_file($_FILES['template_import']['tmp_name'],$tmp_path);
			
			$template_code = pkEntities(file_get_contents($tmp_path));

			unlink($tmp_path);

			$template_name = str_replace("-","/",str_replace(pkEXTTPL,'',$_FILES['template_import']['name']));
			$template_id = 'new';
			}
		elseif(intval($_POST['template_id'])>0 || intval($template_id)>0)
			{
			if(intval($_POST['template_id'])>0 && !$template_id)
				$template_id=$_POST['template_id'];
				
			$template=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_TEMPLATE." WHERE template_id='".$template_id."' LIMIT 1"));
			$templatepack_id=$template['template_packid'];
			$template_code=pkEntities($template['template_value']);
			$template_name=$template['template_name'];
			}
		elseif($ACTION==$_POST['edit'])
			{
			if($templatepack_id=='default') 
				$dir=pkDIRPUBLICTPL;
				
			$filename=$dir.$_POST['template_id'].pkEXTTPL;
			$template_name=$_POST['template_id'];
			$template_code=pkEntities(implode('',file($filename)));
			}
		
		
		$pack_select = '';
		$templatepack_id = $templatepack_id=='default' ? 0 : $templatepack_id;
			
		if(is_array($pack_hash))
			{
			foreach($pack_hash as $pack)
				{
				$pack_select.='<option value="'.$pack['templatepack_id'].'"';
					
				if($pack['templatepack_id']==$templatepack_id)
					$pack_select.=' selected';
					
				$pack_select.='>'.pkEntities($pack['templatepack_name']).'</option>';
				}
			}
		
		$var="select".$templatepack_id;
		$$var="selected";
			
		eval("\$site_body.= \"".pkTpl("templates_edit")."\";");
		}
	else
		{
		if(isset($_REQUEST['template_searchstr'])) 
			$template_searchstr=$_REQUEST['template_searchstr'];
			
		$template_array=array();
		
		if(is_array($pack_hash))
			{ 
			foreach($pack_hash as $pack)
				{
				$pack_select.='<option value="'.$pack['templatepack_id'].'"';
					
				if($pack['templatepack_id']==$templatepack_id)
					{
					$pack_select.=' selected="selected"';
					}
					
				$pack_select.='>'.pkEntities($pack['templatepack_name']).'</option>';
				}
			}


		if($templatepack_id === 'default')
			{
			$selectdefault = ' selected="selected"';
			$template_array = readTemplateDir('',$template_searchstr);
			}
		else
			{
			$var='select'.$templatepack_id;
			$$var='selected';
			
			eval("\$modifypack_link= \"".pkTpl("templates_modifypack_links")."\";");
				
			if(isset($_POST['template_searchstr'])) 
				{
				$searchtemplates=" AND template_name LIKE '%".$SQL->f($_POST['template_searchstr'])."%'";
				}
			else
				{
				$searchtemplates = '';
				}

			if($pack_hash[$templatepack_id]['templatepack_opt']==1)
				{
				#read from file system
				$template_array=readTemplateDir($pack_hash[$templatepack_id]['templatepack_dir'],$template_searchstr);
				}
			else
				{
				$gettemplates=$SQL->query("SELECT template_id, template_name FROM ".pkSQLTAB_TEMPLATE." WHERE template_packid='".$templatepack_id."' ".$searchtemplates);
				while($template=$SQL->fetch_array($gettemplates))
					{
					$template_array[$template['template_name']]='<option value="'.$template['template_id'].'"';
						
					if($template_id==$template['template_id'])
						{
						$template_array[$template['template_name']].=' selected';
						}
						
					$template_array[$template['template_name']].='>'.pkEntities($template['template_name']).'</option>';
					}
				}
			}
			
			
		if(is_array($template_array))
			{
			ksort($template_array);
			$count_templates=count($template_array);

			foreach($template_array as $template)
				$template_list.=$template;
			}
		else
			$count_templates=0;
			
		eval("\$site_body.= \"".pkTpl("templates")."\";");
		
		pkEvent('thirdparty_warning','warning');
		}
	}
?>