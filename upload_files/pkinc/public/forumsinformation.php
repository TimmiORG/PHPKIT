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

if ( !defined( 'pkFRONTEND' ) || pkFRONTEND != 'public' )
{
	die( 'Direct access to this location is not permitted.' );
}

include( pkDIRPUBLICINC . 'forumsheader' . pkEXT );

$finfoid = ( isset( $_REQUEST[ 'finfoid' ] ) && intval( $_REQUEST[ 'finfoid' ] ) > 0 ) ? intval(
	$_REQUEST[ 'finfoid' ] ) : 0;

$foruminfo = $SQL->fetch_array( $SQL->query( "SELECT * FROM " . pkSQLTAB_FORUM_INFO . " WHERE foruminfo_id='" . $finfoid . "' AND foruminfo_time<'" . pkTIME . "' AND (foruminfo_expire=0 OR foruminfo_expire>'" . pkTIME . "') LIMIT 1" ) );

if ( $foruminfo[ 'foruminfo_catids' ] != 0 && !strstr( $foruminfo[ 'foruminfo_catids' ], '-' . $catid . '-' ) )
{
	pkEvent( 'access_refused' );
}
elseif ( !( getrights( $forumcat[ 'forumcat_rrights' ] ) == "true" || userrights( $forumcat[ 'forumcat_mods' ],
                                                                                  $forumcat[ 'forumcat_rrights'
                                                                                  ] ) == "true" || userrights(
	                                                                                                   $forumcat[
	                                                                                                   'forumcat_user'
	                                                                                                   ], $forumcat[
	                                                                                                      'forumcat_rrights'
	                                                                                                      ] ) == "true" ) )
{
	pkEvent( 'access_refused' );
}
else
{
	pkLoadClass( $BBCODE, 'bbcode' );
	pkLoadFunc( 'user' );

	$post_title = pkEntities( $foruminfo[ 'foruminfo_title' ] );
	$post_text = $BBCODE->parse( $foruminfo[ 'foruminfo_text' ], 0, 1, 1, 1 );
	$post_time = formattime( $foruminfo[ 'foruminfo_time' ] );

	if ( !$userinfo = $SQL->fetch_array( $SQL->query( "SELECT * FROM " . pkSQLTAB_USER . " WHERE user_id='" .
	                                                  $foruminfo[ 'foruminfo_autorid' ] . "' LIMIT 1" ) ) )
	{
		$userinfo = $SQL->fetch_array( $SQL->query( "SELECT * FROM " . pkSQLTAB_USER . " WHERE user_status='admin' LIMIT 1" ) );
	}

	$userinfo[ 'user_nick' ] = pkEntities( $userinfo[ 'user_nick' ] );

	if ( isonline( $userinfo[ 'user_id' ] ) )
	{
		eval( "\$info_os= \"" . pkTpl( "member_os_online" ) . "\";" );
	}
	else
	{
		eval( "\$info_os= \"" . pkTpl( "member_os_offline" ) . "\";" );
	}

	if ( $userinfo[ 'user_status' ] == 'admin' && $userinfo[ 'user_sex' ] == 'w' )
	{
		eval( "\$post_autor_status= \"" . pkTpl( "forum/showthread_userstatus_admin_w" ) . "\";" );
	}
	elseif ( $userinfo[ 'user_status' ] == 'admin' )
	{
		eval( "\$post_autor_status= \"" . pkTpl( "forum/showthread_userstatus_admin" ) . "\";" );
	}
	elseif ( $userinfo[ 'user_status' ] == "mod" && $userinfo[ 'user_sex' ] == 'w' )
	{
		eval( "\$post_autor_status= \"" . pkTpl( "forum/showthread_userstatus_mod_w" ) . "\";" );
	}
	elseif ( $userinfo[ 'user_status' ] == "mod" )
	{
		eval( "\$post_autor_status= \"" . pkTpl( "forum/showthread_userstatus_mod" ) . "\";" );
	}

	if ( $userinfo[ 'user_posts' ] > 0 )
	{
		$post_count = postcount( $userinfo[ 'user_posts' ], $userinfo[ 'user_postdelay' ], 0 );
	}
	else
	{
		$postings = $SQL->fetch_array( $SQL->query( "SELECT COUNT(*) FROM " . pkSQLTAB_FORUM_POST . " WHERE forumpost_autorid='" . $userinfo[ 'user_id' ] . "'" ) );
		$SQL->query( "UPDATE " . pkSQLTAB_USER . " SET user_posts='" . $postings[ 0 ] . "' WHERE user_id='" .
		             $userinfo[ 'user_id' ] . "' LIMIT 1" );
		$post_count = postcount( $postings[ 0 ], $userinfo[ 'user_postdelay' ], 0 );
	}

	if ( $userinfo[ 'user_hpage' ] != '' )
	{
		if ( stripos( "http://", $userinfo[ 'user_hpage' ] ) !== FALSE )
		{
			$info_link = pkEntities( $userinfo[ 'user_hpage' ] );
		}
		else
		{
			$info_link = 'http://' . $userinfo[ 'user_hpage' ];
		}

		eval( "\$info_hpage= \"" . pkTpl( "forum/member_hpage_iconlink" ) . "\";" );
	}

	if ( $userinfo[ 'user_emailshow' ] == 1 )
	{
		if ( $config[ 'member_mailer' ] == 1 )
		{
			eval( "\$info_email= \"" . pkTpl( "forum/member_email_iconlink2" ) . "\";" );
		}
		else
		{
			eval( "\$info_email= \"" . pkTpl( "forum/member_email_iconlink" ) . "\";" );
		}
	}

	if ( $userinfo[ 'user_icqid' ] > 0 )
	{
		eval( "\$info_icq= \"" . pkTpl( "forum/member_icq_iconlink" ) . "\";" );
	}

	if ( $userinfo[ 'user_imoption' ] == 1 )
	{
		eval( "\$info_im= \"" . pkTpl( "forum/member_sendim_iconlink" ) . "\";" );
	}

	eval( "\$post_autor= \"" . pkTpl( "forum/member_showprofil_textlink" ) . "\";" );
	eval( "\$info_buddie= \"" . pkTpl( "forum/member_buddie_iconlink" ) . "\";" );

	if ( $config[ 'avatar_eod' ] != 0 && $userinfo[ 'user_avatar' ] != "" && filecheck(
		$config[ 'avatar_path' ] . '/' . $userinfo[ 'user_avatar' ] ) )
	{
		$avatar_dimension = @getimagesize( $config[ 'avatar_path' ] . "/" . $userinfo[ 'user_avatar' ] );
		eval( "\$avatar_show= \"" . pkTpl( "user_avatar_show" ) . "\";" );
	}

	$info_sig = pkUserSignature( $userinfo[ 'user_sig' ] );

	eval( "\$info_user= \"" . pkTpl( "forum/member_userinfo_iconlink" ) . "\";" );
	eval( "\$site_body.= \"" . pkTpl( "forum/showinfo" ) . "\";" );
}

include( pkDIRPUBLICINC . 'forumsfooter' . pkEXT );
?>