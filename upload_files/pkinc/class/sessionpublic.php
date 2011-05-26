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


if(!defined('pkFRONTEND') || pkFRONTEND!='public')
	die('Direct access to this location is not permitted.');


class pkSessionpublic extends pkSession
	{

	function pkSessionpublic()
		{
		$this->__construct();
		}
	
	function main()
		{
		$this->db_cleanup();	

		if($this->db_catch() && $this->validate_client_values()) #publicsession exists
			$this->publicsession=1;
		elseif(!$this->logmode && $this->login_cookie_catch() && $this->validate_cookie_values())
			$this->logmode='cookie';
		else #no valid session found
			$this->setnewid();
		
		
		$this->setid($this->sessionid);
		$this->start();
		
		if($this->logmode=='login')
			$this->login(); #exit

		if($this->logmode=='firstlog')
			$this->firstlogin(); #exit

		if($this->logmode=='relog')
			$this->relogin(); #exit

		if($this->logmode=='logout')
			$this->logout(); #exit
			
		if($this->logmode=='cookie')
			$this->login_cookie();
		elseif($this->publicsession)
			{
			$this->update_session();
			}
		else
			$this->guest();	#new guest 
		}
	
	function login()
		{
		$this->login_request_data();
	
		if(!$this->login_check_data())
			{
			sleep(1);
			pkHeaderLocation('login','','username='.urlencode($this->log_username).'&'.pkArrayUrlencode('error',$this->login_error()));
			}
		
		if(!$this->account_active())
			pkHeaderLocation('','','event=account_inactive');
		
		#login okay
		$this->destroy();
		$this->login_set_data();
		$this->login_save_data(); #db

		pkHeaderLocation('','','event=login&moveto='.urlencode($this->ENV->_request('remove_path')));
		}

	function login_cookie()
		{
		$this->log_setcookie=1;
		$this->destroy();
		$this->login_set_data();
		$this->login_save_data(); #db
		}
		
	function logout()
		{
		$this->db_delete_adminsession();
		$this->destroy();
		$this->login_guest_data();
		$this->login_set_data();
		$this->login_save_data(); #db
		
		pkHeaderLocation('','','event=logout');
		}
	
	function firstlogin()
		{
		$this->firstlogin_request_data();

		if(!$this->firstlogin_check_data())
			pkHeaderLocation('','','event=account_inactive');


		#login okay
		$this->destroy();
		$this->login_set_data();
		$this->login_save_data(); #db

		pkHeaderLocation('','','event=firstlogin');
		}

	function relogin()
		{
		$this->relogin_request_data();

		if(!$this->relogin_check_data())
			pkHeaderLocation('','','event=account_inactive');

		#login okay
		$this->destroy();
		$this->login_set_data();
		$this->login_save_data(); #db

		pkHeaderLocation('','','event=firstlogin');
		}
		
	function guest()
		{
#		$this->ENV->debugcookie();
		$this->destroy();			
		$this->login_guest_data();
		$this->login_set_data();
		$this->login_save_data(); #db		
		}
	}
?>