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

if( !defined( 'pkFRONTEND' ) || pkFRONTEND != 'public' )
{
	die( 'Direct access to this location is not permitted.' );
}

//preset
$iconoption = $option_notify = $post_error = $user_info = '';

include( pkDIRPUBLICINC . 'forumsheader' . pkEXT );

if( intval( $_REQUEST[ 'postid' ] ) > 0 )
{
	$postid = intval( $_REQUEST[ 'postid' ] );

	$forumpost = $SQL->fetch_array( $SQL->query( "SELECT * FROM " . pkSQLTAB_FORUM_POST . " WHERE forumpost_id='" . $postid . "' LIMIT 1" ) );

	if( $forumpost[ 'forumpost_autorid' ] != pkGetUservalue( 'id' ) )
	{
		$forumthread = $SQL->fetch_array( $SQL->query( "SELECT forumthread_catid FROM " . pkSQLTAB_FORUM_THREAD . " WHERE forumthread_id='" .
		                                               $forumpost[ 'forumpost_threadid' ] . "' LIMIT 1" ) );
		$forumcat = $forumcat_cache[ $forumthread[ 'forumthread_catid' ] ];
	}
}
else
{
	$postid = 0;
}

if(pkGetUservalue( 'id' ) == 0)
{
	pkHeaderLocation( 'forumsthread', '', 'threadid=' . $forumpost[ 'forumpost_threadid' ] );
}

if( !userrights( $forumcat[ 'forumcat_mods' ] ) && (
		$forumpost[ 'forumpost_autorid' ] > 0 && $forumpost[ 'forumpost_autorid' ] != pkGetUservalue( 'id' ) ) )
{
	pkEvent( 'access_refused' );

	include( pkDIRPUBLICINC . 'forumsfooter' . pkEXT );
	return;
}

$ACTION = ( isset( $_POST[ 'action' ] ) ) ? $_POST[ 'action' ] : 'view';

if( !empty($_POST[ 'cancel' ]) && $ACTION == $_POST[ 'cancel' ] )
{
	pkHeaderLocation( 'forumsthread', '', 'threadid=' .  $forumpost[ 'forumpost_threadid' ] . '&postid=' . $postid, 'post' . $postid );
}

