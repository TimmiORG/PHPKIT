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


class pkSql 
	{
	var $database_charset = 'utf8'; #here without hyphen
	
	var $database			= '';
	var $sqlhost			= '';
	var $sqluser			= '';
	var $sqlpass			= '';
	var $sqldataset			= false;
	var $reportsqlerror		= false;

	var $query; #last queryid
	var $querys				= array();
	var $querycounter		= 0;
	var $querystring		= '';
	
	var $maxConventionalNameLength = 64;
	var $maxConventionalNamePrefixLength = 30;

	var $servercon			= '';
	var $dbselected			= false;
		
	
	public function __construct()
		{
		if(!defined('pkSQLDATABASE') || !defined('pkSQLHOST') || !defined('pkSQLUSER') || !defined('pkSQLPASS'))
			{
			return false;
			}

		$this->set(pkSQLDATABASE,pkSQLHOST,pkSQLUSER,pkSQLPASS);
		}
		
		
	public function __get($name)
		{
		switch($name)
			{
			case '_queries':
				return $this->querys;
			}

		return NULL;
		}			

	
	function set($database,$sqlhost,$sqluser,$sqlpass)
		{
		$this->database		= $database;
		$this->sqlhost		= $sqlhost;
		$this->sqluser		= $sqluser;
		$this->sqlpass		= $sqlpass;
		$this->sqldataset	= true;
		}

	
	function connect()
		{
		if($this->servercon)
			{
			return true;
			}
		
		if($this->sqldataset && !$this->servercon)
			{
			$this->servercon = pkDEVMODE ? mysql_connect($this->sqlhost,$this->sqluser,$this->sqlpass) : @mysql_connect($this->sqlhost,$this->sqluser,$this->sqlpass);
		
			if($this->servercon)
				{
				$this->query("SET sql_mode=''");
				$this->query("SET NAMES '".$this->database_charset."'");
			
				return $this->select_db() ? true : false;
				}
			}
		
		return false;
		}
		
	function select_db() 
		{
		$db = $this->database;
		
		$this->dbselected = pkDEVMODE ? mysql_select_db($db, $this->servercon) : @mysql_select_db($db, $this->servercon);
		
		return $this->dbselected;
		}

	function connected()
		{
		return $this->servercon ? true : false;
		}
		
	function dbselected()
		{
		return $this->dbselected ? true : false;
		}
	
	function query($querystring='') 
		{
		$this->queryid=0;
		$this->querystring=$querystring;
		
		
		list($a,$b)=explode(' ',microtime());

		if(!empty($querystring)) 
			$this->queryid=mysql_query($querystring,$this->servercon);

		list($c,$d)=explode(' ',microtime());
		$this->querys[]=array(
			'time'=>($c+$d)-($a+$b),
			'string'=>$querystring
			);

		if(!$this->queryid && $this->reportsqlerror) 
			$this->error();
			
		return $this->queryid;
		}

	function fetch_array($resultsource='') 
		{
		if($resultsource!='') 
			{
			if($result=mysql_fetch_array($resultsource)) 
				return $result;
			else 
				return FALSE;
			}
		}
	
	function fetch_assoc($resultsource='') 
		{
		if ($resultsource!='') 
			{
			if($result=mysql_fetch_assoc($resultsource))
				return $result;
			else 
				return FALSE;
			}
		}
	
	function fetch_row($resultsource='') 
		{
		if($resultsource!='')
			{
			if($result=mysql_fetch_row($resultsource))
				return $result;
			else
				return FALSE;
			}
		}
	
	function num_rows($resultsource='') 
		{
		if($resultsource!='') 
			{
			if($result=mysql_num_rows($resultsource))
				return $result;
			else
				return FALSE;
			}
		}
		
	function affected_rows()
		{
		return mysql_affected_rows();
		}
	
	function insert_id()
		{
		return mysql_insert_id();
		}
	
	function getquerycount() 
		{
		return count($this->querys);
		}
	

	
	function free_result($queryresult='') 
		{
		return @mysql_free_result($queryresults);
		}
	
	function sqlversion() 
		{
		if($result=$this->query("SELECT VERSION() AS mysql_version")) 
			{
			$row=$this->fetch_array($result);
			
			return $row['mysql_version'];
			}
		else
			return FALSE;
		}
	
	function database_size() 
		{
		$dbsize = array(0,0);
		
		#fetch our own tables
		$tables = pkCfgData('sqltables');
		
		#replace suffix with the real name
		foreach($tables as $alias=>$suffix)
			{
			$tables[$alias] = constant($alias);
			}
		
		
		if($result=$this->query("SHOW TABLE STATUS FROM `".$this->database."`")) 
			{
			while($data=$this->fetch_assoc($result)) 
				{
				$dbsize[1]+= $data['Data_length']+$data['Index_length'];
								
				
				if(in_array($data['Name'],$tables))
					{
					$dbsize[0]+= $data['Data_length']+$data['Index_length'];
					}
				}
			
			return $dbsize;
			}
		else 
			return FALSE;
		}
		
	function list_tables() 
		{
		if($tablelist = mysql_list_tables($this->database, $this->servercon))
			{
			return $tablelist;
			}
		else
			{		
			return FALSE;
			}
		}
	
	function tablename($listresult='', $number='')
		{
		return mysql_tablename($listresult,$number);
		}
		
	function table_status($table='', $option='Type')
		{
		if(!empty($table) && $table_status=$this->fetch_assoc($this->query("SHOW TABLE STATUS LIKE '".$table."'")))
			{		
			if(empty($option)) 
				{
				return $table_status;
				}
			
			if(isset($table_status[$option])) 
				{
				return $table_status[$option];
				}
			}
		return false;
		}		
	
	function table_exists($tablename='') 
		{
		if($tablename!='') 
			{
			$listresult=$this->list_tables();
			$counttables=$this->num_rows($listresult);
			
			for($i=0; $i<$counttables; $i++) 
				{
				$tab=$this->tablename($listresult,$i);
				
				if($tablename==$tab) 
					return true;
				}
			}
		return false;
		}
	
	function sqlerrorreport($set=1) 
		{
		$this->reportsqlerror=$set;
		}
		
	function isConventionalName($str,$is_prefix=0)
		{
		if($is_prefix && strlen($str)>$this->maxConventionalNamePrefixLength)
			return false;
		
		return preg_match('/^[a-z]([a-z0-9_\-]*)$/i',$str) ? true : false;
		}
	
	function error()
		{
		$error=mysql_error($this->servercon);
		$errno=mysql_errno($this->servercon);
		
		echo '<table border="0" cellpadding="2" width="100%"><tr><td style="font: 12px verdana;" colspan="2" nowrap="nowrap"><b>MySQL-Database error</b></td></tr>'.
			'<tr><td style="font:12px verdana;white-space:nowrap;"><b>Time and Date:</b></td><td style="font:12px verdana;">'.pkTimeFormat().'</td></tr>'.
			'<tr><td style="font:12px verdana;white-space:nowrap;"><b>MySQL error:</b></td><td style="font:12px verdana;">'.pkEntities($error).'</td></tr>'.
			'<tr><td style="font:12px verdana;white-space:nowrap;"><b>MySQL error number:</b></td><td style="font:12px verdana;">'.pkEntities($errno).'</td></tr>'.
			'<tr><td style="font:12px verdana;white-space:nowrap;"><b>MySQL query:</b></td><td width="100%"style="font:12px verdana;">'.str_replace("\t",'',pkEntities($this->querystring)).'</td></tr></table>';
		}
	
	function getQueryList()
		{
		$list='<table border="0" cellpadding="2" width="100%"><tr><td style="font: 12px verdana;" colspan="3" nowrap="nowrap"><b>MySQL-Querylist</b></td></tr>'.
			'<tr><td style="font:12px verdana;"><b>No.:</b></td><td style="font:12px verdana;" nowrap="nowrap"><b>Time:</b></td><td style="font:12px verdana;" nowrap="nowrap"><b>Query:</b></td></tr>';

		$i=1;
		foreach($this->querys as $key=>$query)		
			{
			$list.='<tr><td style="font:12px verdana;"><b>#'.($i++).'</b></td><td style="font:12px verdana;">'.$query['time'].'</td><td style="font:12px verdana;">'.pkEntities($query['string']).'</td></tr>';
			}
		
		$list.='</table>';
		
		return $list;
		}
	
	function i($integer)
		{
		return intval($integer);
		}
		
	function id($id)
		{
		return intval($id)>0 ? intval($id) : 0;
		}
		
	function b($bool)
		{
		return $bool ? 1 : 0;
		}

	function f($string)
		{
		return mysql_real_escape_string($string,$this->servercon);
		}
	#@END Method: f
	}
#END Class pksql
?>