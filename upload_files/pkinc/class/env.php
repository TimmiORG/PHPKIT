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


class pkEnv
	{
	var $GET;
	var $POST;
	var $COOKIE;
	var $REQUEST;
	var $FILES;
	var $SESSION;
	
	var $cookiekey;

	function __construct() 
		{
		$this->cookiekey=pkSITE;

		if(get_magic_quotes_gpc())
			{
			$this->removemagicquotes();
			//@set_magic_quotes_runtime(0); //DEPRECATED
			}

		@ini_set('session.use_cookies','1');

		$this->GET=$_GET;
		$this->POST=$_POST;
		$this->COOKIE=$_COOKIE;
		$this->REQUEST=array_merge($this->GET,$this->POST,$this->COOKIE);
		$this->FILES=$_FILES;
		$this->getsitecoookie();
		}

	function pkEnv()
		{
		$this->__construct();
		}
		
	function removemagicquotes()
		{
		if(is_array($_GET))
			$_GET=pkStripslashes($_GET);

		if(is_array($_POST))
			$_POST=pkStripslashes($_POST);

		if(is_array($_REQUEST))
			$_REQUEST=pkStripslashes($_REQUEST);				

		if(is_array($_COOKIE))
			$_COOKIE=pkStripslashes($_COOKIE);
		}	
	
	
	function _get($key)
		{
		return isset($this->GET[$key]) && !empty($this->GET[$key]) ? $this->GET[$key] : false;
		}


	function _get_id($key='id')
		{
		return isset($this->GET[$key]) && intval($this->GET[$key])>0 ? intval($this->GET[$key]) : 0;
		}


	function _get_action($v1,$v2='action')
		{
		return isset($this->GET[$v1]) && isset($this->GET[$v2]) && $this->GET[$v1]==$this->GET[$v2] ? true : false;
		}

	function _get_mode()
		{
		$key='mode';
		return isset($this->GET[$key]) ? $this->GET[$key] : false;	
		}

	function _isset_get($key='')
		{
		return isset($this->GET[$key]) ? true : false;	
		}
		
	function _isset_post($key='')
		{
		return isset($this->POST[$key]) ? true : false;	
		}	

	function _post($key)
		{
		return isset($this->POST[$key]) && !empty($this->POST[$key]) ? $this->POST[$key] : false;
		}
		
	function _post_ibool($key)
		{
		return isset($this->POST[$key]) && $this->POST[$key] ? 1 : 0;
		}
	
	function _post_id($key='id')
		{
		return isset($this->POST[$key]) && intval($this->POST[$key])>0 ? intval($this->POST[$key]) : 0;	
		}

	function _post_action($var1, $var2='action')
		{
		return isset($this->POST[$var1]) && isset($this->POST[$var2]) && $this->POST[$var1]==$this->POST[$var2] ? true : false;
		}

	function _isset_request($key='')
		{
		return isset($this->REQUEST[$key]) ? true : false;	
		}

	function _request($key)
		{
		return isset($this->REQUEST[$key]) && !empty($this->REQUEST[$key]) ? $this->REQUEST[$key] : false;
		}

	function _request_id($key='id')
		{
		return isset($this->REQUEST[$key]) && intval($this->REQUEST[$key])>0 ? intval($this->REQUEST[$key]) : false;	
		}
		
	function _cookie($name)
		{
		if(!isset($this->SCOOKIE[$name]) || empty($this->SCOOKIE[$name]))
			return false;

		return rawurldecode($this->SCOOKIE[$name]);
		}

	function setCookie($name,$value='')
		{
		if(empty($name))
			return false;

		$value===true ? 1 : $value;
		$value===false ? 0 : $value;
		$value===NULL ? '' : $value;

		$value==='' ? pkArrayExtract($this->SCOOKIE,$name) : $this->SCOOKIE[$name]=$value;
		$this->setsitecookie();
		}
		
	function cookie_exists()
		{
		return array_key_exists($this->cookiekey,$this->SCOOKIE);
		}
	
	function setsitecookie()
		{
		$string='';
		foreach($this->SCOOKIE as $key=>$value)
			$string.=(empty($string) ? '' : ',').rawurlencode($key).'>'.rawurlencode($value);

		setcookie($this->cookiekey,false,pkTIME-8640000,pkGetConfig('cookie_path'),pkGetConfig('cookie_domain'),pkGetConfig('cookie_secure'));
		setcookie($this->cookiekey,$string,pkTIME+8640000,pkGetConfig('cookie_path'),pkGetConfig('cookie_domain'),pkGetConfig('cookie_secure'));
		}
	
	function getsitecoookie()
		{																																																																													
		@pkl(4);
		if(!isset($this->COOKIE[$this->cookiekey]))
			return $this->SCOOKIE=array();
		
		$this->SCOOKIE=array();
		foreach(explode(',',$this->COOKIE[$this->cookiekey]) as $value)
			{			
			$a=explode('>',$value);
			if(isset($a[0]) && isset($a[1]))
				$this->SCOOKIE[rawurldecode($a[0])]=rawurldecode($a[1]);
			}
	
		#kill old cookies
		foreach(array('pkSITE','user_id','user_name','user_pw') as $key)
			{
			if(!array_key_exists($key,$_COOKIE))
				continue;

			setcookie($key,false,pkTIME-32000000);
			}
		}
	
	function debugcookie()
		{
		foreach($_COOKIE as $key=>$value)	
			setcookie($key,false,pkTIME-32000000);
		}
		
	function requestmethod($method)
		{
		return strtolower($method) == strtolower(getenv('REQUEST_METHOD'));
		}
	
	function getvar($var)
		{
		switch($var=strtoupper($var))
			{
			case 'REQUEST_URI' :
			case 'QUERY_STRING' :
			case 'HTTP_REFERER' :
				return pkRemoveSessionId(preg_replace('/[&|?]nid=[^&]*/',"",getenv($var)));
			default :
				return getenv($var);
			}
		}
	}
?>