if($ENV->_post( 'delete_post' ) == 1 )
{
	eval( "\$site_body.= \"" . pkTpl( "forum/editpost_delete" ) . "\";" );

	include( pkDIRPUBLICINC . 'forumsfooter' . pkEXT );
	return;
}
elseif(!empty($_POST[ 'delete' ]) && $ACTION == $_POST[ 'delete' ] && !empty($_POST[ 'delete_confirm' ]) && $_POST[ 'delete_confirm' ] == "confirmed" )
{
	$SQL->query( "DELETE FROM " . pkSQLTAB_FORUM_POST . " WHERE forumpost_id='" . $postid . "' LIMIT 1" );

	if( $threadcount = $SQL->fetch_array( $SQL->query( "SELECT forumpost_time, forumpost_autor, forumpost_autorid FROM " . pkSQLTAB_FORUM_POST . " WHERE forumpost_threadid='" .
	                                                   $forumpost[ 'forumpost_threadid'
	                                                   ] . "' ORDER by forumpost_time DESC LIMIT 1" ) ) )
	{
		$SQL->query( "UPDATE " . pkSQLTAB_FORUM_THREAD . "
			SET forumthread_lastreply_time='" . intval( $threadcount[ 'forumpost_time' ] ) . "',
				forumthread_lastreply_autor='" . $SQL->f( $threadcount[ 'forumpost_autor' ] ) . "',
				forumthread_lastreply_autorid='" . intval( $threadcount[ 'forumpost_autorid' ] ) . "'
			WHERE forumthread_id='" . $forumpost[ 'forumpost_threadid' ] . "'" );

		pkHeaderLocation( 'forumsthread', '', 'threadid=' . $forumpost[ 'forumpost_threadid' ] );
	}

	$cat = $SQL->fetch_array( $SQL->query( "SELECT
			forumthread_catid
		FROM " . pkSQLTAB_FORUM_THREAD . "
		WHERE forumthread_id='" . $forumpost[ 'forumpost_threadid' ] . "'
		LIMIT 1" ) );

	$SQL->query( "DELETE FROM " . pkSQLTAB_FORUM_THREAD . "
		WHERE forumthread_id='" . $forumpost[ 'forumpost_threadid' ] . "'
		LIMIT 1" );

	pkHeaderLocation( 'forumscategory', '', 'catid=' . $cat[ 'forumthread_catid' ] );
}

if( !empty($_POST[ 'save' ]) && $ACTION == $_POST[ 'save' ] )
{
	$SQL->query( "UPDATE " . pkSQLTAB_FORUM_POST . "
		SET forumpost_title='" . $SQL->f( $_POST[ 'post_title' ] ) . "',
			forumpost_text='" . $SQL->f( $_POST[ 'content' ] ) . "',
			forumpost_icon='" . $SQL->f( $_POST[ 'post_icon' ] ) . "',
			forumpost_bbcode='" . $SQL->i( $_POST[ 'post_bbcode' ] ) . "',
			forumpost_smilies='" . $SQL->i( $_POST[ 'post_smilies' ] ) . "',
			forumpost_editcount=forumpost_editcount+1,
			forumpost_edittime='" . pkTIME . "',
			forumpost_editautor='" . $SQL->f( pkGetUservalue( 'nick' ) ) . "'
		WHERE forumpost_id='" . $postid . "'" );

	list( $firstpostid ) = $SQL->fetch_row( $SQL->query( "SELECT MIN(forumpost_id) FROM " . pkSQLTAB_FORUM_POST . " WHERE forumpost_threadid='" .
	                                                     $forumpost[ 'forumpost_threadid' ] . "' LIMIT 1" ) );

	if( $postid == $firstpostid )
	{
		$SQL->query( "UPDATE " . pkSQLTAB_FORUM_THREAD . "
			SET forumthread_title='" . $SQL->f( $_POST[ 'post_title' ] ) . "',
				forumthread_icon='" . $SQL->f( $_POST[ 'post_icon' ] ) . "'
			WHERE forumthread_id='" . $forumpost[ 'forumpost_threadid' ] . "'" );
	}

	pkHeaderLocation( 'forumsthread', '', 'threadid=' .
	                                      $forumpost[ 'forumpost_threadid' ] . '&postid=' . $postid, 'post' . $postid );
}

eval( "\$theme_icon= \"" . pkTpl( "forum/newpost_noicon" ) . "\";" );

$dir = "images/icons";
$width = 2;
$a = opendir( $dir );

while( $datei = readdir( $a ) )
{
	if( strstr( $datei, ".gif" ) )
	{
		if( $width == 10 )
		{
			$theme_icon .= "</tr><tr>";
			$width = 1;
		}

		if( $forumpost[ 'forumpost_icon' ] == $datei )
		{
			$iconoption = " checked";
		}

		eval( "\$theme_icon.= \"" . pkTpl( "forum/newpost_icons" ) . "\";" );
		$width++;

		$iconoption = '';
	}
}
closedir( $a );

$cs = 10 - $width;

if( $cs > 0 )
{
	$theme_icon .= '<td colspan="' . $cs . '"></td>';
}

$post_title = pkEntities( $forumpost[ 'forumpost_title' ] );
$post_text = pkEntities( $forumpost[ 'forumpost_text' ] );

unset( $sign_format );

if( $config[ 'forum_ubb' ] == 1 )
{
	if( $forumpost[ 'forumpost_bbcode' ] == 1 )
	{
		$bbcode = " checked";
	}

	eval( "\$sign_format= \"" . pkTpl( "format_text" ) . "\";" );
	eval( "\$option_bbcode= \"" . pkTpl( "forum/editpost_option_bbcode" ) . "\";" );
}

if( $config[ 'forum_smilies' ] == 1 )
{
	$SMILIES = new smilies( );
	$sign_format .= $SMILIES->getSmilies( 1 );

	if( $forumpost[ 'forumpost_smilies' ] == 1 )
	{
		$smilies = 'checked';
	}

	eval( "\$option_smilies= \"" . pkTpl( "forum/editpost_option_smilies" ) . "\";" );
}

if( $sign_format )
{
	eval( "\$sign_format= \"" . pkTpl( "format_table" ) . "\";" );
}

eval( "\$option_delete= \"" . pkTpl( "forum/editpost_option_delete" ) . "\";" );

if( $option_smilies != '' || $option_bbcode != '' || $option_delete != '' )
{
	eval( "\$editpost_option= \"" . pkTpl( "forum/editpost_option" ) . "\";" );
}

eval( "\$site_body.= \"" . pkTpl( "forum/editpost" ) . "\";" );

include( pkDIRPUBLICINC . 'forumsfooter' . pkEXT );
?>