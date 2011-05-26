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


$update_id		= '164';
$update_from	= '1.6.4';
$update_to		= '1.6.5';

#special cfg
$path_sqlfile = pkDIRREP.'sites'.pkDS.'include'.pkDS.'data'.pkDS.'sql.php'; #path used for sql.php in all 1.6.4 versions


#version check
if(isset($ischeck) && $ischeck)
	{
	$match_versions = array('1.6.4','0'); #by a bug in PHPKIT 1.6.4 the version number may be '0' 
	#path used for sql.php in all 1.6.4 versions

	if(@file_exists($path_sqlfile))
		{
		@include($path_sqlfile);

		if(defined('pkSQLDATABASE') && defined('pkSQLTAB_CONFIG'))#seams to be a valid sql file
			{
			$SQL = new pkSql; #local SQL object
			$SQL->set(pkSQLDATABASE,pkSQLHOST,pkSQLUSER,pkSQLPASS);

			if(!$SQL->connect())
				{
				return false;
				}

			$query = $SQL->query("SELECT * FROM ".pkSQLTAB_CONFIG);
			while($array = $SQL->fetch_assoc($query))
				{
				if(isset($array['version_number']))
					{
					$array['version_number'] = substr($array['version_number'],0,5);

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

		pkLoadClass($SU,'sqlutilities');
		$SU->setDefinition(include(pkDIRSETUP.'sqltabdef'.pkEXT));

		#convert the config table into the new format (id=>value)
		$values = array();
		$preset = pkCfgData('config');
		$config = $S->fetch_assoc($S->query("SELECT * FROM ".pkSQLTAB_CONFIG));
		
		if(is_array($config) && !isset($config['id']) && !isset($config['value']))
			{
			foreach($config as $key=>$value)
				{
				$preset[$key] = $value;
				}
			
			$preset['site_slogan'] = $preset['site_title'];
			}
		
		#add/replace the version number
		$preset['version_number'] = $update_to;
			
		foreach($preset as $key=>$value)
			{
			$values[] = "'".$S->f($key)."','".$S->f(serialize($value))."'";
			}

		#DROP TABLE pkSQLTAB_CONFIG AND create the new one
		$S->query("DROP TABLE ".pkSQLTAB_CONFIG);
		$SU->checkTable('pkSQLTAB_CONFIG');
	
		$S->query("INSERT INTO ".pkSQLTAB_CONFIG." (id,value) VALUES (".implode('), (',$values).")");

		#add presets
		$SU->checkTable('pkSQLTAB_CONFIG_GROUP');
		$this->preset_config_groups();

		$SU->checkTable('pkSQLTAB_ADMIN_MENU');
		$this->preset_adminnavigation();
		
		
		#delete the old sum row in calendar
		$S->query("DELETE FROM ".pkSQLTAB_CALENDAR." WHERE calender_id=1");
		

		#update older templates
		$search = array();
		$replace = array();
		
		$search[]	= '<!DOCTYPE html public "-//W3C//DTD HTML 4.01 Transitional//EN">';
		$replace[]	= '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';

		$search[]	= '<html>';
		$replace[]	= '<html xmlns="http://www.w3.org/1999/xhtml">';
		
		$search[]	= '<meta http-equiv="content-type" content="text/html; charset=iso-8859-1">';
		$replace[]	= '<meta http-equiv="content-type" content="text/html; charset=$LANG[__CHARSET__]" />
<meta http-equiv="content-language" content="$LANG[__LANGCODE__]" />';

		$search[]	= '<h1 id="pkheadline">$sitename</h1>';
		$replace[]	= '<h1 class="site-name"><a href="$site_link_home" title="$site_name - $site_slogan">$site_name</a></h1>';
		
		$search[]	= '<h2 id="pkheadline">$sitetitle</h2>';
		$replace[]	= '<h2 class="site-slogan"><a href="$site_link_home" title="$site_name - $site_slogan">$site_slogan</a></h2>';

		$search[]	= 'table id="pkcontent"';		
		$replace[]	= 'table class="pkcontent"';

		$search[]	= 'td id="pkcontent_left"';
		$replace[]	= 'td class="pkcontent_left"';

		$search[]	= 'td id="pkcontent_main"';
		$replace[]	= 'td class="pkcontent_main"';

		$search[]	= 'td id="pkcontent_right"';
		$replace[]	= 'td class="pkcontent_right"';
		
		$search[]	= '</body>';
		$replace[]	= '$site_closure</body>';


		$query = $S->query("SELECT template_id, template_value FROM ".pkSQLTAB_TEMPLATE." WHERE template_name='site'");
		while(list($id,$str) = $S->fetch_row($query))
			{
			$str = str_replace($search,$replace,$str);			

			$S->query("UPDATE ".pkSQLTAB_TEMPLATE." SET template_value='".$S->f($str)."' WHERE template_id='".$S->f($id)."'");
			}
	
		
		#update older styles
		$search = array();
		$replace = array();

		$search[]	= 'h2#pkheadline';		
		$replace[]	= 'h2.site-slogan';

		$search[]	= '#pkheadline {';
		$replace[]	= 'h1.site-name, h2.site-slogan {
display:block;';

		$search[]	= 'font-family:verdana;';
		$replace[]	= 'font-family:Verdana, Arial, Helvetica, sans-serif;';

		$search[]	= 'table#pkcontent';
		$replace[]	= 'table.pkcontent';

		$search[]	= 'td#pkcontent_left';
		$replace[]	= 'td.pkcontent_left';
		
		$search[]	= 'td#pkcontent_right';
		$replace[]	= 'td.pkcontent_right';

		$search[]	= 'td#pkcontent_main';
		$replace[]	= 'td.pkcontent_main';

		$addcss = '

h1.site-name a,
h2.site-slogan a {
color:#FFF;
font-size:28px;
font-weight:bold;
font-family:Verdana, Arial, Helvetica, sans-serif;
text-decoration:none;
outline:0;
}

h2.site-slogan a {
font-size:14px;
}';

		
		$query = $S->query("SELECT style_id, style_addcss FROM ".pkSQLTAB_STYLE." WHERE style_addcss LIKE '%#headline%' OR style_addcss LIKE '%#pkcontent%'");
		while(list($id,$str) = $S->fetch_row($query))
			{
#echo 'ORIGINAL<br />';
#pk::fprint($str);
#echo '<br />';
			$str = str_replace($search,$replace,$str);
			$str.= $addcss;

#echo 'UPDATED<br />';
#pk::fprint($str);
#echo '<hr />';

#if(			
			$S->query("UPDATE ".pkSQLTAB_STYLE." SET style_addcss='".$S->f($str)."' WHERE style_id='".$S->f($id)."'");
#		echo 'SUCCESS';
#else
#		echo 'FAILED';
#echo '<hr />';

			}
		
		
		#insert all new styles
		$this->style_import();

		#delete old sql file
		pkLoadFunc('file');
		@unlink($path_sqlfile);
		pkRmDir(pkDIRREP.'sites');

		if(pkFileCheck($path_sqlfile))
			{
			$this->body.= pkGetSpecialLang('setup_old_file_exists', 'pkinc/rep/sites/include/data/sql.php');
			}

		$this->body.= pkGetLang('setup_update_succesful_finished');
		$this->body.= pkGetLang('setup_click_to_procced');

		$this->action = pkLink('dbcheck','update');
		break;
	case 2 :
		#get old sql data
		if(!pkFileCheck($path_sqlfile))
			{
			exit('file: pkinc/rep/sites/include/data/sql.php missing');
			}


		include($path_sqlfile);
		
		$sqltabs = array();
		foreach(pkCfgData('sqltables') as $alias=>$suffix)
			{
			$sqltabs[$alias] = defined($alias) ? constant($alias) : pkSQLPREFIX.'_'.$suffix;
			}
		
		if(!$this->create_sql_file(pkSQLDATABASE, pkSQLHOST, pkSQLUSER, pkSQLPASS, pkSQLPREFIX, $sqltabs))
			{
			exit('new sqlfile could not be created');
			}

				
		$this->SQL->set(pkSQLDATABASE, pkSQLHOST, pkSQLUSER, pkSQLPASS);
		if(!$this->SQL->connect())	#do we have a valid db-connection
			{
			exit('datas converted but no database connection detected');
			}
				
		$this->body = pkGetLang('setup_database_data_succesful_transfered');
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

			$this->button = pkGetLang('bl_check_again');
			$this->action = pkLink('update','','version='.$update_id);
			return;
			}
	
		$this->body.= pkGetLang('setup_click_to_procced');		
		$this->action = pkLink('update','','version='.$update_id.'&step=2');
		break;
	#END case 1 | default 	
	}	
?>