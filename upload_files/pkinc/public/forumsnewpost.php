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
# Diese Datei / die PHPKIT Software ist keine Freeware! Für weitere
# Informationen besuchen Sie bitte unsere Website oder kontaktieren uns per E-Mail:
#
# email     : info@phpkit.com
# website   : http://www.phpkit.com
# licence   : http://www.phpkit.com/licence/phpkit
# copyright : Copyright (c) 2002-2009 mxbyte gbr | http://www.mxbyte.com

if( !defined( 'pkFRONTEND' ) || pkFRONTEND != 'public' )
{
	die( 'Direct access to this location is not permitted.' );
}

include( pkDIRPUBLICINC . 'forumsheader' . pkEXT );

$cat_option = $iconoption = $post_error = $post_title = $post_text = $notify = $option_vote = $row = $replyto = $option_notify = '';

if( $config[ 'forum_doublepost' ] != 1 && $threadid > 0 && pkGetUservalue( 'id' ) )
{
	$doublepostinfo = $SQL->fetch_array( $SQL->query( "SELECT forumpost_id, forumpost_autorid FROM " . pkSQLTAB_FORUM_POST . " WHERE forumpost_threadid='" . $threadid . "' ORDER by forumpost_time DESC LIMIT 1" ) );
}

if( $threadid == 0 && $catid == 0 )
{
	if( is_array( $forumcat_cache ) )
	{
		foreach( $forumcat_cache_byname as $selectcat )
		{
			if( ( userrights( $selectcat[ 'forumcat_mods' ] ) == "true" || userrights( $selectcat[ 'forumcat_user'
			                                                                           ] ) == "true" || getrights(
				                                                                                            $selectcat[
				                                                                                            'forumcat_trights'
				                                                                                            ] ) == "true" ) &&
			    $selectcat[ 'forumcat_status' ] == 1 && $selectcat[ 'forumcat_threads_option' ] == 1 )
			{
				$cat_option .= '<option value="' . $selectcat[ 'forumcat_id' ] . '">' . pkEntities(
					$selectcat[ 'forumcat_name' ] ) . '</option>';
			}
		}

		if( $cat_option )
		{
			eval( "\$cat_option= \"" . pkTpl( "forum/newpost_cat_option" ) . "\";" );
		}
	}
}

