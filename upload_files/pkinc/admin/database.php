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


if(!adminaccess('database'))
	return pkEvent('access_forbidden');


$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';


if($ACTION==$_POST['cancel']) 
	{
	pkHeaderLocation('database');
	}


if ($ACTION==$_POST['export'] && is_array($_POST['export_tabs']))
	{
	$content ="#############################################\n";
	$content.="#\n";
	$content.="# PHPKIT Database Backup ".formattime(time())."\n";
	$content.="# Database ".$database."@".$_ENV['SERVER_NAME']."\n";
	$content.="#\n";
	$content.="#############################################\n";
	$content.="\n\n";
	
	
	foreach($_POST['export_tabs'] as $tab)
		{
		if($_POST['export_structure']==1 || $_POST['export_structure']==2) 
			{
			$content.="###########\n";
			$content.="# Table ".$tab."\n";
			$content.="#\n";
			$content.="\n";
			
			if($_POST['export_drop']==1)
				$content.="DROP TABLE IF EXISTS ".$tab.";\n";
				
			$content.="CREATE TABLE ".$tab." (\n";


			$c=0;

			$getinfo=$SQL->query("DESCRIBE ".$tab."");
			$count=$SQL->num_rows($getinfo);
			while($info=$SQL->fetch_array($getinfo))
				{
				$tab_name=$info['Field'];
				$tab_type=" ".$info['Type'];
				
				if($info['Null']=='')
					$tab_null=" NOT NULL";
				else 
					$tab_null=" NULL";
				
				if($info['Default']=='')
					$tab_default='';
				else
					$tab_default=" DEFAULT '".$info['Default']."'";
				
				if($info['Extra']=='')
					$tab_extra='';
				else
					$tab_extra=" ".$info['Extra'];
				
				$c++;
				
				if($c<$count)
					$tab_komma=",\n";
				else
					$tab_komma='';
				
				$content.=" ".$tab_name.$tab_type.$tab_null.$tab_default.$tab_extra.$tab_komma;
				}

			unset($keyarray);
			
			$getinfo=$SQL->query("SHOW KEYS FROM ".$tab."");
			while($info=$SQL->fetch_array($getinfo))
				{
				$keyname=$info['Key_name'];
				
				$comment=(isset($info['Comment'])) ? $info['Comment'] : "";
				$sub_part=(isset($info['Sub_part'])) ? $info['Sub_part'] : "";
				
				
				if($keyname!="PRIMARY" && $info['Non_unique']==0) 
					$keyname="UNIQUE|$keyname";
				
				if($comment=="FULLTEXT")
					$keyname="FULLTEXT|$keyname";
					
				if(!isset($keyarray[$keyname]))
					$keyarray[$keyname]=array();
				
				if($sub_part>1)
					$keyarray[$keyname][]=$info['Column_name']."(".$sub_part.")";
				else
					$keyarray[$keyname][]=$info['Column_name'];
				}
			
			
			if(is_array($keyarray))
				{
				foreach($keyarray as $keyname => $columns)
					{
					$content.=",\n";
					
					if($keyname=="PRIMARY")
						$content.="PRIMARY KEY (";
					elseif(substr($keyname,0,6)=="UNIQUE")
						$content.="UNIQUE ".substr($keyname, 7)." (";
					elseif(substr($keyname,0,8)=="FULLTEXT")
						$content.="FULLTEXT ".substr($keyname, 9)." (";
					else
						$content.="KEY ".$keyname.' (';
					
					$content.=implode($columns,", ").")";
					}
				}
			
			$content.=");\n";
			$content.="\n";
			}
		
		
		if($_POST['export_structure']!=2)
			{
			if($_POST['export_delete']==1)
				$content.="DELETE FROM ".$tab.";\n";
			
			$queryresult=$SQL->query("SELECT * FROM ".$tab);
			while($info=$SQL->fetch_assoc($queryresult))
				{
				unset($values);
				unset($fieldnames);
				
				if($_POST['export_fullinserts']==1)
					{
					foreach($info as $name=>$field)
						{
						if($fieldnames)
							$fieldnames.=",".$name;
						else
							$fieldnames=$name;
						
						if($values)
							$values.=",".formatfield($field); 
						else
							$values=formatfield($field);
						}
					
					$content.="INSERT INTO ".$tab." (".$fieldnames.") VALUES (".$values.");\n";
					}
				else
					{
					foreach($info as $field)
						{
						if($values)
							$values.=",".formatfield($field); 
						else
							$values=formatfield($field);
						}
					
					$content.="INSERT INTO ".$tab." VALUES (".$values.");\n";
					}
				}
			
			$content.="\n";
			$SQL->free_result($queryresult);
			}
		}
	
	
	if($_POST['export_option']==1)
		{
		if(@touch("../tmp/phpkit.sql"))
			{
			$fp=fopen("../tmp/phpkit.sql","w");
			fwrite($fp,$content);
			fclose($fp);
			}
		
		pkHeaderLocation('database');
		}

	if(strstr($_SERVER['HTTP_USER_AGENT'],'Opera') || strstr($_ENV['HTTP_USER_AGENT'],'Opera') || strstr($_ENV['HTTP_USER_AGENT'],'IE') || strstr($_SERVER['HTTP_USER_AGENT'],'IE'))
		$content_type='application/octetstream';
	else
		$content_type='application/octet-stream';
		
	header('Content-Type: '.$content_type);
	
	if(strstr($_ENV['HTTP_USER_AGENT'],'IE') || strstr($_SERVER['HTTP_USER_AGENT'],'IE'))
		{
		header('Content-Disposition: inline; filename="phpkit.sql"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		}
	else
		{
		header('Content-Disposition: attachment; filename="phpkit.sql"');
		header('Expires: 0');
		header('Pragma: no-cache');
		}
	
		
	echo $content;
	exit;
	}

if($_REQUEST['query']=='CHECK' || $_REQUEST['query']=='REPAIR' || $_REQUEST['query']=='OPTIMIZE')
	{
	unset($sqlcommand);
	unset($database_row);
	
	$list_tables=$SQL->list_tables();
	while($tables=$SQL->fetch_row($list_tables))
		{
		if($sqlcommand)
			$sqlcommand.=",`".$tables[0]."`";
		else
			$sqlcommand.=$_REQUEST['query']." TABLE `".$tables[0]."`";
		}
	
	$query=$SQL->query($sqlcommand);
	while($result=$SQL->fetch_assoc($query))
		{
		$row=rowcolor($row);
		eval("\$database_row.=\"".pkTpl("database_row")."\";");
		}
	
	if(!$database_row)
		{
		pkHeaderLocation('database');
		}


	if($_REQUEST['query']=='CHECK')
		eval("\$site_body.=\"".pkTpl("database_check")."\";");
	elseif($_REQUEST['query']=='REPAIR')
		eval("\$site_body.=\"".pkTpl("database_repair")."\";");
	elseif($_REQUEST['query']=='OPTIMIZE')
		eval("\$site_body.=\"".pkTpl("database_optimize")."\";");
	}

else
	{
	$tabcount=0;
	
	$list_tables=$SQL->list_tables();
	while($tables=$SQL->fetch_row($list_tables))
		{
		$tablist.='<option>'.$tables[0].'</option>';
		$tabcount++;
		}
	
	eval("\$site_body.=\"".pkTpl("database")."\";");
	}
?>