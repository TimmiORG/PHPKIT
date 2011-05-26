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

$search_text = '';
$modehash = array(
	'result'
);
$mode = ( isset( $_REQUEST[ 'mode' ] ) && in_array( $_REQUEST[ 'mode' ], $modehash ) ) ? $_REQUEST[ 'mode' ] : NULL;
$rshow = ( isset( $_REQUEST[ 'rshow' ] ) && intval( $_REQUEST[ 'rshow' ] ) == 1 ) ? 1 : 0;
$rorder = ( isset( $_REQUEST[ 'rorder' ] ) && intval( $_REQUEST[ 'rorder' ] ) == 1 ) ? 1 : 0;
$entries = ( isset( $_REQUEST[ 'entries' ] ) && intval( $_REQUEST[ 'entries' ] ) > 0 ) ? intval(
	$_REQUEST[ 'entries' ] ) : 0;
$search_textoption = ( isset( $_REQUEST[ 'search_textoption' ] ) && intval( $_REQUEST[ 'search_textoption'
                                                                            ] ) == 1 ) ? 1 : 0;
$search_useroption = ( isset( $_REQUEST[ 'search_useroption' ] ) && intval( $_REQUEST[ 'search_useroption'
                                                                            ] ) == 1 ) ? 1 : 0;
$ACTION = ( isset( $_POST[ 'action' ] ) ) ? $_POST[ 'action' ] : 'view';

include( pkDIRPUBLICINC . 'forumsheader' . pkEXT );

if ( pkGetConfig( 'captcha' ) && $ENV->_isset_post( 'search' ) && !$ENV->_isset_post( pkCAPTCHAVARNAME ) && !pkCaptchaCodeValid( NULL ) )
{
	#navbox used - redirect to the search form
	$search_text = $ENV->_post( 'search_text' );
	$search_text = ( $search_text == pkGetLang( 'search_forums' ) ? '' : urlencode( $search_text ) );

	pkHeaderLocation( 'forumsearch', '', 'search_text=' . $search_text );
}

