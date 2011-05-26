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


if(!defined('pkFRONTEND'))
	return;


class pkSqlutilities
	{

	var $table='';
	var $cols='';
	var $keys='';
	var $tableHash=array();
	var $def=array();

	var $mode=0;			//0 = check and create - 1 = check and alter
	var $dropCols=0;
	var $dropKeys=0;

	var $message=array();
	var $errorCount=0;
	var $errorFixed=0;
	var $skippedCols=0;
	var $skippedKeys=0;
	
	var $exportHash=array();
	var $databaseSize=0;

	function __construct()
		{
		global $SQL;

		$this->SQL = &$SQL;
		$this->tableHash = include(pkDIRCFG.'sqltables'.pkEXT);
		}

	function pkSqlUtilities()
		{
		$this->__construct();
		}

	function checkTables()
		{
		foreach($this->tableHash as $alias=>$suffix)
			{
			$this->checkTable($alias);
			}
		}

	function checkTable($table)
		{
		if(!$this->setTable($table))
			return false;

		$exists=false;

		if($this->SQL->table_exists($this->table))
			{
			$message='setup_table_already_exists';
			$exists=true;
			}
		else
			{
			$message=$this->createTable() ? 'setup_table_successful_created' : 'setup_table_could_not_created';
			}

		$this->message(pkGetSpecialLang($message,$this->table));

		if($this->mode==1 && $exists==true)
			{
			$this->checkFull();
			}
		}

	function checkFull()
		{
		if(!isset($this->def[$this->table]))
			return;

		#check all Cols		
		$this->checkTableCols();

		#check the Keys
		$this->message(pkGetLang('setup_check_keys'));
		$this->checkTableKeys();

		#check Table-Type
		$msg=$this->checkTableType();
		$this->message(pkGetSpecialLang('setup_check_tabletype',$this->def[$this->table]['type']).$msg);

		$this->SQL->free_result();
		}


	function checkCol($Field,$col)
		{
		$check=true;

		if(!$this->checKCol_Type($Field, $col['Type'])) #also checks the charset
			{
			$check=false;
			}
		elseif((empty($this->ColsHash[$Field]['Null']) && strtoupper($col['Null'])=='NO') || (!empty($this->ColsHash[$Field]['Null']) && strtoupper($col['Null'])=='YES'))
			{
			$check=false;
			}
		elseif(strtolower($col['Extra'])!=strtolower($this->ColsHash[$Field]['Extra']))
			{
			$check=false;
			}
		elseif(isset($this->ColsHash[$Field]['Default']) && $col['Default']!=$this->ColsHash[$Field]['Default'])
			{
			$check=false;
			}
		elseif($this->ColsHash[$Field]['Key']=='PRI' && !empty($col['Null']))
			{
			$check=false;
			}


		if($check==true)
			{
			$return=pkGetLang('OK');
			}
		else
			{
			$this->errorCount++;

			if($this->SQL->query("ALTER TABLE ".$this->table." CHANGE ".$Field."  ".$this->col($Field,$col)))
				{
				$return = pkGetLang('repaired');
				$this->errorFixed++;
				}
			else
				{
				$return=pkGetLang('unrepaired');
				}
			}

		unset($this->ColsHash[$Field]);
		return $return;
		}


	function checkCol_Type($Field,$type)
		{
		$type = strtolower($type);

		if(strstr($type,'binary'))
			{
			#check the collation first
			if($this->ColsHash[$Field]['Collation']!='utf8_bin')
				{
				return false;
				}

			#Type not Binary - extract the BINARY keyword
			#1. replace BINARY with an empty string
			#2. remove whitespaces
			$type = trim(str_replace('binary','',$type));
			}
		elseif(!empty($this->ColsHash[$Field]['Collation']))
			{
			if($this->ColsHash[$Field]['Collation']!='utf8_general_ci')
				{
				return false;
				}
			}

		return $type==strtolower($this->ColsHash[$Field]['Type']) ? true : false;
		}


	function checkTableCols()
		{
		$this->ColsHash=array();

		$result=$this->SQL->query("SHOW FULL COLUMNS FROM ".$this->table);
		while($a=$this->SQL->fetch_assoc($result))
			{
			$this->ColsHash[$a['Field']]=$a;
			}


		#check cols by defintion
		foreach($this->def[$this->table]['cols'] as $Field=>$col)
			{
			if(!isset($this->ColsHash[$Field]))
				{
				$this->errorCount++;
				$this->message(pkGetSpecialLang('setup_col_missing',$Field));

				if($this->SQL->query("ALTER TABLE ".$this->table." ADD ".$this->col($Field,$col)))
					{
					$this->message(pkGetLang('created'));
					$this->errorFixed++;
					}
				else
					{
					$this->message(pkGetLang('setup_could_not_created'));
					}
				}
			else
				{
				$msg=$this->checkCol($Field,$col);
				$this->message(pkGetSpecialLang('setup_col_found',$Field).$msg);
				}
			}


		if(empty($this->ColsHash))
			{
			return;
			}


		#if additional cols are left
		foreach($this->ColsHash as $Field)
			{
			if($this->dropCols==1 && isset($this->def[$this->table]['note']) && $this->def[$this->table]['note']!='skip')
				{
				$bool = $this->SQL->query("ALTER TABLE ".$this->table." DROP ". $Field['Field']);
				$this->message(pkGetSpecialLang(($bool ? 'setup_col_delete' : 'setup_col_could_not_delete'),$Field['Field']));
				}
			else
				{
				$this->message(pkGetSpecialLang('setup_col_skipped',$Field['Field']));
				$this->skippedCols++;
				}
			}
		}


	function checkTableKeys()
		{
		$this->keyHash=array();

		$result=$this->SQL->query("SHOW KEYS FROM ".$this->table);
		while($info=$this->SQL->fetch_assoc($result))
			{
			$this->keyHash[]=$info;
			}

		if(count($this->keyHash)<1)
			{
			$this->message(pkGetLang('setup_no_keys_defined'));
			}


		foreach($this->def[$this->table]['keys'] as $keys)
			{
			$key = $keys['type'];
			$col = $keys['cols'];
			$name = $keys['name'];

			$check = strstr($keys['cols'],",") ? $this->checkKeyMulti($key,$col) : $this->checkKey($key,$col);

			$message = $key.' '.$col.' ';

			if($check==true)
				{
				$this->message($message.pkGetLang('OK'));
				}
			else
				{
				$this->errorCount++;


				if(!empty($keyPrimary) && $keyPrimary==$col)
					{
					$this->message($message.pkGetLang('setup_primary_key_alreade_exists'));
					}

				if($key=='PRIMARY')
					{
					$this->SQL->query("ALTER TABLE ".$this->table." DROP PRIMARY KEY");
					$sql = "ALTER TABLE ".$this->table." ADD PRIMARY KEY (".$col.")";
					}
				elseif($key=='UNIQUE')
					{
					$sql = "ALTER TABLE ".$this->table." ADD UNIQUE KEY (".$col.")";
					}
				else
					{
					$sql = "ALTER TABLE ".$this->table." ADD KEY (".$col.")";
					}

				if($this->SQL->query($sql))
					{
					$this->message($message.pkGetLang('repaired'));
					$this->errorFixed++;
					}
				else
					{
					$this->message($message.pkGetLang('unrepaired'));
					}
				}
			}

		if(!empty($keyHash))
			{
			$this->message(pkGetLang('setup_undefined_keys'));
			$this->skippedKeys=true;

			foreach($keyHash as $key)
				{
				$message=$key['Column_name'];

				if($this->dropKeys==1)
					{
					$b=$this->SQL->query("ALTER TABLE ".$this->table." DROP INDEX ".$key['Key_name']);
					$this->message($message.pkGetLang($b ?'dropped' : 'could_not_dropped'));
					}
				else
					{
					$this->message($message.pkGetLang('skipped'));
					}
				}
			}
		}


	function checkKey($key,$col)
		{
		foreach($this->keyHash as $i=>$nomatter)
			{
			if($this->keyHash[$i]['Column_name']!=$col)
				continue;

			if($key==$this->keyHash[$i]['Key_name'])
				{
				if($this->keyHash[$i]['Key_name']=='PRIMARY' && isset($this->keyHash[$i+1]) && $col==$this->keyHash[$i+1]['Column_name'])
					{
					unset($this->keyHash[$i+1]);
					}

				return true;
				}
			elseif($key=='UNIQUE' && $this->keyHash[$i]['Non_unique']==0)
				{
				unset($this->keyHash[$i]);
				return true;
				}
			elseif($key=='INDEX' && $this->keyHash[$i]['Non_unique']==1)
				{
				unset($this->keyHash[$i]);
				return true;
				}
			}#END foreach keyHash

		return false;
		}


	function checkKeyMulti($key,$col)
		{
		$multicheck=array();
		$multi=explode(",",$col);
		$check=false;

		foreach($multi as $k=>$v)
			{
			$multicheck[$k]=false;

			foreach($this->keyHash as $i=>$nomatter)
				{
				if(($this->keyHash[$i]['Key_name']!='PRIMARY' && $this->keyHash[$i]['Key_name']!=$multi[0]) || $this->keyHash[$i]['Column_name']!=trim($v))
					continue;

				if($this->keyHash[$i]['Seq_in_index']==$k+1 && $this->keyHash[$i]['Column_name']==trim($v))
					{
					$multicheck[$k]=array(true,$i,$k,$v);
					break;
					}
				}
			}

		foreach($multicheck as $v)
			{
			if($v[0]==true)
				$check=true;
			else
				{
				$check=false;
				break;
				}
			}

		if($check==true)
			{
			foreach($multicheck as $v)
				{
				unset($this->keyHash[$v[1]]);
				}
			}

		return $check;
		}


	function checkTableType()
		{
		$type = $this->SQL->table_status($this->table,'Engine');
		$collation = $this->SQL->table_status($this->table,'Collation');


		if($this->def[$this->table]['type']==$type && $collation=='utf8_general_ci')
			{
			return pkGetLang('OK');
			}

		$this->errorCount++;

		if($this->SQL->query("ALTER TABLE ".$this->table." ENGINE=".$this->def[$this->table]['type']." ".$this->charset()))
			{
			$this->errorFixed++;
			return pkGetLang('repaired');
			}

		return pkGetLang('unrepaired');
		}


	function createTable()
		{
		$this->createCols();
		$this->createKeys();
		$this->createType();

		return $this->SQL->query("CREATE TABLE ".$this->table." (".$this->cols." ".$this->keys.") ENGINE=".$this->type." ".$this->charset());
		}


	function createCols()
		{
		$this->cols='';
		$array = isset($this->def[$this->table]['cols']) ? $this->def[$this->table]['cols'] : array();

		foreach($array as $Field=>$col)
			{
			$this->cols.=(empty($this->cols) ? '' : ',') . $this->col($Field,$col);
			}
		}


	function createKeys()
		{
		$this->keys=$keys='';

		if(!is_array($this->def[$this->table]['keys']))
			return;

		foreach($this->def[$this->table]['keys'] as $keydef)
			{
			$key=($keydef['type']=='INDEX') ? ' KEY ' : $keydef['type'].' KEY';

			if(!empty($this->cols))
				$keys.=",";

			if(strstr($keydef['cols'],","))
				{
				$multi=explode(",",$keydef['cols']);
				$value='';

				for($i=0; $i<count($multi); $i++)
					{
					if(!empty($value)) $value.=',';
					if($i==0) $key.=' '.$multi[0];
					$value.=$multi[$i];
					}
				}
			else
				$value=$keydef['cols'];

			$keys.=$key."(".$value.")";
			}

		$this->keys=$keys;
		}


	function cleanTable($constant,$fields="*")
		{
		if(!defined($constant))
			return NULL;

		if(!array_key_exists(constant($constant),$this->def))
			return NULL;

		$table=constant($constant);
		$tabledef=$this->def[$table];


		$result=$this->SQL->query("SELECT ".$fields." FROM ".$table);
		$count=intval($this->SQL->num_rows($result));
		if($count<1)
			$count=0;

		#check also the key cols
		if($fields=="*")
			{
			$this->message(pkGetSpecialLang('setup_table_data_records_total',$count,$table));

			foreach($tabledef['keys'] as $keydef)
				{
				if($keydef['type']=='PRIMARY' || $keydef['type']=='UNIQUE')
					{
					$this->cleanTable($constant,$keydef['cols']);
					}
				}
			}

		if($count<1)
			return;

		$total=0;
		$hash=array();

		set_time_limit(0);

		while($row=$this->SQL->fetch_assoc($result))
			{
			$sql=$values='';
			foreach($row as $key=>$value)
				{
				$sql.=((empty($sql)) ? '' : ' AND ').$key."='".$this->SQL->f($value)."'";
				$values.=$value;
				}

			$values=md5($values);
			if(!isset($hash[$values]))
				{
				$hash[$values]=$values;
				continue;
				}

			list($count)=$this->SQL->fetch_row($this->SQL->query("SELECT COUNT(*) FROM ".$table." WHERE ".$sql));
			if($count<2)
				continue;

			#keep the first record
			$keepme=$this->SQL->fetch_assoc($this->SQL->query("SELECT * FROM ".$table." WHERE ".$sql." LIMIT 1"));

			$c=$count-1;
			$total+=$c;
			$this->SQL->query("DELETE FROM ".$table." WHERE ".$sql);

			$sql='';
			foreach($keepme as $key=>$value)
				$sql.=(empty($sql) ? '':',').$key."='".$this->SQL->f($value)."'";

			$this->SQL->query("INSERT INTO ".$table." SET ".$sql);
			}

		$this->SQL->free_result();
		$this->message(pkGetSpecialLang('setup_data_records_deleted',$total,$fields));
		$this->SQL->query("OPTIMIZE TABLE ".$table);
		}


	function createType()
		{
		$this->type=(isset($this->def[$this->table]['type']) && !empty($this->def[$this->table]['type'])) ? $this->def[$this->table]['type'] : 'MYISAM';
		}


	function setDefinition($def='')
		{
		if(!is_array($def))
			return;

		$this->def=$def;
		}


	function setMode($mode=0)
		{
		$this->mode=$mode;
		}


	function setExportTables($array)
		{
		if(!is_array($array))
			return false;

		$this->exportHash=$array;
		return true;
		}

	function setTable($table)
		{
		$this->table=defined($table) ? constant($table) : NULL;
		return $this->table ? true : false;
		}


	#######################
	#  Output-Methods
	#######################


	function getMessages()
		{
		return $this->message;
		}


	function getErrorCount()
		{
		return $this->errorCount;
		}


	function getErrorFixed()
		{
		return $this->errorFixed;
		}

	function getSkippedCols()
		{
		return $this->skippedCols;
		}

	function getSkippedKeys()
		{
		return $this->skippedKeys;
		}

	function getDBsize()
		{
		if($this->databaseSize>0)
			return $this->databaseSize;

		if(!$result=$this->SQL->query("SHOW TABLE STATUS FROM `".pkSQL_DATABASE."`"))
			return false;

		while($data=$this->SQL->fetch_assoc($result))
			{
			$this->databaseSize += $data['Data_length']+$data['Index_length'];
			}

		return $this->databaseSize;
		}


	function getExportHeader()
		{
		echo 	"#############################################\n".
				"#\n".
				"# PHPKIT Database-Backup ".pkTimeFormatTimeselect()."\n".
				"# Database ".$this->SQL->getDBname()."@".addslashes(getenv('SERVER_NAME'))."\n".
				"#\n".
				"#############################################\n".
				"\n\n";
		}

	function getExportData()
		{
		foreach($this->exportHash as $tab)
			{
			if(isset($_POST['export_structure']) && ($_POST['export_structure']==1 || $_POST['export_structure']==2))
				{
				echo "###########\n";
				echo "# Table ".$tab."\n";
				echo "#\n";
				echo "\n";

				if(isset($_POST['export_drop']) && $_POST['export_drop']==1)
					{
					echo "DROP TABLE IF EXISTS ".$tab.";\n";
					}

				echo "CREATE TABLE ".$tab." (\n";

				$getinfo=$this->SQL->query("DESCRIBE ".$tab);
				$count=$this->SQL->num_rows($getinfo);
				$c=0;
				while($info=$this->SQL->fetch_assoc($getinfo))
					{
					$c++;
					$tab_name=$info['Field'];
					$tab_type=' '.$info['Type'];
					$tab_null=($info['Null']=='') ? " NOT NULL" : " NULL";
					$tab_default=($info['Default']=='') ? '' : " DEFAULT '".$info['Default']."'";
					$tab_extra=($info['Extra']=='') ? '' : ' '.$info['Extra'];
					$tab_komma=($c<$count) ? ",\n" : '';

					echo " ".$tab_name.$tab_type.$tab_null.$tab_default.$tab_extra.$tab_komma."";
					}

				$keyarray=array();

				$getinfo=$this->SQL->query("SHOW KEYS FROM ".$tab);
				while($info=$this->SQL->fetch_assoc($getinfo))
					{
					$keyname=$info['Key_name'];
					$comment=(isset($info['Comment'])) ? $info['Comment'] : '';
					$sub_part=(isset($info['Sub_part'])) ? $info['Sub_part'] : '';
					if($keyname!="PRIMARY" && $info['Non_unique']==0)
						$keyname="UNIQUE|$keyname";
					if($comment=="FULLTEXT")
						$keyname="FULLTEXT|$keyname";
					if(!isset($keyarray[$keyname]))
						$keyarray[$keyname]=array();

					$keyarray[$keyname][]=($sub_part>1) ? $info['Column_name']."(".$sub_part.")" : $keyarray[$keyname][]=$info['Column_name'];
					}

				if(is_array($keyarray))
					{
					foreach($keyarray as $keyname => $columns)
						{
						echo ",\n";

						if($keyname=="PRIMARY")
							{
							echo "PRIMARY KEY (";
							}
						elseif(substr($keyname, 0, 6) == "UNIQUE")
							{
							echo "UNIQUE ".substr($keyname, 7)." (";
							}
						elseif(substr($keyname, 0, 8) == "FULLTEXT")
							{
							echo "FULLTEXT ".substr($keyname, 9)." (";
							}
						else
							{
							echo "KEY ".$keyname.' (';
							}

						echo implode($columns,", ").")";
						}
					}

				echo "\n ) TYPE=".$this->SQL->table_status($tab).";\n";
				echo "\n";
				} //END TABLE STRUCTUR

			if(isset($_POST['export_structure']) && $_POST['export_structure']!=2)
				{
				if(isset($_POST['export_delete']) && $_POST['export_delete']==1)
					{
					echo "DELETE FROM ".$tab.";\n";
					}

				$queryresult=$this->SQL->query("SELECT * FROM ".$tab);
				while($info=$this->SQL->fetch_assoc($queryresult))
					{
					$fieldnames=$values='';

					if(isset($_POST['export_fullinserts']) && $_POST['export_fullinserts']==1)
						{
						foreach($info as $name=>$field)
							{
							$fieldnames.=(empty($fieldnames)) ? $name : ",".$name;
							$values.=(empty($values)) ? $this->formatfield($field) : ",".$this->formatfield($field);
							}

						echo "INSERT INTO ".$tab." (".$fieldnames.") VALUES (".$values.");\n";
						}
					else
						{
						foreach($info as $field)
							$values.=(empty($values)) ? $this->formatfield($field) : ",".$this->formatfield($field);

						echo "INSERT INTO ".$tab." VALUES (".$values.");\n";
						}
					}
				echo "\n";
				$this->SQL->free_result($queryresult);
				}
			}
		}


	#######################
	# Mini-Methods (privat)
	#######################

	#@Desc:		Useable on fields and table engine(type).
	function charset($set='utf8',$collate='utf8_general_ci')
		{
		return ' CHARACTER SET '.$set.' COLLATE '.$collate.' ';
		}

	function col($Field,$col)
		{
		$charset = $this->charset();
		$search = array('int','float','double','blob','binary'); #maybe extened for further use

		foreach($search as $str)
			{
			#for numeric fields unset the charset
			if(strstr(strtolower($col['Type']),$str))
				{
				$charset = '';
				break;
				}
			}

		#create the field defintion
		$str = $Field.' '.$col['Type'].' '.$col['Extra'];
		$str.= $charset;
		$str.= $col['Null']=='YES' ? ' NULL' : ' NOT NULL';
		$str.= empty($col['Default']) && $col['Default']!='0' ? '' : " DEFAULT '".$col['Default']."'";

		return $str;
		}

	function formatfield($field)
		{
		if((string)(intval($field))!="$field")
			{
			$field=str_replace("\n","\\n",str_replace("\r","\\r",str_replace("\t","\\t",addslashes($field))));
			}

		return "'".$field."'";
		}

	function message($msg)
		{
		$this->message[]=$msg;
		}
	} // End Class pkSqlUtilities
?>