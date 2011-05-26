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


class pkStyle
	{
	#declarations
	var $style=array();
	var $stylid=0;
	var $imagedirpath='';

	var $css='';

	var $connected=false;

	var $tplfile='';

	var $styleexport_ext='.pkxstyle';

	#specific declarations
	var $css_bgimage_repeat_definition=array('repeat','no-repeat','repeat-x','repeat-y');
	#END decalarations
	
	
	#@Method: 	__construct
	#@Access:	public
	
	public function __construct()
		{
		global $SQL,$config,$ENV;
		
		$this->ENV=&$ENV;
		$this->SQL=&$SQL;
		
		$this->connected = $this->SQL->connect();
		$this->tplfile = pkDIRPUBLICTPL.'style.css';
		}
	#END Method: __construct

	
	#methode connected
	function connected()
		{
		return $this->connected;
		}
	#END methode connected
	
	
	#methode getcss
	function getcss()
		{
		return $this->css;
		}
	#END methode getcss
		

	#methode load
	function load()
		{
		$SQL = &$this->SQL;
		
		$sqlwhere = $sqlorder='';
		$styleid = $this->ENV->_get_id('id');
		$site_style = $user_design = 0;
		

		$query = $SQL->query("SELECT id,value FROM ".pkSQLTAB_CONFIG." WHERE id IN('site_style','user_design')");
		while(list($key,$value) = $SQL->fetch_row($query))
			{
			$$key = @unserialize($value);
			}

		if($user_design && $styleid > 0 && $styleid != $site_style)
			{
			$sqlwhere = "(style_id='".intval($styleid)." AND style_user=1') OR ";
			
			$sqlorder = " ORDER BY style_id ".($styleid<$site_style ? 'ASC' : 'DESC');
			}

		$this->style = $SQL->fetch_assoc($SQL->query("SELECT
			*
			FROM ".pkSQLTAB_STYLE."
			WHERE ".$sqlwhere." 
				style_id='".$site_style."' ".
			$sqlorder." 
			LIMIT 1"));

		return $this->styleid = $this->style['style_id'];
		}
	#END methode load


	#methode parse
	function parse()
		{
		$style = &$this->style;
		
		$this->mkimagedirpath();

		#global and body
		$style_addcss	= '';
		$body_margin	= 
				($style['bodymargintop']>0 ? ' margin-top:'.$style['bodymargintop'].'px;' : NULL).
				($style['bodymarginleft']>0 ? ' margin-left:'.$style['bodymarginleft'].'px;' : NULL).
				($style['bodymarginright']>0 ? ' margin-right:'.$style['bodymarginright'].'px;' : NULL).
				($style['bodymarginbottom']>0 ? ' margin-bottom:'.$style['bodymarginbottom'].'px;' : NULL);

		$bodybgimage				= $this->css_bgimage($style['bodybgimage'],$style['bodybgimagerepeat']);
		$style_align 				= $style['style_align'];
		$site_width 				= $this->css_width($style['style_width']);
		$margin_left				= $style_align=='center' || $style_align=='right' ? 'auto' : $style['bodymargin'].'px';
		$margin_right				= $style_align=='center' || $style_align=='left' ? 'auto' : $style['bodymargin'].'px';
		$bodybgcolor				= $this->css_bgcolor($style['bodybgcolor']);
		$scrollbarcolor				= empty($style['scrollbarcolor']) ? '' : 'color:#'.$style['scrollbarcolor'].';';		
	
		#page decorations
		$pagedecorationwidth		= intval($style['style_width'])>$style['pagedecorationwidth'] ? $style_width : $style['pagedecorationwidth'].'px';
		$pagedecorationwidth		= empty($pagedecorationwidth) ? '' : 'width:'.$pagedecorationwidth.';';
		$pagedecorationbgimage		= $this->css_bgimage($style['pagedecorationbgimage']);
		$pageheaderbgimage			= $this->css_bgimage($style['pageheaderbgimage']);
		$pageheaderbgcolor			= $this->css_bgcolor($style['pageheaderbgcolor']);
		$pagedecorationbgcolor		= $this->css_bgcolor($style['pagedecorationbgcolor']);
		$pagefooterbgcolor			= $this->css_bgcolor($style['pagefooterbgcolor']);		
		$pagefooterbgimage			= $this->css_bgimage($style['pagefooterbgimage']);

		#fix the IE6/7
		$pageheaderheight			 = $style['pageheaderheight']>0 ? 'height:'.$style['pageheaderheight'].'px;' : 'display:none;';
		$pagefooterheight			 = $style['pagefooterheight']>0 ? 'height:'.$style['pagefooterheight'].'px;' : 'display:none;';
	
		#branding + adview default
		$brandingheight				= $style['brandingheight']>0 ? 'height:'.$style['brandingheight'].'px;' : '';
		$brandingimage				= $this->css_bgimage($style['brandingimage'],'no-repeat');
		$brandingimagewidth			= $style['brandingimagewidth']>0 ? 'width:'.$style['brandingimagewidth'].'px;' : 'width:0;';
		$brandingimageheight		= $style['brandingimageheight']>0 ? 'height:'.$style['brandingimageheight'].'px;' : 'height:0;';
		$brandingimageposition		= $this->position($style['brandingimageposition'],$style['brandingimagepositiontop'],$style['brandingimagepositionleft']);
		$brandingbgimage			= $this->css_bgimage($style['brandingbgimage'],$style['brandingbgimagerepeat']);
		$brandingbgcolor			= $this->css_bgcolor($style['brandingbgcolor']);
		$adviewbgcolor				= $this->css_bgcolor($style['adviewbgcolor']);

		#IE FIX - branding image position
		$brandingalignIE 			= $style['brandingimageposition']=='center' || $style['brandingimageposition']=='right' ? 'text-align:'.$style['brandingimageposition'] : '';
			
		#contenttop main navigation - hl
		$contenttophlalign			= $style['contenttophlalign']=='center' || $style['contenttophlalign']=='right' ? 'text-align:'.$style['contenttophlalign'].';' : '';
		$contenttophlheight			= $style['contenttophlheight']>0 ? 'height:'.$style['contenttophlheight'].'px;' : '';
		$contenttophlpadding		= $style['contenttophlpadding']>0 ? 'top:'.$style['contenttophlpadding'].'px; padding:'.$style['contenttophlpadding'].'px;' : '';
		$contenttophlspacing		= $style['contenttophlspacing']>0 ? 'padding-left:'.$style['contenttophlspacing'].'px; padding-right:'.$style['contenttophlspacing'].'px;' : '';
		$contenttophlbgcolor		= $this->css_bgcolor($style['contenttophlbgcolor']);
		$contenttophlbgimage		= $this->css_bgimage($style['contenttophlbgimage']);
		$contenttophlfont			= $this->css_fontfamily($style['contenttophlfont']);
		$contenttophlfontsize		= $this->css_fontsize($style['contenttophlfontsize']);
		$contenttophlcolor			= $this->css_color($style['contenttophlcolor']);
		$contenttophlcolorhover		= $this->css_color($style['contenttophlcolorhover']);

		#contenttop main navigation - li
		$contenttopalign			= $style['contenttopalign']=='center' || $style['contenttopalign']=='right' ? 'text-align:'.$style['contenttopalign'].';' : '';
		$contenttopheight			= $style['contenttopheight']>0 ? 'height:'.$style['contenttopheight'].'px;' : '';
		$contenttoppadding			= $style['contenttoppadding']>0 ? 'top:'.$style['contenttoppadding'].'px; padding:'.$style['contenttoppadding'].'px;' : '';
		$contenttopspacing			= $style['contenttopspacing']>0 ? 'padding-left:'.$style['contenttopspacing'].'px; padding-right:'.$style['contenttopspacing'].'px;' : '';
		$contenttopbgcolor			= $this->css_bgcolor($style['contenttopbgcolor']);
		$contenttopbgimage			= $this->css_bgimage($style['contenttopbgimage']);
		$contenttopfont				= $this->css_fontfamily($style['contenttopfont']);
		$contenttopfontsize			= $this->css_fontsize($style['contenttopfontsize']);
		$contenttopcolor			= $this->css_color($style['contenttopcolor']);
		$contenttopcolorlink		= $this->css_color($style['contenttopcolorlink']);
		$contenttopcolorhover		= $this->css_color($style['contenttopcolorhover']);

		#contentbottom additional navigation -hl
		$contentbottomhlalign		= $style['contentbottomhlalign']=='center' || $style['contentbottomhlalign']=='right' ? 'text-align:'.$style['contentbottomhlalign'].';' : ''; 
		$contentbottomhlheight		= $style['contentbottomhlheight']>0 ? 'height:'.$style['contentbottomhlheight'].'px;' : '';
		$contentbottomhlpadding		= $style['contentbottomhlpadding']>0 ? 'top:'.$style['contentbottomhlpadding'].'px; padding:'.$style['contentbottomhlpadding'].'px;' : '';
		$contentbottomhlspacing		= $style['contentbottomhlspacing']>0 ? 'padding-left:'.$style['contentbottomhlspacing'].'px; padding-right:'.$style['contentbottomhlspacing'].'px;' : '';$style_addcss.='#pkC{margin-left:'.$margin_left.';margin-right:'.$margin_right.';min-width:770px;'.$site_width.'}';
		$contentbottomhlbgcolor		= $this->css_bgcolor($style['contentbottomhlbgcolor']);
		$contentbottomhlbgimage		= $this->css_bgimage($style['contentbottomhlbgimage']);
		$contentbottomhlfont		= $this->css_fontfamily($style['contentbottomhlfont']);
		$contentbottomhlfontsize	= $this->css_fontsize($style['contentbottomhlfontsize']);
		$contentbottomhlcolor		= $this->css_color($style['contentbottomhlcolor']);		
		$contentbottomhlcolorhover	= $this->css_color($style['contentbottomhlcolorhover']);

		#contentbottom additional navigation - li
		$contentbottomalign			= $style['contentbottomalign']=='center' || $style['contentbottomalign']=='right' ? 'text-align:'.$style['contentbottomalign'].';' : '';
		$contentbottomheight		= $style['contentbottomheight']>0 ? 'height:'.$style['contentbottomheight'].'px;' : '';
		$contentbottompadding		= $style['contentbottompadding']>0 ? 'top:'.$style['contentbottompadding'].'px; padding:'.$style['contentbottompadding'].'px;' : '';
		$contentbottomspacing		= $style['contentbottomspacing']>0 ? 'padding-left:'.$style['contentbottomspacing'].'px; padding-right:'.$style['contentbottomspacing'].'px;' : '';
		$contentbottombgcolor		= $this->css_bgcolor($style['contentbottombgcolor']);
		$contentbottombgimage		= $this->css_bgimage($style['contentbottombgimage']);
		$contentbottomfont			= $this->css_fontfamily($style['contentbottomfont']);
		$contentbottomfontsize		= $this->css_fontsize($style['contentbottomfontsize']);
		$contentbottomcolor			= $this->css_color($style['contentbottomcolor']);
		$contentbottomcolorlink		= $this->css_color($style['contentbottomcolorlink']);
		$contentbottomcolorhover	= $this->css_color($style['contentbottomcolorhover']);

		#contentleft - left navigation
		$contentleftpadding			= intval($style['contentleftpadding'])>0 ? 'padding:'.$style['contentleftpadding'].'px;' : '';
		$contentleftwidth			= intval($style['contentleftwidth'])>0 ? intval($style['contentleftwidth'])-(2*$style['contentleftpadding']) : 0;
		$contentleftwidth			= 'width:'.($contentleftwidth ? $contentleftwidth.'px' : 'auto').';';

		$contentleftbgcolor			= $this->css_bgcolor($style['contentleftbgcolor']);
		$contentleftbgimage			= $this->css_bgimage($style['contentleftbgimage'],$style['contentleftbgimagerepeat']);

		#contentleft - left navigation -hl
		$contentlefthlindent		= $this->css_indent($style['contentlefthlindent'],$style['contentlefthlalign']);
		$contentlefthlalign			= 'text-align:'.$style['contentlefthlalign'].';';
		$contentlefthlfont			= $this->css_fontfamily($style['contentlefthlfont']);
		$contentlefthlfontsize		= $this->css_fontsize($style['contentlefthlfontsize']);
		$contentlefthlcolor			= $this->css_color($style['contentlefthlcolor']);
		$contentlefthlcolorhover	= $this->css_color($style['contentlefthlcolorhover']);
		$contentlefthlbgcolor		= $this->css_bgcolor($style['contentlefthlbgcolor']);
		$contentlefthlbgimage		= $this->css_bgimage($style['contentlefthlbgimage']);

		#contentleft - left navigation - li
		$contentleftindent			= $this->css_indent($style['contentleftindent'],$style['contentleftalign']);
		$contentleftalign			= 'text-align:'.$style['contentleftalign'].';';
		$contentleftfont			= $this->css_fontfamily($style['contentleftfont']);
		$contentleftfontsize		= $this->css_fontsize($style['contentleftfontsize']);
		$contentleftcolor			= $this->css_color($style['contentleftcolor']);
		$contentleftcolorlink		= $this->css_color($style['contentleftcolorlink']);
		$contentleftcolorhover		= $this->css_color($style['contentleftcolorhover']);
		$contentleftcbgcolor		= $this->css_bgcolor($style['contentleftcbgcolor']);
		$contentleftcbgimage		= $this->css_bgimage($style['contentleftcbgimage']);
		$contentleftboxspacer		= $style['contentleftboxspacer'] ? 'margin-bottom:'.$style['contentleftboxspacer'].'px;' : '';

		#contentright - right navigation
		$contentrightpadding		= intval($style['contentrightpadding'])>0 ? 'padding:'.$style['contentrightpadding'].'px;' : '';
		$contentrightwidth			= intval($style['contentrightwidth'])>0 ? intval($style['contentrightwidth'])-(2*$style['contentrightpadding']) : 0;
		$contentrightwidth			= 'width:'.($contentrightwidth ? $contentrightwidth.'px' : 'auto').';';

		$contentrightbgcolor		= $this->css_bgcolor($style['contentrightbgcolor']);
		$contentrightbgimage		= $this->css_bgimage($style['contentrightbgimage'],$style['contentrightbgimagerepeat']);

		#contentright - right navigation -hl
		$contentrighthlindent		= $this->css_indent($style['contentrighthlindent'],$style['contentrighthlalign']);
		$contentrighthlalign		= 'text-align:'.$style['contentrighthlalign'].';';
		$contentrighthlfont			= $this->css_fontfamily($style['contentrighthlfont']);
		$contentrighthlfontsize		= intval($style['contentrighthlfontsize'])>0 ? 'font-size:'.intval($style['contentrighthlfontsize']).'px;' : '';
		$contentrighthlcolor		= $this->css_color($style['contentrighthlcolor']);
		$contentrighthlcolorhover	= $this->css_color($style['contentrighthlcolorhover']);
		$contentrighthlbgcolor		= $this->css_bgcolor($style['contentrighthlbgcolor']);
		$contentrighthlbgimage		= $this->css_bgimage($style['contentrighthlbgimage']);

		#contentright - right navigation -li
		$contentrightindent			= $this->css_indent($style['contentrightindent'],$style['contentrightalign']);
		$contentrightalign			= 'text-align:'.$style['contentrightalign'].';';
		$contentrightfont			= $this->css_fontfamily($style['contentrightfont']);
		$contentrightfontsize		= intval($style['contentrightfontsize'])>0 ? 'font-size:'.intval($style['contentrightfontsize']).'px;' : '';
		$contentrightcolor			= $this->css_color($style['contentrightcolor']);
		$contentrightcolorlink		= $this->css_color($style['contentrightcolorlink']);
		$contentrightcolorhover		= $this->css_color($style['contentrightcolorhover']);
		$contentrightcbgcolor		= $this->css_bgcolor($style['contentrightcbgcolor']);
		$contentrightcbgimage		= $this->css_bgimage($style['contentrightcbgimage']);
		
		$contentrightboxspacer		= $style['contentrightboxspacer'] ? 'margin-bottom:'.$style['contentrightboxspacer'].'px;' : '';			

		#contentmain
		$contentmainbgcolor			= $this->css_bgcolor($style['contentmainbgcolor']);
		$contentmainbgimage			= $this->css_bgimage($style['contentmainbgimage'],$style['contentmainbgimagerepeat']);
		$contentmainpadding			= intval($style['contentmainpadding'])>0 ? 'padding:'.intval($style['contentmainpadding']).'px;' : '';
				
		#sitecopyright
		$sitecopyrightbgimage		= $this->css_bgimage($style['sitecopyrightbgimage']);
		$sitecopyrightfont			= $this->css_fontfamily($style['sitecopyrightfont']);
		$sitecopyrightfontsize		= $this->css_fontsize($style['sitecopyrightfontsize']);
		$sitecopyrightcolor			= $this->css_color($style['sitecopyrightcolor']);
		$sitecopyrightbgcolor		= $this->css_bgcolor($style['sitecopyrightbgcolor']);
		$sitecopyrightalign			= 'text-align:'.$style['sitecopyrightalign'].';';

		#global elements		
		$fontsize					= $this->css_fontsize($style['fontsize']);
		$fontsizebig				= $this->css_fontsize($style['fontsizebig']);
		$fontsizesmall				= $this->css_fontsize($style['fontsizesmall']);		
		$bodyfont					= $this->css_fontfamily($style['bodyfont']);
		$bodyfontcolor				= $this->css_color($style['bodyfontcolor']);

		#tables
		$tablebgcolor				= $this->css_bgcolor($style['tablebgcolor']);
		$tdfontsize					= $this->css_fontsize($style['tdfontsize']);
		$tdfont						= $this->css_fontfamily($style['tdfont']);
		$tdfontcolor				= $this->css_color($style['tdfontcolor']);
		$tdqoute					= $this->css_bgcolor($style['tdquote']);
		$tdquotefont				= $this->css_fontfamily($style['tdquotefont']);
		$tdquotefontsize			= $this->css_fontsize($style['tdquotefontsize']);
		
		$tdheadscolor				= $this->css_color($style['tdheadscolor']);
		$tdheadsbgcolor				= $this->css_bgcolor($style['tdheadsbgcolor']);
		$tdheadsbgimage				= $this->css_bgimage($style['tdheadsbgimage']);
		$tdheadsfont				= $this->css_fontfamily($style['tdheadsfont']);
		$tdheadsfontsize			= $this->css_fontsize($style['tdheadsfontsize']);
		$tdheadscolor2				= $this->css_color($style['tdheadscolor2']);		
		$tdleftbgcolor				= $this->css_bgcolor($style['tdleftbgcolor']);
		$tdrightbgcolor				= $this->css_bgcolor($style['tdrightbgcolor']);		
		$tdstandardbgcolor			= $this->css_bgcolor($style['tdstandardbgcolor']);
		$tdhighlightbgcolor			= $this->css_bgcolor($style['tdhighlightbgcolor']);
		$tdoddbgcolor				= $this->css_bgcolor($style['tdoddbgcolor']);
		$tdodd2bgcolor				= $this->css_bgcolor($style['tdodd2bgcolor']);
		$tdevenbgcolor				= $this->css_bgcolor($style['tdevenbgcolor']);
		$tdeven2bgcolor				= $this->css_bgcolor($style['tdeven2bgcolor']);
		
		#forms
		$inputbgcolor				= $this->css_bgcolor($style['inputbgcolor']);
		$inputfont					= $this->css_fontfamily($style['inputfont']);
		$inputfontsize				= $this->css_fontsize($style['inputfontsize']);
		$inputfontcolor				= $this->css_color($style['inputfontcolor']);
		
		#hyperlinks
		$linkfont					= $this->css_fontfamily($style['linkfont']);
		$linkfontsize				= $this->css_fontsize($style['linkfontsize']);		
		$linkfontcolor				= $this->css_color($style['linkfontcolor']);
		$linkhovercolor				= $this->css_color($style['linkhovercolor']);		
	
		#horizontal rule
		$hrcolor					= $this->css_color($style['hrcolor']);

		#misc
		$site_parsertime			= $this->css_display($style['site_parsertime']);
		$site_parsertime_fontsize	= $this->css_fontsize($style['fontsizesmall']);
		$site_adminlogin_fontsize	= $this->css_fontsize($style['fontsizesmall']);
		$site_date					= $this->css_display($style['site_date']);

		$_fontsizesmall				= $style['fontsizesmall'] ? 'font-size:'.$style['fontsizesmall'].'px ! important;' : '';		
		$_textmargin				= 'margin-top:'.$style['fontsize'].'px; margin-bottom:'.($style['fontsize']*2).'px;';
	
	
		#addintional css-file
		$style_addcss.= empty($style['style_css']) ? NULL : @file_get_contents(@realpath(pkDIRWWWROOT.$style['style_css']));
	
		#addinional css definitions
		$style_addcss.= empty($style['style_addcss']) ? NULL : str_replace('{IMAGEDIR}',$style['style_images'],$style['style_addcss']);
		
		eval("\$this->css=\"".str_replace("\"","\\\"",@file_get_contents($this->tplfile))."\";");
		}
	#END methode parse
	
	#methode export
	function export($styleid)
		{
		$S=&$this->SQL;
		$packid='';
		$pack=$templates=array();
		$content='';
	
		$style=$S->fetch_assoc($S->query("SELECT * FROM ".pkSQLTAB_STYLE." WHERE style_id='".intval($styleid)."' LIMIT 1"));
		$stylename=$style['style_name'];
		
		foreach($style as $key=>$value)
			{
			if($key=='style_template')
				{
				$packid=$value;
				}
			elseif($key!='style_id')
				{
				$content.="\t\t<".rawurlencode($key).">".rawurlencode($value)."</".rawurlencode($key).">\r\n";
				}
			} 

		if(!$content)
			{		
			exit('No valid Design was loaded!');
			}
		
	
		$content	= '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>'."\r\n".
					  "<!--
PHPKIT WCMS - Web Content Management System

THIS FILE AND THE FORMAT OF THIS FILE IS A PART OF THE 
PHPKIT WCMS SOFTWARE

COMMERCIAL USE IS PROHIBITED WITHOUT GRANTED PERMISSION

YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
FILE AND/OR TO REMOVE THIS INFORMATION

Copyright 2002-2009 mxbyte gbr - http://www.phpkit.com
-->\r\n".
					  "<design>\r\n".
					  "\t<style>\r\n".
					   $content.
					  "\t</style>\r\n";
		
		
		if($packid>=0)
			{
			$pack=$S->fetch_assoc($S->query("SELECT * FROM ".pkSQLTAB_TEMPLATE_PACK." WHERE templatepack_id='".intval($packid)."' LIMIT 1"));
			}
		
		if($pack['templatepack_opt']==1 || $pack== -1)
			{
			if($pack!= -1)
				{
				$dir=$pack['templatepack_dir'];
				}
			else
				{
				$dir='';
				}
			
			$templates=readTemplateDir($dir,'',1);
			}
		else
			{
			$gettemplates=$S->query("SELECT * FROM ".pkSQLTAB_TEMPLATE." WHERE template_packid='".$pack['templatepack_id']."'");
			while($template=$S->fetch_assoc($gettemplates))
				{
				$templates[$template['template_name']]=$template['template_value'];
				}
			}
		
		if(!empty($templates))
			{
			$content.="\t<templates>\r\n";
			foreach($templates as $name=>$value)
				{
				$content.="\t\t<".rawurlencode($name).">".rawurlencode($value)."</".rawurlencode($name).">\r\n";
				}

			$content.="\t</templates>\r\n";
			}		
		
		$stylename=str_replace("  ","",$stylename);
		$stylename=str_replace(" ","_",$stylename);
		$stylename=str_replace(";","",$stylename);
		$stylename=str_replace("'","",$stylename);
		$stylename=str_replace("\"","",$stylename);
		$stylename=str_replace("\\","",$stylename);
		$stylename=str_replace("/","",$stylename);
		$stylename=str_replace("<","",$stylename);
		$stylename=str_replace(">","",$stylename);
		
		$content.='</design>';
		$content_disposition=(USR_BROWSER_AGENT == 'IE') ? 'inline;':'attachment;';
		
		
		header('Content-Type: '.$content_type);
		header('Content-disposition: '.$content_disposition.'filename='.$stylename.$this->styleexport_ext);
		header('Pragma: no-cache');
		header('Expires: 0');
			
		echo $content;
		exit;
		#pkHeaderDownload('fixed-name.xml');
#		$this->styleexport_ext);
#	header('Content-Length: '.$content_size);
/*
		$content_type =	strstr(getenv('HTTP_USER_AGENT'),'IE') ? 'text/plain' : 'application/plain';
		$content_size = strlen($content);
		$content=$content_type.$content;
		
		header('Content-Type: '.$content_type);

		header('Expires: 0');		
		
		if(strstr(getenv('HTTP_USER_AGENT'),'IE'))
			{
			header('Content-Disposition: inline; filename="'.$stylename.$this->styleexport_ext.'"');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			}
		else
			{
			header('Content-Disposition: attachment; filename="'.$stylename.$this->styleexport_ext.'"');
			header('Pragma: no-cache');
			}
*/
		
		exit($content);
		}
	#END methode export
	
	#methode import
	function import($dump)
		{
		$this->lastimport_id=0;
		$this->lastimport_name='';
		

		if(empty($dump) || !is_array($dump))
			return 0;

		$str = implode('',$dump);
		$str = preg_replace('/<!--(?:[^-]|-(?!->))*-->/','',$str);
		$dump = explode("\n",$str);
		
		$S=&$this->SQL;

		$tag=$value='';
		$tag_open = $is_style = $is_template = $is_value = false;
		$style=$templates=array();
		
		foreach($dump as $line)
			{
			$line = trim($line);
			if(empty($line))
				{
				continue;
				}
			
			if(strpos($line,'<style>')!==false)
				{
				$is_style=true;
				continue;
				}
				
			if(strpos($line,'</style>')!==false)
				{
				$is_style=false;
				continue;
				}

			if(strpos($line,'<templates>')!==false)
				{
				$is_template=true;
				continue;
				}
				
			if(strpos($line,'</templates>')!==false)
				{
				$is_template=false;
				continue;
				}
			
			#find tag
			for($i=0; $i<strlen($line); $i++)
				{
				$c=$line[$i];
				
				if($is_value && $c=='<')
					{
					$is_value=false;

					if($is_style)
						$style[$tag]=rawurldecode($value);
					elseif($is_template)
						$templates[$tag]=rawurldecode($value);
					
					$value=$tag='';
					break;
					}
				
				if(!$tag_open && $c=='<')
					{
					$tag_open=true;
					continue;
					}
				
				if($tag_open && $c=='>')
					{
					$tag_open=false;
					$is_value=true;
					continue;
					}

				if($is_value && $c!='<')
					$value.=$c;

				if($tag_open && $c!='>')
					$tag.=$c;
				} # for
			}#END foreach

		if(empty($style))
			return 0;

		$styleid=0;
		$stylename=!empty($style['style_name']) ? $style['style_name'] : 'no title';
		$sql_style=$sql_templates='';
			
		#is there a style with the same definitions? preventing double-entries
		$query=$S->query("SELECT * FROM ".pkSQLTAB_STYLE." WHERE style_name='".$S->f($stylename)."'");
		while($hash=$S->fetch_assoc($query))
			{
			$styleid = $hash['style_id'];
			
			foreach($hash as $key=>$value)
				{
				if($key=='style_id' || $key=='style_template')
					continue;

				if(!array_key_exists($key,$style) || $style[$key]!=$value)
					{
					$styleid=0;
					break;
					}
				}
			#style alread exists
			}#END while

		if($styleid)
			{
			$this->lastimport_id=$styleid;
			$this->lastimport_name=$stylename;			
			return $styleid;
			}

		
		$result=$S->query("DESCRIBE ".pkSQLTAB_STYLE);		
		while($info=$S->fetch_assoc($result))
			{
			if(array_key_exists($info['Field'],$style))
				{
				$sql_style.=(empty($sql_style) ? '' : ',').$info['Field']."='".$S->f($style[$info['Field']])."'";
				}
			}
			
		if(!empty($templates))
			{			
			$S->query("INSERT INTO ".pkSQLTAB_TEMPLATE_PACK." SET templatepack_name='".$S->f($stylename)."'");
			$templatepackid=$S->insert_id();

			foreach($templates as $k=>$v)
				{
				$sql_templates.=(empty($sql_templates) ? '' : ',')."('".$S->f(rawurldecode($k))."','".$S->f($v)."','".$S->i($templatepackid)."')";
				}
			
			$S->query("INSERT INTO ".pkSQLTAB_TEMPLATE." (template_name, template_value, template_packid) VALUES ".$sql_templates);
			}
		else
			{
			$templatepackid= -1;
			}
		
		$S->query("INSERT INTO ".pkSQLTAB_STYLE." 
			SET ".($templatepackid ? "style_template='".$S->i($templatepackid)."'," : '').
			$sql_style);
		
		$this->lastimport_id = $S->insert_id();
		$this->lastimport_name = $stylename;
		
		return $this->lastimport_id;
		}
	#END methode import
	
	function getLastImportId()
		{
		return $this->lastimport_id;
		}
		
	function getLastImportName()
		{
		return $this->lastimport_name;
		}
	
	#END public:methodes	
	#private:methodes (css)


	#private:methode css_bgcolor
	function css_bgcolor($color)
		{
		return empty($color) ? NULL : 'background-color:#'.$color.';';
		}
	#END methode css_bgcolor
	

	#private:methode css_bgimage	
	function css_bgimage($image,$repeat=false)
		{
		$bgimage='';
		
		if(empty($image))
			return NULL;

		$bgimage='background-image:url('.$this->imagedir().$image.');';
		
		if($repeat && in_array($repeat,$this->css_bgimage_repeat_definition))
			$bgimage.='background-repeat:'.$repeat.';';
		
		return $bgimage;
		}
	#END methode css_bgimage


	#private:methode css_color
	function css_color($color)
		{
		return empty($color) ? '' : 'color:#'.$color.';';
		}
	#END methode css_color
	

	#private:methode css_display	
	function css_display($var)
		{
		return $var ? '' : 'display:none;';
		}
	#END methode css_display
	
	
	#private:methode css_indent()
	function css_indent($indent,$align)
		{
		if($indent<=0)
			return '';
		
		if($align!='right' && $align!='center')
			$align='left';
		
		switch($align)
			{
			case 'center' :
				return 'padding-left:'.intval($indent).'px;'.
					'padding-right:'.intval($indent).'px;';
			case 'right' :
			case 'left' :
				return 'padding-'.$align.':'.intval($indent).'px;';
			}
		}
	#END private:methode css_indent

	
	#private:methode css_fontsize
	function css_fontsize($size)
		{
		return intval($size)>0 ? 'font-size:'.intval($size).'px;' : '';
		}
	#END private:methode css_fontsize


	#private:methode css_fontfamily
	function css_fontfamily($family)
		{
		return empty($family) ? '' : 'font-family:'.$family.';';
		}
	#private:methode css_fontfamily


	#private:methode css_width		
	function css_width($width)
		{
		return empty($width) ? '' : 'width:'.(substr($width,-1)=='%' ? $width : $width.'px').';';
		}
	#END methode css_width
	
	
	#END private:methodes (css)	
	#private:methodes (misc)
	
	#private:methode imagedir
	function imagedir()
		{
		return $this->imagedirpath;
		}
	#END methode imagedir
	

	#private:methode mkimagedirpath
	function mkimagedirpath()
		{
		$imagedirpath=$this->style['style_images'];
		
		while(substr($imagedirpath,-1,1)=='/')
			$imagedirpath=substr($imagedirpath,0,-1);

		
		$this->imagedirpath=$imagedirpath.'/';
		}
	#END methode mkimagedirpath


	#methode position
	function position($type,$top,$left)
		{
		$pos='';
		
		if($type=='center')
			$pos='margin:auto;';
		elseif($type=='right')
			$pos='margin-left:auto;margin-right:0;';

		if($top)
			$pos.='top:'.intval($top).'px;';

		if($left)
			$pos.='left:'.intval($left).'px;';

		return $pos;
		}
	#END methode position
	}
#END class pkStyle	
?>