switch ( $mode )
{
	case 'result' :
		if ( $ACTION != 'view' && !pkCaptchaCodeValid( $ENV->_post( pkCAPTCHAVARNAME ) ) )
		{
			$search_text = $ENV->_post( 'search_text' );
			$search_user = $ENV->_post( 'search_user' );
			$userid = $ENV->_request( 'userid' );

			pkHeaderLocation( 'forumsearch', '', 'error=securitycode_invalid&rshow=' . $rshow . '&rorder=' . $rorder . '&search_text=' . urlencode( $search_text ) . '&search_user=' . urlencode( $search_user ) . '&userid=' . intval( $userid ) . '&search_textoption=' . $search_textoption );
		}

		$order = " ORDER BY forumpost_time " . ( ( $rorder ) ? "ASC" : "DESC" );

		if ( $SESSION->exists( 'save_rposts' ) )
		{
			$save_rposts = $SESSION->get( 'save_rposts' );
		}
		else
		{
			unset( $save_rposts );
		}

		if ( $ENV->_get( 'search' ) == 'new' )
		{
			$searchstring = " forumpost_threadid IN(0" . implode( ',', $FORUM->getUnreadedThreadids( ) ) . ") AND forumpost_time>'" . pkGetUservalue( 'lastlog' ) . "' ";
		}
		elseif ( isset( $_REQUEST[ 'userid' ] ) && intval( $_REQUEST[ 'userid' ] ) > 0 )
		{
			$searchstring = " forumpost_autorid='" . intval( $_REQUEST[ 'userid' ] ) . "' ";
		}
		elseif ( !empty( $_POST[ 'search_user' ] ) )
		{
			$suser = $SQL->f( $_POST[ 'search_user' ] );

			$userids = '';
			$query = $SQL->query( "SELECT
					user_id 
				FROM " . pkSQLTAB_USER . "
				WHERE user_nick" . ( ( $_POST[ 'search_useroption' ] == 1 ) ? "='" . $suser . "' " : "='" . $suser . "' OR
					user_nick LIKE '%" . $suser . "%' OR
					user_nick LIKE '" . $suser . "%' OR
					user_nick LIKE '%" . $suser . "' OR
					user_nick LIKE '" . $suser . "' " ) );
			while ( list( $id ) = $SQL->fetch_row( $query ) )
			{
				$userids .= ( empty( $userids ) ? '' : ',' ) . $id;
			}

			if ( $userids )
			{
				$searchstring = " forumpost_autorid IN(" . $userids . ") ";
			}

			$searchstring .= ( $searchstring ? " OR (" : '' ) . "forumpost_autor" . ( (
					$_POST[ 'search_useroption' ] == 1 ) ? "='" . $suser . "' " : "='" . $suser . "' OR
				forumpost_autor LIKE '%" . $suser . "%' OR
				forumpost_autor LIKE '%" . $suser . "' OR
				forumpost_autor LIKE '" . $suser . "%' OR
				forumpost_autor LIKE '" . $suser . "' " ) . ") ";
		}

		if ( !empty( $_POST[ 'search_text' ] ) )
		{
			$search_text = trim( $ENV->_post( 'search_text' ) );
			$search_text = $search_text == pkGetLang( 'search_forums' ) ? '' : $search_text;
			$search_string = explode( " or ", strtolower( str_replace( '-', " OR ", str_replace( '+', " AND ", trim( $search_text ) ) ) ) );

			foreach ( $search_string as $k )
			{
				if ( !stripos( 'or', $k ) !== FALSE && strlen( $k ) < pkGetConfig( 'search_min_length' ) )
				{
					$search_text = $ENV->_post( 'search_text' );
					$search_user = $ENV->_post( 'search_user' );
					$userid = $ENV->_request( 'userid' );

					pkHeaderLocation( 'forumsearch', '', 'error=searchterm_too_short&rshow=' . $rshow . '&rorder=' . $rorder . '&search_text=' . urlencode( $search_text ) . '&search_user=' . urlencode( $search_user ) . '&userid=' . intval( $userid ) . '&search_textoption=' . $search_textoption );
				}
			}

			$ic = 1;
			$searchtext = "(";

			foreach ( $search_string as $i )
			{
				$i = trim( $i );
				$i = str_replace( " ", " AND ", $i );

				if ( stripos( " AND ", $i ) !== FALSE )
				{
					$searchtext .= "(";

					$ii = explode( " and ", strtolower( $i ) );
					$iic = 1;

					foreach ( $ii as $iii )
					{
						$iii = trim( $iii );

						if ( intval( $_POST[ 'search_textoption' ] ) == 1 )
						{
							$searchtext .= "(forumpost_title LIKE '%" . $SQL->f( $iii ) . "%')";
						}
						else
						{
							$searchtext .= "(forumpost_title LIKE '%" . $SQL->f( $iii ) . "%' OR forumpost_text LIKE '%" . $SQL->f( $iii ) . "%')";
						}

						if ( count( $ii ) > $iic )
						{
							$searchtext .= " AND ";
							$iic++;
						}
					}

					$searchtext .= ")";
				}
				else
				{
					if ( intval( $_POST[ 'search_textoption' ] ) == 1 )
					{
						$searchtext .= "(forumpost_title LIKE '%" . $SQL->f( $i ) . "%')";
					}
					else
					{
						$searchtext .= "(forumpost_title LIKE '%" . $SQL->f( $i ) . "%' OR forumpost_text LIKE '%" . $SQL->f( $i ) . "%')";
					}
				}

				if ( count( $search_string ) > $ic )
				{
					$searchtext .= " OR ";
					$ic++;
				}
			}

			$searchtext .= ")";
			$searchstring .= " " . $searchtext . " ";
		}

		if ( isset( $_REQUEST[ 'search' ] ) && $_REQUEST[ 'search' ] == 'activ' )
		{
			$st = pkTIME - ( 24 * 3600 );
			$searchstring = "forumpost_time>'" . $st . "'";
		}

		unset( $search_cat_string );

		if ( !empty( $searchstring ) )
		{
			if ( $_POST[ 'search_cat' ][ 0 ] != "-1" && is_array( $_POST[ 'search_cat' ] ) )
			{
				foreach ( $_POST[ 'search_cat' ] as $sc )
				{
					if ( $search_cat_string )
					{
						$search_cat_string .= "," . intval( $sc );
					}
					else
					{
						$search_cat_string = " AND forumthread_catid IN (" . intval( $sc );
					}
				}
			}

			if ( $search_cat_string )
			{
				$search_cat_string .= ")";
			}

			unset( $sqlcommand );
			unset( $post_cache );

			$getpostings = $SQL->query( "SELECT forumpost_id, forumpost_threadid
				FROM " . pkSQLTAB_FORUM_POST . "
				WHERE " . $searchstring . " " . $order . "
				LIMIT " . pkGetConfig( 'search_max' ) );
			while ( $posts = $SQL->fetch_assoc( $getpostings ) )
			{
				if ( $sqlcommand )
				{
					$sqlcommand .= " OR forumthread_id='" . $posts[ 'forumpost_threadid' ] . "'";
				}
				else
				{
					$sqlcommand .= "SELECT forumthread_catid, forumthread_id FROM " . pkSQLTAB_FORUM_THREAD . " WHERE (forumthread_id='" .
					               $posts[ 'forumpost_threadid' ] . "'";
				}

				$post_cache[ ] = $posts;
			}

			if ( $sqlcommand )
			{
				$sqlcommand .= ")";
				$getthreads = $SQL->query( $sqlcommand . $search_cat_string );
				while ( $threads = $SQL->fetch_assoc( $getthreads ) )
				{
					$threads_cache[ $threads[ 'forumthread_id' ] ] = $threads;
				}

				if ( is_array( $post_cache ) && is_array( $threads_cache ) )
				{
					foreach ( $post_cache as $posts )
					{
						$threads = $threads_cache[ $posts[ 'forumpost_threadid' ] ];

						if ( !empty( $threads[ 'forumthread_catid' ] ) )
						{
							$cats = $forumcat_cache[ $threads[ 'forumthread_catid' ] ];

							if ( getrights( $cats[ 'forumcat_rrights' ] ) || userrights(
								$cats[ 'forumcat_mods' ], $cats[ 'forumcat_rrights' ] ) || userrights(
								$cats[ 'forumcat_user' ], $cats[ 'forumcat_rrights' ] ) )
							{
								$rposts[ ] = array(
									$posts[ 'forumpost_id' ], $posts[ 'forumpost_threadid' ]
								);
							}
						}
					}
					#END foreach
				}
			}
		}

		if ( !is_array( $rposts ) && !is_array( $save_rposts ) )
		{
			$search_text = $ENV->_post( 'search_text' );
			$search_user = $ENV->_post( 'search_user' );
			$userid = $ENV->_request( 'userid' );

			pkHeaderLocation( 'forumsearch', '', 'error=search_noresult&rshow=' . $rshow . '&rorder=' . $rorder . '&search_text=' . urlencode( $search_text ) . '&search_user=' . urlencode( $search_user ) . '&userid=' . intval( $userid ) . '&search_textoption=' . $search_textoption );
		}

		if ( !$SESSION->exists( 'save_rposts' ) || ( isset( $save_rposts ) && isset( $rposts ) ) )
		{
			$SESSION->set( 'save_rposts', $rposts );
		}

		if ( isset( $_POST[ 'search' ] ) || isset( $_REQUEST[ 'userid' ] ) || $_REQUEST[ 'search' ] == 'activ' )
		{
			if ( count( $rposts ) >= pkGetConfig( 'search_max' ) )
			{
				$link = pkHeaderLink( 'forumsearch', 'result', 'rshow=' . $rshow . '&rorder=' . $rorder . '&entries=' . $entries, NULL, NULL, false );

				pkHeaderLocation( '', '', 'event=searchresult_limited&moveto=' . urlencode( $link ) );
			}

			pkHeaderLocation( 'forumsearch', 'result', 'rshow=' . $rshow . '&rorder=' . $rorder . '&entries=' . $entries );
		}

		if ( !isset( $rposts ) && isset( $save_rposts ) )
		{
			$rposts = $save_rposts;
		}

		$epp = 20;
		$resultcount = count( $rposts );

		if ( $rshow == 1 )
		{
			foreach ( $rposts as $x )
			{
				$rthreads[ $x[ 1 ] ] = $x[ 1 ];
			}

			$resultthreads = count( $rthreads );

			unset( $resultstring );

			foreach ( $rthreads as $x )
			{
				if ( $resultstring )
				{
					$resultstring .= " OR forumthread_id='" . intval( $x ) . "'";
				}
				else
				{
					$resultstring = "SELECT * FROM " . pkSQLTAB_FORUM_THREAD . " WHERE forumthread_id='" . intval( $x ) . "'";
				}
			}

			$order = " ORDER BY forumthread_lastreply_time " . ( ( $rorder == 1 ) ? "ASC" : "DESC" );

			$sqlcommand = '';

			$getforumthread = $SQL->query( $resultstring . $order . " LIMIT " . $entries . "," . $epp );
			while ( $forumthread = $SQL->fetch_array( $getforumthread ) )
			{
				$forumthread_cache[ ] = $forumthread;

				$sqlcommand .= ',' . $forumthread[ 'forumthread_autorid' ] . ',' .
				               $forumthread[ 'forumthread_lastreply_autorid' ];
			}

			if ( !empty( $sqlcommand ) )
			{
				$getuserinfo = $SQL->query( "SELECT
						user_id,
						user_nick
					FROM " . pkSQLTAB_USER . "
					WHERE user_id IN (0" . $sqlcommand . ")" );
				while ( $userinfo = $SQL->fetch_assoc( $getuserinfo ) )
				{
					$userinfo_cache[ $userinfo[ 'user_id' ] ] = $userinfo;
				}
			}

			foreach ( $forumthread_cache as $forumthread )
			{
				$forumcat = $forumcat_cache[ $forumthread[ 'forumthread_catid' ] ];

				$category_name = pkEntities( $forumcat[ 'forumcat_name' ] );
				$thread_time = formattime( $forumthread[ 'forumthread_lastreply_time' ] );
				$thread_title = pkEntities( $forumthread[ 'forumthread_title' ] );

				if ( empty( $forumthread[ 'forumthread_title' ] ) || empty( $forumthread[ 'forumthread_autor' ] ) )
				{
					$info = $SQL->fetch_assoc( $SQL->query( "SELECT
							forumpost_title,
							forumpost_autor,
							forumpost_autorid 
						FROM " . pkSQLTAB_FORUM_POST . "
						WHERE forumpost_threadid='" . $forumthread[ 'forumthread_id' ] . "'
						ORDER BY forumpost_time ASC
						LIMIT 1" ) );

					$SQL->query( "UPDATE " . pkSQLTAB_FORUM_THREAD . "
						SET forumthread_title='" . $SQL->f( $info[ 'forumpost_title' ] ) . "',
							forumthread_autor='" . $SQL->f( $info[ 'forumpost_autor' ] ) . "',
							forumthread_autorid='" . intval( $info[ 'forumpost_autorid' ] ) . "'
						WHERE forumthread_id='" . $forumthread[ 'forumthread_id' ] . "'" );

					$forumthread[ 'forumthread_title' ] = $info[ 'forumpost_title' ];
					$forumthread[ 'forumthread_autor' ] = $info[ 'forumpost_autor' ];
					$forumthread[ 'forumthread_autorid' ] = $info[ 'forumpost_autorid' ];
				}

				$posts[ 0 ] = $forumthread[ 'forumthread_replycount' ] + 1;
				$thread_replys = $forumthread[ 'forumthread_replycount' ];

				if ( $posts[ 0 ] > $forumcat[ 'forumcat_posts' ] )
				{
					$sidelink = " - " . sidelinksmall(
						$posts[ 0 ], $forumcat[ 'forumcat_posts' ], "include.php?path=forumsthread&threadid=" .
						                                            $forumthread[ 'forumthread_id' ], "small" );
				}

				if ( empty( $forumthread[ 'forumthread_icon' ] ) )
				{
					$forumthread_icon = 'blank.gif';
				}
				else
				{
					$forumthread_icon = "icons/" . basename( $forumthread[ 'forumthread_icon' ] );
				}

				if ( $forumthread[ 'forumthread_status' ] == 0 || $forumthread[ 'forumthread_status' ] == 3 )
				{
					$threadstatus = "close";
				}
				else
				{
					$threadstatus = "open";
				}

				if ( $forumthread[ 'forumthread_status' ] == 2 || $forumthread[ 'forumthread_status' ] == 3 )
				{
					$threadstatus .= "fixed";
				}

				if ( $FORUM->isUnreadedThread( $forumthread[ 'forumthread_catid' ], $forumthread[ 'forumthread_id' ],
				                               $forumthread[ 'forumthread_lastreply_time' ] ) )
				{
					$threadstatus .= "new";

					eval( "\$newpostlink= \"" . pkTpl( "forum/showcat_thread_row_newpostlink" ) . "\";" );
				}

				if ( $forumthread[ 'forumthread_replycount' ] >= $forumcat[ 'forumcat_threads' ] ||
				     $forumthread[ 'forumthread_viewcount' ] >= $forumcat[ 'forumcat_views' ] )
				{
					$threadstatus .= "hot";
				}

				unset( $userinfo );

				if ( $forumthread[ 'forumthread_autorid' ] > 0 )
				{
					$userinfo = $userinfo_cache[ $forumthread[ 'forumthread_autorid' ] ];
				}

				if ( !empty( $userinfo[ 'user_nick' ] ) )
				{
					$userinfo[ 'user_nick' ] = pkEntities( pkStringCut( $userinfo[ 'user_nick' ],
					                                                    $config[ 'forum_threadautor_cut' ] ) );

					eval( "\$thread_autor= \"" . pkTpl( "member_showprofil_textlink" ) . "\";" );
				}
				else
				{
					$thread_autor = pkEntities( pkStringCut( $forumthread[ 'forumthread_autor' ],
					                                         $config[ 'forum_threadautor_cut' ] ) );
				}

				unset( $userinfo );

				if ( $forumthread[ 'forumthread_lastreply_autorid' ] > 0 )
				{
					$userinfo = $userinfo_cache[ $forumthread[ 'forumthread_lastreply_autorid' ] ];
				}

				if ( !empty( $userinfo[ 'user_nick' ] ) )
				{
					$userinfo[ 'user_nick' ] = pkEntities( pkStringCut( $userinfo[ 'user_nick' ],
					                                                    $config[ 'forum_threadautor_cut' ] ) );

					eval( "\$thread_last_autor= \"" . pkTpl( "member_showprofil_textlink" ) . "\";" );
				}
				else
				{
					$thread_last_autor = pkEntities( pkStringCut( $forumthread[ 'forumthread_lastreply_autor' ],
					                                              $config[ 'forum_threadautor_cut' ] ) );
				}

				$thread_time = formattime( $forumthread[ 'forumthread_lastreply_time' ] );
				$thread_title = pkEntities( $forumthread[ 'forumthread_title' ] );

				eval( "\$thread_row.= \"" . pkTpl( "forum/search_result_thread_row" ) . "\";" );

				unset( $sidelink );
				unset( $thread_last_autor );
				unset( $newpostlink );
			}

			$sidelink = sidelinkfull( $resultthreads, $epp, $entries, "include.php?path=forumsearch&mode=result&rshow=" . $rshow . "&rorder=" . $rorder, "sitebodysmall" );

			if ( $resultthreads == 1 )
			{
				$resultthreads = "1 " . $lang[ 'thread' ];
			}
			else
			{
				$resultthreads = $resultthreads . " " . $lang[ 'threads' ];
			}

			eval( "\$site_body.= \"" . pkTpl( "forum/searchresult_thread" ) . "\";" );
		}
		else
		{
			pkLoadClass( $BBCODE, 'bbcode' );
			pkLoadFunc( 'user' );

			unset( $resultstring );

			foreach ( $rposts as $x )
			{
				if ( $resultstring )
				{
					$resultstring .= " OR forumpost_id='" . $x[ 0 ] . "'";
				}
				else
				{
					$resultstring = "SELECT * FROM " . pkSQLTAB_FORUM_POST . " WHERE forumpost_id='" . $x[ 0 ] . "'";
				}
			}

			unset( $forumpost_cache );
			unset( $sqlcommand );
			unset( $sqlcommand_user );

			$getforumpost = $SQL->query( $resultstring . " " . $order . " LIMIT " . $entries . "," . $epp );
			while ( $forumpost = $SQL->fetch_array( $getforumpost ) )
			{
				$forumpost_cache[ ] = $forumpost;

				if ( $sqlcommand )
				{
					$sqlcommand .= " OR forumthread_id='" . $forumpost[ 'forumpost_threadid' ] . "'";
				}
				else
				{
					$sqlcommand = "SELECT forumthread_id, forumthread_title, forumthread_icon FROM " . pkSQLTAB_FORUM_THREAD . " WHERE forumthread_id='" .
					              $forumpost[ 'forumpost_threadid' ] . "'";
				}

				if ( $forumpost[ 'forumpost_autorid' ] > 0 )
				{
					if ( $sqlcommand_user )
					{
						$sqlcommand_user .= " OR user_id='" . $forumpost[ 'forumpost_autorid' ] . "'";
					}
					else
					{
						$sqlcommand_user = "SELECT * FROM " . pkSQLTAB_USER . " WHERE user_id='" .
						                   $forumpost[ 'forumpost_autorid' ] . "'";
					}
				}
			}

			unset( $forumthread_cache );
			$getforumthread = $SQL->query( $sqlcommand );
			while ( $forumthread = $SQL->fetch_array( $getforumthread ) )
			{
				$forumthread_cache[ $forumthread[ 'forumthread_id' ] ] = $forumthread;
			}

			unset( $userinfo_cache );

			if ( !empty($sqlcommand_user) )
			{
				$getuserinfo = $SQL->query( $sqlcommand_user );
				while ( $userinfo = $SQL->fetch_array( $getuserinfo ) )
				{
					$userinfo_cache[ $userinfo[ 'user_id' ] ] = $userinfo;
				}
			}

			foreach ( $forumpost_cache as $forumpost )
			{
				$forumthread = $forumthread_cache[ $forumpost[ 'forumpost_threadid' ] ];
				$row = rowcolor( $row );

				if ( $forumthread[ 'forumthread_icon' ] != '' )
				{
					$thread_icon = "icons/" . basename( $forumthread[ 'forumthread_icon' ] );

					eval( "\$thread_icon= \"" . pkTpl( "forum/search_result_post_threadicon" ) . "\";" );
				}

				if ( $forumpost[ 'forumpost_icon' ] != '' )
				{
					$post_icon = "icons/" . basename( $forumpost[ 'forumpost_icon' ] );

					eval( "\$post_icon= \"" . pkTpl( "forum/showthread_row_posticon" ) . "\";" );
				}

				if ( $forumpost[ 'forumpost_autorid' ] > 0 && array_key_exists(
					$forumpost[ 'forumpost_autorid' ], $userinfo_cache ) )
				{
					$userinfo = $userinfo_cache[ $forumpost[ 'forumpost_autorid' ] ];
					$userinfo[ 'user_nick' ] = pkEntities( $userinfo[ 'user_nick' ] );

					if ( $userinfo[ 'user_id' ] > 0 )
					{
						if ( isonline( $userinfo[ 'user_id' ] ) )
						{
							eval( "\$info_os= \"" . pkTpl( "member_os_online" ) . "\";" );
						}
						else
						{
							eval( "\$info_os=\"" . pkTpl( "member_os_offline" ) . "\";" );
						}

						eval( "\$post_autor= \"" . pkTpl( "forum/member_showprofil_textlink" ) . "\";" );
						eval( "\$info_user= \"" . pkTpl( "forum/member_userinfo_iconlink" ) . "\";" );

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
							$postings = $SQL->fetch_array( $SQL->query( "SELECT COUNT(*) FROM " . pkSQLTAB_FORUM_POST . " WHERE forumpost_autorid='" .
							                                            $userinfo[ 'user_id' ] . "'" ) );
							$post_count = postcount( $postings[ 0 ], $userinfo[ 'user_postdelay' ], 0 );

							$SQL->query( "UPDATE " . pkSQLTAB_USER . "
								SET user_posts='" . $postings[ 0 ] . "'
								WHERE user_id='" . $userinfo[ 'user_id' ] . "' LIMIT 1" );
						}

						if ( !empty( $userinfo[ 'user_hpage' ] ) )
						{
							if ( stripos( 'http://', $userinfo[ 'user_hpage' ] ) !== FALSE )
							{
								$info_link = pkEntities( $userinfo[ 'user_hpage' ] );
							}
							else
							{
								$info_link = 'http://' . pkEntities( $userinfo[ 'user_hpage' ] );
							}

							eval( "\$info_hpage= \"" . pkTpl( "forum/member_hpage_iconlink" ) . "\";" );
						}

						if ( $userinfo[ 'user_emailshow' ] == 1 )
						{
							eval( "\$info_email= \"" . pkTpl( "forum/member_email_iconlink" ) . "\";" );
						}

						if ( intval( $userinfo[ 'user_icqid' ] ) > 0 )
						{
							eval( "\$info_icq= \"" . pkTpl( "forum/member_icq_iconlink" ) . "\";" );
						}

						if ( $userinfo[ 'user_imoption' ] == 1 )
						{
							eval( "\$info_im= \"" . pkTpl( "forum/member_sendim_iconlink" ) . "\";" );
						}
						else
						{
							eval( "\$info_im= \"" . pkTpl( "forum/member_sendim_nolink" ) . "\";" );
						}

						eval( "\$info_buddie= \"" . pkTpl( "forum/member_buddie_iconlink" ) . "\";" );

						if ( $config[ 'avatar_eod' ] == 1 || $config[ 'avatar_eod' ] )
						{
							if ( $userinfo[ 'user_avatar' ] != '' && @filecheck(
								$config[ 'avatar_path' ] . "/" . $userinfo[ 'user_avatar' ] ) )
							{
								$avatar_dimension[ 3 ] = @getimagesize(
									$config[ 'avatar_path' ] . "/" . $userinfo[ 'user_avatar' ] );

								eval( "\$avatar_show= \"" . pkTpl( "user_avatar_show" ) . "\";" );
							}
						}

						$info_sig = pkUserSignature( $userinfo[ 'user_sig' ] );
					}
				}
				else
				{
					eval( "\$info_os= \"" . pkTpl( "guest_os_icon" ) . "\";" );

					$post_autor = pkEntities( $forumpost[ 'forumpost_autor' ] );
					$post_count = $lang[ 'guest' ];
				}

				if ( $forumpost[ 'forumpost_editcount' ] > 0 )
				{
					$edit_time = formattime( $forumpost[ 'forumpost_edittime' ] );
					$forumpost[ 'forumpost_editautor' ] = formattime( $forumpost[ 'forumpost_editautor' ] );

					eval( "\$edit_message= \"" . pkTpl( "forum/showthread_row_editmessage" ) . "\";" );
				}

				$forumthread_title = pkEntities( $forumthread[ 'forumthread_title' ] );
				$forumpost[ 'forumpost_title' ] = pkEntities( $forumpost[ 'forumpost_title' ] );
				$post_time = formattime( $forumpost[ 'forumpost_time' ] );
				$post_text = $BBCODE->parse( $forumpost[ 'forumpost_text' ], 0, $forumpost[ 'forumpost_bbcode' ],
				                             $forumpost[ 'forumpost_smilies' ], $config[ 'forum_images'
				                                                                ], 1, pkGetConfig( 'forum_imageresize' ), pkGetConfig( 'forum_textwrap' ) );

				eval( "\$result_postrow.= \"" . pkTpl( "forum/search_result_post_row" ) . "\";" );

				unset( $avatar_show );
				unset( $post_icon );
				unset( $info_sig );
				unset( $post_autor_status );
				unset( $edit_time );
				unset( $post_count );
				unset( $post_autor );
				unset( $info_os );
				unset( $userinfo );
				unset( $post_edit );
				unset( $info_user );
				unset( $info_email );
				unset( $info_im );
				unset( $info_hpage );
				unset( $info_icq );
				unset( $info_buddie );
				unset( $edit_message );
				unset( $thread_icon );
			}

			$sidelink = sidelinkfull( $resultcount, $epp, $entries, "include.php?path=forumsearch&mode=result&rshow=" . $rshow . "&rorder=" . $rorder, "sitebodysmall" );

			if ( pkGetUservalue( 'sigoption' ) )
			{
				$setsig = 0;
				$sigoption = $lang[ 'hide' ];
			}
			else
			{
				$setsig = 1;
				$sigoption = $lang[ 'show' ];
			}

			eval( "\$site_body.= \"" . pkTpl( "forum/searchresult_post" ) . "\";" );
		}
		break;
	#END case result
	default :
		$SESSION->deset( 'save_rposts' );

		if ( isset( $_REQUEST[ 'error' ] ) && $_REQUEST[ 'error' ] == 'search_noresult' )
		{
			pkEvent( 'search_noresult', 0 );
		}
		elseif ( isset( $_REQUEST[ 'error' ] ) && $_REQUEST[ 'error' ] == 'securitycode_invalid' )
		{
			pkEvent( 'securitycode_invalid', 0 );
		}
		elseif ( isset( $_REQUEST[ 'error' ] ) && $_REQUEST[ 'error' ] == 'searchterm_too_short' )
		{
			pkEvent( 'searchterm_too_short', 0 );
		}

		$cat_option = '';

		if ( is_array( $forumcat_cache_byname ) )
		{
			foreach ( $forumcat_cache_byname as $forumcat )
			{
				if ( $FORUM->getCategoryRrights( $forumcat[ 'forumcat_id' ] ) )
				{
					$cat_option .= '<option value="' . $forumcat[ 'forumcat_id' ] . '">' . str_repeat( '-', $forumcat[ 'level'] ) . ' ' . pkEntities(
						$forumcat[ 'forumcat_name' ] ) . '</option>';
				}
			}
		}

		$order0 = $rorder1 = $rshow0 = $rshow1 = $search_useroption1 = $search_useroption0 = '';

		$form_action = pkLink( 'forumsearch', 'result' );

		$site_name = pkGetConfigF( 'site_name' );
		$search_text = pkEntities( urldecode( $ENV->_get( 'search_text' ) ) );
		$search_user = pkEntities( urldecode( $ENV->_get( 'search_user' ) ) );
		$userid = $ENV->_get_id( 'userid' );

		$search_useroption ? $search_useroption1 = ' checked="checked"' : $search_useroption0 = ' checked="checked"';
		$search_textoption ? $search_textoption1 = ' checked="checked"' : $search_textoption0 = ' checked="checked"';

		$rorder ? $rorder1 = ' checked="checked"' : $rorder0 = ' checked="checked"';
		$rshow ? $rshow1 = ' checked="checked"' : $rshow0 = ' checked="checked"';

		$captcha = pkCaptchaField( NULL, 2, 3 );

		eval( "\$site_body.= \"" . pkTpl( "forum/search" ) . "\";" );
		break;
	#END default
}

include( pkDIRPUBLICINC . 'forumsfooter' . pkEXT );
?>