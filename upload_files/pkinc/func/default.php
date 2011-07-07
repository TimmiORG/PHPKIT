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

if (!defined('pkFRONTEND'))
{
	die('Direct access to this location is not permitted.');
}

if (function_exists('pkl'))
{
	return;
}
function __autoload($class)
{
	return @include_once(pkDIRCLASS . $class . pkEXT);
}

#@Function:		pkArrayExtract
#@Param:		array &array
#@Param:		string extract
#@Return:		void
#@Desc:			
function pkArrayExtract(&$array, $extract)
{
	if (!is_array($array))
	{
		return;
	}

	$new = array(
	);

	foreach ($array as $key => $value)
	{
		if ($key != $extract)
		{
			$new[$key] = $value;
		}
	}

	$array = $new;
}

#@END Function:	pkArrayExtract

#@Function:		pkArrayUrlencode
#@Param:		string varname		name of the variable to store the array
#@Param:		array array			array to transfer via an URL
#@Return:		string
#@Desc:			Encodes an array to be used in an URL.
function pkArrayUrlencode($varname, $array)
{
	if (!is_array($array))
	{
		return $varname . '=' . urlencode($array);
	}

	$string = '';

	foreach ($array as $key => $value)
	{
		$string .= (empty($string) ? '' : '&') . $varname . '%5B' . urlencode($key) . '%5D=' . urlencode($value);
	}

	return $string;
}

#@END Function:	pkArrayUrlencode

#@Function:		pkConstant
#@Param:		string str
#@Return:		mixed
#@Desc:	
function pkConstant($str)
{
	if (substr($str, 0, 2) == 'pk')
	{
		$str = substr($str, 2);
	}

	$str = 'pk' . strtoupper($str);

	return constant($str);
}

#@END Function:	pkConstant

#@Function:		pkGetConfig
#@Param:		string key
#@Return:		mixed
#@Desc:			Returns the value for the given configuration key.
function pkGetConfig($key)
{
	global $config;

	return isset($config[$key]) ? $config[$key] : false;
}

#@END function pkGetConfig

#@Function:		pkGetConfigF
#@Param:		string key
#@Return:		mixed
#@Desc:			
function pkGetConfigF($key)
{
	$config = pkGetConfig($key);
	return pkEntities($config);
}

#@END Function:	pkGetConfigF

#function pkSetConfig ( string key, mixed value )
#return void
function pkSetConfig($key, $value)
{
	global $config;

	$config[$key] = $value;
}

#END function pkSetConfig

#function pkGetUserMessageCount( void )
#return int
function pkGetUserMessageCount()
{
	return imstatus();
}

#END function pkGetUserMessageCount

#function pkGetUservalue( string key )
#return mixed
function pkGetUservalue($key)
{
	global $SESSION;

	return $SESSION->getUservalue($key);
}

#END function pkGetUservalue

#function pkGetUservalueF( string key )
#return mixed
function pkGetUservalueF($key)
{
	return pkEntities(pkGetUservalue($key));
}

#END function pkGetUservalueF

#function pkSetUservalue( string key )
#return void
function pkSetUservalue($key, $value)
{
	global $SESSION;

	$SESSION->setUservalue($key, $value);
}

#END function pkSetUservalue

#function void pkLoadClass ( object var, string class )
#return void
function pkLoadClass(&$var, $class)
{
	include_once(pkDIRCLASS . $class . pkEXT);

	$name = 'pk' . strtoupper(substr($class, 0, 1)) . strtolower(substr($class, 1));

	if (is_object($var) && strtolower($name) == strtolower(get_class($var)))
	{
		return;
	}

	$var = new $name;
}

#END function pkLoadClass

#function pkLoadFunc ( string filename )
#return void
function pkLoadFunc($filename)
{
	include_once(pkDIRFUNC . $filename . pkEXT);
}

#END function pkLoadFunc

#function pkLoadLang( string filename )
#return bool
function pkLoadLang($filename = 'default')
{
	global $LANG;

	$vars = include_once(pkDIRLANG . 'de/' . $filename . pkEXT);

	if (is_array($vars))
	{
		$vars = array_map('utf8_encode', $vars);
		$LANG = array_merge($LANG, $vars);
		return true;
	}

	return false;
}

#END function pkLoadLang

#function pkGetLang( string key )
#return string
function pkGetLang($key)
{
	global $LANG;

	return isset($LANG[$key]) ? $LANG[$key] : (pkDEVMODE ? 'L_' . $key : NULL);
}

#END function pkGetLang

#function pkGetSpecialLang()
#return string
function pkGetSpecialLang()
{
	$array = func_get_args();

	if (!isset($array[0]))
	{
		return NULL;
	}

	switch ($array[0])
	{
		case 'month' :
			$i = isset($array[1]) ? $array[1] : 1;
			return pkGetLang('month' . $i);
		case 'private_message' :
			$c = pkGetUserMessageCount();
			return !$c ? pkGetLang('you_got_no_new_private_messages') : ($c . ' ' . pkGetLang($c == 1 ? 'private_message' : 'private_messages'));
		case 'pncenter_message_delete' :
			return !$array[1] ? pkGetLang('pncenter_message_delete_0') : (
			$array[1] > 1 ? sprintf(pkGetLang('pncenter_message_delete_n'),
			                        $array[1]) : pkGetLang('pncenter_message_delete_1'));
		case 'categories' :
			$c = isset($array[1]) ? $array[1] : 1;
			return !$c ? pkGetLang('no_categories') : ($c . ' ' . pkGetLang($c == 1 ? 'category' : 'categories'));
		case 'comment' :
			$c = isset($array[1]) ? $array[1] : 0;
			return $c == 0 ? pkGetLang('no_comments') : ($c . ' ' . pkGetLang($c == 1 ? 'comment' : 'comments'));
		case 'guests' :
			$c = isset($array[1]) ? $array[1] : 0;
			return $c == 0 ? pkGetLang('no_guests') : ($c . ' ' . pkGetLang($c == 1 ? 'guest' : 'guests'));
		case 'guests_online' :
			$c = isset($array[1]) ? $array[1] : 0;
			return $c == 0 ? pkGetLang('no_guests_online') : ($c . ' ' . pkGetLang($c == 1 ? 'guest' : 'guests'));
		case 'match' :
			$c = isset($array[1]) ? $array[1] : 0;
			return $c == 0 ? pkGetLang('match_no') : (sprintf(pkGetLang($c == 1 ? 'match' : 'matches'), $c));
		case 'matches_in_threads' :
			$m = isset($array[1]) ? $array[1] : 0;
			$t = isset($array[2]) ? $array[2] : 0;

			if (!$m || !$t)
			{
				return pkGetLang('match_no');
			}

			$matches = sprintf(pkGetLang($m == 1 ? 'match' : 'matches'), $m);
			$threads = pkGetLang($t == 1 ? 'thread' : 'threads');
			return $matches . ' ' . pkGetLang('in') . ' ' . $t . ' ' . $threads;
		case 'threadinformation' :
			$c = isset($array[1]) ? $array[1] : 1;
			$b = isset($array[2]) ? $array[2] : 1;

			if ($b == 1)
			{
				$b = pkGetLang('open');
			}
			elseif ($b == 2)
			{
				$b = pkGetLang('fixed');
			}
			elseif ($b == 3)
			{
				$b = pkGetLang('fixed') . ' &amp; ' . pkGetLang('closed');
			}
			else
			{
				$b = pkGetLang('closed');
			}

			return sprintf(pkGetLang($c == 1 ? 'threadinformation_1' : 'threadinformation_x'), $c, $b);
		default :
			$lkey = array_shift($array);
			$str = pkGetLang($lkey);

			if (!count($array))
			{
				#empty args to replace
				return $str;
			}

			$str = @vsprintf($str, $array);

			return $str;
		#END default
	}
	#END switch
}

#END function pkGetSpecialLang

function pkGetLangCharset()
{
	global $LANG;

	$charset = isset($LANG['__CHARSET__']) ? $LANG['__CHARSET__'] : 'utf-8';

	return $charset;
}

#function pkGetLangError( string key )
#return string
function pkGetLangError($key)
{
	global $LANG;

	pkLoadLang('error');

	$key = 'error_' . $key;
	$error = isset($LANG[$key]) ? $LANG[$key] : (pkDEVMODE ? 'L_' . $key : NULL);
	return $error ? '<span class="error">' . $error . '</span>' : NULL;
}

