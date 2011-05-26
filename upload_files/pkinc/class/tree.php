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


#@Class:	pkTree
#@Parent:	
#@Access:	
#@Methods:	
#			__construct
#			build
#			set
#			get
#@Desc:		Sorts arrays. Uses fluid interface.
class pkTree
	{
	private $hash = array();
	private $tree = array();
	private $tree_object = array();
	
	private $_self = 'id';
	private $_parent = 'pid';
	private $_tier = 'tier';
	
	
	#@Method:	__construct
	#@Access:	public
	#@Param:	void
	#@Return:	void
	#@Desc:		Constructor.
	public function __construct()
		{
		#void
		}
	#@END Method: __construct
	
	
	#@Method:	build
	#@Access:	public
	#@Param:	mixed parent
	#@Param:	mixed tier
	#@Return:	object
	#@Desc:		Constructor.
	public function build($parent=0,$tier=0)
		{
		foreach($this->hash as $key=>$item)
			{
			if(!isset($item[$this->_parent]) || $item[$this->_parent]!=$parent)
				{
				continue;
				}
			
			$this->tree[$key] = $item;
			$this->tree[$key][$this->_tier] = $tier;

			if($item[$this->_parent]!=$item[$this->_self])
				{
				$this->build($item[$this->_self],$tier+1);
				}
			}#END foreach
		
		return $this;
		}	
	#END Method: build
	

	#@Method:	set
	#@Access:	public
	#@Param:	array hash
	#@Param:	int $_self
	#@Param:	int $_parent
	#@Param:	int $_childs
	#@Param:	int $_tier
	#@Return:	object
	#@Desc:		Initial settings.	
	public function set(
			$hash,				#given raw data - array
			$_self='id',		#id/key
			$_parent='pid',		#parent id
			$_tier='tier'		#additional key for the tier/level
			)
		{
		$this->tree = array();	#reset if used more than once
		$this->hash = $hash;

		$this->_self = $_self;
		$this->_parent = $_parent;
		
		return $this;
		}
	#@END Method: set
	
	
	#@Method:	get
	#@Access:	public
	#@Param:	void
	#@Return:	object
	#@Desc:		Returns an sorteted array.
	public function get()
		{
		if(empty($this->tree) && !empty($this->hash))
			{
			$this->build();
			}
		
		return $this->tree;
		}
	#@END Method: get
	}
#@END Class: pktree
?>