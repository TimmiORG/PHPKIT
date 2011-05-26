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


return array(
array(
	'tag'	=> 'hr',
	'html'	=> '<hr />',
	'type'	=> 'single',
	),
array(
	'tag'	=>'list=1',
	'html'	=>'<ol type="1">{text}</ol>',
	'type'	=> 'list',
	),
array(
	'tag'	=> 'list=a',
	'html'	=> '<ol type="a">{text}</ol>',
	'type'	=> 'list',
	),
array(
	'tag'	=> 'list',
	'html'	=> '<ul>{text}</ul>',
	'type'	=> 'list',
	),
array(
	'tag'	=>	'b',
	'html'	=> '<b>{text}</b>',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'i',
	'html'	=> '<i>{text}</i>',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'u',
	'html'	=> '<u>{text}</u>',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'd',
	'html'	=> '<strike>{text}</strike>',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'h2',
	'html'	=> '<h2>{text}</h2>',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'h3',
	'html'	=> '<h3>{text}</h3>',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'h4',
	'html'	=> '<h4>{text}</h4>',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'p',
	'html'	=> '<p>{text}</p>',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'sub',
	'html'	=> '<sub>{text}</sub>',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'sup',
	'html'	=> '<sup>{text}</sup>',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'left',
	'html'	=> '<div style="text-align:left">{text}</div>',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'center',
	'html'	=> '<div style="text-align:center">{text}</div>',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'right',
	'html'	=> '<div style="text-align:right">{text}</div>',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'block',
	'html'	=> '<div style="text-align:justify">{text}</div>',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'code',
	'html'	=> '<code>{text}</code>',
	'type'	=> '',#empty=default
	),
array(#@TODO: Revise the HTML-Code an single div-container with headline is much smarter
	'tag'	=> 'quote',
	'html'	=> '<table class="quote" width="98%" cellpadding="4" cellspacing="1" align="center"><tr><td class="quote"><b>'.pkGetLang('quote').'</b><br /><font class="quote">{text}</font></td></tr></table><br />',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'white',
	'html'	=> '[color=#FFFFFF]{text}[/color]',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'gray',
	'html'	=> '[color=#CCCCCC]{text}[/color]',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'dimgray',
	'html'	=> '[color=#333333]{text}[/color]',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'navy',
	'html'	=> '[color=#003399]{text}[/color]',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'royalblue',
	'html'	=> '[color=#0099FF]{text}[/color]',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'red',
	'html'	=> '[color=#FF3333]{text}[/color]',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'orange',
	'html'	=> '[color=#FF7F00]{text}[/color]',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'yellow',
	'html'	=> '[color=#FFFF00]{text}[/color]',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'green',
	'html'	=> '[color=#00FF00]{text}[/color]',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'white',
	'html'	=> '[color=#333333]{text}[/color]',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'color',
	'html'	=> '<span style="color:{option};">{text}</span>',
	'type'	=> 'double',
	),
array(
	'tag'	=> 'email',
	'html'	=> '<a href="mailto:{text}">{text}</a>',
	'type'	=> '',#empty=default
	),
array(
	'tag'	=> 'email',
	'html'	=> '<a href="mailto:{option}">{text}</a>',
	'type'	=> 'double',
	),
array(
	'tag'	=> 'img',
	'html'	=> '<img border="0" alt="" src="{image}" />',
	'type'	=> 'img'
	),
array(
	'tag'	=> 'imgr',
	'html'	=> '<img border="0" alt="" src="{image}" align="right" />',
	'type'	=> 'img',
	),
array(
	'tag'	=> 'imgl',
	'html'	=> '<img border="0" alt="" src="{image}" align="left" />',
	'type'	=> 'img',
	),	
array(
	'tag'	=> 'url',
	'html'	=> '<a href="{option}" target="_blank">{text}</a>',
	'type'	=> 'url',
	),
);
?>