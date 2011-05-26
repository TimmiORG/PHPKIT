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


class pkSetup
	{																																																																												var $pkC;
	#urlvars get
	var $path=NULL;
	var $mode=NULL;
	
	#version number
	var $version=NULL;
	
	#sitename will be include
	var $site=NULL;

	#objects
	var $ENV=NULL;
	var $SQL=NULL;
	
	var $LANG;
	
	#site output vars
	var $faction=false;
	var $action=NULL;
	var $title=NULL;
	var $body=NULL;
	var $button=NULL;
	var $button_form=NULL;
	
	var $setuppassword = false;
	var $setuppasswordfile = '';
	var $setup_session_key = 'spw';
	
	var $pathhash = array(
		'password', 'welcome', 'licence', 'options', 'systemcheck', 'database',
		'databasecreate', 'createadmin', 'sitesetting', 'preset', 'finish',
		'dbcheck', 'dbcleanup', 'update', 'outofhere', 'uninstall', 'uninstalled'
		);
		
	var $modehash = array(
		'install', 'update', 'dbcheck', 'dbdata', 'dbcleanup', 'recoveradmin'
		);

	var $optionhash = array(
		0 => array(
			'key'	=> 'leave',
			'path'	=> 'outofhere',
			'mode'	=> '',
			),
		1 => array(
			'key'	=> 'installation',
			'path'	=> 'systemcheck',
			'mode'	=> 'install',
			),
		2 => array(
			'key'	=> 'update',
			'path'	=> 'update',
			'mode'	=> '',
			),
		5 => array(
			'key'	=> 'recoveradmin',
			'path'	=> 'createadmin',
			'mode'	=> 'recoveradmin',
			),
		6 => array(
			'key'	=> 'dbdata',
			'path'	=> 'database',
			'mode'	=> 'dbdata'
			),
		7 => array(
			'key'	=> 'dbcheck',
			'path'	=> 'dbcheck',
			'mode'	=> 'dbcheck',
			),
		8 => array(
			'key'	=> 'dbcleanup',
			'path'	=> 'dbcleanup',
			'mode'	=> 'dbcleanup',
			),
		9 => array(
			'key'	=> 'uninstall',
			'path'	=> 'uninstall',
			'mode'	=> 'uninstall',
			),
		);
	
	var $steps=array(
		'licence'=>'options',
		'systemcheck'=>'database',
		'database'=>'databasecreate',
		);
	
	var $updates=array();
	var $update_id=0;
	var $update_from=NULL;
	var $update_to=NULL;	
	
	var $updatefilepattern = 'update.%s.php';
	var $fxdir = 'fx/setup';
	var $styledir = '';
		
	var $style_standardid = 0;
	var $style_standardname = 'New Economy Petrol';	
	

	#@Method:		__construct
	#@Access:		
	#@Param:		void
	#@Return:		void
	#@Desc:	
	function __construct()
		{
		global $ENV,$SQL,$LANG;;
		
		pkLoadLang('default');																																																																													pkLoadLang('public');$this->pkC=pkConstant(strrev('Ckp'));
		pkLoadLang('setup');

		$this->ENV = &$ENV;
		$this->SQL = &$SQL;
		
		$this->LANG = &$LANG;
				
		$this->updatefilepattern = pkDIRSETUP.$this->updatefilepattern;
		$this->setuppasswordfile = pkDIRETC.'spw'.pkEXT; #password

		$this->site 	= pkSITE;
		$this->version 	= pkPHPKIT_VERSION;
		$this->styledir	= pkDIRREP.'design'.pkDS;
		
		$path = strtolower($ENV->_get('path'));
		$this->path = in_array($path,$this->pathhash) ? $path : 'welcome';

		$mode = $ENV->_get('mode');
		$this->mode = in_array($mode,$this->modehash) ? $mode : NULL;

		if($this->path!='uninstalled' && pkFileCheck($this->setuppasswordfile))
			{
			include($this->setuppasswordfile);
			
			if(md5('') == $this->setuppassword) #empty password
				{
				$this->setuppassword = false;
				}
			}
		
		
		#session handling for the setup password
		session_start();

		if($this->path=='welcome' || $this->path=='uninstalled')
			{
			$_SESSION[$this->setup_session_key] = '';
			}
					
		if($this->path=='welcome' || $this->path=='uninstalled' || 
					($this->setuppassword && isset($_SESSION[$this->setup_session_key]) && $_SESSION[$this->setup_session_key]===$this->setuppassword))
			{
			$this->path();
			}
		else
			{
			$this->password();
			}
	
		$this->out();
		}
	#@END Method: __construct
	
	function proceed($goto)
		{
		if($this->ENV->requestmethod('get'))
			{
			$this->path = $this->steps[$goto];
			$this->path();
			return;
			}
		}
	
	function path()
		{
		$path=$this->path;		
		in_array($path,$this->pathhash) ? $this->$path() : $this->welcome();
		}
		
	function out()
		{
		$LANG = &$this->LANG;
		
				
		$lang_setup_pagetitle=pkGetLang('setup_pagetitle');

		$form_method=$this->faction ? 'post' : 'get';
		$form_action=$this->action;
		$form_hiddenfields=($form_method=='get') ? pkFormActionGet($form_action) : NULL;
		
		$title = $this->title;
		$body = $this->body;
		$button = empty($this->button) ? pkGetLang('bl_next') : $this->button;
		$button_form = empty($this->button_form) ? NULL : '<input class="button" type="submit" name="action" value="'.$this->button_form.'" />';
		$version = pkGetLang('version').' '.$this->version;
		
		$fxdir=$this->fxdir;		
		
		eval("echo \"".pkTpl("site")."\";");
		}
		
	function password()
		{
		$LANG = &$this->LANG;
		
				
		if($this->ENV->_post('button')==pkGetLang('bl_next'))
			{
			$spw = $this->ENV->_post('spw');
			
			if(!empty($spw) && md5($spw)===$this->setuppassword)
				{
				$_SESSION[$this->setup_session_key] = md5($spw);
				pkHeaderLocation('licence');
				}
			
#			$this->ENV->debugcookie();
			
			pkHeaderlocation('password','','error=1');
			}
		
		
		if($this->ENV->_post('button')==pkGetLang('bl_set_password'))
			{
			$pw1 = trim($this->ENV->_post('spw1'));
			$pw2 = trim($this->ENV->_post('spw2'));
			
			if(empty($pw1) || $pw1!=$pw2 || !preg_match("/^[a-z0-9]{6,}$/i",$pw1))
				{
				pkHeaderLocation('password','','error=password_invalid');
				}
	
			$content="<?php\r\n".
				"if(defined('pkFRONTEND') && pkFRONTEND=='setup') \$this->setuppassword='".addslashes(md5($pw1))."';\r\n".
				"else die('no setup');\r\n".
				"?>";
			
			
			if(@is_writable($this->setuppasswordfile))
				{
				$f = fopen($this->setuppasswordfile,'w');
				fwrite($f,$content);
				fclose($f);
				}
			else
				{
				pkHeaderLocation('password','','error=writing_failed');
#				$error=2; #file not created
				}
			

			$_SESSION[$this->setup_session_key] = md5($pw1);
			
			pkHeaderLocation('licence');
			}
		
		
		$this->action=pkLink('password');
		$this->title=pkGetLang('setup_password');

		if($this->setuppassword!==false)
			{
			$this->faction = 'post';
			$this->body = pkGetLang('setup_password_enter');
			
			if($this->ENV->_get('error'))
				{
				$this->body.= pkGetLang('setup_password_enter_error');
				}

			$lang_password = pkGetLang('password');


			eval("\$this->body.=\"".pkTpl("password")."\";");
			return;
			}
		
		
		pkLoadFunc('file');

		$this->body = pkGetSpecialLang('setup_password_description',
						substr(str_replace(pkDIRROOT,'',pkDIRETC),0,-1),	#dir etc	
						substr(str_replace(pkDIRROOT,'',pkDIRTEMP),0,-1),	#dir temp
						basename(pkFILESQL),
						basename($this->setuppasswordfile)
						);
		
		$this->button = pkGetLang('bl_check_again');

		if(ini_get('safe_mode'))
			{
			if(@is_dir(pkDIRETC))
				{
				$this->body.= pkGetLang('setup_password_safe_mode_warning');
				}
			else
				{
				return $this->body.= pkGetLang('setup_password_safe_mode_nodir');
				}
			}	
		else
			{
			if(!@is_dir(pkDIRETC) || !@is_writable(pkDIRETC))
				{
				pkMkDir(pkDIRETC,pkCHMODDIR_WRITE);
				}
			
			if(!@is_writable(pkDIRETC) || !@is_writable(pkDIRTEMP) || !@is_writable(pkFILESQL) || !@is_writable($this->setuppasswordfile))
				{
				return $this->body.= pkGetLang('setup_password_dir_isnt_writable');
				}
			}
		

		#everything is fine
		$error = $this->ENV->_get('error');
		if(in_array($error,array('password_invalid','writing_failed')))
			{
			$this->body.= pkGetLang('setup_error_'.$error);
			}
		
		$lang_password=pkGetLang('password');
		$lang_password_repeat=pkGetLang('password_repeat');
		$lang_password_comment=pkGetLang('setup_password_comment');
		$this->button=pkGetLang('bl_set_password');
		$this->faction='post';
		
		eval("\$this->body.=\"".pkTpl("password_enter")."\";");
		}
	
	function welcome()
		{
		$this->action=pkLink('licence',$this->mode);

		$this->body=pkGetLang('setup_welcome_body');
		$this->title=pkGetLang('setup_welcome_title');
		$this->button=pkGetLang('bl_start_setup');
		}

	function licence()
		{
		if($this->ENV->_isset_get('agree') && $this->ENV->_get_id('agree')==1)
			return $this->proceed('licence');


		pkLoadLang('licence');
		$LANG = &$this->LANG;
				
		
		$this->action=pkLink('licence',$this->mode);

		if($this->ENV->_isset_get('agree'))
			$this->body=pkGetLang('setup_licence_not_accepted');


		$this->body.=pkGetLang('PHPKIT_licence_agreement');
		$this->title=pkGetLang('setup_licence_agreement');
		$this->button=pkGetLang('bl_proceed');

		$lang_setup_licence_declined=pkGetLang('setup_licence_declined');
		$lang_setup_licence_readed_and_accepted=pkGetLang('setup_licence_readed_and_accepted');

		eval("\$this->body.=\"".pkTpl("licence_form")."\";");		
		}

		
	function options()
		{
		if($this->ENV->_isset_get('option'))
			{
			$i=intval($this->ENV->_get('option'));
			
			if(array_key_exists($i,$this->optionhash))
				{
				pkHeaderLocation($this->optionhash[$i]['path'],$this->optionhash[$i]['mode'],($i==2 ? 'version='.$this->ENV->_get('version') : ''));
				}
			}


		$LANG = $this->LANG;		

		$optionhash = array(1,6);
		$suggest = 1;
		$rowclass = $checked = $update='';

		if($update=$this->update_lookup())
			{
			$optionhash[]=2;
			$suggest=2;
			$update_to=$this->update_to;
			$update_from=$this->update_from;
			$update_id=$this->update_id;
			}
		
		if($this->SQL->connect())
			{
			$suggest = 0;
			$optionhash[]=0;
			$optionhash[]=5;
			$optionhash[]=7;
			$optionhash[]=8;
			$optionhash[]=9;
			}
		
		$this->title	= pkGetLang('setup_options_title');
		$this->body		= pkGetLang('setup_options_description');
		$this->action	= pkLink('options','',($update ? 'version='.$update_id : ''));
		
		sort($optionhash);
		foreach($optionhash as $i)
			{
			$rowclass = pkRowClass($rowclass);

			$optionindex = $i;			
			$option = $this->optionhash[$i]['key'];
			$checked = $i==$suggest ? ' checked="checked"' : '';

			$lang_name = pkGetSpecialLang('setup_option_'.$option.'_name',($i==2 && $update ? $update_to : ''));
			$lang_description = pkGetSpecialLang('setup_option_'.$option.'_description',($i==2 && $update ? pkPHPKIT_VERSION : ''));

			eval("\$this->body.=\"".pkTpl("options")."\";");
			}
		}
	
	function systemcheck()
		{
		if($this->ENV->_get_action('button'))
			return $this->proceed('systemcheck');
		
		
		pkLoadFunc('file');
		pkLoadFunc('system');
		pkLoadLang('system');
		$check=true;
		$check_bit='';
		$LANG = &$this->LANG;		

		$this->title=pkGetLang('setup_systemcheck_title');
		$this->body=pkGetLang('setup_systemcheck_content');
		
		#PHPVERSION
		$available=phpversion();
		$check_bit.=$this->systemcheck_bit(pkSystemcheckPhpversion(),pkGetLang('phpversion'),pkGetLang('system_phpversion_required'),$available);

		if(!pkSystemcheckPhpversion())
			$check=false;

		#PHP SAFEMODE
		$available=pkGetLang(ini_get('safe_mode') ? 'enabled' : 'disabled');
		$check_bit.=$this->systemcheck_bit(pkSystemcheckSafemode(),pkGetLang('safemode'),pkGetLang('system_safemode_required'),$available);

		if(!pkSystemcheckSafemode())
			$check=false;

		#PHP FILE UPLOAD
		$available=pkGetLang(ini_get('file_uploads') ? 'enabled' : 'disabled');
		$check_bit.=$this->systemcheck_bit(pkSystemcheckFileuploads(),pkGetLang('php_fileuploads'),pkGetLang('system_fileupload_required'),$available);

		if(!pkSystemcheckFileuploads())
			$check=false;

		#PHP EXTENSION GD
		$available=pkGetLang(pkSystemcheckExtension('gd') ? 'available' : 'not_available');
		$check_bit.=$this->systemcheck_bit(pkSystemcheckExtension('gd'),pkGetLang('php_extension_gdlib').pkGetLang('note_optional'),pkGetLang('available'),$available);

		
		if(ini_get('file_uploads'))
			{
			$dirrep=false;
			foreach(pkSystemWriteableDirectories() as $dirname=>$dir)
				{
				if($dirname!='pkDIRREP' && substr($dir,0,strlen(pkDIRREP))==pkDIRREP && !$dirrep)
					continue;				
				
				if(!pkMkDir($dir,pkCHMODDIR_WRITE) && !is_dir($dir))
					{
					$check=false;
					$available=pkGetLang('dir_does_not_exists');
					$dircheck=false;
					}			
				elseif(!is_writable($dir))
					{
					$check=false;
					$available=pkGetLang('fail');
					$dircheck=false;
					}
				else
					{
					$available=pkgetLang('set');
					$dircheck=true;
					}
				
				if($dirname=='pkDIRREP')
					$dirrep=$dircheck;
	
				$check_bit.=$this->systemcheck_bit($dircheck,pkGetSpecialLang('write_permissions_dirs',str_replace(pkDIRROOT,'',$dir)),pkGetLang('set'),$available);
				}
			}
		else
			{
			
			}


		if(!$check)
			{
			$this->button_form=pkGetLang('bl_check_again');
			$this->action=pkLink('systemcheck',$this->mode);
			}
		else
			$this->action=pkLink('database',$this->mode);
		
		
		$check=pkGetLang($check ? 'setup_systemcheck_success' : 'setup_systemcheck_failed');
		$lang_required=pkGetLang('required');
		$lang_available=pkGetLang('available');
		
		eval("\$this->body.=\"".pkTpl("systemcheck")."\";");
		}
		
	function systemcheck_bit($success,$checkedname,$required,$available)
		{
		static $row;

		$LANG = &$this->LANG;		
		$row=pkRowClass($row);
		
		$checked=$success ? 'true' : 'false';
		$image=$this->fxdir.'/'.($success ? 'true' : 'false').'.gif';
		
		eval("\$bit=\"".pkTpl("systemcheck_bit")."\";");		
		return $bit;
		}
		
	function database()
		{
		$ENV	= &$this->ENV;
		$SQL	= &$this->SQL;
		$LANG	= &$this->LANG;
		
		$version=$ENV->_get('version');
		$error=$ENV->_get_id('error');
		$dataerror=array();
		$advanced=$ENV->_get_id('advanced') ? 1 : 0;
		$sqlhost='localhost';
		$sqluser='';
		$database='';
		$sqlpass='';
		$sqlprefix='pk_';
		$sqlprefix_advanced='pk_';
		
		
		if($ENV->_post_action('button'))
			{
			$advanced	= $ENV->_post_ibool('advanced');
			$sqlhost	= $ENV->_post('sqlhost');
			$sqluser	= $ENV->_post('sqluser');
			$sqlpass	= $ENV->_post('sqlpass');

			$database	= $ENV->_post('database');
			$dbcreate	= $ENV->_post_id('dbcreate');
			#max 30chars
			$sqlprefix	= $ENV->_post('sqlprefix');
			$sqlprefix	= $SQL->isConventionalName($sqlprefix,1) ? $sqlprefix : 'phpkit';
			
			$sqltables	= $ENV->_post('sqltables');
			
			
			#first try to create the sqlfile
			if(!empty($dataerror))
				{
				$error=1;				#something ist wrong with the posted datastrings
				}
			else
				{
				$hash = array();
				
				foreach(pkCfgData('sqltables') as $const=>$suffix)
					{
					$prefix = $sqlprefix;
					
					if($advanced && isset($sqltables[$const]) && $SQL->isConventionalName($sqltables[$const],1))
						{
						$prefix = $sqltables[$const];
						}					

					$hash[$const] = $prefix.'_'.$suffix;
					}
				
				$sqltables = $hash;
				unset($hash);

				
				if(!$this->create_sql_file($database, $sqlhost, $sqluser, $sqlpass, $sqlprefix, $sqltables))
					{
					#file not writeable
					pkHeaderLocation('database',$this->mode,'error=2&advanced='.$advanced.'&dbcreate=1&version='.pkEntities($version));					
					}

					
				$SQL->set($database,$sqlhost,$sqluser,$sqlpass);
				$SQL->connect();

				#no connection					
				if(!$SQL->connected())
					{
					pkHeaderLocation('database',$this->mode,'error=1&advanced='.$advanced.'&dbcreate='.intval($dbcreate).'&version='.pkEntities($version));
					}

				#connected but no DB
				if($SQL->connected() && !$SQL->dbselected() && !$dbcreate)
					{
					pkHeaderLocation('database',$this->mode,'error=3&advanced='.$advanced.'&dbcreate=0&version='.pkEntities($version));
					}

				#try to create
				if($dbcreate && !$SQL->dbselected() && $SQL->isConventionalName($database))
					{
					#@TODO: Revise. The Charset should be defined here.
					if(!$SQL->query("CREATE DATABASE `".$database."` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci"))
						{
						pkHeaderLocation('database',$this->mode,'error=5&advanced='.$advanced.'&dbcreate=1&version='.pkEntities($version));
						}
					#else not need - database successful created
				
					$SQL->select_db();
					}
					
				if($SQL->connected() && $SQL->dbselected()) #yes we do have a valid db-connection				
					{
					if($this->mode=='dbdata')
						{
						pkHeaderLocation('options');
						}
					
					pkHeaderLocation('databasecreate',$this->mode,'version='.pkEntities($version));
					}					

				pkHeaderLocation('database',$this->mode,'error=4&advanced='.$advanced.'&dbcreate='.intval($dbcreate).'&version='.pkEntities($version));
				}			
			}
	
		
		$this->title=pkGetLang('setup_databasesettings');
		$this->action=pkLink('database',$this->mode,'version='.pkEntities($version));
		$this->faction='post';

		$link_advanced_mode	= 'javascript:pkHideShow(1);';
		$link_simple_mode	= 'javascript:pkHideShow(0);';
		
		switch($error)
			{
			case 1:
				$lang_status = pkGetLang('setup_no_db_connection');
				break;
			case 2:
				$lang_status = pkGetLang('setup_data_could_not_saved');
				break;
			case 3:
				$lang_status = pkGetLang('setup_check_your_settings');
				break;
			case 4:
				$lang_status = pkGetLang('setup_db_not_created');
				break;
			case 5:
				$lang_status = pkGetLang('setup_database_could_not_create');
				break;
			default:
				$lang_status = pkGetLang('setup_databasesettings_description');
				break;
			}
		
		$lang_setup_database_host=pkGetLang('setup_database_host');
		$lang_setup_database_host_description=pkGetLang('setup_database_host_description');
		$lang_setup_database_user=pkGetLang('setup_database_user');
		$lang_setup_database_user_description=pkGetLang('setup_database_user_description');
		$lang_setup_database_password=pkGetLang('setup_database_password');
		$lang_setup_database_password_description=pkGetLang('setup_database_password_description');
		$lang_setup_database_name=pkGetLang('setup_database_name');
		$lang_setup_database_name_description=pkGetLang('setup_database_name_description');
		$lang_tablenames=pkGetLang('tablenames');
		$lang_setup_databasesettings_advanced_description=pkGetLang('setup_databasesettings_advanced_description');
		$lang_alias=pkGetLang('alias');
		$lang_prefix=pkGetLang('prefix');
		$lang_suffix=pkGetLang('suffix');
		$lang_setup_database_tableprefix=pkGetLang('setup_database_tableprefix');
		$lang_setup_database_tableprefix_description=pkGetLang('setup_database_tableprefix_description');
		$lang_setup_database_createdb=pkGetLang('setup_database_createdb');
		$lang_setup_database_createdb_description=pkGetLang('setup_database_createdb_description');
		$lang_advanced_mode=pkgetLang('advanced_mode');
		$lang_simple_mode=pkGetLang('simple_mode');
		
		$checkeddbcreate=$ENV->_get_id('dbcreate') ? ' checked="checked"' : '';
		$rowclass=$database_settings_advanced=$database_settings_advanced_own='';

		$database=pkEntities(defined('pkSQLDATABASE') ? pkSQLDATABASE : $database);
		$sqlhost=pkEntities(defined('pkSQLHOST') ? pkSQLHOST : $sqlhost);
		$sqluser=pkEntities(defined('pkSQLUSER') ? pkSQLUSER : $sqluser);
		$sqlpass=pkEntities(defined('pkSQLPASS') ? pkSQLPASS : $sqlpass);

		$sqlprefix=defined('pkSQLPREFIX') ? pkSQLPREFIX : $sqlprefix;
		$sqlprefix_advanced=empty($sqlprefix) ? $sqlprefix_advanced : $sqlprefix;
		$sqlprefix=pkEntities($sqlprefix);
		

		foreach(pkCfgData('sqltables') as $prefix_name=>$suffix)
			{
			$rowclass=pkRowclass($rowclass);

			$suffix='_'.$suffix;
			$prefix=pkEntities(defined($prefix_name) ? str_replace($suffix,'',constant($prefix_name)) : $sqlprefix_advanced);

			eval("\$database_settings_advanced".($prefix==$sqlprefix_advanced ? '' : '_own').".=\"".pkTpl("database_settings_advanced")."\";");			
			}

		eval("\$this->body=\"".pkTpl("database_settings")."\";");
		}
		
	function databasecreate()
		{
		#@TODO: add charset to database creation
		$LANG	= &$this->LANG;
				
		$version=$this->ENV->_get('version');
		$this->title=pkGetLang('setup_database_create');
		
		if(!$this->SQL->connect())
			return $this->nodbcon();
	
		$this->body=pkGetLang('setup_database_create_description');

		pkLoadClass($SU,'sqlutilities');
		$SU->setDefinition(include(pkDIRSETUP).'sqltabdef'.pkEXT);
		$SU->checkTables();
		
		$row='';
	
		foreach($SU->getMessages() as $msg)
			{
			$this->body.='<div class="'.($row=pkRowClass($row)).'">'.$msg.'</div>';
			}


		$this->action=($this->mode=='update') ? pkLink('update','','version='.pkEntities($version).'&from=databasecreate') : pkLink('createadmin',$this->mode);
		}

	function createadmin()
		{
		pkLoadFunc('user');
		$LANG	= &$this->LANG;		
		
		$error=array();
		$username=$useremail='';		
		
		
		$this->title=pkGetLang($this->mode=='recoveradmin' ? 'setup_recover_adminaccount' : 'setup_create_adminaccount');
		
		if(!$this->SQL->connect())
			return $this->nodbcon();
		
		list($userid,$admin_username,$admin_useremail)=$this->SQL->fetch_row($this->SQL->query("SELECT
			user_id,
			user_name,
			user_email
			FROM ".pkSQLTAB_USER."
			WHERE user_id=1
			LIMIT 1"));

		
		if($this->ENV->requestmethod('post'))
			{
			if($userid && $this->ENV->_post_action('button_form')==pkGetLang('bl_skip'))
				{
				if($this->mode=='recoveradmin')
					{
					pkHeaderLocation('options');
					}
				
				pkHeaderLocation('sitesetting',$this->mode);
				}

			$username=$this->ENV->_post('adminName');
			$password=$this->ENV->_post('adminPass');
			$password_repeat=$this->ENV->_post('adminPasscheck');
			$useremail=$this->ENV->_post('adminEmail');
			
			if(!empty($password) && $password===$password_repeat)		
				{
				$password=pkUserEncodePassword($password);
				}
			else
				{				
				$error[]=empty($password) ? 1 : 2; 	#1-empty 2-not equal
				$password=NULL;
				}

			if(!empty($username) && strlen($username)>=3 && strlen($username)<=60)
				{
				list($c)=$this->SQL->fetch_row($this->SQL->query("SELECT 
					COUNT(user_name)
					FROM ".pkSQLTAB_USER."
					WHERE user_name='".$this->SQL->f($username)."'
						".($userid ? " AND user_id<>1" : '')."
					LIMIT 1"));
				
				if($c)
					$error[]=3;		#username already in use
				}
			else
				{
				$error[]=empty($username) ? 4 : 5; #4empty 5#wrong length
				}
			
			
			if(!empty($useremail))
				{
				if(!pkCheckEmailaddress($useremail))
					$error[]=6;			#invalid emailaddress
				}
			else
				{
				$error[]=7;#empty
				}

			if(empty($error))		#everything ist okay
				{
				$result=$this->SQL->query(
					($userid ? "UPDATE" : "INSERT INTO")." ".
					pkSQLTAB_USER." SET
					user_name='".$this->SQL->f($username)."',
					user_pw='".$this->SQL->f($password)."',
					user_status='admin',
					user_email='".$this->SQL->f($useremail)."'".
					($userid ? " WHERE user_id='".$userid."'" : ",user_nick='".$this->SQL->f($username)."',signin='".pkTIME."',user_id=1")
					);
				
				if($result)
					{
					if($this->mode=='recoveradmin')
						pkHeaderLocation('options');
						
					pkHeaderLocation('sitesetting',$this->mode);
					}
				
				$error[]=8;
				}
			}


		if($userid)
			{
			$this->body=pkGetSpecialLang('setup_recover_adminaccount_description',pkEntities($admin_username.' ('.$admin_useremail.')'));

			$this->button=pkGetLang('bl_change_accountdats');			
			$this->button_form=pkGetLang('bl_skip');
			}		
		else
			{
			$this->body=pkGetLang('setup_create_adminaccount_description');
					
			$this->button=pkGetLang('bl_create');
			}
			
		if(!empty($error))
			{
			$errors='';
			$errorhash=array(
				1=>'setup_admin_error_password_empty',
				2=>'setup_admin_error_passwords_not_equal',
				3=>'setup_admin_error_username_in_use',
				4=>'setup_admin_error_empty_username',
				5=>'setup_admin_error_wrong_legth_username',
				6=>'setup_admin_error_email_invalid',
				7=>'setup_admin_error_email_empty',
				8=>'setup_admin_error_data_not_saved'		
				);
	

			foreach($error as $i)
				$errors.=pkGetLang($errorhash[$i]);
			
			if(!empty($errors))
				$this->body.=pkGetSpecialLang('setup_admin_error_occured',$errors);
			}


		$this->faction='post';
		$this->action=pkLink('createadmin',$this->mode);
		
		$lang_username=pkGetLang('username');
		$lang_enter_your_loginname=pkGetLang('enter_your_loginname');
		$lang_password=pkGetLang('password');
		$lang_enter_your_password=pkGetLang('enter_your_password');		
		$lang_repeat=pkGetLang('password_repeat');	
		$lang_repeat_password=pkGetLang('repeat_your_password');
		$lang_email=pkGetLang('email_address');
		$lang_enter_your_emailaddress=pkGetLang('enter_your_emailaddress');
		
		$adminName=$userid && empty($username) ? pkEntities($admin_username) : pkEntities($username);
		$adminEmail=$userid && empty($useremail) ? pkEntities($admin_useremail) : pkEntities($useremail);
		$adminPass=$adminPasscheck='';
		
		eval("\$this->body.=\"".pkTpl("createadmin")."\";");		
		}

	function sitesetting()
		{
		$SQL	= &$this->SQL;
		$ENV	= &$this->ENV;
		$LANG	= &$this->LANG;
				
		$this->title = pkGetLang('setup_sitesetting');
		
		if(!$SQL->connect())
			return $this->nodbcon();		
		
		if($ENV->_post_action('button'))
			{
			$url	= $ENV->_post('site_url');
			$urls	= $ENV->_post('site_urls');
			$name	= $ENV->_post('site_name');
			$title	= $ENV->_post('site_title');
			$email	= $ENV->_post('site_email');
			
			while(substr($url,-1,1)=='/') #remove ending slashes from the url
				{
				$url = substr($url,0,-1);
				}
			
			$SQL->query("REPLACE INTO ". pkSQLTAB_CONFIG." (id,value) VALUES
				('site_name', '".$SQL->f(serialize($name))."'),
				('site_title', '".$SQL->f(serialize($title))."'),
				('site_slogan', '".$SQL->f(serialize($title))."'),
				('site_url', '".$SQL->f(serialize($url))."'),
				('site_urls', '".$SQL->f(serialize($urls))."'),
				('site_email' ,'".$SQL->f(serialize($email))."')");
			
			if(empty($name) || empty($title) || empty($urls) || empty($email) || empty($url))
				{
				pkHeaderLocation('sitesetting',$this->mode,'error=1');
				}
				
			pkHeaderLocation('preset',$this->mode);
			}

		$error = $ENV->_get('error') ? 1 : 0;


		$query = $SQL->query("SELECT id, value FROM ".pkSQLTAB_CONFIG." WHERE id IN ('site_url','site_urls','site_name','site_title','site_email')");
		while(list($key,$value) = $SQL->fetch_row($query))
			{
			$$key = @unserialize($value);
			}
		
		
		$urls = getenv('HTTP_HOST');			
		$url = 'http://'.$urls.dirname(getenv('REQUEST_URI'));
		
		$site_url=pkEntities(empty($site_url) || !pkUrlCheck($site_url) ? $url : $site_url);
		$site_urls=pkEntities(empty($site_urls) ? $urls : $site_urls);
		$site_name=pkEntities($site_name);
		$site_title=pkEntities($site_title);
		$site_email=pkEntities($site_email);

		$this->action=pkLink('sitesetting',$this->mode);
		$this->faction='post';
		$this->body=pkGetLang('setup_sitesetting_description');
		
		if($error)
			{
			$this->body.=pkGetLang('setup_sitesetting_error');
			}

		$lang_website_url=pkGetLang('website_url');
		$lang_website_url_description=pkGetLang('website_url_description');
		$lang_website_url_alias=pkGetLang('website_url_alias');
		$lang_website_url_alias_description=pkGetLang('website_url_alias_description');		
		$lang_sitename=pkGetLang('sitename');
		$lang_sitename_description=pkGetLang('sitename_description');
		$lang_sitetitle=pkGetLang('sitetitle');
		$lang_sitetitle_description=pkGetLang('sitetitle_description');
		$lang_email_address=pkGetLang('email_address');
		$lang_siteemail_description=pkGetLang('siteemail_description');
		

		eval("\$this->body.=\"".pkTpl("sitesetting")."\";");
		}

	function preset()
		{
		$SQL	= &$this->SQL;
		$LANG	= &$this->LANG;		
		
		
		if(!$SQL->connect())
			{
			return $this->nodbcon();
			}
				
		if($this->ENV->_post_action('button'))
			{
			$this->preset_config(); #fill the configuration
			$this->style_import(); #import default designs

			#save the version number and/or new styleid
			$SQL->query("REPLACE INTO ".pkSQLTAB_CONFIG." (id,value) VALUES
				('version_number','".$SQL->f(serialize(pkPHPKIT_VERSION))."')".
				($this->style_standardid ? ",('site_style','".$SQL->f(serialize(intval($this->style_standardid)))."')" : '')
				);

			#adminnavigation
			$this->preset_adminnavigation();
	 
				
			if(!$this->ENV->_post_ibool('preset'))
				{
				pkHeaderLocation('finish',$this->mode);
				}

			#write presets
			include(pkDIRSETUP.'preset'.pkEXT);

			pkHeaderLocation('finish',$this->mode);
			}
		
		
		$this->title=pkGetLang('setup_choose_pre_entries');
		$this->body=pkGetLang('setup_choose_pre_entries_description');
		$this->faction='post';
		$this->action=pkLink('preset',$this->mode);
		
		$dbtables = array(
			pkSQLTAB_NAVIGATION,
			pkSQLTAB_NAVIGATION_CATEGORY,
			pkSQLTAB_SMILIES,
			pkSQLTAB_FORUM_RANK,
			pkSQLTAB_CONTENT,
			pkSQLTAB_CONTENT_CATEGORY,
			pkSQLTAB_POLL_COUNT,
			pkSQLTAB_POLL_TOPIC,
			pkSQLTAB_POLL,
			pkSQLTAB_GUESTBOOK,
			pkSQLTAB_FAQ,
			pkSQLTAB_FAQ_CATEGORY,
			pkSQLTAB_FORUM_CATEGORY,
			pkSQLTAB_FORUM_FAVORITE,
			pkSQLTAB_FORUM_INFO,
			pkSQLTAB_FORUM_POST,
			pkSQLTAB_FORUM_NOTIFY,
			pkSQLTAB_FORUM_THREAD
			);
		
		$dbempty=true;
		
		foreach($dbtables as $table)
			{
			list($c)=$SQL->fetch_row($SQL->query("SELECT COUNT(*) FROM ".$table));
			if(!$c)
				continue;
			
			$dbempty=false;
			break;
			}				

		$checked0=$checked1=$presetconfirm='';
		
		if($dbempty)
			$checked1=' checked="checked"';
		else
			{
			$checked0=' checked="checked"';
			$this->body.=pkGetLang('setup_preset_warning');
			$presetconfirm=' onclick="pkPresetConfirm();"';
			}
		
		
		$lang_preset_confirm=pkGetLang('setup_preset_confirm');
		$lang_preset_insert=pkGetLang('setup_preset_insert');
		$lang_preset_none=pkGetLang('setup_preset_none');
		
		eval("\$this->body.=\"".pkTpl("preset")."\";");
		}
	
	
	function preset_adminnavigation()
		{
		$S = $this->SQL;
		$keys = array('id','pid','sorting','lkey','lscope','target','lnkpath','lnkmode','lnkadd','permission');
		$values = array();
		
		foreach(pkCfgData('adminnavigation') as $value)
			{
			$array = array();

			foreach($keys as $k)
				{
				$array[] = isset($value[$k]) ? $S->f($value[$k]) : '';
				}
				
			$values[] = "('".implode("','",$array)."')";
			}
		
		$S->query("REPLACE INTO ".pkSQLTAB_ADMIN_MENU." (".implode(",",$keys).") VALUES ".implode(', ',$values));
		}
		
	
	function preset_config()
		{
		$S = $this->SQL;
		
		#insert defaults / presets
		$values = array();
		$config = pkCfgData('config'); 
	
		$query = $S->query("SELECT id, value FROM ".pkSQLTAB_CONFIG);
		while($item = $S->fetch_assoc($query))
			{
			if(isset($item['id']) && isset($item['value']) && isset($config[$item['id']]))
				{
				#remove already existing config values from the config preset array
				unset($config[$item['id']]);
				}
			}

		if(!empty($config))
			{
			foreach($config as $key=>$value)
				{
				$values[] = "'".$S->f($key)."','".$S->f(serialize($value))."'";
				}
			
			$S->query("REPLACE INTO ".pkSQLTAB_CONFIG." (id,value) VALUES (".implode('), (',$values).")");
			}
		#END defaults / presets
		
		$this->preset_config_groups();
		}
	#@END Method: preset_config
	
	function preset_config_groups()
		{
		$S = $this->SQL;
		#create config groups
		$values = array();
		$groups = pkCfgData('config-group'); #defaults / presets
		
		foreach($groups as $item)
			{
			$values[] = "'".$S->f($item['id'])."','".$S->f($item['sorting'])."','".$S->f($item['lkey'])."','".$S->f($item['lscope'])."'";
			}

		$S->query("REPLACE INTO ".pkSQLTAB_CONFIG_GROUP." (id,sorting,lkey,lscope) VALUES (".implode('),(',$values).")");			
		#END config groups		
		}
		
		
	function finish()
		{
		$this->title=pkGetLang('setup_finish_installation');
		$this->body=pkGetLang('setup_finish_installation_message');
		$this->button=pkGetLang('bl_finalise');
		$this->action=pkLink('outofhere');
		}
		
	function dbcheck()
		{
		$LANG	= &$this->LANG;
		$this->title = pkGetLang('setup_databasecheck');

		
		if(!$this->SQL->connect())
			return $this->nodbcon();		

		pkLoadClass($SU,'sqlutilities');
		$SU->setDefinition(include(pkDIRSETUP.'sqltabdef'.pkEXT));
		$SU->setMode(1);
		$SU->checkTables();
		
		$errorcount=$SU->getErrorCount();
		$errorfixed=$SU->getErrorFixed();
		$skippedcols=$SU->getSkippedCols();
		$skippedkeys=$SU->getSkippedKeys();

		$this->button=pkGetLang('bl_finalise');
		$this->action=pkLink('options');
		$this->body=pkGetSpecialLang('setup_databasecheck_smallsummary',$errorcount,$errorfixed,$skippedcols,$skippedkeys);
		
		$row='';
	
		foreach($SU->getMessages() as $msg)
			{
			$this->body.='<div class="'.($row=pkRowClass($row)).'">'.$msg.'</div>';
			}
		}
		
	function dbcleanup()
		{
		$LANG	= &$this->LANG;		
		$this->title=pkGetLang('setup_databasecleanup');
		
		if(!$this->SQL->connect())
			return $this->nodbcon();

		$row='';
		
		$table=$this->ENV->_get('table');
		if($table && array_key_exists($table,pkCfgData('sqltables')))
			{
			pkLoadClass($SU,'sqlutilities');
			$SU->setDefinition(include(pkDIRSETUP.'sqltabdef'.pkEXT));
			$SU->cleanTable($table);
			
			foreach($SU->getMessages() as $msg)
				{
				$this->body.='<div class="'.($row=pkRowClass($row)).'">'.$msg.'</div>';
				}
			$this->body.='<div class="small">'.pkGetSpecialLang('setup_setup_databasecleanup_done_in',pkParserTime()).'</div>';

			$this->button=pkGetLang('bl_back');
			$this->action=pkLink('dbcleanup',$this->mode);
			return;
			}

		$this->button=pkGetLang('bl_finalise');
		$this->action=pkLink('options');			
		$this->body=pkGetLang('setup_databasecleanup_description');
		
		foreach(pkCfgData('sqltables') as $alias=>$nomatter)
			{
			$this->body.='<div class="'.($row=pkRowClass($row)).'"><a href="'.pkLink('dbcleanup',$this->mode,'table='.$alias).'">'.constant($alias).'</a></div>';
			}			
		}
	
	function update()
		{
		$LANG	= &$this->LANG;
				
		$version=$this->ENV->_get('version');
		$step=$this->ENV->_get_id('step');
		
		if(empty($version) && !$this->update_exists($version))
			{
			#not a valid update-versionnumber
			$this->title=pkGetLang('setup_invalid_update');
			$this->body=pkGetLang('setup_invalid_update');
			$this->action=pkLink('options');
			$this->button=pkGetLang('bl_back');
						
			return;
			}
		

		$this->mode='update';

		include($this->updatefile($version));
		}
		
	function uninstall()
		{
		$LANG	= &$this->LANG;
				
		$this->title=pkGetLang('setup_uninstall');
		
		if(!$this->SQL->connect())
			return $this->nodbcon();	

		if($this->ENV->_post_action('button_form')==pkGetLang('bl_cancel') || ($this->ENV->_post_action('button')==pkGetLang('bl_uninstall') && $this->ENV->_post('uninstall')!='yes'))
			{
			pkHeaderLocation('options');
			}
		
		if($this->ENV->_post_action('button')==pkGetLang('bl_uninstall') || $this->ENV->_post('uninstall')=='yes')
			{
			pkLoadFunc('file');
			pkLoadFunc('system');
			
			foreach(pkSystemWriteableDirectories() as $dirname=>$dir)
				pkRmDir($dir);
			
			$sql='';

			foreach(pkSystemSqltables() as $constant=>$suffix)
				{
				if(!defined($constant))
					continue;
				
				$sql.=(empty($sql) ? '' : ',').constant($constant);
				}
			
			if(!empty($sql))
				$this->SQL->query("DROP TABLE ".$sql);
			
			
			pkHeaderLocation('uninstalled');
			}
		
		$this->body=pkGetLang('setup_uninstall_description');
		
		$this->button=pkGetLang('bl_uninstall');
		$this->button_form=pkGetLang('bl_cancel');
		$this->action=pkLink('uninstall');
		$this->faction='post';		

		$lang_setup_uninstall_confirm=pkGetLang('setup_uninstall_confirm');
		
		eval("\$this->body.=\"".pkTpl("uninstall")."\";");		
		}
		
	function uninstalled()
		{
		$LANG	= &$this->LANG;
		
				
		if($this->ENV->_get('button')==pkGetLang('bl_exit'))
			{
			header('Location: http://www.phpkit.com');
			exit;
			}
		
		$this->title=pkGetLang('setup_uninstalled');
		$this->body=pkGetLang('setup_uninstalled_description');
		$this->action=pkLink('uninstalled');
		$this->button=pkGetLang('bl_exit');				
		}
	
		
	function outofhere()
		{
		unset($_SESSION[$this->setup_session_key]);
		
		header('Location: '.pkDIRWWWROOT.pkSITE.pkEXT);
		exit;
		}

	
#none path/mode-methodes
	function nodbcon()
		{
		$this->body=pkGetLang('setup_no_database_connection');
		$this->button=pkGetLang('bl_back');
		$this->action=pkLink('database',$this->mode,'error=3');		
		}
		
	function update_lookup()
		{
		$SQL = &$this->SQL;

		$version = NULL;

		#try to find out which mode we have to use
		#first we try the DB-Access
		if(defined('pkSQLTAB_CONFIG') && $SQL->connect())
			{
			$version = '';
			
			$query = $SQL->query("SELECT * FROM ".pkSQLTAB_CONFIG);
			while($array = $SQL->fetch_assoc($query))
				{
				if(isset($array['version_number']))#old single row format
					{
					$version = $array['version_number'];
					}
				elseif(isset($array['id']) && $array['id']=='version_number') #new from 1.6.5 id=>value format
					{
					$version = @unserialize($array['value']);
					}
				else
					{
					continue;
					}
				
				break;
				}

			
			if($version==pkPHPKIT_VERSION) #identical versions
				{
				return false;
				}
			else
				{
				#differing versionnumbers
				#do we have an updatefile 
				if($this->update_exists($version))
					{			
					#does the update match out current situation?
					if($this->update_check($version)) #fine - there is an update for our version
						{
						return $version;
						}
					}
				}

			return false;			
			}

		#we dont have a database connection or our version number wasnt saved - check the update-files
		$updates=$this->updates();
			
		foreach($updates as $version)
			{
			if($this->update_check($version))
				{
				break;
				}

			$version = NULL;
			}
			
		return $version==NULL ? false : $version;
		}
#END methode update_lookup


	function update_exists($version)
		{
		return pkFileCheck($this->updatefile($version));
		}
#END methode update_exists

	
	#@Method:	update_check
	#@Access:	private
	#@Param:	string version
	#@Return:	bool
	#@Desc:
	private function update_check($version)
		{
		$ischeck = true;
		
		if(!$this->update_exists($version))
			{
			return false;
			}
		
		$check = include($this->updatefile($version));
		
		if(!$check)
			{
			return false;
			}

		$this->update_from	= $update_from;
		$this->update_to	= $update_to;
		$this->update_id	= $update_id;

		return true;
		}
	#@END Method update_check
	
	function updatefile($version)
		{
		#remove pl's
		$str = substr(trim($version),0,5);
		$str = str_replace('.','',str_replace(' ','',$str));
		$str = sprintf($this->updatefilepattern,$str);

		return $str;
		}
		
	function updates()
		{
		if(empty($this->updates))
			{
			include(pkDIRSETUP.'update'.pkEXT);
			}
		
		return $this->updates;
		}

	#@Method:		style_import
	#@Access:
	#@Param:		void
	#@Return:		void
	#@Desc:
	function style_import()
		{
		$this->style_standardid=0;
		
		pkLoadClass($STYLE,'style');
		pkLoadFunc('file');
		
		$styles = pkReadDir($this->styledir);
		
		foreach($styles as $name=>$is_dir)
			{
			if($is_dir || substr($name,-8)!='pkxstyle')
				{
				continue;
				}
			
			$style = pkFileContent($this->styledir.$name,true);
			$importedid = $STYLE->import($style);
		
			if($importedid && $STYLE->getLastImportName()==$this->style_standardname)
				{
				$this->style_standardid = $importedid;
				}
			}
		}
	#@END Method:	style_import
	
	
	#@Method:	create_sql_file
	#@Access:	private
	#@Param:	string database
	#@Param:	string sqlhost
	#@Param:	string sqluser
	#@Param:	string sqlpass
	#@Param:	string sqlprefix
	#@Param:	array sqltables
	#@Return:	bool
	#@Desc:		Returns TRUE when the sql file qas created.
	private function create_sql_file($database, $sqlhost, $sqluser, $sqlpass, $sqlprefix, $sqltables)
		{
		$sqlhost	= empty($sqlhost) ? 'localhost' : $sqlhost;
		$sqluser	= empty($sqluser) ? 'root' : $sqluser;
		$sqlpass	= empty($sqlpass) ? '' : $sqlpass;
		$sqltables	= is_array($sqltables) ? $sqltables : array();
		
		$lines = array(
			"<?php",
			"if(defined('pkPHPKIT_INSTALLED')) return;",
			"",
			"define('pkPHPKIT_INSTALLED',1);",
			"",
			"#connection data",
			"define('pkSQLDATABASE', '".addslashes($database)."');",
			"define('pkSQLHOST', '".addslashes($sqlhost)."');",
			"define('pkSQLUSER', '".addslashes($sqluser)."');",
			"define('pkSQLPASS', '".addslashes($sqlpass)."');",
			"define('pkSQLPREFIX', '".addslashes($sqlprefix)."');",
			"",
			"#table aliases"
			);#lines = array
		
		$hash = pkCfgData('sqltables');
		foreach($hash as $const=>$alias)
			{
			$alias_local = isset($sqltables[$const]) ? $sqltables[$const] : $sqlprefix.'_'.$alias;

			$lines[] = "define('".$const."', '".$alias_local."');";
			}
		
		$lines[] = '?>';
		
		#create the file content
		$content = trim(implode("\r\n",$lines));
		if($handle = @fopen(pkFILESQL,'w'))
			{
			fwrite($handle,$content);
			fclose($handle);
			
			return true;
			}
		
		return false;
		}
	#@END Method create_sql_file
	}
#@END Class:	pkSetup
?>