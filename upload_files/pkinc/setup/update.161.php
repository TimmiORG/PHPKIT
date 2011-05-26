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


if(!defined('pkFRONTEND') || pkFRONTEND!='setup')
	die('Direct access to this location is not permitted.');


$update_id		= '161';
$update_from	= '1.6.1/1.6.03';
$update_to		= '1.6.4';


#version check
if(isset($ischeck) && $ischeck)
	{
	$match_versions = array('1.6.1','1.6.03','1.6.02');
	
	$path  = pkDIRROOT.'admin'.pkDS.'config'.pkDS.'inc.sql.php'; #path used for sql.php in 1.6.02/1.6.03/1.6.1
	$path2 = pkDIRROOT.'admin'.pkDS.'config'.pkDS.'inc.dbtabs.php'; #seperated file for the tables names

	if(@file_exists($path) && file_exists($path2))
		{
		@include($path);
		@include($path2);

		if(isset($database) && isset($db_tab['config']))#seams to be a valid sql file / keep $db_tab here
			{
			$SQL = new pkSql; #local SQL object
			$SQL->set($database,$sqlhost,$sqluser,$sqlpass);
			
			if(!$SQL->connect())
				{
				return false;
				}

			$query = $SQL->query("SELECT * FROM ".$db_tab['config']);#keep $db_tab here
			while($array = $SQL->fetch_assoc($query))
				{
				if(isset($array['version_number']))
					{
					if(in_array($array['version_number'],$match_versions))
						{
						return true;
						}
					}
				}#END while fetch_assoc
			}
		}#END file exists	
	
	return false;
	}#END version check


#perform update
$this->title = pkGetSpecialLang('setup_perform_update',$update_from,$update_to);
	
