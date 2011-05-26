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


if(!defined('pkFRONTEND') || pkFRONTEND!='admin')
	die('Direct access to this location is not permitted.');


class pkSessionadmin extends pkSession
	{
	function main()
		{		
		$this->db_cleanup();

		if($this->db_catch() && $this->validate_client_values()) #adminsession exists
			{
			$this->adminsession=1;
			}
		elseif($this->db_catch(pkSQLTAB_SESSION) && $this->validate_client_values()) #publicsession exists
			{
			$this->publicsession=1;
			}
		elseif(!$this->logmode && $this->login_cookie_catch() && $this->validate_cookie_values()) #cookie
			{
			$this->logmode='cookie';			
			}
		else #no valid session found
			{
			$this->setnewid();
			}

		#adminsession auto true
		$this->setid($this->sessionid);
		$this->start();

		if($this->publicsession && pkGetconfig('session_adminautolog'))		
			{
			$this->login_save_data();
			$this->adminsession = 1;
			}
		
		if($this->logmode=='login')
			{
			$this->login(); #exit
			}

		if($this->logmode=='logout')
			{
			$this->logout(); #exit
			}
		
		if($this->logmode=='cookie')
			{
			$this->login_cookie();
			}
		elseif($this->adminsession)
			{
			$this->update_session();
			}
		}
	
	function login()
		{
		$this->login_request_data();
	
		if(!$this->login_check_data())
			{
			sleep(3);
			pkHeaderLocation('','','username='.urlencode($this->log_username).'&'.pkArrayUrlencode('error',$this->login_error()));
			}
		
		#login okay
		$this->login_set_data();
		$this->adminsession = adminaccess('adminarea');
		$this->login_save_data(); #db

		pkHeaderLocation('','','event=login');
		}
		
	function login_cookie()
		{
		$this->log_setcookie = 1;
		$this->destroy();
		$this->login_set_data();
		
		pkGetconfig('session_adminautolog') ? $this->adminsession=adminaccess('adminarea') : 0;
		
		$this->login_save_data(); #db
		}		
		
	function logout()
		{
		$this->db_delete_adminsession();
		$this->destroy();
		
		$this->adminsession = 0;
		$this->publicsession = 0;
		
		$this->login_guest_data();
		$this->login_set_data();
		$this->login_save_data(); #db

		pkHeaderLocation('','','event=logout');
		}
		
	function isadminsession()
		{
		return $this->adminsession;
		}
	}
?>