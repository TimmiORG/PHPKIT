<?php
# PHPKIT WCMS | Web Content Management System
#
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATION
#
# SIE SIND NICHT BERECHTIGT, UNRECHTM�SSIGE KOPIEN DIESER
# DATEI ZU ERSTELLEN UND/ODER DIESE INFORMATIONEN ZU ENTFERNEN
#
# This file / the PHPKIT software is no freeware! For further 
# information please visit our website or contact us via email:
#
# Diese Datei / die PHPKIT Software ist keine Freeware! F�r weitere
# Informationen besuchen Sie bitte unsere Website oder kontaktieren uns per E-Mail:
#
# email     : info@phpkit.com
# website   : http://www.phpkit.com
# licence   : http://www.phpkit.com/licence
# copyright : Copyright (c) 2002-2009 mxbyte gbr | http://www.mxbyte.com


#@Class:	pk
#@Parent:	./.
#@Access:	
#@Desc:		PHPKIT multi functionality class. Collection of useful functions composed as a class.
#			Some methods requires global configurations variables, passed as constants.
class pk
	{
	#@Method:	fprint
	#@Access:	static public	
	#@Param: 	mixed var
	#@Param:	[ bool return ]
	#@Return: 	string
	#@Desc:		Prints a formated variable.
	static public function fprint($var,$return=false)
		{
		$buffer='';
		
		if($return)
			{
			ob_start();
			}
		
		echo '<pre>';
		print_r($var);
		echo '</pre>';
		
		if($return)
			{
			$buffer = ob_get_contents();
			ob_end_clean();
			}
		
		return $buffer;
		}
	#@END Method: fprint
	
		
	#@Method:	txtwrap
	#@Access:	public
	public static function txtwrap($text,$maxlength)
		{
		$wordend = array(" ","\n","\r","\f","\v","\0");
		$count = 0;
		$break = ' ';
		$string = '';
		$open = false;
		$entitie = false;
		
		for($i=0; $i<strlen($text); $i++)
			{
			$string.= $text{$i};
			
			if($text{$i}=='<')
				{
				$open = true;
				continue;
				}
			
			if($open && $text{$i}=='>')
				{
				$open = false;
				continue;
				}
			
			if($text{$i}=='&')
				{
				$entitie = true;
				continue;
				}

			if($entitie && $text{$i}==';')
				{
				$entitie = false;
				continue;
				}
			
			if(!$open && !$entitie)
				{
				if(!in_array($text{$i},$wordend))
					{
					$count++;
					
					if($count==$maxlength)
						{
						$string.= $break;
						$count=0;
						}
					}
				else
					{
					$count = 0;
					}
				}
			}#@END for
		
		return $string;
		}	
	#@END Method: textwrap
	
	}
#END Class: pk
?>