switch($step)	
	{
	case 3 :
		$S = $this->SQL;
		
		if(!$S->connect())
			{
			exit('No database connection');
			}
	
		$row='';
		
		pkLoadClass($SU,'sqlutilities');
		$SU->setDefinition(include(pkDIRSETUP.'sqltabdef'.pkEXT));
		$SU->cleanTable('pkSQLTAB_USER');
		$SU->cleanTable('pkSQLTAB_USER_FRIENDLIST');
		$SU->setMode(1);
		$SU->checkTable('pkSQLTAB_STYLE');
		
		$this->style_import();
	
		$sql = '';
		
		if($this->style_standardid)
			{
			$sql .=", site_style='".intval($this->style_standardid)."'";
			}
			
		$S->query("UPDATE ".pkSQLTAB_CONFIG." SET version_number='".$S->f($update_to)."'".$sql);

		#output
		$this->body.= pkGetLang('setup_update_succesful_partial');
		
		#delete old sql file
		pkLoadFunc('file');
		@unlink(pkDIRROOT.'admin/config/inc.sql.php');
		pkRmDir(pkDIRROOT.'admin/config');
		
		if(pkFileCheck(pkDIRROOT.'admin/config/inc.sql.php'))
			{
			$this->body.= pkGetSpecialLang('setup_old_file_exists', 'admin/config/inc.sql.php');
			}

		$this->action = pkLink('update','','version=164&step=3');
		break;
		#END case 3
	case 2 :
		if(!pkFileCheck(pkDIRROOT.'admin/config/inc.sql.php'))
			{
			exit('file: admin/config/inc.sql.php missing');
			}
		
		if(!pkFileCheck(pkDIRROOT.'admin/config/inc.dbtabs.php'))
			{
			exit('file: admin/config/inc.dbtabs.php missing');
			}

		include(pkDIRROOT.'admin/config/inc.sql.php');
		include(pkDIRROOT.'admin/config/inc.dbtabs.php');
		
		
		$sqltabs = isset($db_tabs) && is_array($db_tabs) ? $db_tabs : array();
		
		if(!$this->create_sql_file($database, $sqlhost, $sqluser, $sqlpass, $sqlprefix, $sqltabs))
			{
			exit('new sqlfile could not be created');
			}

				
		$this->SQL->set($database,$sqlhost,$sqluser,$sqlpass);
		if(!$this->SQL->connect())	#do we have a valid db-connection
			{
			exit('datas converted but no database connection detected');
			}
		
		$this->body = pkGetLang('setup_database_data_succesful_transfered');


		#ALTER db structure
		if(isset($db_tab['blacklist']) && $this->SQL->table_exists($db_tab['blacklist']))
			{	
			$this->SQL->query("DROP TABLE ".$db_tab['blacklist']);
			}#END table exists blacklist
		

		$query = $this->SQL->query("DESCRIBE ".pkSQLTAB_USER_FRIENDLIST);		
		while($info = $this->SQL->fetch_assoc($query))
			{
			if($info['Field']=='buddy_id')
				{
				$this->SQL->query("ALTER TABLE ".pkSQLTAB_USER_FRIENDLIST." DROP buddy_id");
				}
			}#END table exists user friendlist

		$this->SQL->query("ALTER TABLE ".pkSQLTAB_USER_FRIENDLIST." ADD PRIMARY KEY (buddy_userid,buddy_friendid)");
		#END pkSQLTAB_USER_FRIENDLIST		
			
			
		$result = $this->SQL->query("DESCRIBE ".pkSQLTAB_CONTENT);		
		while($info = $this->SQL->fetch_assoc($result))
			{
			if($info['Field']=='content_template')
				{
				$this->SQL->query("ALTER TABLE ".pkSQLTAB_CONTENT." DROP content_template");
				}
			}			
			
		$result=$this->SQL->query("DESCRIBE ".pkSQLTAB_NAVIGATION_CATEGORY);		
		while($info=$this->SQL->fetch_assoc($result))
			{
			if($info['Field']=='navigationcat_boxdir')
				{
				$this->SQL->query("ALTER TABLE ".pkSQLTAB_NAVIGATION_CATEGORY." DROP navigationcat_boxdir");
				}
			}
			
		$result=$this->SQL->query("DESCRIBE ".pkSQLTAB_FORUM_THREAD);		
		while($info=$this->SQL->fetch_assoc($result))
			{
			if($info['Field']=='forumthread_votetitle')
				{
				$this->SQL->query("ALTER TABLE ".pkSQLTAB_FORUM_THREAD." DROP forumthread_votetitle");
				}
			}

		$result=$this->SQL->query("DESCRIBE ".pkSQLTAB_FORUM_CATEGORY);		
		while($info=$this->SQL->fetch_assoc($result))
			{
			if($info['Field']=='forumcat_voteoption' || $info['Field']=='forumcat_vote_option' || $info['Field']=='forumcat_attachments')
				{
				$this->SQL->query("ALTER TABLE ".pkSQLTAB_FORUM_CATEGORY." DROP ".$info['Field']);
				}
			}

		$result=$this->SQL->query("DESCRIBE ".pkSQLTAB_STYLE);		
		while($info=$this->SQL->fetch_assoc($result))
			{
			switch($info['Field'])
				{
				case 'bodymargin' :
				case 'navtablecolor' :
				case 'navheadbgimage' :
				case 'navheadfont' : 
				case 'navheadfontsize' :
				case 'navheadfontcolor' :
				case 'navheadbackground' : 
				case 'navbodyfont' : 
				case 'navbodyfontsize' : 
				case 'navbodyfontcolor' : 
				case 'navbodyhover' : 
				case 'navbodybackground' :				
					$this->SQL->query("ALTER TABLE ".pkSQLTAB_STYLE." DROP ".$info['Field']);
					break;
				}
			}

		$result=$this->SQL->query("DESCRIBE ".pkSQLTAB_CONFIG);
		while($info=$this->SQL->fetch_assoc($result))
			{
			switch($info['Field'])
				{
				case 'profil_id' :
				case 'profil_name' :
				case 'site_width' :
				case 'site_design' :
				case 'site_layout' :
				case 'site_logo' :
				case 'site_layout' :
				case 'template_dir' :
				case 'forum_attach_dir' :
				case 'forum_attach_ext' :
				case 'forum_attach_size' :
					$this->SQL->query("ALTER TABLE ".pkSQLTAB_CONFIG." DROP ".$info['Field']);
					break;
				
				case 'profil_active' :
					list($count)=$this->SQL->fetch_row($this->SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_CONFIG." WHERE profil_active=1"));

					if($count>1)
						{
						$this->SQL->query("DELETE FROM ".pkSQLTAB_CONFIG." WHERE profil_active<>1");
						}

					$this->SQL->query("ALTER TABLE ".pkSQLTAB_CONFIG." DROP profil_active");
					break;
				}
			}
		
		list($referer_delete) = $this->SQL->fetch_row($this->SQL->query("SELECT referer_delete FROM ".pkSQLTAB_CONFIG." LIMIT 1"));
		
		$this->SQL->query("UPDATE ".pkSQLTAB_CONFIG." SET version_number='".$update_to."',referer_delete='".(ceil($referer_delete/24))."'");
		
		$this->body.= pkGetLang('setup_click_to_procced');
		$this->action = pkLink('update','','version='.$update_id.'&step=3');
		break;
		#END case 2
	case 1 :
	default:
		pkLoadFunc('file');
		pkLoadFunc('system');
		pkLoadLang('system');
		
		#new directories
		$error = 0;
		$dirhash = array(pkDIRETC,pkDIRTEMP);
		
		foreach($dirhash as $dir)	
			{
			#check the new directory
			$shortdirpath = str_replace(pkDIRROOT,'',$dir);
			$this->body.='<div>'.pkGetSpecialLang('write_permissions_dirs',$shortdirpath).' : ';

			if(!pkMkDir($dir,pkCHMODDIR_WRITE) && !@is_dir($dir))
				{
				$this->body.=pkGetLang('dir_does_not_exists').'</div>';
				$error=1;
				break;
				}
			
			if(!@is_writable($dir))
				{
				$this->body.=pkGetLang('fail').'</div>';
				$error=2;
				break;
				}
				
			$this->body.=pkGetLang('set').'</div>';
			}
		
		if($error)
			{
			if($error==1)
				{
				$this->body.=pkGetSpecialLang('setup_directory_doesnt_exists_explain',$shortdirpath);
				}
			elseif($error==2)
				{
				$this->body.=pkGetSpecialLang('setup_directory_doesnt_have_writepermissions_explain',$shortdirpath);
				}

			$this->button=pkGetLang('bl_check_again');
			$this->action=pkLink('update','','version='.$update_id);
			return;
			}
	
		$this->body.=pkGetLang('setup_click_to_procced');		
		$this->action=pkLink('update','','version='.$update_id.'&step=2');
		break;
	#END case 1 | default 	
	}	
?>