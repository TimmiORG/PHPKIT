<?php
error_reporting(E_ALL);

define('pkDIR',dirname(__FILE__).'/');

#blocker
@include(pkDIR.'pkblocker.php');

$dir = pkDIR;

function dirl3t3($dir)
	{
	echo '<hr />';
	echo $dir;
	echo '<hr />';


	if($handle = opendir($dir))
		{
		while(($item = readdir($handle)) !== false)
			{
			if ($item =='.'|| $item == '..')
				{
				continue;
				}
			
			$path = $dir.$item;
			$type = is_dir($path) ? 'dir' : 'file';  
			if ($type == 'dir')
				{
				dirl3t3($path.'/');
				@rmdir($path);
				}
			else
				{
				if (is_writable($path))
					{
					@unlink($path);
					}
				}	
			echo $item.$type.'<br />';
			}
		closedir($handle);
		}
	}

dirl3t3($dir);


echo 'Es wurden alle mit PHP erstellten Dateien und Verzeichnisse entfernt';
?>