#END function pkGetLang

#function pkUrlCheck( string url )
#return bool
function pkUrlCheck($str)
{
	return preg_match("/^(http|https|ftp|ftps)?:\/\/([a-z0-9-äöüß]+\.)+([a-z]{2,6})/si", trim($str));
}

#END function pkUrlCheck

function pkMtSrand()
{
	if (pkPHPVERSION < 420)
	{
		mt_srand((double)pkMICROTIME * 1000000);
	}
}

#function pkRand( void )
#return string
function pkRand()
{
	return md5(pkStringRandom(32));
}

#END function pkRand

function pkStringRandom($length)
{
	pkMtSrand();

	$chardef = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz0123456789';
	$string = '';

	for ($i = 0; $i < $length; $i++)
	{
		$string .= substr($chardef, mt_rand(0, 71), 1);
	}

	return $string;
}

#@Function:		pkPrivateLinkmaker
#@Param:		string amp
#@Param:		string base
#@Param:		string path
#@Param:		string mode
#@Param:		string add
#@Param:		bool full
#@Return:		string
#@Desc:			Creates a Link.
function pkPrivateLinkmaker($amp, $base, $path, $mode, $add, $full = true)
	//	 pkPrivateLinkmaker('&', pkWWWSELF, $path, $mode, $add) . ($anchor ? '#' . $anchor : '');
{
	#testing this feature
	$cfg = pkGetConfig('site_link_base');
	$base = $cfg ? str_replace('include.php', $cfg, $base) : $base;

	$query = ($path ? 'path=' . $path : '');
	$query .= ($mode ? (empty($query) ? '' : $amp) . 'mode=' . $mode : '');
	$query .= ($add ? (empty($query) ? '' : $amp) . str_replace('&', $amp, $add) : '');

	$link = ($full ? $base : '');
	$link .= (empty($query) ? '' : '?' . $query);
	$link = str_replace('?' . $amp, '?', $link);

	return $link;
}

#@END Function: pkPrivateLinkmaker

#function pkLink( [string path [, string mode [, string add [, string htmlanchor [, string parsed[, bool full ]]]]]] )
#return string
function pkLink($path = '', $mode = '', $add = '', $anchor = '', $parsed = '', $full = true)
{
	$link = pkPrivateLinkmaker('&amp;', pkWWWSELF, $path, $mode, $add, $full);
	$link .= $parsed ? (strpos($link, '?') === false ? '?' : '&amp;') : '';
	$link .= $parsed;
	$link .= $anchor ? '#' . $anchor : '';

	return $link;
}

#END function pkLink

#function pkLinkAdmin( [string path [, string mode [, string add [, string htmlanchor, string parsed]]]]] )
#return string
function pkLinkAdmin($path = '', $mode = '', $add = '', $anchor = '', $parsed = '')
{
	$link = pkPrivateLinkmaker('&amp;', pkDIRWWWADMIN . pkSITE . pkEXT, $path, $mode, $add);
	$link .= $parsed ? (strpos($link, '?') === false ? '?' : '&amp;') : '';
	$link .= $parsed;
	$link .= $anchor ? '#' . $anchor : '';

	return $link;
}

#END function pkLinkPublic

#function pkLinkPublic( [string path [, string mode [, string add [, string htmlanchor, string parsed]]]]] )
#return string
function pkLinkPublic($path = '', $mode = '', $add = '', $anchor = '', $parsed = '')
{
	return pkPrivateLinkmaker('&amp;', pkDIRWWWROOT . pkSITE . pkEXT, $path, $mode, $add) . ($parsed ? '&amp;' . $parsed : '') . ($anchor ? '#' . $anchor : '');
}

#END function pkLinkPublic

#function pkLinkFull( [string path [, string mode [, string add [, string htmlanchor, string parsed]]]]] )
#return string
function pkLinkFull($path = '', $mode = '', $add = '', $anchor = '', $parsed = '', $admin = false)
{
	return pkPrivateLinkmaker('&amp;', pkGetConfig('site_url') . '/' . ($admin ? 'pk/' : '') . pkREQUESTEDFILE, $path, $mode, $add) . ($parsed ? '&amp;' . $parsed : '') . ($anchor ? '#' . $anchor : '');
}

#END function pkLinkFull

function pkLinkMail($path = '', $mode = '', $add = '', $anchor = '', $parsed = '', $admin = false)
{
	$link = pkLinkFull($path, $mode, $add, $anchor, $parsed, $admin);
	$link = str_replace('&amp;', '&', $link);

	return $link;
}

#function pkLinkFx( [string path [, string mode [, string add [, string htmlanchor, string parsed]]]]] )
#return string
function pkLinkFx($fx = '', $add = '')
{
	return pkPrivateLinkmaker('&amp;', pkDIRWWWROOT . pkSITE . pkEXT, '', '', 'fx=' . $fx . '&' . $add);
}

#END function pkLinkPublic

#function pkHtmlLink
function pkHtmlLink($link, $value, $target = '', $id = '', $class = '', $title = '')
{
	return '<a href="' . $link . '"' . ($id ? ' id="' . $id . '"' : '') . ($class ? ' class="' . $class . '"' : '') . ($target ? ' target="' . $target . '"' : '') . ($title ? ' title="' . $title . '"' : '') . '>' . $value . '</a>';
}

#END function pkHtmlLink

#function pkHeader( void )
#return void
function pkHeader()
{
	if (pkFRONTEND == 'public' && !@pkl(2))
	{
		global $site;
		$p = strrpos($site, '</div');
		(defined('pkC') ? $site = substr($site, 0, $p) . pkC . substr($site, $p) : ((@filectime(pkDIRINC . 'lang/de/public' . pkEXT) + 144000) < pkTIME && pkGetUservalue('status') != 'admin' ? exit(strrev('>--devomer thgirypoc--!<')) : NULL));
	}
	if (headers_sent())
	{
		return;
	}

	header("Expires: " . gmdate("D, d M Y H:i:s", pkTIME - 1) . " GMT");
	header("Last-Modified: " . gmdate("D, d M Y H:i:s", pkTIME - 1) . " GMT");
	header("Content-Type: text/html; charset=" . pkGetLang('__CHARSET__'));
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
}

#END function pkHeader

