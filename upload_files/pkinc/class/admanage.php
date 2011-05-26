<?php
# PHPKIT Web Content Management System
# --------------------------------------------
# Copyright (c) 2002-2007 Gersöne & Schott GbR
#
# This file / the PHPKIT-software is no freeware!
# For further informations please vistit our website
# or contact us via email:
#
# Diese Datei / die PHPKIT-Software ist keine Freeware!
# Für weitere Information besuchen Sie bitte unsere 
# Webseite oder kontaktieren uns per E-Mail:
#
# Website : http://www.phpkit.de
# Mail    : info@phpkit.de
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATIONS
#
# SIE SIND NICHT BERECHTIGT, UNRECHTMÄSSIGE KOPIEN DIESER
# DATEI ZU ERSTELLEN UND/ODER DIESE INFORMATIONEN ZU ENTFERNEN


if(!defined('pkFRONTEND'))
	return;
	

class pkAdmanage
	{
	var $SQL;			# object
	var $hash=array();	// 
	
	
	// constructor
	function pkAdmanage()
		{
		$this->__construct();
		}

	function __construct()
		{
		global $SQL;
		$this->SQL=&$SQL;
		}

	function get()
		{																																																													if(!@pkl ( 1))return NULL;																																																							
		$adview_maxcount=0;
		
		$result=$this->SQL->query("SELECT 
			* 
			FROM ".pkSQLTAB_ADVIEW." 
			WHERE adview_status='1'");
		while($adviewinfo=$this->SQL->fetch_assoc($result)) 
			{
			$this->hash[]=$adviewinfo; 
			$adview_maxcount += $adviewinfo['adview_relation'];
			}

		if(!$adview_maxcount>=1) 
			return NULL;

		pkMtSRand();
		$counter=0;			
		$rand=$adview_maxcount>1 ? mt_rand(1,$adview_maxcount) : 1;
		
		shuffle($this->hash);
		
		foreach($this->hash as $k) 
			{
			if($k['adview_relation']+$counter>=$rand) 
				{
				$this->SQL->query("UPDATE ".pkSQLTAB_ADVIEW." 
					SET adview_views=adview_views+1 
					WHERE adview_id='".$k['adview_id']."'");
				
				return $k['adview_code'];
				}
				
			$counter=$counter+$k['adview_relation'];
			}
		
		return NULL;
		}
	}
?>