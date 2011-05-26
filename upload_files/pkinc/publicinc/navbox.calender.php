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


if(!defined('pkFRONTEND') || pkFRONTEND!='public')
	die('Direct access to this location is not permitted.');


if(isset($_GET['calender_month']) && intval($_GET['calender_month'])>0 && intval($_GET['calender_month'])<13)
	$intMonth=intval($_GET['calender_month']); 
elseif(isset($_GET['month']) && intval($_GET['calender_month'])>0 && intval($_GET['calender_month'])<13)
	$intMonth=$_GET['month']; 
elseif($SESSION->exists('intSessMonth'))
	$intMonth=$SESSION->get('intSessMonth');
else
	$intMonth=date('n',pkTIME);

if(isset($_GET['calender_year']) && intval($_GET['calender_year'])>=1970 && intval($_GET['calender_year'])<2035)
	$intYear=intval($_GET['calender_year']);
elseif(isset($_GET['year']))
	$intYear=$_GET['year'];
elseif($SESSION->exists('intSessYear'))
	$intYear=$SESSION->get('intSessYear');
else
	$intYear=date('Y',pkTIME);


if(isset($_REQUEST['nextCalender']))
	{
	$intMonth++;
	
	if($intMonth>=13)
		{
		$intMonth=1;
		$intYear++;
		}
	}
elseif(isset($_REQUEST['backCalender']))
	{
	$intMonth--;
	
	if($intMonth<=0)
		{
		$intMonth=12;
		$intYear--;
		}
	}


$SESSION->set('intSessMonth',$intMonth);
$SESSION->set('intSessYear',$intYear);

$intFirstDay=strftime('%w', mktime(0,0,0,$intMonth,1,$intYear));
$intFirstDay=($intFirstDay == 0) ? 7 : $intFirstDay;

$intLastDay=date('t', mktime(0,0,0,$intMonth,1,$intYear));
$intLastDay2=strftime('%w', mktime(0,0,0,$intMonth,$intLastDay,$intYear));

$row=1;
$i=0;
$d=1;
$j=date('d',pkTIME);
$m=date('n',pkTIME);
$y=date('Y',pkTIME);
$cs=$intFirstDay-1;
unset($calender_row);

while($i<$intLastDay)
	{
	if($i==0)
		{
		eval("\$calender_row.= \"".pkTpl("navigation/calender_row_cw")."\";");
		
		if($cs>0)
			eval("\$calender_row.= \"".pkTpl("navigation/calender_row_spacer")."\";");
		}
	
	if($d==$j && $intMonth==$m && $intYear==$y)
		eval("\$calender_row.= \"".pkTpl("navigation/calender_row_today")."\";");
	else
		eval("\$calender_row.= \"".pkTpl("navigation/calender_row_field")."\";");
	
	if((($i+$intFirstDay)%7==0) && $i < ($intLastDay-1))
		{
		eval("\$calender_row.= \"".pkTpl("navigation/calender_row_cw")."\";");
		$row++;
		}
	
	$d++;
	$i++;
	}

$cs=7-$intLastDay2;

if($cs>0 && $cs<7)
	eval("\$calender_row.= \"".pkTpl("navigation/calender_row_spacer")."\";");

if($row<6)
	eval("\$calender_row.= \"".pkTpl("navigation/calender_spacer_row")."\";");

unset($row);

$prev_month=$intMonth;
$next_month=$intMonth;
$prev_year=$intYear;
$next_year=$intYear;
$prev_month--;
$next_month++; 

if($prev_month<=0)
	{
	$prev_month=12;
	$prev_year--;
	}

if($next_month>=13)
	{
	$next_month=1;
	$next_year++;
	}

foreach(range(1,12) as $i)
	{
	$calender_option_month.='<option value="'.$i.'"';
		
	if($intMonth==$i)
		{
		$strMonth=pkGetLang('month'.$i);
		$calender_option_month.=' selected="selected"';
		}
		
	if($prev_month==$i)
		$calender_prev=pkGetLang('month'.$i).' ';
		
	if($next_month==$i)
		$calender_next=pkGetLang('month'.$i).' ';
		
	$calender_option_month.='>'.pkGetLang('month'.$i).'</option>';
	}

$year=date('Y');
$y=$year-5;
if($prev_year<$y)
	unset($calender_prev);
else
	{
	$calender_prev.=$prev_year;
	
	eval("\$prev_form= \"".pkTpl("navigation/calender_form_back")."\";");
	}

while($y<$year+5)
	{
	$calender_option_year.='<option value="'.$y.'"'; 
	
	if($intYear==$y)
		{
		$strYear=$y;
		$calender_option_year.=' selected';
		} 
	
	$calender_option_year.='>'.$y.'</option>';
	$y++;
	}

if($next_year>($y-1))
	unset($calender_next);
else
	{
	$calender_next.=$next_year;
	
	eval("\$next_form= \"".pkTpl("navigation/calender_form_next")."\";");
	}
	

$querystring=urldecode($ENV->getvar('QUERY_STRING'));

#clean the query
$querystring=preg_replace('/([&|?])?nextCalender=(.*)[^&]/i','',$querystring);
$querystring=preg_replace('/([&|?])?backCalender=(.*)[^&]/i','',$querystring);
$querystring=preg_replace('/([&|?])?calender_month=(.*)[^&]/i','',$querystring);
$querystring=preg_replace('/([&|?])?calender_year=(.*)[^&]/i','',$querystring);

$form_action=pkLink('','','','',$querystring);
$form_additional_fields=pkFormActionGet($form_action);

eval("\$calendar=\"".pkTpl("navigation/calender")."\";");

return array($calendar);
?>