#function pkHeaderDownload
#return void
function pkHeaderDownload($filename = 'default.txt')
{
	if (headers_sent())
	{
		return;
	}

	@ob_end_clean();

	$filename = basename($filename);

	$browser = getenv('HTTP_USER_AGENT');
	$content_type = (strstr($browser, 'Opera') || strstr($browser, 'IE')) ? 'application/octetstream' : 'application/octet-stream';

	header('Content-Type: ' . $content_type);

	if (strstr($browser, 'IE'))
	{
		header('Content-Disposition: inline; filename="' . $filename . '"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		return;
	}

	header('Content-Disposition: attachment; filename="' . $filename . '"');
	header('Expires: 0');
	header('Pragma: no-cache');
}

#END function pkHeaderDownload

#function pkHeaderFxCache( string contenttype )
#return void
function pkHeaderFxCache($contenttype)
{
	if (!pkDEVMODE && headers_sent())
	{
		return;
	}

	header('Content-Type: ' . $contenttype);
	header("Last-Modified: " . pkTimeFormat(pkTIME, '%a, %d %b %Y %H:%M:%S') . " GMT");
	header("Expires: " . pkTimeFormat(pkTIME + 86400, '%a, %d %b %Y %H:%M:%S') . " GMT");
}

#END function pkHeaderFxCache

#@Function:	pkHeaderLink( [string path [, string mode [, string add [, string anchor ]]]] )
#@Return:	string
#@Desc:		Returns a link without leading ? 
function pkHeaderLink($path = '', $mode = '', $add = '', $anchor = '', $parsed = '', $full = true)
{
	$link = pkPrivateLinkmaker('&', pkWWWSELF, $path, $mode, $add, $full) . ($anchor ? '#' . $anchor : '');
	$link = str_replace('?', '', $link);

	return $link;
}

#END function pkHeaderLink

#function pkHeaderLocation( [string path [, string mode [, string add [, string anchor ]]]] )
#return void
function pkHeaderLocation($path = '', $mode = '', $add = '', $anchor = '')
{
	global $SESSION;

	$loc = pkPrivateLinkmaker('&', pkWWWSELF, $path, $mode, $add) . ($anchor ? '#' . $anchor : '');

	pkDEVMODE ? header('Location: ' . $loc) : @header('Location: ' . $loc);
	exit;
}

#END function pkHeaderLocation

#function pkEntities( string string )
#return string
function pkEntities($str)
{
	$str = htmlentities($str, ENT_QUOTES, pkGetLangCharset());

	return pkSpecialEnts($str);
}

#END function pkEntities

#function pkSpecialchars( string string )
#return string
function pkSpecialchars($str)
{
	$str = htmlspecialchars($str, ENT_QUOTES, pkGetLangCharset());

	return pkSpecialEnts($str);
}

#END function pkSpecialchars

function pkSpecialEnts($str)
{
	$search = array(
		'$', '(', ')'
	);
	$replace = array(
		'&#36;', '&#40;', '&#41;'
	);

	return str_replace($search, $replace, $str);
}

#function pkLinkUnEntities ( string string )
#return string
function pkLinkUnEntities($string)
{
	return str_replace('&amp;', '&', pkEntities($string));
}

#END function pkLinkUnEntities

#function pkEntitiesReplace
#return string
function pkEntitiesReplace($string)
{
	$hash = array(
		'&gt;' => '<', '&lt;' => '>', '&szlig;' => 'ß', '&uuml;' => 'ü', '&Uuml;' => 'Ü', '&auml;' => 'ä',
		'&Auml;' => 'Ä', '&ouml;' => 'ö', '&Ouml;' => 'Ö', '&quot;' => '"', '&acute;' => '´', '&#39;' => "'",
		'&#36;' => '$', '&amp;' => '&'
	);

	foreach ($hash as $k => $v)
		$string = str_replace($k, $v, $string);

	return $string;
}

#END function pkEntitiesReplace

#function pkParsertime ( void )
#return string
function pkParsertime()
{
	$mtime[0] = explode(" ", pkMICROTIME);
	$mtime[1] = explode(" ", microtime());
	return number_format(($mtime[1][1] + $mtime[1][0]) - ($mtime[0][1] + $mtime[0][0]), 5, ".", ".");
}

#END function pkParsertime

#function pkTimeFormat( [ int time [, string type]])
#return string
function pkTimeFormat($time = 0, $type = '%d.%m.%Y - %H:%M')
{
	return formattime($time, 0, $type);
}

#END function pkTimeFormat

#function pkStripslashes( mixed var )
#return mixed
function pkStripslashes($var)
{
	if (!is_array($var))
	{
		return stripslashes($var);
	}

	foreach ($var as $k => $v)
		$var[$k] = pkStripslashes($v);

	return $var;
}

#END function pkStripslashes

#function pkFileCheck( string filepath )
#return bool
function pkFileCheck($filepath)
{
	return filecheck($filepath);
}

#END function pkFileCheck

function pkFormActionGet(&$link)
{
	$hash = array(
	);
	$i = strpos($link, '?');

	if ($i !== false)
	{
		$add = substr($link, $i + 1, strlen($link));
		$link = substr($link, 0, $i);

		$add = str_replace('&amp;', '&', $add);
		$hash = explode('&', $add);
	}

	if (empty($hash))
	{
		return '';
	}

	$vars = array(
	);
	$additionalfields = '';

	foreach ($hash as $string)
	{
		$key = $value = '';
		$i = strpos($string, '=');

		if ($i !== false)
		{
			$key = substr($string, 0, $i);
			$value = substr($string, $i + 1, strlen($string));
		}

		if (empty($key) && empty($value))
		{
			continue;
		}

		$vars[$key] = $value;
	}

	foreach ($vars as $key => $value)
	{
		$additionalfields .= '<input type="hidden" name="' . $key . '" value="' . $value . '" />';
	}

	return $additionalfields;
}

function pkRowClass($row)
{
	return rowcolor($row);
}

function pkCfgData($key)
{
	global $pkCFGHASH;

	if (!array_key_exists($key, $pkCFGHASH))
	{
		$cfgdata = include(pkDIRCFG . $key . pkEXT);
		$pkCFGHASH[$key] = $cfgdata;
	}

	return $pkCFGHASH[$key];
}

function pkCheckEmailaddress($checkemail, $censor = 0)
{
	return emailcheck($checkemail, $censor);
}

function pkDocmeta($key)
{
	global $pkDOCMETA;

	if (isset($pkDOCMETA[$key]))
	{
		return;
	}

	$pkDOCMETA[$key] = 1;

	if ($key == 'js_default')
	{
		pkSetHtml('__docbody_params__', pkGetHtml('__docbody_params__') . ' onunload="pkPopupCleaner();"');
	}
}

function pkGetDocmeta()
{
	global $pkDOCMETA;
	$out = '';

	foreach ($pkDOCMETA as $key => $nomatter)
		$out .= ($v = pkGetHtml($key)) ? $v : $key;

	return $out;
}

function pkLoadHtml($filename = 'default')
{
	global $pkHTMLBITS;

	$vars = include_once(pkDIRHTML . 'default/' . $filename . pkEXT);
	if (is_array($vars))
	{
		$pkHTMLBITS = array_merge($pkHTMLBITS, $vars);
	}
}

# string pkGetHtml( string key )
function pkGetHtml($key)
{
	global $pkHTMLBITS;

	return isset($pkHTMLBITS[$key]) ? $pkHTMLBITS[$key] : $key;
}

function pkHtmlImage($key, $alt = '')
{
	return '<img src="' . pkGetHtml($key) . '" alt="' . $alt . '" />';
}

# string pkGetSpecialHtml()
function pkGetSpecialHtml()
{
	$array = func_get_args();

	if (!isset($array[0]))
	{
		return NULL;
	}

	switch ($array[0])
	{
		case 'sp_icqadd' :
			$number = isset($array[1]) ? $array[1] : NULL;
			$user = isset($array[2]) ? $array[2] : NULL;
			$icon = isset($array[3]) ? intval($array[3]) : 5;
			return @sprintf(pkGetHtml('sp_icqadd'), $number, $number, $icon, $user);

		default :
			$str = array_shift($array);
			$str = pkGetHtml($str);

			if (!count($array))
			{
				#empty args to replace
				return $str;
			}

			$str = @vsprintf($str, $array);
			return $str;
		#END default
	}
}

#@Function:	pkStringCut
#@Desc:		Cuts a string if longer then length.
function pkStringCut($string, $length = 20, $add = '..')
{
	$length = intval($length) > 0 ? intval($length) : 20;
	$string = strlen($string) > $length ? mb_substr($string, 0, $length, pkGetLang('__CHARSET__')) . $add : $string;

	return $string;
}

#@END Function pkStringCut

function pkRemoveSessionId($string)
{
	global $SESSION;

	return preg_replace('/([&|?])?(' . pkPHPKITSID . ')=([a-z0-9]{0,32})/i', '', $string);
}

function pkMkTime($h, $m, $s, $M, $D, $Y)
{
	return ($h || $m || $s || $M || $D || $Y) ? mktime(intval($h), intval($m), intval($s), intval($M), intval($D), intval($Y)) : 0;
}

#@Param:	float number
#@Param:	int decimals
#@Return:	string
function pkNumberFormat($number, $decials = 0)
{
	$decimal_sep = pkGetLang('__NUMBER_DECIMAl_SEP__');
	$thousands_sep = pkGetLang('__NUMBER_THOUSANDS_SEP__');

	return number_format($number, $decials, $decimal_sep, $thousands_sep);
}

#@END function pkNumberFormat

function stripslashes_array($array)
{
	reset($array);
	while (list($k, $v) = each($array))
	{
		if (is_string($v))
		{
			$array[$k] = stripslashes($v);
		}
		elseif (is_array($v))
		{
			$array[$k] = stripslashes_array($v);
		}
	}

	return $array;
}

function getAge($d, $m, $y)
{
	$strDate = $d . '.' . $m . '.' . $y;

	if (!preg_match('/([0-9]{1,2}).([0-9]{1,2}).([0-9]{4})/', $strDate, $arrParts))
	{
		return false;
	}

	$intAge = date('Y') - $arrParts[3];

	if ($arrParts[2] > date('m'))
	{
		$intAge--;
	}
	else
	{
		if ($arrParts[2] == date('m'))
		{
			if ($arrParts[1] > date('d'))
			{
				$intAge--;
			}
		}
	}

	return $intAge;
}

function contentcats()
{
	global $SQL, $contentcat_info_array;

	if (!empty($contentcat_info_array))
	{
		return $contentcat_info_array;
	}

	$query = $SQL->query("SELECT * FROM " . pkSQLTAB_CONTENT_CATEGORY . " ORDER by contentcat_name ASC");
	while ($contentcat = $SQL->fetch_array($query))
	{
		$contentcat_cache[$contentcat['contentcat_id']] = $contentcat;
	}

	$contentcat_info_array[0] = $contentcat_cache;

	return $contentcat_info_array;
}

function filecheck($file)
{
	if (!($fp = @fopen($file, 'r')))
	{
		return false;
	}

	fclose($fp);
	return true;
}

function FileSizeExt($file = '', $ext = 'Byte', $size = 0)
{
	$size_ext = array(
		'', 'K', 'M', 'G', 'T'
	);

	if (!empty($file))
	{
		$size = @filesize($file);
	}

	if ($size <= 0)
	{
		return false;
	}

	$div = 0;

	while ($size >= pow(1024, $div))
	{
		$div++;
	}

	return number_format(($size / pow(1024, $div - 1)), 1, ",", ".") . " " . $size_ext[$div - 1] . $ext;
}

function formatfield($field)
{
	if ((string)(intval($field)) != "$field")
	{
		$field = str_replace("\n", "\\n", str_replace("\r", "\\r", str_replace("\t", "\\t", addslashes($field))));
	}

	return "'$field'";
}

#@Function:	formattime
#@Return:	mixed
#@Desc:		Converts timestamps to dates.
#			This function will be revised in the future
function formattime($time = 0, $offset = 0, $type = '%d.%m.%Y - %H:%M', $format = '')
{
	$gmt = $stime = 0;

	if (!intval($time) > 0)
	{
		$time = pkTIME;
	}

	$time = $time + pkGetConfig('time_offset');

	if (pkGetConfig('time_summertime') && date("I"))
	{
		$stime = 3600;
	}

	if (!$offset)
	{
		$offset = pkGetConfig('time_gmtzone');
		$offset = $offset * 3600;
	}

	$gmt = $stime + $offset;
	$time = $time + $gmt;
	$day = @gmdate("w", $time);

	switch ($type)
	{
		case 'stamp' :
			return $time;

		case 'istamp' :
			return $time - $gmt - $gmt;

		case 'date' :
			return strftime("%d.%m.%Y", $time);

		case 'extend' :
		case 'datelong' :
			#DAY, 12. Month 2009 
			$month = date("n", $time);
			$str = pkGetLang('day' . $day) . ", " . date("j", $time) . ". " . pkGetLang('month' . $month) . " " . date("Y", $time);

			if ($type == 'extend')
			{
				$str .= ' - ' . date("H", $time) . ":" . date("i", $time);
			}

			return $str;
		case 'idate' :
			return strftime("%Y-%m-%d", $time);
		case 'time' :
			return strftime("%H:%M", $time);
		case 'time_full' :
			return strftime("%H:%M:%S", $time);
		case 'spoken' :
			return strftime(pkGetLang('timeformat_spoken'), $time);
		case 'RFC822' :
			#example: Wed, 02 Oct 2002 13:00:00 GMT
			#shorten day and month names (english)
			return date('D, d M Y H:i:s \G\M\T', $time);
		default :
			return strftime($type, $time);
	}
}

function emailcheck($checkemail, $censor = 0)
{
	$censor_email = pkGetConfig('censor_email');
    $checkemail = strtolower($checkemail);
	if (!preg_match("/^([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,}))$/si", $checkemail))
	{
		return false;
	}

	if ($censor != 1 || empty($censor_email))
	{
		return true;
	}

	$emails = explode("\n", $censor_email);

	foreach ($emails as $e)
	{
		$e = trim($e);
		if (!strstr($e, "*@"))
		{
			if ($checkemail == $e)
			{
				return false;
			}
		}
		else
		{
			$ec = explode("@", $checkemail);
			$ee = explode("@", $e);

			if ($ec[1] == $ee[1])
			{
				return false;
			}
		}
	}

	return true;
}

function mailsender($receiver = '', $subject, $message, $header = '', $addheader = '', $type = 'txt')
{
	$line_length_max = 70;

	switch ($type)
	{
		case 'htm' :
		case 'html' :
			$content_type = 'text/html';
                        $message = utf8_encode($message);
			break;
		default :
			$content_type = 'text/plain';                        
                        $message = html_entity_decode($message, ENT_NOQUOTES, "UTF-8");
			break;
	}

	#prepare
	$receiver = empty($receiver) ? mailalias(pkGetConfig('site_email'), pkGetConfig('site_name')) : $receiver;
	$header = empty($header) ? 'From: ' . mailalias(pkGetConfig('site_email'), pkGetConfig('site_name')) . "\n" : $header;

	$header = "MIME-Version: 1.0" . "\n" . "Content-Type: " . $content_type . "; charset=utf-8;\r\n" . $header;

        $subject = html_entity_decode($subject, ENT_NOQUOTES, "UTF-8");
	//$subject = utf8_encode($subject);
	$subject = mailencode($subject);
        

	#lines are allowed with max 70 chars - wrap longer lines
	$array = explode("\n", $message);
	foreach ($array as $i => $str)
	{
		$array[$i] = wordwrap($str, $line_length_max, "\n", false);
	}

	$message = implode("\n", $array);

	#SMTP 
	#@TODO: needs a redesign
	$smtp_server = pkGetConfig('smtp_server');

	if ($smtp_server != "" && ini_get("SMTP") != $smtp_server)
	{
		@ini_set("SMTP", $smtp_server);
	}

	#add mail signature / footer
	if ($type == "html" && pkGetConfig('site_mail_htm'))
	{
		$message .= "<br /><br /><br />" . stripslashes(pkGetConfig('site_mail_htm'));
	}
	elseif (pkGetConfig('site_mail_txt') != '')
	{
		$message .= "\r\n\r\n\r\n" . stripslashes(pkGetConfig('site_mail_txt'));
	}
       
	return mail($receiver, $subject, $message, $header);
}

function mailalias($email, $alias = '')
{
	if (empty($alias))
	{
		$string = $email;
	}
	else
	{
		$alias = utf8_decode($alias);
		$string = mailencode($alias) . ' <' . $email . '>';
	}

	return $string;
}

function mailencode($string)
{
	$pattern = '/([\xA0-\xFF|\x3C-\x40|\x5B|\x5D])/';

	if (preg_match($pattern, $string))
	{
		$string = preg_replace($pattern . 'e', '"=" .strtoupper(dechex(ord("$1")))', $string);
		$string = '=?utf-?Q?' . $string . '?=';
	}

	return $string;
}

$x = strrev('edoced_46esab');
eval($x('ZnVuY3Rpb24gcGtsKCRvPTApeyRvPWludHZhbCgkbyk7aWYoISRvKSByZXR1cm4gMDtpZihkZWZpbmVkKCdwa0wnKSlyZXR1cm4gKCgkbz09MiAmJiAocGtMPT0yIHx8IHBrTD09NCkpIHx8ICgkbz09PTEgJiYgKHBrTD09PTEgfHwgcGtMPT00KSkpOyRsPXRyaW0ocGtHZXRDb25maWcoJ2xpY2VuY2VrZXknKSk7aWYoc3RybGVuKCRsKSE9MTkpcmV0dXJuIDA7JHU9dHJpbShwa0dldENvbmZpZygnc2l0ZV91cmwnKSk7aWYoc3RybGVuKCR1KTwxMCkgcmV0dXJuIDA7Zm9yZWFjaChhcnJheSgxLDIsNCkgYXMgJGxldmVsKXskdXJsaGFzaD1tZDUoJHUpOyRmPSdhcnJheV9rZXlfZXhpc3RzJzska2V5PSRzdHJpbmc9Jyc7Zm9yKCRpPTA7JGk8MzI7JGkrKyl7JGM9JHVybGhhc2hbJGldOyRjPSRjPyRjOjEwOyRhcnJheT1hcnJheSgnYSc9PjExLCdiJz0+MTYsJ2MnPT4yMSwnZCc9PjUsJ2UnPT4xMiwnZic9PjIwLCdnJz0+MjIsJ2gnPT42LCdpJz0+MTMsJ2onPT4yMywnayc9PjcsJ2wnPT4xOSwnbSc9PjEsJ24nPT4yNiwnbyc9PjksJ3AnPT4yNSwncSc9PjE0LCdyJz0+MTgsJ3MnPT4yLCd0Jz0+OCwndSc9PjI0LCd2Jz0+MTUsJ3cnPT4zLCd4Jz0+MTAsJ3knPT4xNywneic9PjQpOyRrZXkuPShhcnJheV9rZXlfZXhpc3RzKCRjLCRhcnJheSk/JGFycmF5WyRjXTokYykqJGxldmVsO30ka2V5PW1kNSgka2V5KTskZmFycmF5PWFycmF5X2ZsaXAoJGFycmF5KTtmb3IoJGk9MDskaTwxNjskaSsrKXskYT0ka2V5WyRpXTskYj0ka2V5WygkaSsxNildO2lmKCRmKCRhLCRmYXJyYXkpICYmICRmKCRiLCRmYXJyYXkpKXtpZigkYT49JGIpeyRjPWNlaWwoJGErJGIrJGkvJGxldmVsKTt3aGlsZSgkYz45KSRjPWNlaWwoJGMvMik7fWVsc2V7JHg9Y2VpbCgoJGErMSkqKCRpKzEpKiRiKigkbGV2ZWwrMSkpO3doaWxlKCR4PjI1KSR4PWNlaWwoJHgvMikrMTskYz0kZmFycmF5WyR4XTt9fWVsc2VpZigkZigkYSwkYXJyYXkpICYmICRmKCRiLCRhcnJheSkpeyRjPSRhcnJheVskYV0+JGFycmF5WyRiXT8kYTokYjt9ZWxzZXtpZigkZigkYSwkYXJyYXkpKXskeD1jZWlsKCgkYisxKSooJGkrMSkqJGFycmF5WyRhXSooJGxldmVsKzEpKTt3aGlsZSgkeD4yNSkkeD1jZWlsKCR4LzIpKzE7JGM9JGZhcnJheVskeF07fWVsc2VpZigkZigkYiwkYXJyYXkpKXskeD1jZWlsKCgkYSsxKSooJGkrMSkqJGFycmF5WyRiXS8oJGxldmVsKzEpKTt3aGlsZSgkeD4yNikkeD1jZWlsKCR4LzIpKzE7JGM9JGZhcnJheVskeF07fWVsc2UgJGM9JGE7fSRzdHJpbmcuPSRjO2lmKCRpJTQ9PTMgJiYgJGkhPTE1KSRzdHJpbmcuPSctJzt9aWYoc3RydG91cHBlcigkc3RyaW5nKT09JGwpe2RlZmluZSgncGtMJywkbGV2ZWwpOyByZXR1cm4gcGtsKCRvKTt9fXJldHVybiAwO30='));

function parseregiexp($exp = '')
{
	$newexp = '';
	
	for ($i = 0; $i < strlen($exp); $i++)
	{
		if ($exp[$i] == '^' || $exp[$i] == '.' || $exp[$i] == '[' || $exp[$i] == ']' || $exp[$i] == '$' ||
		    $exp[$i] == '(' || $exp[$i] == ')' || $exp[$i] == '|' || $exp[$i] == '*' || $exp[$i] == '+' ||
		    $exp[$i] == '?' || $exp[$i] == '{' || $exp[$i] == '}' || $exp[$i] == '\\')
		{
			$newexp .= '\\' . $exp[$i];
		}
		else
		{
			$newexp .= $exp[$i];
		}
	}

	return $newexp;
}

function postcount($userposts, $postdelay, $rankonly)
{
	global $SQL, $forumrank_info_array;

	$userposts = $userposts + $postdelay;
	$p = pkGetLang($userposts == 1 ? 'post' : 'posts');

	if (empty($forumrank_info_array) && pkGetConfig('forum_showrank') == 1)
	{
		$getrank = $SQL->query("SELECT forumrank_post, forumrank_title FROM " . pkSQLTAB_FORUM_RANK . " ORDER by forumrank_post ASC");
		while ($rank = $SQL->fetch_array($getrank))
		{
			$forumrank_info_array[] = $rank;
		}
	}

	if (is_array($forumrank_info_array))
	{
		foreach ($forumrank_info_array as $r)
		{
			if ($userposts < $r['forumrank_post'])
			{
				break;
			}

			$postrank = $r['forumrank_title'];
		}
	}

	if ($rankonly != 1)
	{
		if ($postrank != "")
		{
			$post_status = " - " . $postrank;
		}

		$userrank = $userposts . " " . $p . " " . $post_status;
	}
	else
	{
		$userrank = $postrank;
	}

	return $userrank;
}

function imstatus()
{
	global $SQL, $imstatus_info;

	if (!intval(pkGetUservalue('id')))
	{
		return false;
	}

	if (isset($imstatus_info))
	{
		return $imstatus_info;
	}

	list($imstatus_info) = $SQL->fetch_row($SQL->query("SELECT 
		COUNT(im_id) 
		FROM " . pkSQLTAB_USER_PRIVATEMESSAGE . "
		WHERE im_to='" . intval(pkGetUservalue('id')) . "'
			AND im_view=0
			AND im_del=0" . (pkGetConfig('user_pndelete') ? " AND im_time>'" . (pkTIME - pkGetConfig('user_pndelete') * 86400) . "'" : '')));

	return $imstatus_info;
}

function ipcheck($userip)
{
	$censor_ip = pkGetConfig('censor_ip');
	if (empty($censor_ip))
	{
		return true;
	}

	$ip_cache = explode("\n", preg_replace("/\s*\n\s*/", "\n", strtolower(trim($censor_ip))));

	for ($i = 0; $i < count($ip_cache); $i++)
	{
		$ip_cache[$i] = trim($ip_cache[$i]);

		if (!$ip_cache[$i])
		{
			continue;
		}

		if ($userip == $ip_cache[$i])
		{
			return false;
		}

		if (strstr($ip_cache[$i], "*"))
		{
			$ip_cache[$i] = str_replace("*", ".*", $ip_cache[$i]);

			if (preg_match("/$ip_cache[$i]/i", $userip))
			{
				return false;
			}
		}
	}

	return true;
}

function pagelink($counter, $epp, $active, $pagelink)
{
	$total_side = pkGetLang('page') . ": ";
	$c = 0;
	$side = 1;

	while ($counter > $c)
	{
		$link = $epp * $side;
		$c = $c + $epp;
		$total_side .= $side == $active ? ' <b><a href="' . $pagelink . '&amp;page=' . $link . '">' . $side . '</a></b>' : ' <a href="' . $pagelink . '&amp;page=' . $link . '">' . $side . '</a>';
		$side++;
	}

	return $total_side;
}

function sidelinksmall($counter, $epp, $pagelink)
{
	$pagelink = pkentities($pagelink);
	$total_side = pkGetLang('page') . ": ";
	$c = 0;
	$side = 1;

	while ($counter > $c)
	{
		$link = $epp * $side - $epp;
		$c = $c + $epp;
		$total_side .= ' <a href="' . $pagelink . '&amp;entries=' . $link . '">' . $side . '</a>';
		$side++;
	}

	return $total_side;
}

function sidelink($counter, $epp, $entries, $pagelink)
{
	global $lang;
	$pagelink = pkentities($pagelink);
	$c = 0;
	$s = 0;
	$total_side = '';
	while ($counter > $c)
	{
		$c = $c + $epp;
		$s++;
	}

	eval("\$total_side=\"" . pkTpl("sidelink") . "\";");

	if ($entries > 0)
	{
		$total_side .= "<a href=\"" . $pagelink . "&amp;entries=0\">&lt;&lt;</a> ";

		/* TODO not in use - 04.05.2011 maXus
		$p = $entries - $epp;

		if ($p < 0)
		{
			$p = 0;
		}
		*/
	}

	$c = 0;
	$side = 1;
	$cside = (($entries + $epp) / $epp);

	while ($counter > $c)
	{
		$link = $epp * $side - $epp;
		$c = $c + $epp;

		if ($side == $cside || ($cside < $side + pkGetConfig('sidelinkfull_pages') && $cside > $side - pkGetConfig('sidelinkfull_pages')))
		{
			if ($entries != $link)
			{
				$total_side .= "<a href=\"" . $pagelink . "&amp;entries=" . $link . "\">" . $side . "</a> ";
			}
			else
			{
				$total_side .= "<b>(" . $side . ")</b> ";
			}
		}

		$side++;
	}

	$t = $counter - $epp;

	if ($t > $entries)
	{
		$n = $entries + $epp;
		$total_side .= "<a href=\"" . $pagelink . "&amp;entries=" . $link . "\">&gt;&gt;</a>";
	}

	return $total_side;
}

function sidelinkfull($counter, $epp, $entries, $pagelink, $class = '')
{
	global $lang;
	$pagelink = pkentities($pagelink);
	$c = 0;
	$s = 0;
	$total_side = '';
	while ($counter > $c)
	{
		$c = $c + $epp;
		$s++;
	}

	if ($entries > 0)
	{
		$p = $entries - $epp;

		if ($p < 0)
		{
			$p = 0;
		}

		eval("\$total_side=\"" . pkTpl("sidelink_prev") . "\";");
	}

	$c = 0;
	$side = 1;
	$cside = (($entries + $epp) / $epp);

	while ($counter > $c)
	{
		$link = $epp * $side - $epp;
		$c = $c + $epp;

		if ($cside == $side)
		{
			eval("\$total_side.=\"" . pkTpl("sidelink_page_match") . "\";");
		}
		elseif ($cside < $side + pkGetConfig('sidelinkfull_pages') && $cside > $side - pkGetConfig('sidelinkfull_pages'))
		{
			eval("\$total_side.=\"" . pkTpl("sidelink_page_nomatch") . "\";");
		}

		$side++;
	}

	$t = $counter - $epp;
	if ($t > $entries)
	{
		$n = $entries + $epp;
		eval("\$total_side.=\"" . pkTpl("sidelink_next") . "\";");
	}

	eval("\$total_side=\"" . pkTpl("sidelink") . "\";");
	return $total_side;
}

function checkusername($name, $opt = '')
{
	global $SQL;

	$censor_username = pkGetConfig('censor_username');

	$name = trim($name);

	if (empty($name))
	{
		return false;
	}

	if (strlen($name) < pkGetConfig('user_namemin') || strlen($name) > pkGetConfig('user_namemax'))
	{
		return false;
	}

	if (!empty($censor_username))
	{
		$ch = explode("\n", $censor_username);
		$ch = array_filter($ch);
		$ch = array_filter($ch, 'trim');
		foreach ($ch as $c)
		{
			if (strstr(strtolower($name), strtolower(parseregiexp($c))))
			{
				return false;
			}

			// exact
			if (strtolower($c) == strtolower('{' . $name . '}'))
			{
				return false;
			}
		}
	}

	if (!pkGetUservalue('id') && $opt == 1)
	{
		list($count) = $SQL->fetch_row($SQL->query("SELECT 
			COUNT(*)
			FROM " . pkSQLTAB_USER . "
			WHERE user_nick='" . $SQL->f($name) . "' OR 
				user_name='" . $SQL->f($name) . "'
			LIMIT 1"));

		if ($count)
		{
			return false;
		}
	}

	if (intval(pkGetUservalue('id')) && $name == pkGetUservalue('nick'))
	{
		return true;
	}

	return true;
}

function usercount()
{
	global $SQL;

	list($count) = $SQL->fetch_row($SQL->query("SELECT COUNT(user_activate) FROM " . pkSQLTAB_USER . " WHERE user_activate=1"));

	$SQL->query("REPLACE INTO " . pkSQLTAB_CONFIG . " (id,value) VALUES ('user_usercount','" . $SQL->f(serialize($count)) . "')");
	pkSetConfig('user_usercount', $count);
}

function newestuser()
{
	global $SQL;

	list($id) = $SQL->fetch_row($SQL->query("SELECT MAX(user_id) FROM " . pkSQLTAB_USER . " WHERE user_activate=1"));

	$SQL->query("REPLACE INTO " . pkSQLTAB_CONFIG . " (id,value) VALUES ('user_newestuserid','" . $SQL->f(serialize($id)) . "')");
	pkSetConfig('user_newestuserid', $id);
}

function bdusertoday()
{
	global $SQL;

	$string = '';

	$query = $SQL->query("SELECT user_id FROM " . pkSQLTAB_USER . " WHERE user_activate=1 AND user_bd_day='" . date("d", pkTIME) . "' AND user_bd_month='" . date("m", pkTIME) . "'");
	while (list($id) = $SQL->fetch_row($query))
	{
		$string .= (empty($string) ? '' : ',') . $id;
	}

	$SQL->query("REPLACE INTO " . pkSQLTAB_CONFIG . " (id,value) VALUES ('user_bduser','" . $SQL->f(serialize($string)) . "')");
	pkSetConfig('user_bduser', $string);
}

function phpkitstatus()
{
	global $phpkit_status, $SQL, $SESSION;

	if (!empty($phpkit_status))
	{
		return $phpkit_status;
	}

	$sqlcommand = '';

	$guests_hash = array(
	);
	$useridhash = array(
	);
	$online_user = array(
	);
	$bd_user = array(
	);
	$userinfo_hash = array(
	);

	$counter = pkGetConfig('user_usercount');
	$newestuserid = pkGetConfig('user_newestuserid');
	$bdusers = pkGetConfig('user_bduser');

	$bd_d = date("d", pkTIME);
	$bd_m = date("m", pkTIME);

	#newestuser
	$useridhash[] = $newestuserid;

	if (!empty($bdusers))
	{
		foreach (explode(',', $bdusers) as $id)
			$useridhash[] = $id;
	}

	$getisonline = $SQL->query("SELECT 
			session_userid,
			session_url,
			session_ip,
			session_expire
		FROM " . pkSQLTAB_SESSION . "
		WHERE session_expire>" . pkTIME . " 
			AND session_isbot<>1");
	while ($isonline = $SQL->fetch_assoc($getisonline))
	{
		if ($isonline['session_userid'] > 0)
		{
			$userinfo_hash[$isonline['session_userid']] = $isonline;
			$useridhash[] = $isonline['session_userid'];
		}
		else
		{
			$isonline['logtime'] = $isonline['session_expire'] - $SESSION->getExpire(1, 0, 1);
			$guests_hash[] = $isonline;
		}
	}

	$useridhash = array_unique($useridhash);

	$getstatus = $SQL->query("SELECT
			TRIM(user_nick) AS user_nick,
			user_id,
			user_bd_day,
			user_bd_month,
			user_bd_year,
			user_ghost,
			user_status,
			logtime 
			FROM " . pkSQLTAB_USER . " 
			WHERE user_id IN(0" . implode(',', $useridhash) . ")
			ORDER BY TRIM(user_nick) ASC");
	while ($status = $SQL->fetch_assoc($getstatus))
	{
		if ($status['user_id'] == $newestuserid)
		{
			$userinfo = $status;
		}

		if (array_key_exists($status['user_id'], $userinfo_hash) && $userinfo_hash[$status['user_id']] > 0)
		{
			$status['user_nick'] = $status['user_nick'];
			$status['expire'] = $userinfo_hash[$status['user_id']]['session_expire'];
			$status['user_lasturl'] = $userinfo_hash[$status['user_id']]['session_url'];
			$status['user_ipaddr'] = $userinfo_hash[$status['user_id']]['session_ip'];
			$online_user[$status['user_id']] = $status;
		}

		if ($status['user_bd_day'] == $bd_d && $status['user_bd_month'] == $bd_m)
		{
			$bd_user[$status['user_nick']] = $status;
		}
	}

	$counter_today = 0;
	$picount_today = 0;
	$counter_yesterday = 0;
	$picount_yesterday = 0;

	$getinfo = $SQL->query("SELECT
			calender_counter,
			calender_date,
			calender_picount
		FROM " . pkSQLTAB_CALENDAR . "
		WHERE calender_date='" . pkTIMETODAY . "' 
			OR calender_date='" . (pkTIMETODAY - 86400) . "'
		LIMIT 2");

	while (list($info, $date, $picount) = $SQL->fetch_row($getinfo))
	{
		if ($date == pkTIMETODAY)
		{
			$counter_today = $info;
			$picount_today = $picount;
		}
		else
		{
			$counter_yesterday = $info;
			$picount_yesterday = $picount;
		}
	}

	list($counter_total, $picount_total) = $SQL->fetch_row($SQL->query("SELECT SUM(calender_counter), SUM(calender_picount) FROM " . pkSQLTAB_CALENDAR));

	$counter_total = $counter_total ? $counter_total : 0;

	$phpkit_status['user_counter'] = $counter;
	$phpkit_status['online_guests'] = count($guests_hash);
	$phpkit_status['guests_hash'] = $guests_hash;
	$phpkit_status['online_usercounter'] = count($online_user);
	$phpkit_status['online_user'] = $online_user;
	$phpkit_status['newest_user'] = $userinfo;
	$phpkit_status['bd_user'] = $bd_user;
	$phpkit_status['online_total'] = $phpkit_status['online_usercounter'] + $phpkit_status['online_guests'];

	#stats
	$phpkit_status['counter_today'] = $counter_today;
	$phpkit_status['picount_today'] = $picount_today;
	$phpkit_status['counter_yesterday'] = $counter_yesterday;
	$phpkit_status['picount_yesterday'] = $picount_yesterday;
	$phpkit_status['counter_total'] = $counter_total;
	$phpkit_status['picount_total'] = $picount_total;

	if ($phpkit_status['online_total'] > pkGetConfig('site_mv_count'))
	{
		pkSetConfig('site_mv_count', $phpkit_status['online_total']);
		pkSetConfig('site_mv_time', pkTIME);

		$SQL->query("REPLACE INTO " . pkSQLTAB_CONFIG . " (id,value) VALUES
				('site_mv_time','" . $SQL->f(serialize(pkTIME)) . "'),
				('site_mv_count','" . $SQL->f(serialize($phpkit_status['online_total'])) . "')");
	}

	return $phpkit_status;
}

#END function phpkitstatus

function isonline($userid = 0)
{
	pkLoadFunc('user');

	return pkUserOnline($userid);
}

function rowcolor($row)
{
	switch ($row)
	{
		case 'odd' :
			return 'even';
		case 'even' :
			return 'odd2';
		case 'odd2' :
			return 'even2';
		default :
			return 'odd';
	}
}

function rowcolor2($row)
{
	switch ($row)
	{
		case 'odd' :
			return 'even';
		case 'even2' :
			return 'odd2';
		case 'even' :
			return 'even2';
		default :
			return 'odd';
	}
}

function notifymail($type, $title, $text)
{
	$mail_hash = pkGetConfig('notify_' . $type . '_m');

	if (empty($mail_hash))
	{
		return;
	}

	$mails = explode("\n", $mail_hash);
	if (is_array($mails))
	{
		foreach ($mails as $m)
		{
			$m = trim($m);
			if (emailcheck($m, 0))
			{
				mailsender($m, $title, $text);
			}
		}
	}
}

function notifyim($type, $title, $text)
{
	global $SQL;

	$im_hash = pkGetConfig('notify_' . $type . '_i');
	$sql = '';

	if (empty($im_hash))
	{
		return;
	}

	$ims = explode("\n", $im_hash);
	if (is_array($ims))
	{
		foreach ($ims as $id)
		{
			if ($id == pkGetUservalue('id') || !intval($id) > 0)
			{
				continue;
			}

			$sql .= (empty($sql) ? '' : ',') . intval($id);
		}

		if (!$sql)
		{
			return;
		}

		$query = $SQL->query("SELECT user_id FROM " . pkSQLTAB_USER . " WHERE user_id IN(" . $sql . ") AND user_imoption=1");
		while (list($id) = $SQL->fetch_row($query))
		{
			$SQL->query("INSERT INTO " . pkSQLTAB_USER_PRIVATEMESSAGE . " (im_to, im_title, im_text, im_time, im_delautor) VALUES 
				('" . $id . "','" . $SQL->f($title) . "','" . $SQL->f($text) . "','" . pkTIME . "','1')");
		}
	}
}

function readTemplateDir($basedir = '', $searchstring = '', $option = 0)
{
	if ($basedir == '')
	{
		$basedir = pkDIRPUBLICTPL;
	}

	$template_array = array();

	$templatedir['dir'] = $basedir;

	$a = opendir($templatedir['dir']);
	while ($info = readdir($a))
	{
		if (is_dir($templatedir['dir'] . $info) && $info != '.' && $info != '..')
		{
			$templatedir[$info] = $info;
		}
	}

	if (is_array($templatedir))
	{
		$template_id = '';
		foreach ($templatedir as $dirinfo)
		{
			if ($dirinfo == '')
			{
				continue;
			}

			if ($dirinfo == $templatedir['dir'])
			{
				$dir = $templatedir['dir'];
				$dirinfo = '';
			}
			else
			{
				$dir = $templatedir['dir'] . $dirinfo . '/';
			}

			$a = opendir($dir);
			while ($info = readdir($a))
			{
				if (filecheck($dir . $info) && strstr($info, pkEXTTPL))
				{
					if ($dirinfo != '')
					{
						$i = $dirinfo . str_replace(pkEXTTPL, '', '/' . $info);
					}
					else
					{
						$i = str_replace(pkEXTTPL, '', $info);
					}

					if ($searchstring != '' && !strstr($i, $searchstring))
					{
						continue;
					}

					if ($template_array[$i] == '')
					{
						if ($option == 1)
						{
							$template_array[$i] = implode('', file($dir . $info));
						}
						else
						{
							$template_array[$i] = '<option value="' . $i . '"';

							if ($template_id == $i)
							{
								$template_array[$i] .= ' selected="selected"';
							}

							$template_array[$i] .= '>' . $i . '</option>';
						}
					}
				}
			}
		}
		#END foreach
	}
	#END 

	if (is_array($template_array) && !empty($template_array[0]))
	{
		return $template_array;
	}

	return false;
}

function file_extension($file = '')
{
	$array = explode('.', $file);

	return (($i = count($array)) > 1) ? $array[$i - 1] : NULL;
}


class UPLOAD
{
	function images($file = '', $dir = '.', $filename = '')
	{
		$file['name']; //originaldateiname
		$file['type']; //type der Datei
		$file['tmp_name']; //tmp-pfad
		$file['error']; //fehlermeldeung
		$file['size']; //bytegroesse

		if ($filename == '')
		{
			$filename = $filereturn[1] = $dir . '/' . $file['name'];
		}
		else
		{
			$filename = $filereturn[1] = $dir . '/' . $filename;
		}

		if (@move_uploaded_file($file['tmp_name'], $filereturn[1]))
		{
			$filereturn[0] = TRUE;
		}
		elseif (copy($file['tmp_name'], $filereturn[1]))
		{
			$filereturn[0] = TRUE;
		}
		else
		{
			$filereturn[0] = FALSE;
		}

		if ($filereturn[0] && !filecheck($filereturn[0]))
		{
			$this->chmodfile($filereturn[1]);
		}

		return $filereturn;
	}

	function chmodfile($filename = '')
	{
		if ($filename != '' && @chmod($filename, 0644))
		{
			return true;
		}

		return false;
	}
}


class moderators
{
	function getMods($option, $catid)
	{
		global $SQL, $forumcat_cache, $global_mods, $mod_cache;

		if ($mod_cache == '' && is_array($forumcat_cache))
		{
			$userids = '';
			foreach ($forumcat_cache as $forumcat)
				$userids .= $forumcat['forumcat_mods'];
			$userid = explode("-", $userids);

			$sqlcommand = "SELECT 
					user_nick,
					user_id,
					user_status
				FROM " . pkSQLTAB_USER . "
				WHERE user_status='mod' OR 
					user_status='admin'";

			if (is_array($userid))
			{
				foreach ($userid as $id)
				{
					if (intval($id) > 0)
					{
						$sqlcommand .= " OR user_id='" . intval($id) . "'";
					}
				}
			}

			$getuserinfo = $SQL->query($sqlcommand);
			while ($userinfo = $SQL->fetch_array($getuserinfo))
			{
				$mod_cache[$userinfo['user_id']] = $userinfo;
				$userinfo['user_nick'] = pkEntities($userinfo['user_nick']);

				if ($userinfo['user_status'] == 'admin' || $userinfo['user_status'] == 'mod')
				{
					if ($global_mods != '')
					{
						$global_mods .= ', ';
					}

					eval("\$global_mods.=\"" . pkTpl("forum/moderator") . "\";");
				}
			}

			unset($userinfo);
			unset($userid);
		}

		$forumcat = $forumcat_cache[$catid];
		if ($forumcat['forumcat_mods'] != '' && $forumcat['forumcat_mods'] != '-0-')
		{
			$userid = explode("-", $forumcat['forumcat_mods']);
			$cat_mod = '';

			foreach ($userid as $id)
			{
				if (!isset($mod_cache[$id]))
				{
					continue;
				}

				$userinfo = $mod_cache[$id];
				$userinfo['user_nick'] = pkEntities($userinfo['user_nick']);

				if ($userinfo != '')
				{
					if (!empty($cat_mod))
					{
						$cat_mod .= ", ";
					}

					eval("\$cat_mod.=\"" . pkTpl("forum/moderator") . "\";");
				}
			}
		}
		else
		{
			$cat_mod = $global_mods;
		}

		return $cat_mod;
	}
}


class smilies
{
	function getSmilieCache()
	{
		global $SQL, $smilie_cache;

		if (empty($smilie_cache))
		{
			$getsmilies = $SQL->query("SELECT * FROM " . pkSQLTAB_SMILIES);
			while ($smilies = $SQL->fetch_array($getsmilies))
			{
				$smilies['smilie_path'] = pkDIRWWWROOT . $smilies['smilie_path'];
				$smilie_cache[] = $smilies;
			}
		}

		return $smilie_cache;
	}


	function getSmilies($option = 0, $isadmindir = 0)
	{
		$smilie_cache = $this->getSmilieCache();
		$format_smilies = '';

		if (is_array($smilie_cache))
		{
			$row = $smilies_row = $count = $smilies_more = $align = $rowclass = '';
			foreach ($smilie_cache as $smilies)
			{
				$smilies['smilie_code'] = pkEntities($smilies['smilie_code']);
				$smilies['smilie_title'] = pkEntities($smilies['smilie_title']);

				if ($option == $smilies['smilie_option'] || $option == "all")
				{
					if ($option == 1)
					{
						eval("\$smilies_row.=\"" . pkTpl("format_smilies_row") . "\";");
					}
					elseif ($option == 'all')
					{
						$row = rowcolor($row);
						eval("\$format_smilies.=\"" . pkTpl("format_allsmilies_row") . "\";");
					}
					else
					{
						$align = $align == 'left' ? 'right' : 'left';

						if ($align == 'left')
						{
							$format_smilies .= empty($format_smilies) ? '<tr>' : '</tr><tr>';
							$rowclass = rowcolor($rowclass);
						}

						eval("\$format_smilies.=\"" . pkTpl("smiliewindow_bit") . "\";");
					}
				}

				if ($option == "1" && $smilies['smilie_option'] != 1)
				{
					$count = 1;
				}
			}
		}

		if ($option == 1)
		{
			if ($count == 1)
			{
				eval("\$smilies_more=\"" . pkTpl("format_smilies_morelink") . "\";");
			}

			eval("\$format_smilies=\"" . pkTpl("format_smilies") . "\";");
		}

		elseif ($option == 0)
		{
			if ($align == 'left')
			{
				$smilies['smilie_path'] = pkDIRWWWROOT . 'images/blank.gif';

				$smilies['smilie_code'] = '&nbsp;';

				eval("\$format_smilies.=\"" . pkTpl("smiliewindow_bit") . "\";");
				$format_smilies .= '</tr>';
			}
		}

		return $format_smilies;
	}
}


function adminaccess($loc = '')
{
	global $SQL, $ADMINACCESS;

	if (pkGetUservalue('status') == 'admin')
	{
		return true;
	}

	if (!pkGetUservalue('group') > 0)
	{
		return false;
	}

	if (!is_array($ADMINACCESS) || $ADMINACCESS == NULL)
	{
		$ADMINACCESS = array(
		);

		if (pkGetUservalue('group'))
		{
			$ADMINACCESS = $SQL->fetch_assoc($SQL->query("SELECT * FROM " . pkSQLTAB_USER_GROUP . " WHERE usergroup_id='" . pkGetUservalue('group') . "' LIMIT 1"));
		}

		if (!is_array($ADMINACCESS))
		{
			$ADMINACCESS = array(
			);
		}
	}

	if ($loc == 'adminarea')
	{
		foreach ($ADMINACCESS as $k => $v)
		{
			if ($v == 1 && substr($k, 0, 7) == 'access_' && $k != 'access_gbdelete' && $k != 'access_gbedit')
			{
				return true;
			}
		}
	}

	if ($loc == 'cms')
	{
		foreach ($ADMINACCESS as $k => $v)
		{
			if ($v == 1 && substr($k, 0, 7) == 'access_' && ($k == 'access_content' || $k == 'access_article' || $k == 'access_news' || $k == 'access_links' || $k == 'access_download' || $k == 'access_submit' || $k == 'access_contentcat'))
			{
				return true;
			}
		}
	}
	else
	{
		$loc = 'access_' . $loc;
		if ($ADMINACCESS[$loc] == 1)
		{
			return true;
		}
	}

	return false;
}

function getrights($needed)
{
	if (pkGetUservalue('status') == "admin")
	{
		return true;
	}

	if ($needed == 'none' || empty($needed))
	{
		return false;
	}

	if (pkGetUservalue('status') == 'mod' && $needed != 'admin')
	{
		return true;
	}

	if (pkGetUservalue('status') == 'member' && ($needed == 'member' || $needed == 'user' || $needed == 'guest'))
	{
		return true;
	}

	if (pkGetUservalue('status') == 'user' && ($needed == 'user' || $needed == 'guest'))
	{
		return true;
	}

	if (pkGetUservalue('status') == 'guest' && $needed == 'guest')
	{
		return true;
	}

	return false;
}

function userrights($user, $status = 'mod')
{
	if (!intval(pkGetUservalue('id')))
	{
		return false;
	}

	if (pkGetUservalue('status') == 'admin')
	{
		return true;
	}

	if (!empty($status) && $status != 'admin' && pkGetUservalue('status') == 'mod')
	{
		return true;
	}

	if (strstr($user, '-' . pkGetUserValue('id') . '-'))
	{
		return true;
	}

	return false;
}

function sqlrights($sql)
{
	$sqlcommand = "(" . $sql . "='guest'";

	if (pkGetUservalue('status') == "user")
	{
		$sqlcommand .= " OR " . $sql . "='user')";
	}
	elseif (pkGetUservalue('status') == "member")
	{
		$sqlcommand .= " OR " . $sql . "='user' OR " . $sql . "='member')";
	}
	elseif (pkGetUservalue('status') == "mod")
	{
		$sqlcommand .= " OR " . $sql . "='user' OR " . $sql . "='member' OR " . $sql . "='mod')";
	}
	elseif (pkGetUservalue('status') == "admin")
	{
		$sqlcommand .= " OR " . $sql . "='user' OR " . $sql . "='member' OR " . $sql . "='mod' OR " . $sql . "='admin')";
	}
	else
	{
		$sqlcommand .= ")";
	}

	return $sqlcommand;
}

function pkLicencekeyCheck($key)
{
	return (md5($key) == 'pkLICENCEKEYCRYPT') ? true : false;
}


function sanitize($data){

    //remove spaces from the input

    $data=trim($data);

    //convert special characters to html entities
    //most hacking inputs in XSS are HTML in nature, so converting them to special characters so that they are not harmful

    $data=htmlspecialchars($data);

    //sanitize before using any MySQL database queries
    //this will escape quotes in the input.

    $data = mysql_real_escape_string($data);
    return $data;
}

?>