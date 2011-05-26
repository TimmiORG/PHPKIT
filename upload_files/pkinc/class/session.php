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


class pkSession
	{
	var $started=false;
	var $USER='USER';
	
	var $ENV;
	var $SQL;
	
	var $logmode=NULL;
	var $adminsession=0;	
	var $publicsession=0;	
	
	var $sessionid=NULL;
	var $sessionuserid=0;

	var $ip='';
	var $browser='';
	var $lang='';
	var $isbot=0;
	
	var $db_result='';
	
	var $login_error=array();
	var $log_userid=0;
	var $log_username='';
	var $log_userpass='';
	var $log_setcookie=0;
	var $log_uid='';				
	var $userdata=array();
	
	var $botmatches = array(
		'googlebot', 'yahoo! slurp', 'msnbot', 'sensis web crawler'
		);
	
	protected $session_user_vars = array(
			'sip'			=> 'sip',
			'sbrowser'		=> 'sbrowser',
			'slang'			=> 'slang',
			'id'			=> 'user_id',
			'status'		=> 'user_status',
			'name'			=> 'user_name',
			'pass'			=> 'user_pw',
			'nick'			=> 'user_nick',
			'group'			=> 'user_groupid',
			'email'			=> 'user_email',
			'sex'			=> 'user_sex',
			'hpage'			=> 'user_hpage',
			'icqid'			=> 'user_icqid',
			'design'		=> 'user_design',
			'sigoption'		=> 'user_sigoption',
			'imoption'		=> 'user_imoption',
			'user_ghost'	=> 'user_ghost',
			'lastlog'		=> 'lastlog',
			'logtime'		=> 'logtime'
			);
	

	function __construct()
		{
		global $ENV,$SQL;

		$this->ENV=&$ENV;
		$this->SQL=&$SQL;


		if(!@ini_get('session.use_only_cookies'))
			@ini_set('session.use_only_cookies','1');

		if(@ini_get('session.use_trans_sid'))
			pkPHPVERSION<500 ? @ini_set('url_rewriter.tags','') : @ini_set('session.use_trans_sid','0');

		if(@ini_get('session.auto_start'))
			{
			$this->started=true;
			$this->sessionid=session_id();
			}
		else
			{
			session_name(pkPHPKITSID);
		
			#detect session id
			if($this->ENV->_cookie(pkPHPKITSID) && $this->isvalidid($this->ENV->_cookie(pkPHPKITSID)))			#site-cookie
				{
				$this->sessionid=$this->ENV->_cookie(pkPHPKITSID);
				}
			elseif($this->ENV->_request(pkPHPKITSID) && $this->isvalidid($this->ENV->_request(pkPHPKITSID)))	#get/post or php session-cookie
				{
				$this->sessionid=$this->ENV->_request(pkPHPKITSID);
				}
			else #newid
				{
				$this->sessionid=$this->mkid();
				}
			}

				
		#client informations
		$this->ip=$this->ENV->getvar('REMOTE_ADDR');
		$this->browser=$this->ENV->getvar('HTTP_USER_AGENT');
		$this->lang=$this->ENV->getvar('HTTP_ACCEPT_LANGUAGE');
		
		#detect bots and crawlers
		foreach($this->botmatches as $bot)
			{
			if(strpos(strtolower($this->browser),$bot)!==false)
				{
				$this->isbot=1;
				}
			}
		
		#detect logmode
		$this->logmode=$this->detect_logmode();
		}
	
		
	function detect_bugged_cookie()
		{
		$scookie_sid=$this->ENV->_cookie(pkPHPKITSID);
		$cookie_sid=$this->ENV->_request(pkPHPKITSID);
		
		if($this->isvalidid($scookie_sid) && $this->isvalidid($scookie_sid) && $scookie_sid==$cookie_sid)
			{
			return;
			}

		$logtime=$this->ENV->_cookie('logtime');
		$expire=$this->getExpire(0,1,1); # guest sess expire
		
		if($logtime<(pkTIME-$expire))
			{
			$this->ENV->debugcookie();
			}
		}
	

	function detect_logmode()
		{
		foreach(array('logout','firstlog','relog','login') as $i)
			{
			if($this->ENV->_request($i))
				{
				return $i;
				}
			}
		
		return false;
		}
	
		
	function result($key)	
		{
		return isset($this->result[$key]) ? $this->result[$key] : false;
		}
		
		
	function start()
		{
		if($this->started())
			{
			if(!pkDEVMODE)
				return;

			exit('Sessions cant be started twice');
			}
	
		pkDEVMODE ? session_start(): @session_start(); 	#@ prevents an ugly notice when tmp-dir is readable
		$this->sessionid=session_id();		
		
		if(!array_key_exists($this->USER,$_SESSION))
			{
			$_SESSION[$this->USER]=array();
			}
		
		return $this->started=true;
		}
	
	
	function started()
		{
		return $this->started;
		}
	
	
	function getid()
		{
		return session_id();
		}
	
	
	function setid($id='')
		{
		if($this->started() || (!$this->isvalidid($id) && !empty($id)))
			return false;
		
		if(empty($id))
			{
			$id=$this->mkid();
			}
			
		session_id($id);
		$this->sessionid_kill=$this->sessionid;
		$this->sessionid=session_id();

		if($this->sessionid==$id)
			{
			$this->ENV->setCookie(pkPHPKITSID,$this->sessionid);
			return true;
			}
		
		return false;
		}
	

	function isvalidid($id)
		{
		return preg_match("/^([a-z0-9]{32})$/i",$id);
		}
	
		
	function mkid()
		{
		return $this->started() ? $this->sessionid : pkStringRandom(32);	
		}
	
	
	function destroy()
		{
		if(!$this->started())
			return false;
			
		#detect a maybe bugged cookie
		$this->detect_bugged_cookie();			
		
		foreach($_SESSION as $key=>$nomatter)
			{
			if($key===$this->USER)
				{
				$_SESSION[$this->USER]=array();
				}
			else
				{
				unset($_SESSION[$key]);
				}
			}
		
		return true;
		}
	
	
	function registered()
		{
		return isset($_SESSION[$this->USER]) && !empty($_SESSION[$this->USER]);
		}
	

	function set($key,$value='')
		{
		if(!$key)
			{
			return false;
			}
		
		$_SESSION[$key]=$value;
		return isset($_SESSION[$key]);
		}
	

	function exists($var)
		{
		return isset($_SESSION[$var]) ? true : NULL;
		}
	

	function get($var)
		{
		return isset($_SESSION[$var]) ? $_SESSION[$var] : NULL;
		}
	
		
	function deset($key)
		{
		if($key!=$this->USER)
			{
			unset($_SESSION[$key]);
			}
		}
	
		
	function setUservalue($key,$value)
		{
		if(!$key)
			{
			return;
			}

		$user = array_key_exists($this->USER,$_SESSION) ? $_SESSION[$this->USER] : array();
		$user[$key]=$value;
		
		return $this->set($this->USER,$user);
		}
	
		
	function getUservalue($key)
		{
		if(!$key)
			{
			return false;
			}

		return isset($_SESSION[$this->USER][$key]) ? $_SESSION[$this->USER][$key] : NULL;
		}
	
		
	function getExpire($public=1,$thisuser=1,$dif=0)
		{
		$expire=pkGetConfig((($thisuser ? pkGetUservalue('id') : $thisuser) ? ($public ? 'session_expire_user' : 'session_expire_admin')  : 'session_expire_guest'));
		return $dif ? $expire : pkTIME + $expire;
		}
	
		
	function isbot()
		{
		return $this->isbot;
		}
	
		
	function db_cleanup()
		{
		pkMtSrand();
		
		if(mt_rand(1,100)!=69)
			return;
		
		$this->SQL->query("DELETE FROM ".pkSQLTAB_ADMINSESSION." WHERE session_expire<='".pkTIME."'");
		$this->SQL->query("DELETE FROM ".pkSQLTAB_SESSION." WHERE session_expire<='".pkTIME."'");
		}
	

	function db_catch($table=false)
		{
		$S = $this->SQL;

		if(!$table)
			{
			$table=pkFRONTEND=='public' ? pkSQLTAB_SESSION : pkSQLTAB_ADMINSESSION;
			}
		
		$this->db_result=$S->fetch_assoc($S->query("SELECT
				session_id,
				session_userid,
				session_ip,
				session_browser,
				session_lang
			FROM ".$table."
			WHERE session_id='".$S->f($this->sessionid)."' 
				AND session_expire>".pkTIME));
		
		return !empty($this->db_result);
		}
	
		
	function db_catch_lost_session($table=false)
		{
		if(!$this->log_userid)
			return false;
		
		$S = $this->SQL;
				
		if(!$table)
			{
			$table=pkFRONTEND=='public' ? pkSQLTAB_SESSION : pkSQLTAB_ADMINSESSION;
			}
		

		list($sessionid)=$S->fetch_row($S->query("SELECT
				session_id
			FROM ".$table."
			WHERE session_userid='".$S->i($this->log_userid)."'
				AND session_expire>".pkTIME."
				AND session_ip='".$S->f($this->ip)."'
				AND session_browser='".$S->f($this->browser)."'
				AND session_lang='".$S->f($this->lang)."'"));
			
		if(!$this->isvalidid($sessionid))
			{
			return false;
			}
		
		$this->setid($sessionid);
		return true;		
		}
	

	function db_save($table=false)
		{
		$S=&$this->SQL;
		if(!$table)
			$table=pkFRONTEND=='public' ? pkSQLTAB_SESSION : pkSQLTAB_ADMINSESSION;
		
		$S->query("REPLACE ".$table."
			SET session_id='".$S->f($this->getid())."',
				session_expire='".$S->i($this->getExpire($table==pkSQLTAB_SESSION ? 1 : 0))."',
				session_userid='".$S->i($this->getUservalue('id'))."',
				session_ip='".$S->f($this->getUservalue('sip'))."',
				session_browser='".$S->f($this->getUservalue('sbrowser'))."',
				session_lang='".$S->f($this->getUservalue('slang'))."',				
				session_url='".$S->f($this->ENV->getvar('REQUEST_URI'))."'".
				($table==pkSQLTAB_SESSION ? ",session_ghost='".($this->getUservalue('user_ghost') ? 1 : 0)."',session_isbot='".$this->isbot."'" : '')
			);

		if(!$this->getUservalue('id'))
			return true;

		#prevent shared account using whitout a cookie
		$S->query("DELETE FROM ".$table."
			WHERE session_userid=".$S->i($this->getUservalue('id'))."
				AND session_id<>'".$S->f($this->getid())."'");
	
		return true;
		}
	
		
	function db_update($table=false)
		{
		$S=&$this->SQL;
		if(!$table)
			$table=pkFRONTEND=='public' ? pkSQLTAB_SESSION : pkSQLTAB_ADMINSESSION;
		
		$S->query("UPDATE ".$table."
			SET session_expire='".$S->i($this->getExpire($table==pkSQLTAB_SESSION ? 1 : 0))."',
				session_url='".$S->f($this->ENV->getvar('REQUEST_URI'))."'
			WHERE session_id='".$S->f($this->getid())."'");

		if($this->getUservalue('id'))
			{
			$S->query("UPDATE ".pkSQLTAB_USER."
				SET logtime='".pkTIME."'
				WHERE user_id='".$S->f($this->getUservalue('id'))."'
					AND user_name='".$S->f($this->getUservalue('name'))."'
					AND user_pw='".$S->f($this->getUservalue('pass'))."'");
			}
	
		return true;
		}
		
	
	function db_user_update()
		{
		$S=&$this->SQL;
		
		$userdata=$S->fetch_assoc($S->query("SELECT 
				user_id,
				user_status,
				user_groupid,
				user_activate,
				user_ghost		
			FROM ".pkSQLTAB_USER."
			WHERE user_id='".$S->f($this->getUservalue('id'))."'
				AND user_name='".$S->f($this->getUservalue('name'))."'
				AND user_pw='".$S->f($this->getUservalue('pass'))."'"));

		if($userdata['user_id']!=$this->getUservalue('id') || intval($userdata['user_activate'])!==1)
			$this->destroy();
		
		if($userdata['user_status']=='ban')
			return $this->user_banned();

		$vars=array(
			'status'=>'user_status',
			'group'=>'user_groupid',
			'user_ghost'=>'user_ghost'
			);	
	
		foreach($vars as $k=>$v)
			$this->setUservalue($k,$userdata[$v]);
		}
		
	
	function db_result($key)
		{
		return isset($this->db_result[$key]) ? $this->db_result[$key] : false;
		}
	
	
	function setnewid()
		{
		if(!$this->started())
			$this->sessionid=$this->mkid();
		}
	
		
	function validate_client_values()
		{
		return ($this->sessionid==$this->db_result('session_id') && 
			$this->ip==$this->db_result('session_ip') && 
			$this->browser==$this->db_result('session_browser') &&
			$this->lang==$this->db_result('session_lang'));
		}
	

	function validate_cookie_values()
		{
		if(!$this->login_check_data())
			return false;
		
		if(!$this->account_active())
			{
			$this->ENV->setCookie('user_id',0);
			return false;			
			}
			
		if(!$this->db_catch_lost_session())
			$this->setnewid();
		else
			return $this->login_check_data();
		
		return true;
		}
	
		
	function login_clean()
		{
		$this->login_error = array();
		$this->log_username = NULL;
		$this->log_userpass = NULL;
		$this->log_setcookie = NULL;				
		$this->userdata = array();
		}
	
		
	function login_request_data()
		{
		$this->log_username = $this->ENV->_post('user');
		$this->log_userpass = $this->ENV->_post('userpw');
		$this->log_setcookie = $this->ENV->_post('login_setcookie');
		}
	
		
	function firstlogin_request_data()
		{
		$this->log_username = $this->ENV->_request('user');
		$this->log_userpass = md5($this->ENV->_request('userpw'));
		$this->log_setcookie = $this->ENV->_request('login_setcookie');
		$this->log_uid = $this->ENV->_request('uid');
		}
	
		
	function relogin_request_data()
		{
		$this->log_username=$this->ENV->_request('user');
		$this->log_uid=$this->ENV->_request('uid');		
		}
	
		
	function login_check_data()
		{
		if(empty($this->log_username) && empty($this->log_userpass))
			{
			$this->login_error[]=1;
			}
		elseif(empty($this->log_username))
			{
			$this->login_error[]=2;
			}
		elseif(empty($this->log_userpass))
			{
			$this->login_error[]=3;
			}			
		elseif($this->logmode=='login')
			{
			$this->log_userpass=md5($this->log_userpass);
			}

		
		if($this->login_error)
			return false;
		
		$S = $this->SQL;
		
		$userdata = $S->fetch_assoc($S->query("SELECT
				user_id,
				user_status,
				user_name,
				user_nick,
				user_pw,
				user_groupid,
				user_email,
				user_sex,
				user_hpage,
				user_icqid,
				user_design,
				user_sigoption,
				user_imoption,
				user_activate,
				user_ghost,
				lastlog,
				logtime	
			FROM ".pkSQLTAB_USER."
			WHERE user_name='".$S->f($this->log_username)."'
				AND user_pw='".$S->f($this->log_userpass)."'
			LIMIT 1"));
		
		if(!$userdata['user_id'])
			{
			$this->login_error[]=4;
			return false;
			}

		if($userdata['user_status']=='ban')
			{
			return $this->user_banned();
			}

		$this->account_active = $userdata['user_activate']==1 ? true : false;

		if(!$this->account_active)
			{
			return false;
			}
		
		
		$userdata['sip'] = $this->ip;
		$userdata['sbrowser'] = $this->browser;
		$userdata['slang'] = $this->lang;
		
		#check the logtimes - if valid values exists dont update
		$lastlog = $this->getUservalue('lastlog');
		$userdata['lastlog'] = $lastlog ? $lastlog : $userdata['logtime'];
		
		$logtime = $this->getUservalue('logtime');
		$userdata['logtime'] = $logtime ? $logtime : pkTIME;
		
		
		$this->log_userdata = $userdata;
		
		return true;
		}
	
		
	function firstlogin_check_data()
		{
		$S = $this->SQL;
		
		$this->log_uid = trim($this->log_uid);
		
		if(empty($this->log_uid))
			{
			return false;
			}		
		
		$S->query("UPDATE ".pkSQLTAB_USER." 
			SET user_activate=1, uid=''
			WHERE user_name='".$S->f($this->log_username)."'
				AND user_pw='".$S->f($this->log_userpass)."'
				AND uid='".$S->f($this->log_uid)."'
			LIMIT 1");
	
		#cleanup uids
		$S->query("UPDATE ".pkSQLTAB_USER." 
			SET uid=''
			WHERE lastlog<'".$S->i(pkTIME-86400)."'");
	
		return $this->login_check_data();
		}
	
		
	function relogin_check_data()
		{
		$S = $this->SQL;
		
		$this->log_uid = trim($this->log_uid);		
		
		if(empty($this->log_uid))
			{
			return false;
			}		
		
		list($this->log_userpass)=$S->fetch_row($S->query("SELECT user_pw FROM ".pkSQLTAB_USER."
			WHERE user_name='".$S->f($this->log_username)."'
				AND uid='".$S->f($this->log_uid)."'
			LIMIT 1"));
		
		$S->query("UPDATE ".pkSQLTAB_USER." 
			SET uid=''
			WHERE user_name='".$S->f($this->log_username)."'
				AND uid='".$S->f($this->log_uid)."'
			LIMIT 1");
	
		#cleanup uids
		$S->query("UPDATE ".pkSQLTAB_USER." 
			SET uid=''
			WHERE lastlog<'".$S->i(pkTIME-86400)."'");
	
		return $this->login_check_data();
		}
		
	
	function login_error()
		{
		return $this->login_error;
		}
	
		
	function login_guest_data()
		{
		$lastlog=$this->ENV->_cookie('lastlog');
		$lastlog=$lastlog>0 ? $lastlog : pkTIME;

		$logtime=$this->ENV->_cookie('logtime');
		$logtime=$logtime>0 ? $logtime : pkTIME;
		
		$this->log_userdata = array(
			'user_status'=>'guest',
			'user_id'=>0,
			'user_name'=>'',
			'user_nick'=>pkGetLang('guest_status'),
			'user_pw'=>'',
			'user_groupid'=>0,
			'user_email'=>'',
			'user_sex'=>'',
			'user_hpage'=>'',
			'user_icqid'=>'',
			'user_design'=>0,
			'user_sigoption'=>1,
			'user_imoption'=>0,
			'user_ghost'=>0,
			'lastlog'=>$lastlog,
			'logtime'=>$logtime,
			'sip'=>$this->ip,
			'sbrowser'=>$this->browser,
			'slang'=>$this->lang			
			);
		
		$this->log_setcookie=1;		
		}
	
	
	function login_set_data()
		{
		$this->userdata['user_ghost'] = pkGetConfig('user_ghost') ? $this->userdata['user_ghost'] : 0;
		
		foreach($this->session_user_vars as $k=>$v)
			{
			$this->setUservalue($k,$this->log_userdata[$v]);
			}

		#clean cookie
		$this->ENV->setCookie('user_id',0);
		$this->ENV->setCookie('user_name','');		
		$this->ENV->setCookie('user_pw','');
		$this->ENV->setCookie('lastlog',$this->log_userdata['lastlog']);
		$this->ENV->setCookie('logtime',$this->log_userdata['logtime']);

		#set coookie (new values)
		if($this->log_setcookie)
			{
			$this->ENV->setCookie('user_id',$this->log_userdata['user_id']);
			$this->ENV->setCookie('user_name',$this->log_userdata['user_name']);		
			$this->ENV->setCookie('user_pw',$this->log_userdata['user_pw']);	
			}
		}
	
	
	function login_save_data()
		{
		$this->db_save(pkSQLTAB_SESSION);
		
		if(pkFRONTEND=='admin' && $this->adminsession && $this->getUservalue('id'))
			{
			$this->db_save(pkSQLTAB_ADMINSESSION);
			}
		}
	
	
	function update_session()
		{
		if($this->getUservalue('id'))
			{
			$this->db_user_update();
			}
			
		$this->db_update(pkSQLTAB_SESSION);
		$this->ENV->setCookie('logtime',pkTIME);
		
		if(pkFRONTEND=='admin' && $this->adminsession && $this->getUservalue('id'))
			{
			$this->db_update(pkSQLTAB_ADMINSESSION);
			}
		}
	
	
	function account_active()
		{
		return $this->account_active;
		}
	
	
	function user_banned()
		{
		if($this->getUservalue('status')=='ban')
			{
			pkLoadFunc('except');
			pkSiteException(3);
			}				
		}
	
	
	function login_cookie_catch()
		{
		if(!$this->ENV->_cookie('user_id',0) || $this->logmode)
			{
			return false;
			}

		$this->log_userid=$this->ENV->_cookie('user_id');
		$this->log_username=$this->ENV->_cookie('user_name');
		$this->log_userpass=$this->ENV->_cookie('user_pw');
		
		return true;
		}
	
	
	function db_delete_adminsession()
		{
		$this->SQL->query("DELETE FROM ".pkSQLTAB_ADMINSESSION." WHERE session_id='".$this->SQL->f($this->getid())."'");
		}		
	}
?>