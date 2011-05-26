<?php
error_reporting(E_ALL);

define('pkDIR',dirname(__FILE__).'/');

function pkFileCheck($file)
	{
	if(!($fp=@fopen($file,'r')))
		return false;
	
	fclose($fp);
	return true;
	}

#blocker
@include(pkDIR.'pkblocker.php');

$path = pkDIR.'pkinc/etc/spw.php';

if(pkFileCheck($path))
	{
	@unlink($path);
	clearstatcache();
	
	if(pkFileCheck($path))
		die('setup password could not unset');
	else
		die('setup password unset');
	}

die('no setup password found');
?>