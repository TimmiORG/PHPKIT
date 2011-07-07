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

// presets
$kopf_extension = $kopf_favorit = $kopf_userinfo = $forum_action = $new_im_msg = $kopf_login = $forum_path = $userinfo_cache = '';

if( !pkGetConfig( 'forum_eod' ) )
{
	pkHeaderLocation( '', '', 'event=forum_closed' );
}

if( $ENV->_isset_get( 'newposttime' ) )
{
	$SQL->query( "UPDATE " . pkSQLTAB_USER . " SET lastlog='" . pkTIME . "' WHERE user_id='" . pkGetUservalue( 'id' ) . "'" );
	$SQL->query( "DELETE FROM " . pkSQLTAB_FORUM_THREAD_READED . " WHERE sid='" . $SQL->f( $SESSION->getid( ) ) . "'" );

	pkSetUservalue( 'lastlog', pkTIME );
	$ENV->setCookie( 'lastlog', pkTIME );
}

if( rand( 1, 100 ) == 69 )
{
	$cleanup = '';

	$query = $SQL->query( "SELECT f.sid FROM " . pkSQLTAB_FORUM_THREAD_READED . " AS f,
			" . pkSQLTAB_SESSION . " AS s
		WHERE f.sid=s.session_id
		GROUP BY f.sid" );
	while( list( $sid ) = $SQL->fetch_row( $query ) )
	{
		$cleanup .= ( empty( $cleanup ) ? '' : ' AND ' ) . "sid<>'" . $SQL->f( $sid ) . "'";
	}

	if( !empty( $cleanup ) )
	{
		$SQL->query( "DELETE FROM " . pkSQLTAB_FORUM_THREAD_READED . " WHERE " . $cleanup );
	}
}

pkLoadLang( 'forum' );
pkLoadClass( $FORUM, 'forum' );

#new threads since last visit
$query = $SQL->query( "SELECT forumthread_id,
		forumthread_catid,
		forumthread_lastreply_time 
	FROM " . pkSQLTAB_FORUM_THREAD . "
	WHERE forumthread_status IN(1,2) AND
		forumthread_catid IN(0" . implode( ',', $FORUM->getCategories( ) ) . ") AND
		forumthread_lastreply_time>'" . pkGetUservalue( 'lastlog' ) . "'" );
while( list( $threadid, $catid, $time ) = $SQL->fetch_row( $query ) )
{
	$pkFORUMNEWTHREADS[ $catid ][ $threadid ] = $time;
}

$query = $SQL->query( "SELECT
	threadid,
	rtime
	FROM " . pkSQLTAB_FORUM_THREAD_READED . "
	WHERE sid='" . $SQL->f( $SESSION->getid( ) ) . "'" );
while( list( $threadid, $rtime ) = $SQL->fetch_row( $query ) )
{
	$pkFORUMREADEDTHREADS[ $threadid ] = $rtime;
}

if( pkGetConfig( 'forum_standalone' ) == 2 || pkGetConfig( 'forum_standalone' ) == 9 )
{
	$pkNAVIGATIONHIDE[ 'left' ] = 1;
}

if( pkGetConfig( 'forum_standalone' ) == 3 || pkGetConfig( 'forum_standalone' ) == 9 )
{
	$pkNAVIGATIONHIDE[ 'right' ] = 1;
}

$threadid = isset( $_REQUEST[ 'threadid' ] ) && intval( $_REQUEST[ 'threadid' ] ) > 0 ? intval(
	$_REQUEST[ 'threadid' ] ) : 0;
$catid = isset( $_REQUEST[ 'catid' ] ) && intval( $_REQUEST[ 'catid' ] ) > 0 ? intval( $_REQUEST[ 'catid' ] ) : 0;

$forumcat_cache_byname = $forumcat_cache = $FORUM->getTree( );
$catcount = $FORUM->getCountCategories( );
$threadcount = $FORUM->getCountThreads( );
$postcount = $FORUM->getCountPostings( );
$lang_posts_and_threads_in_forums = pkGetSpecialLang( 'posts_and_threads_in_forums', $postcount, $threadcount, $catcount );

#set the prefix and suffix overwrite
#will not overwrite the default if the prefix/suffix is empty
$CMS->site_title_prefix_set( pkGetConfig( 'forum_title_prefix' ) );
$CMS->site_title_suffix_set( pkGetConfig( 'forum_title_suffix' ) );

if( $threadid > 0 )
{
	$forumthread = $SQL->fetch_array( $SQL->query( "SELECT * FROM " . pkSQLTAB_FORUM_THREAD . " WHERE forumthread_id='" . $threadid . "'" ) );
	$catid = $forumthread[ 'forumthread_catid' ];
	$forumcat = $FORUM->getCategory( $catid );

	if( userrights( $forumcat[ 'forumcat_mods' ], $forumcat[ 'forumcat_rrights' ] ) == "true" || userrights( $forumcat[
	                                                                                                         'forumcat_user'
	                                                                                                         ],
	                                                                                                         $forumcat[
	                                                                                                         'forumcat_rrights'
	                                                                                                         ] ) == "true" || getrights(
		                                                                                                                          $forumcat
		                                                                                                                          [
		                                                                                                                          'forumcat_rrights'
		                                                                                                                          ] ) == "true" )
	{
		if( $path == "forumsnewpost" || $path == "forumseditpost" || $path == "forumsmoderate" )
		{
			$thread_title = pkEntities( $forumthread[ 'forumthread_title' ] );

			eval( "\$forum_path= \"" . pkTpl( "forum/kopf_threadlink" ) . "\";" );
		}
		else
		{
			$forum_path .= ' &#187; ' . pkEntities( $forumthread[ 'forumthread_title' ] );
		}
	}
	else
	{
		$forum_path .= ' &#187; ' . $lang[ 'access_refuse' ];
	}

	if( $forumthread[ 'forumthread_status' ] == 0 )
	{
		eval( "\$forum_action= \"" . pkTpl( "forum/action_closed" ) . "\";" );
	}
}

if( $catid > 0 )
{
	$forumcat = $FORUM->getCategory( $catid );

	if( $path == "forumscategory" )
	{
		#set the site title
		$name = pkEntities( $forumcat[ 'forumcat_name' ] );
		$CMS->site_title_set( $name, true );

		$forum_path = " &#187; " . $name;
	}
	else
	{
		$forumcat[ 'forumcat_name' ] = pkEntities( $forumcat[ 'forumcat_name' ] );

		eval( "\$forum_pathadd= \"" . pkTpl( "forum/kopf_catlink" ) . "\";" );
		$forum_path = $forum_pathadd . $forum_path;
	}

	if( $forumcat[ 'forumcat_subcat' ] > 0 )
	{
		while( $forumcat[ 'forumcat_subcat' ] > 0 )
		{
			$forumcat = $FORUM->getCategory( $forumcat[ 'forumcat_subcat' ] );

			eval( "\$forum_pathadd= \"" . pkTpl( "forum/kopf_catlink" ) . "\";" );

			$forum_path = $forum_pathadd . $forum_path;
		}

		$forumcat = $FORUM->getCategory( $catid );
	}

	if( $path == "forumsthread" )
	{
		#set the site title
		$CMS->site_title_set( $forumthread[ 'forumthread_title' ] );

		if( $forumcat[ 'forumcat_status' ] != 1 )
		{
			eval( "\$forum_action= \"" . pkTpl( "forum/action_closed" ) . "\";" );
		}
		elseif( $forumthread[ 'forumthread_status' ] == 0 || $forumthread[ 'forumthread_status' ] == 3 )
		{
			eval( "\$forum_action= \"" . pkTpl( "forum/action_closed" ) . "\";" );
		}
		elseif( getrights( $forumcat[ 'forumcat_wrights' ] ) == "true" || userrights( $forumcat[ 'forumcat_mods' ],
		                                                                              $forumcat[ 'forumcat_wrights'
		                                                                              ] ) == "true" || userrights(
			                                                                                               $forumcat[
			                                                                                               'forumcat_user'
			                                                                                               ], $forumcat[
			                                                                                                  'forumcat_wrights'
			                                                                                                  ] ) == "true" )
		{
			if( ( getrights( $forumcat[ 'forumcat_trights' ] ) == "true" || userrights( $forumcat[ 'forumcat_mods' ],
			                                                                            $forumcat[ 'forumcat_trights'
			                                                                            ] ) == "true" || userrights(
				                                                                                             $forumcat[
				                                                                                             'forumcat_user'
				                                                                                             ],
				                                                                                             $forumcat[
				                                                                                             'forumcat_trights'
				                                                                                             ] ) == "true" ) &&
			    $forumcat[ 'forumcat_threads_option' ] == 1 )
			{
				eval( "\$forum_action.= \"" . pkTpl( "forum/action_thread" ) . "\";" );
			}

			eval( "\$forum_action.= \"" . pkTpl( "forum/action_answer" ) . "\";" );
		}
	}
	elseif( $path == "forumscategory" )
	{
		if( $forumcat[ 'forumcat_status' ] != 1 )
		{
			eval( "\$forum_action.= \"" . pkTpl( "forum/action_closed" ) . "\";" );
		}
		elseif( ( getrights( $forumcat[ 'forumcat_trights' ] ) == "true" || userrights( $forumcat[ 'forumcat_mods' ],
		                                                                                $forumcat[ 'forumcat_trights'
		                                                                                ] ) == "true" || userrights(
			                                                                                                 $forumcat[
			                                                                                                 'forumcat_user'
			                                                                                                 ],
			                                                                                                 $forumcat[
			                                                                                                 'forumcat_trights'
			                                                                                                 ] ) == "true" ) &&
		        $forumcat[ 'forumcat_status' ] == 1 && $forumcat[ 'forumcat_threads_option' ] == 1 )
		{
			eval( "\$forum_action.= \"" . pkTpl( "forum/action_thread" ) . "\";" );
		}
	}
}

switch( $path )
{
	case 'forumsdisplay':
		$title = pkGetConfigF( 'forum_title_forumsdisplay' );
		$title = $title ? $title : pkGetLang( 'forumsdisplay' );

		$CMS->site_title_set( $title, true );
		break;
	#END case forumsdisplay
	case 'forumsnewpost':
		if( intval( $threadid ) > 0 )
		{
			$forum_path .= ' &#187; ' . $lang[ 'forum_new_answer' ];
			$action_type = $lang[ 'forum_new_answer' ] . ' ' . $lang[ 'in' ] . ' ' . pkEntities(
				$forumthread[ 'forumthread_title' ] );
		}
		else
		{
			$forum_path .= ' &#187; ' . $lang[ 'forum_new_thread' ];
			$action_type = $lang[ 'forum_new_thread' ] . ' ' . $lang[ 'in' ] . ' ' . $forumcat[ 'forumcat_name' ];
		}
		break;
	#END case forumsnewpost
	case 'forumseditpost':

		$forum_path .= ' &#187; ' . $lang[ 'forum_editpost' ];
		break;
	#END case forumseditpost
	case 'forumsfavorites':

		$forum_path .= ' &#187; ' . $lang[ 'forum_favorits' ];
		break;
	#END case forumsfavorite
	case 'forumsmoderate':

		$forum_path .= ' &#187; ' . $lang[ 'forum_moderate' ];
		break;
	#END case forumsmoderate
	case 'forumspostinfo':

		if( $mode == 'report' )
		{
			$forum_path .= ' &#187; ' . $lang[ 'forum_report' ];
		}
		break;
	#END case 'forumspostinfo'
	case 'forumsearch':
		$name = pkGetLang( $mode == 'result' ? 'forumsearch_result' : 'forumsearch' );

		$CMS->site_title_set( $name, true );
		$forum_path .= ' &#187; ' . $name;
		break;
	#END case forumsearch
	case 'forumsinformation':

		$forum_path .= ' &#187; ' . $lang[ 'forum_showinfo' ];
		break;
	#END case forumsinformation
	case 'forumstopuser' :

		$forum_path .= ' &#187; ' . $lang[ 'forum_topuser' ];
		break;
	#END case forumstopuser
	case 'forumsteam':

		$forum_path .= ' &#187; ' . $lang[ 'forum_team' ];
		break;
	#END case forumsteam
}
#END switch $path

$kopf_admin = $kopf_search = '';


$newpost = $FORUM->unreadedPostings( $threadid );
$lang_newpostings = ( $newpost ? pkHtmlLink( pkLink( 'forumsearch', 'result', 'search=new&rshow=1' ), pkGetLang( 'there_are_new_postings' ) ) : pkGetLang( 'there_are_no_new_postings' ) ) . pkGetSpecialLang( 'since_your_last_vistit', pkTimeFormat( $FORUM->getPosttime( ) ) );
$lang_mark_all_forums_readed = $newpost ? pkHtmlLink( pkLink( 'forumsdisplay', '', 'newposttime=' . pkTIME ), pkGetLang( 'mark_all_forums_readed' ) ) : '';

if( intval( pkGetUservalue( 'id' ) ) > 0 )
{
	$user_nick = pkGetUservalueF( 'nick' );
	$online_time = pkTimeFormat( pkGetUservalue( 'logtime' ), 'time' );

	$favstatus = $SQL->fetch_array( $SQL->query( "SELECT COUNT(*) FROM " . pkSQLTAB_FORUM_FAVORITE . " WHERE forumfav_userid='" . pkGetUservalue( 'id' ) . "' LIMIT 1" ) );
	if( $favstatus[ 0 ] > 0 )
	{
		eval( "\$kopf_favorit= \"" . pkTpl( "forum/kopf_favorit" ) . "\";" );
	}

	if( intval( $imstatus_info = imstatus( ) ) > 0 )
	{
		eval( "\$new_im_msg= \"" . pkTpl( "forum/kopf_newim" ) . "\";" );
	}

	if( adminaccess( 'adminarea' ) )
	{
		$link_administration = pkLinkAdmin( );
		$lang_administration = pkGetLang( 'administration' );

		eval( "\$kopf_admin= \"" . pkTpl( "forum/kopf_admin" ) . "\";" );
	}
        
        $result = $SQL->fetch_array( $SQL->query( "SELECT * FROM pk__config WHERE id = 'forum_searcheod'") );
        $is_forum_search_visible = unserialize($result['value']);
                
        if ($is_forum_search_visible)
            {
                eval( "\$kopf_search= \"" . pkTpl( "forum/kopf_search" ) . "\";" );
            }

	eval( "\$kopf_userinfo= \"" . pkTpl( "forum/kopf_userinfo" ) . "\";" );
	eval( "\$kopf_logreg= \"" . pkTpl( "forum/kopf_logout" ) . "\";" );
}
else
{
	$current_path = pkEntities( $ENV->getvar( 'QUERY_STRING' ) );

	eval( "\$kopf_logreg= \"" . pkTpl( "forum/kopf_login" ) . "\";" );
	eval( "\$kopf_login= \"" . pkTpl( "forum/kopf_login_small" ) . "\";" );
}

eval( "\$site_body.= \"" . pkTpl( "forum/kopf" ) . "\";" );

if( $ENV->_isset_request( 'setsig' ) )
{
	$SESSION->setUservalue( 'sigoption', $ENV->_request( 'setsig' ) ? 1 : 0 );
}

if( $ENV->_isset_request( 'changestyle' ) )
{
	$FORUM->setLayout( $ENV->_request( 'changestyle' ) ? 1 : 0 );
}
?>