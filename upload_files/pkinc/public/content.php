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

pkLoadClass( $BBCODE, 'bbcode' );
$modehash = array(
	'print', 'vote'
);

$mode = ( isset( $_REQUEST[ 'mode' ] ) && in_array( $_REQUEST[ 'mode' ], $modehash ) ) ? $_REQUEST[ 'mode' ] : NULL;
$contentid = ( isset( $_REQUEST[ 'contentid' ] ) && intval( $_REQUEST[ 'contentid' ] ) > 0 ) ? intval(
	$_REQUEST[ 'contentid' ] ) : ( ( isset( $contentid ) && $contentid > 0 ) ? $contentid : 0 );

switch ( $mode )
{
	case 'print' :
		$pkDISPLAYPRINT = true;

		$info = $SQL->fetch_assoc( $SQL->query( "SELECT * FROM " . pkSQLTAB_CONTENT . " where content_id='" . $contentid . "'" ) );

		if ( $info[ 'content_status' ] != 1 || $info[ 'content_time' ] > pkTIME )
		{
			pkEvent( 'article_not_available' );
			return;
		}

		$cat = $SQL->fetch_assoc( $SQL->query( "SELECT * FROM " . pkSQLTAB_CONTENT_CATEGORY . " WHERE contentcat_id='" .
		                                       $info[ 'content_cat' ] . "'" ) );

		if ( !getrights( $cat[ 'contentcat_rights' ] ) )
		{
			pkEvent( 'access_refused' );
			return;
		}

		$content_cat = pkEntities( $cat[ 'contentcat_name' ] );

		if ( $info[ 'content_themeid' ] != 0 )
		{
			$theme = $SQL->fetch_assoc( $SQL->query( "SELECT * FROM " . pkSQLTAB_CONTENT_THEME . " WHERE contenttheme_id='" .
			                                         $info[ 'content_themeid' ] . "'" ) );

			if ( !empty( $theme[ 'contenttheme_name' ] ) )
			{
				$content_theme = pkEntities( $theme[ 'contenttheme_name' ] );

				eval( "\$content_theme= \"" . pkTpl( "content/printable_themetitle" ) . "\";" );
			}
		}

		if ( $info[ 'content_autorid' ] )
		{
			$autor = $SQL->fetch_assoc( $SQL->query( "SELECT user_nick FROM " . pkSQLTAB_USER . " WHERE user_id='" . intval($info[ 'content_autorid' ] ) . "'" ) );
		}

		if ( $autor[ 'user_nick' ] != "" )
		{
			$content_autor = pkEntities( $autor[ 'user_nick' ] );
		}
		else
		{
			$content_autor = pkEntities( $info[ 'content_autor' ] );
		}

		$content_title = pkEntities( $info[ 'content_title' ] );

		if ( $info[ 'content_text' ] != '' )
		{
			$content_text = $BBCODE->parse( str_replace( array('<break>', '<BREAK>'), '', $info[ 'content_text' ] ), $info[ 'content_html' ], $info[ 'content_ubb' ], $info[ 'content_smilies' ], 1 );
		}

		if ( $info[ 'content_altdat' ] != '' && ( $info[ 'content_option' ] == 0 or $info[ 'content_option' ] == 1 ) )
		{
			$i = explode( "\n", $info[ 'content_altdat' ] );

			foreach ( $i as $d )
			{
				if ( pkFileCheck( $d ) )
				{
					$content_text = implode( "", file( $d ) );
				}
			}
		}

		$content_time = formattime( $info[ 'content_time' ] );
		$time_now_formated = formattime( pkTIME );

		#prefix.title.suffix
		$CMS->site_title_set( pkGetLang( 'print_view_prefix' ) .
		                      $info[ 'content_title' ] . pkGetLang( 'print_view_suffix' ) );

		eval( "\$site_body.= \"" . pkTpl( "content/printable" ) . "\";" );
		break;
	#END mode print
	case 'vote' :
		pkLoadLang( 'content' );

		$contentvote = ( isset( $_REQUEST[ 'content_vote' ] ) && intval( $_REQUEST[ 'content_vote' ] ) >= 1 && intval($_REQUEST['content_vote'] ) <= 10 ) ? intval($_REQUEST[ 'content_vote' ] ) : 0;

		if ( $contentvote && $contentid )
		{
			$contentinfo = $SQL->fetch_array( $SQL->query( "SELECT
					content_rating,
					content_rating_total,
					content_rating_status,
					content_cat,
					content_option
				FROM " . pkSQLTAB_CONTENT . "
				WHERE content_id='" . $contentid . "'
				LIMIT 1" ) );

			$contentcat = $SQL->fetch_array( $SQL->query( "SELECT
					contentcat_rights 
				FROM " . pkSQLTAB_CONTENT_CATEGORY . "
				WHERE contentcat_id='" . $contentinfo[ 'content_cat' ] . "'
				LIMIT 1" ) );

			if ( !getrights( $contentcat[ 'contentcat_rights' ] ) || $contentinfo[ 'content_rating_status' ] != 1 )
			{
				pkHeaderLocation( '', '', 'event=function_disabled' );
			}

			$info = $SQL->fetch_array( $SQL->query( "SELECT
				COUNT(*)
				FROM " . pkSQLTAB_POLL_COUNT . "
				WHERE vote_rated_cat='cont' AND
					vote_rated_contid='" . $contentid . "' AND
					(vote_rated_userid='" . pkGetUservalue( 'id' ) . "' OR
					vote_rated_ip='" . $SQL->f( $ENV->getvar( 'REMOTE_ADDR' ) ) . "')
				LIMIT 1" ) );

			if ( $info[ 0 ] == '0' )
			{
				if ( $contentinfo[ 'content_rating' ] > 0 )
				{
					$i = ( ( $contentinfo[ 'content_rating' ] *
					         $contentinfo[ 'content_rating_total' ] ) + $contentvote ) / (
							$contentinfo[ 'content_rating_total' ] + 1 );
				}
				else
				{
					$i = $contentvote;
				}

				$j = $contentinfo[ 1 ] + 1;
				$votetotal = number_format( $i, 2, ",", "." );

				$SQL->query( "UPDATE " . pkSQLTAB_CONTENT . "
					SET content_rating='" . $i . "',
						content_rating_total=content_rating_total+1
					WHERE content_id='" . $contentid . "'" );

				$SQL->query( "INSERT INTO " . pkSQLTAB_POLL_COUNT . "
					(vote_rated_contid, vote_rated_userid, vote_rated_cat, vote_rated_ip)
					VALUES 
					('" . $contentid . "',
					'" . pkGetUservalue( 'id' ) . "',
					'cont',
					'" . $SQL->f( $ENV->getvar( 'REMOTE_ADDR' ) ) . "')" );

				eval( "\$content_vote_body=\"" . pkTpl( "content/contentvote_voted" ) . "\";" );
			}
			else
			{
				$content_vote_body = pkGetLang( 'content_vote_multivote_msg' );
			}
		}
		else
		{
			eval( "\$content_vote_body=\"" . pkTpl( "content/contentvote_invalid" ) . "\";" );
		}

		eval( "\$site_body.= \"" . pkTpl( "content/contentvote" ) . "\";" );
		break;
	#END case vote
	default :
		if ( !$contentid )
		{
			pkHeaderLocation( 'contentarchive' );
		}

		$contentinfo = $SQL->fetch_array( $SQL->query( "SELECT * FROM " . pkSQLTAB_CONTENT . " WHERE content_id='" . $contentid . "' LIMIT 1" ) );

		if ( $contentinfo[ 'content_status' ] != 1 || $contentinfo[ 'content_time' ] > pkTIME )
		{
			pkEvent( 'article_not_available' );
			return;
		}

		$contentcat_cache = contentcats( );
		$contentcatinfo = $contentcat_cache[ 0 ][ $contentinfo[ 'content_cat' ] ];

		if ( !getrights( $contentcatinfo[ 'contentcat_rights' ] ) )
		{
			pkEvent( 'access_refused' );
			return;
		}

		#redirect if we have the wrong content type		
		if ( $contentinfo[ 'content_option' ] >= 1 && $contentinfo[ 'content_option' ] <= 4 )
		{
			$array = array(
				1 => 'article', 2 => 'news', 3 => 'link', 4 => 'download'
			);

			pkHeaderLocation( $array[ $contentinfo[ 'content_option' ] ], '', 'contentid=' . $contentid );
		}

		$content_text = $BBCODE->parse(
			$contentinfo[ 'content_header' ], $contentinfo[ 'content_html' ], $contentinfo[ 'content_ubb' ],
			$contentinfo[ 'content_smilies' ], 1, 1 );

		if ( !empty( $contentinfo[ 'content_altdat' ] ) )
		{
			$i = explode( "\n", $contentinfo[ 'content_altdat' ] );

			if ( is_array( $i ) )
			{
				foreach ( $i as $d )
				{
					$d = trim( $d );

					if ( pkFileCheck( $d ) )
					{
						$content_text .= implode( '', file( $d ) );
					}
				}
			}
		}

		$content_teaser = '';
		if ( !empty( $contentinfo[ 'content_teaser' ] ) )
		{
			$teaser_dimension = @getimagesize( $contentcatinfo[ 'content_teaser' ] );

			eval( "\$content_teaser=\"" . pkTpl( "content/content_teaser" ) . "\";" );
		}

		$content_title = pkEntities( $contentinfo[ 'content_title' ] );
		$content_text .= $BBCODE->parse(
			$contentinfo[ 'content_text' ], $contentinfo[ 'content_html' ], $contentinfo[ 'content_ubb' ],
			$contentinfo[ 'content_smilies' ], 1, 1 );
		$content_footer = '';

		if ( $path != 'start' )
		{
			$CMS->site_title_set( $contentinfo[ 'content_title' ] );

			eval( "\$content_footer=\"" . pkTpl( "content/content_footer" ) . "\";" );
		}

		eval( "\$site_body.=\"" . pkTpl( "content/content" ) . "\";" );
		break;
	#END default
}
?>