if( $catid > 0 && ( $forumcat[ 'forumcat_threads_option' ] != 1 || $forumcat[ 'forumcat_status' ] != 1 ) )
{
	pkEvent( 'forum_closed' );
}
elseif( ( $catid == 0 || !$catid ) && ( $threadid == 0 || !$threadid ) && !$cat_option )
{
	pkEvent( 'access_refused' );
}
elseif( $threadid != 0 && !pkGetConfig( 'forum_doublepost' ) && pkGetUservalue( 'id' ) && pkGetUservalue( 'id' ) ==
                                                                                          $doublepostinfo[
                                                                                          'forumpost_autorid' ] )
{
	pkEvent( 'entry_repeat', true, pkLink( 'forumsthread', '', 'threadid=' . $threadid ) );
}
elseif( $threadid != 0 && ( $forumthread[ 'forumthread_status' ] == 0 || $forumthread[ 'forumthread_status' ] == 3 ) )
{
	pkEvent( 'thread_closed', true, pkLink( 'forumsthread', '', 'threadid=' . $threadid ) );
}
elseif( ( $catid != 0 && ( userrights( $forumcat[ 'forumcat_mods' ] ) || userrights(
	$forumcat[ 'forumcat_user' ] ) || getrights(
	$forumcat[ 'forumcat_trights' ] ) ) ) || ( $catid == 0 && $threadid == 0 && $cat_option != '' ) #???
        || ( $threadid != 0 && ( getrights( $forumcat[ 'forumcat_wrights' ] ) || userrights(
			$forumcat[ 'forumcat_mods' ] ) || userrights( $forumcat[ 'forumcat_user' ] ) ) ) )
{
	$ACTION = ( isset( $_POST[ 'action' ] ) ) ? $_POST[ 'action' ] : 'view';

	if( ( !empty( $_POST[ 'save' ] ) && $ACTION == $_POST[ 'save' ] ) || ( !empty( $_POST[ 'preview' ] ) && $ACTION ==
	                                                                                                        $_POST[
	                                                                                                        'preview'
	                                                                                                        ] ) )
	{
		if( !pkGetUserValue( 'id' ) && !pkCaptchaCodeValid( $ENV->_post( pkCAPTCHAVARNAME ) ) )
		{
			$ACTION = '';
		}

		if( !empty( $_POST[ 'content' ] ) )
		{
			$post_text = $_POST[ 'content' ];
		}
		else
		{
			$ACTION = '';
		}

		if( !$_POST[ 'threadid' ] && trim( $_POST[ 'post_title' ] ) != '' )
		{
			$post_title = $_POST[ 'post_title' ];
		}
		elseif( !$_POST[ 'threadid' ] )
		{
			$ACTION = '';
		}

		if( pkGetUservalue( 'id' ) )
		{
			$post_autor = pkGetUservalue( 'nick' );
		}
		elseif( !pkGetUservalue( 'id' ) && !empty( $_POST[ 'post_autor' ] ) && checkusername(
			$_POST[ 'post_autor' ], 1 ) )
		{
			$post_autor = $_POST[ 'post_autor' ];
		}
		elseif( !pkGetUservalue( 'id' ) )
		{
			$post_autor = $_POST[ 'post_autor' ];
			$ACTION = '';
		}
	}

	if( !empty( $_POST[ 'save' ] ) && $ACTION == $_POST[ 'save' ] && ( isset( $_POST[ 'threadid' ] ) || isset(
	$_POST[ 'catid' ] ) ) )
	{
		$check_time = pkTIME - 1800;
		$SQL->query( "UPDATE " . pkSQLTAB_FORUM_POST . " SET forumpost_uid='' WHERE forumpost_time<'" . $check_time . "'" );
		$postuid = $SQL->fetch_array( $SQL->query( "SELECT COUNT(*) FROM " . pkSQLTAB_FORUM_POST . " WHERE forumpost_uid='" . $SQL->f(
			$_POST[ 'post_uid' ] ) . "' AND forumpost_time>'" . $check_time . "' LIMIT 1" ) );

		if( $postuid[ 0 ] == 0 )
		{
			unset( $update_threadcount );

			if( $threadid > 0 )
			{
				$SQL->query( "UPDATE " . pkSQLTAB_FORUM_THREAD . "
					SET forumthread_replycount=forumthread_replycount+1,
					forumthread_lastreply_autor='" . $SQL->f( $_POST[ 'post_autor' ] ) . "',
					forumthread_lastreply_time='" . pkTIME . "',
					forumthread_lastreply_autorid='" . $SQL->i( pkGetUservalue( 'id' ) ) . "'
				WHERE forumthread_id='" . $threadid . "'" );
			}
			else
			{
				$SQL->query( "INSERT INTO " . pkSQLTAB_FORUM_THREAD . "
					(forumthread_autor,forumthread_autorid,forumthread_catid,
					 forumthread_icon,forumthread_title,forumthread_lastreply_time,
					 forumthread_lastreply_autor,forumthread_lastreply_autorid)  
					VALUES
					('" . $SQL->f( $_POST[ 'post_autor' ] ) . "',
					 '" . $SQL->i( pkGetUservalue( 'id' ) ) . "',
					 '" . $SQL->i( $catid ) . "',
					 '" . $SQL->f( $_POST[ 'post_icon' ] ) . "',
					 '" . $SQL->f( $_POST[ 'post_title' ] ) . "',
					 '" . pkTIME . "',
					 '" . $SQL->f( $_POST[ 'post_autor' ] ) . "',
					 '" . $SQL->i( pkGetUservalue( 'id' ) ) . "')" );

				$threadid = $SQL->insert_id( );
				$update_threadcount = ", forumcat_threadcount=forumcat_threadcount+1";
			}

			$sqlcommand = "forumcat_id='" . $catid . "'";
			$id = $forumcat_cache[ $catid ][ 'forumcat_subcat' ];

			if( $id > 0 )
			{
				while( $id > 0 )
				{
					$sqlcommand .= " OR forumcat_id='" . $id . "'";
					$id = $forumcat_cache[ $id ][ 'forumcat_subcat' ];
				}
			}

			$SQL->query( "UPDATE " . pkSQLTAB_FORUM_CATEGORY . "
				SET forumcat_postcount=forumcat_postcount+1 " . $update_threadcount . ",
					forumcat_lastreply_time='" . pkTIME . "',
					forumcat_lastreply_threadid='" . $SQL->i( $threadid ) . "',
					forumcat_lastreply_autor='" . $SQL->f( $_POST[ 'post_autor' ] ) . "',
					forumcat_lastreply_autorid='" . $SQL->i( pkGetUservalue( 'id' ) ) . "' WHERE " . $sqlcommand );

			$SQL->query( "INSERT INTO " . pkSQLTAB_FORUM_POST . "
				(forumpost_threadid, forumpost_autor, forumpost_autorid, 
				 forumpost_icon, forumpost_title, forumpost_text,
				 forumpost_time, forumpost_bbcode, forumpost_smilies,
				 forumpost_uid, forumpost_ipaddr, forumpost_reply) 
				VALUES 
				('" . $SQL->i( $threadid ) . "',
				 '" . $SQL->f( $_POST[ 'post_autor' ] ) . "',
				 '" . $SQL->i( pkGetUservalue( 'id' ) ) . "',
				 '" . $SQL->f( $_POST[ 'post_icon' ] ) . "',
				 '" . $SQL->f( $_POST[ 'post_title' ] ) . "',
				 '" . $SQL->f( $_POST[ 'content' ] ) . "',
				 '" . pkTIME . "',
				 '" . $SQL->i( $_POST[ 'post_bbcode' ] ) . "',
				 '" . $SQL->i( $_POST[ 'post_smilies' ] ) . "',
				 '" . $SQL->f( $_POST[ 'post_uid' ] ) . "',
				 '" . $SQL->f( $ENV->getvar( 'REMOTE_ADDR' ) ) . "',
				 '" . $SQL->id( $_POST[ 'replyto' ] ) . "')" );

			$postid = $SQL->insert_id( );

			if( pkGetUservalue( 'id' ) )
			{
				$userposts = $SQL->fetch_array( $SQL->query( "SELECT COUNT(*) FROM " . pkSQLTAB_FORUM_POST . " WHERE forumpost_autorid='" . pkGetUservalue( 'id' ) . "'" ) );
				$SQL->query( "UPDATE " . pkSQLTAB_USER . " SET user_posts='" .
				             $userposts[ 0 ] . "' WHERE user_id='" . pkGetUservalue( 'id' ) . "'" );
			}
		}

		if( pkGetUservalue( 'id' ) )
		{
			$exception = " AND forumnotify_userid!='" . pkGetUservalue( 'id' ) . "'";
		}
		else
		{
			unset( $exception );
		}

		unset( $mailhash );
		unset( $sqlcommand );

		$getnotify = $SQL->query( "SELECT forumnotify_email, forumnotify_userid FROM " . pkSQLTAB_FORUM_NOTIFY . " WHERE forumnotify_threadid='" . $threadid . "' " . $exception );
		while( $notify = $SQL->fetch_array( $getnotify ) )
		{
			if( $notify[ 'forumnotify_userid' ] > 0 )
			{
				if( $sqlcommand )
				{
					$sqlcommand .= " OR user_id='" . $notify[ 'forumnotify_userid' ] . "'";
				}
				else
				{
					$sqlcommand = "SELECT user_email FROM " . pkSQLTAB_USER . " WHERE user_id='" .
					              $notify[ 'forumnotify_userid' ] . "'";
				}
			}
			else
			{
				$mailhash[ $notify[ 'forumnotify_email' ] ] = $notify[ 'forumnotify_email' ];
			}
		}

		if( $sqlcommand )
		{
			$getmails = $SQL->query( $sqlcommand );
			while( $userinfo = $SQL->fetch_array( $getmails ) )
			{
				$mailhash[ $userinfo[ 'user_email' ] ] = $userinfo[ 'user_email' ];
			}
		}

		pkLoadLang( 'email' );

		$forumthread = $SQL->fetch_assoc( $SQL->query( "SELECT forumthread_title FROM " . pkSQLTAB_FORUM_THREAD . " WHERE forumthread_id='" . $threadid . "' LIMIT 1" ) );

		#mail notify
		$link = pkGetConfig( 'site_url' ) . '/include.php?path=forumsthread&threadid=' . $threadid . '&postid=' . $postid;

		$mail_title = pkGetSpecialLang( 'newpost_notify_mail_title', pkGetConfig( 'site_name' ),
		                                $forumthread[ 'forumthread_title' ] );
		$mail_text = pkGetSpecialLang( 'newpost_notify_mail_body', pkGetConfig( 'site_name' ), $post_autor, $link );

		notifymail( 'forum', $mail_title, $mail_text );

		#pn notify
		$pn_title = pkGetLang( 'new_post' ) . ' ' . $_POST[ 'post_title' ];
		$pn_text = pkGetSpecialLang( 'newpost_pn_notify', $post_autor, $threadid, $postid );

		notifyim( 'forum', $pn_title, $pn_text );

		if( is_array( $mailhash ) )
		{
			$mail_title = pkGetSpecialLang( 'newpost_subscriber_mail_title', pkGetConfig( 'site_name' ),
			                                $forumthread[ 'forumthread_title' ] );
			$mail_text = pkGetSpecialLang( 'newpost_subscriber_mail_body', pkGetConfig( 'site_name' ), $post_autor,
			                               $forumthread[ 'forumthread_title'
			                               ], pkGetConfig( 'site_url' ) . '/include.php?path=forumsthread&threadid=' . $threadid . '&postid=' . $postid, pkGetConfig( 'site_name' ), pkGetConfig( 'site_url' ) );

			foreach( $mailhash as $email )
			{
				mailsender( $email, $mail_title, $mail_text );
			}
		}

		if( $_POST[ 'post_notify' ] == 1 && $threadid > 0 )
		{
			$post_email = pkGetUservalue( 'id' ) ? pkGetUservalue( 'email' ) : $_POST[ 'post_email' ];

			if( emailcheck( $post_email ) )
			{
				$SQL->query( "INSERT INTO " . pkSQLTAB_FORUM_NOTIFY . " (forumnotify_userid,forumnotify_email,forumnotify_threadid) VALUES ('" . $SQL->i( pkGetUservalue( 'id' ) ) . "','" . $SQL->f( $post_email ) . "','" . $SQL->i( $threadid ) . "')" );
			} 
		}

		$FORUM->setReaded( $catid, $threadid, pkTIME );

		pkHeaderLocation( 'forumsthread', '', 'threadid=' . $threadid . '&postid=' . $postid, 'post' . $postid );
	}
	else
	{
		pkLoadClass( $BBCODE, 'bbcode' );

		$error_message = '';

		if( !empty( $_POST[ 'preview' ] ) && $ACTION == $_POST[ 'preview' ] )
		{
			$row = 'odd';

			if( !empty( $_POST[ 'post_icon' ] ) )
			{
				$post_icon = "icons/" . basename( $_POST[ 'post_icon' ] );

				eval( "\$post_icon= \"" . pkTpl( "forum/showthread_row_posticon" ) . "\";" );
			}

			$post_time = formattime( );
			$post_autor = pkEntities( $post_autor );
			$post_title = pkEntities( $post_title );
			$post_text = $BBCODE->parse(
				$_POST[ 'content' ], 0, intval( $_POST[ 'post_bbcode' ] ), intval( $_POST[ 'post_smilies' ] ),
				$config[ 'forum_images' ], 1, pkGetConfig( 'forum_imageresize' ), pkGetConfig( 'forum_textwrap' ) );

			eval( "\$preview_row= \"" . pkTpl( "forum/showthread_row" ) . "\";" );
			eval( "\$site_body.= \"" . pkTpl( "forum/newpost_preview" ) . "\";" );
		}
		elseif( !isset( $ACTION ) || $ACTION != 'view' )
		{
			$errorcount = 0;

			if( !pkCaptchaCodeValid( $ENV->_post( pkCAPTCHAVARNAME ) ) && !pkGetUserValue( 'id' ) && isset(
			$_POST[ 'action' ] ) && $_POST[ 'action' ] != $_POST[ 'preview' ] )
			{
				$errorcount++;
				$error_message .= pkGetLang( 'newpost_error_captcha' ); #pkGetLang('');
			}

			if( empty( $post_text ) )
			{
				$errorcount++;
				$error_message .= pkGetLang( 'newpost_error_text' );
			}

			if( !$threadid && empty( $post_title ) )
			{
				$errorcount++;
				$error_message .= pkGetLang( 'newpost_error_title' );
			}

			if( empty( $post_autor ) )
			{
				$errorcount++;
				$error_message .= pkGetLang( 'newpost_error_author' );
			}
			elseif( !checkusername( $post_autor, 1 ) )
			{
				$errorcount++;
				$error_message .= pkGetLang( 'newpost_error_name' );
			}

			if( $errorcount )
			{
				$lang_newpost_error_occured = $errorcount > 1 ? pkGetSpecialLang( 'newpost_errors_occured', $errorcount ) : pkGetLang( 'newpost_error_occured' );
				eval( "\$post_error=\"" . pkTpl( "forum/newpost_error" ) . "\";" );
			}
		}

		if( !empty( $_GET[ 'quote' ] ) && intval( $_GET[ 'quote' ] ) > 0 )
		{
			if( $forumpost = $SQL->fetch_array( $SQL->query( "SELECT forumpost_text, forumpost_title, forumpost_autor FROM " . pkSQLTAB_FORUM_POST . " WHERE forumpost_id='" . intval(
				$_GET[ 'quote' ] ) . "' AND forumpost_threadid='" . $threadid . "' LIMIT 1" ) ) )
			{
				$replyto = intval( $_GET[ 'replyto' ] );

				$post_title = 'RE: ' . pkEntities( $forumpost[ 'forumpost_title' ] );
				$post_text = '[quote][i]' . $lang[ 'original_written_by' ] . ' ' .
				             $forumpost[ 'forumpost_autor' ] . "[/i]\n\n" . pkEntities(
					$forumpost[ 'forumpost_text' ] ) . '[/quote]';
			}
		}

		elseif( isset( $_POST[ 'replyto' ] ) && intval( $_POST[ 'replayto' ] ) > 0 )
		{
			$replyto = intval( $_POST[ 'replyto' ] );
		}

		if( pkGetUservalue( 'id' ) )
		{
			$usernick = pkGetUservalueF( 'nick' );
                        eval( "\$option_notify= \"" . pkTpl( "forum/newpost_option_notify_form" ) . "\";" );
			eval( "\$user_info= \"" . pkTpl( "forum/newpost_user" ) . "\";" );
		}
		else
		{
			if( empty( $post_autor ) )
			{
				$post_autor = pkGetUservalueF( 'nick' );
			}

			eval( "\$user_info= \"" . pkTpl( "forum/newpost_guest" ) . "\";" );
			
		}

		unset( $sign_format );

		if( $config[ 'forum_ubb' ] == 1 )
		{
			if( !empty( $_POST[ 'post_bbcode' ] ) && intval( $_POST[ 'post_bbcode' ] == 1 ) || $ACTION == 'view' )
			{
				$bbcode = 'checked';
			}

			eval( "\$sign_format= \"" . pkTpl( "format_text" ) . "\";" );
			eval( "\$option_bbcode= \"" . pkTpl( "forum/newpost_option_bbcode" ) . "\";" );
		}

		if( $config[ 'forum_smilies' ] == 1 )
		{
			$smilies = new smilies( );
			$sign_format .= $smilies->getSmilies( 1 );

			$smilies = '';
			if( !empty( $_POST[ 'post_smilies' ] ) && intval( $_POST[ 'post_smilies' ] ) == 1 || $ACTION == 'view' )
			{
				$smilies = 'checked = "checked"';
			}


			eval( "\$option_smilies= \"" . pkTpl( "forum/newpost_option_smilies" ) . "\";" );
		}

		if( $sign_format )
		{
			eval( "\$sign_format= \"" . pkTpl( "format_table" ) . "\";" );
		}

		eval( "\$theme_icon= \"" . pkTpl( "forum/newpost_noicon" ) . "\";" );

		$dir = 'images/icons';
		$width = 2;
		$a = opendir( $dir );

		while( $datei = readdir( $a ) )
		{
			$iconoption = '';
			if( strstr( $datei, ".gif" ) )
			{
				if( $width == 10 )
				{
					$theme_icon .= "</tr><tr>";
					$width = 1;
				}

				if( !empty( $_POST[ 'post_icon' ] ) && basename( $_POST[ 'post_icon' ] ) == $datei )
				{
					$iconoption = ' checked="checked"';
				}

				eval( "\$theme_icon.= \"" . pkTpl( "forum/newpost_icons" ) . "\";" );
				$width++;
				$iconoption = '';
			}
		}

		$cs = 10 - $width;

		if( $cs > 0 )
		{
			$theme_icon .= '<td colspan="' . $cs . '">&nbsp;</td>';
		}

		closedir( $a );

		if( empty( $_POST[ 'post_uid' ] ) )
		{
			srand( (double)microtime( ) * 1000000 );
			$post_uid = md5( uniqid( rand( ) ) );
		}
		else
		{
			$post_uid = pkEntities( $_POST[ 'post_uid' ] );
			$post_title = pkEntities( $_POST[ 'post_title' ] );
			$post_text = pkEntities( $_POST[ 'content' ] );

			if( $_POST[ 'post_notify' ] == 1 )
			{
				$notify = 'checked';
			}
		}

		#captcha only shown for guests
		$captcha = pkGetUserValue( 'id' ) ? '' : pkCaptchaField( );

		eval( "\$site_body.= \"" . pkTpl( "forum/newpost" ) . "\";" );

		if( $config[ 'forum_viewreply' ] > 0 && $threadid > 0 )
		{
			$reply_row = '';

			$query = $SQL->query( "SELECT * FROM " . pkSQLTAB_FORUM_POST . " WHERE forumpost_threadid='" . $threadid . "' ORDER BY forumpost_time DESC LIMIT " . pkGetConfig( 'forum_viewreply' ) );
			while( $replypost = $SQL->fetch_assoc( $query ) )
			{
				$row = rowcolor( $row );

				$replypost_text = $BBCODE->parse( $replypost[ 'forumpost_text' ], 0, $replypost[ 'forumpost_bbcode' ],
				                                  $replypost[ 'forumpost_smilies' ], $config[ 'forum_images'
				                                                                     ], 1, pkGetConfig( 'forum_imageresize' ), pkGetConfig( 'forum_textwrap' ) );
				$replypost_time = formattime( $replypost[ 'forumpost_time' ] );
				$replypost_autor = pkEntities( $replypost[ 'forumpost_autor' ] );

				eval( "\$reply_row.=\"" . pkTpl( "forum/newpost_viewreplys_row" ) . "\";" );
			}

			if( $reply_row )
			{
				eval( "\$site_body.=\"" . pkTpl( "forum/newpost_viewreplys" ) . "\";" );
			}
		}
	}
}
else
{
	pkEvent( 'access_refused' );
}

include( pkDIRPUBLICINC . 'forumsfooter' . pkEXT );
?>