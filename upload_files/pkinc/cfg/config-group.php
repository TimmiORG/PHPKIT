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


#@Note:	These groups are inserted into the database on setup.
return array(
array(
	'id'			=> 'master',
	'sorting'		=> 1,
	'lkey'			=> 'master_data',
	'lscope'		=> '',	
	),
array(
	'id'			=> 'meta',
	'sorting'		=> 2,	
	'lkey'			=> 'meta_settings',
	'lscope'		=> '',	
	),
array(
	'id'			=> 'mailer',
	'sorting'		=> 3,	
	'lkey'			=> 'mailer_settings',
	'lscope'		=> '',	
	),
array(
	'id'			=> 'frontpage',
	'sorting'		=> 4,	
	'lkey'			=> 'frontpage_settings',
	'lscope'		=> '',	
	),		
array(
	'id'			=> 'censor',
	'sorting'		=> 4,	
	'lkey'			=> 'censor_settings',
	'lscope'		=> '',	
	),
array(
	'id'			=> 'notify',
	'sorting'		=> 5,	
	'lkey'			=> 'notify_settings',
	'lscope'		=> '',	
	),
array(
	'id'			=> 'register',
	'sorting'		=> 6,	
	'lkey'			=> 'register_settings',
	'lscope'		=> '',	
	),
array(
	'id'			=> 'member',
	'sorting'		=> 7,	
	'lkey'			=> 'user_settings',
	'lscope'		=> 'user',	
	),
array(
	'id'			=> 'avatar',
	'sorting'		=> 8,	
	'lkey'			=> 'avatar_settings',
	'lscope'		=> '',	
	),
array(
	'id'			=> 'referer',
	'sorting'		=> 9,	
	'lkey'			=> 'referer_settings',
	'lscope'		=> '',	
	),
array(
	'id'			=> 'offline',
	'sorting'		=> 10,	
	'lkey'			=> 'maintenance_mode',
	'lscope'		=> 'except',	
	),
array(
	'id'			=> 'comment',
	'sorting'		=> 11,	
	'lkey'			=> 'comment_settings',
	'lscope'		=> '',	
	),
array(
	'id'			=> 'guestbook',
	'sorting'		=> 12,	
	'lkey'			=> 'guestbook_settings',
	'lscope'		=> 'guestbook',	
	),
array(
	'id'			=> 'content',
	'sorting'		=> 13,	
	'lkey'			=> 'content_settings',
	'lscope'		=> 'content',	
	),
array(
	'id'			=> 'forum',
	'sorting'		=> 14,	
	'lkey'			=> 'forum_settings',
	'lscope'		=> 'forum',	
	),
array(
	'id'			=> 'rssfeed',
	'sorting'		=> 15,
	'lkey'			=> 'rss_feed_settings',
	'lscope'		=> 'rss',	
	),
array(
	'id'			=> 'social',
	'sorting'		=> 15,
	'lkey'			=> 'social_networks_settings',
	'lscope'		=> 'social',
	),	
);
?>