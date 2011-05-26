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


#@Class:		pkcms
#@Access:		*default*
#@Parent:		*none*
#@Interface:	*none*
#@Desc:			Singleton class. Helper Class for the public frontend.
class pkcms
	{
	private static $_instance = NULL;
	
	#@Vars:			site_title*
	private $site_title = '';
	private $site_title_formated = false;
	private $site_title_prefix = '';
	private $site_title_suffix = '';
	private $site_title_default = '';
	
	private $site_styleid = 0;
	
	
	#@Method:		__construct
	#@Access:		private
	#@Param:		void
	#@Return:		void
	#@Desc:			Constructor. 
	private function __construct()
		{
		#void
		}
	#@END Method: 	__construct
	

	#@Method:		__construct
	#@Access:		public static
	#@Param:		void
	#@Return:		void
	#@Desc:		Pseudo magical function. Returns the specfic object for this class.
	public static function _instance()
		{
		return self::$_instance = (self::$_instance ? self::$_instance : new self);
		}
	#@END Method:	_instance
	

	#@Method:		site_title_affix
	#@Access:		public
	#@Param:		string prefix
	#@Param:		string suffix
	#@Return:		void
	#@Desc:
	public function site_title_affix($prefix,$title,$suffix)
		{
		$this->site_title = $title;
		$this->site_title_formated = false; #reset it
		
		$this->site_title_prefix = $prefix;
		$this->site_title_suffix = $suffix;
		
		$this->site_title_default = $this->site_title_get();
		}
	#@END Method: site_title_


	#@Method:		site_title_set
	#@Access:		public
	#@Param:		string title
	#@Param:		bool formated
	#@Return:		void
	#@Desc:
	public function site_title_set($title,$formated=false)
		{
		if(!empty($title))
			{
			$this->site_title = $title;
			$this->site_title_formated = $formated;
			}
		}
	#@END Method:	site_title_set


	#@Method:		site_title_preix_set
	#@Access:		public
	#@Param:		string prefix
	#@Return:		void
	#@Desc:
	public function site_title_prefix_set($prefix)
		{
		if(!empty($prefix))
			{
			$this->site_title_prefix = $prefix;
			}
		}
	#@END Method:	site_title_prefix_set


	#@Method:		site_title_suffix_set
	#@Access:		public
	#@Param:		string suffix
	#@Return:		void
	#@Desc:
	public function site_title_suffix_set($suffix)
		{
		if(!empty($suffix))
			{
			$this->site_title_suffix = $suffix;
			}
		}
	#@END Method:	site_title_suffix_set


	#@Method:		site_title_get
	#@Access:		public
	#@Param:		void
	#@Return:		string
	#@Desc:
	public function site_title_get()
		{
		$prefix = strip_tags($this->site_title_prefix); #remove html tags
		$prefix = pkEntities($prefix);  #encode all remaining specialchars
		
		$suffix = strip_tags($this->site_title_suffix);
		$suffix = pkEntities($suffix);
		
		$title = strip_tags($this->site_title);
		$title = $this->site_title_formated ? $title : pkEntities($title);

		$site_title = $prefix . $title . $suffix;
		
		return $site_title;
		}
	#@END Method:	site_title_get


	#@Method:		site_title_default_get
	#@Access:		public
	#@Param:		void
	#@Return:		string
	#@Desc:	
	public function site_title_default_get()
		{
		return $this->site_title_default;
		}
	#@END Method:	site_title_default_get
	

	#@Method:		site_title_temp_get
	#@Access:		public
	#@Param:		void
	#@Return:		string
	#@Desc:	
	public function site_title_temp_get($title,$formated=false)
		{
		$prefix = strip_tags($this->site_title_prefix); #remove html tags
		$prefix = pkEntities($prefix);  #encode all remaining specialchars
		
		$suffix = strip_tags($this->site_title_suffix);
		$suffix = pkEntities($suffix);
		
		$title = strip_tags($title);
		$title = $formated ? $title : pkEntities($title);		

		$site_title = $prefix . $title . $suffix;
		
		return $site_title;
		}
	#@END Method:	site_title_default_get	
	

	#@Method:		site_styleid_set
	#@Access:		public
	#@Param:		int styleid
	#@Return:		string
	#@Desc:		
	public function site_styleid_set($styleid)
		{
		$this->site_styleid = intval($styleid);
		}
	#@END Method:	site_styleid_set


	#@Method:		site_styleid_get
	#@Access:		public
	#@Param:		void
	#@Return:		int
	#@Desc:		
	public function site_styleid_get()
		{
		return $this->site_styleid;
		}
	#@END Method:	site_styleid_get
	}
#@END Class:	pkcms 
?>