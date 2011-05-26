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

//preset
$row = $searchresult_row = '';

$modehash = array('result');
$mode = isset( $_REQUEST[ 'mode' ] ) && in_array( $_REQUEST[ 'mode' ], $modehash ) ? $_REQUEST[ 'mode' ] : NULL;
$ACTION = isset( $_POST[ 'action' ] ) ? $_POST[ 'action' ] : 'view'; #unsecure!

$search_text = $ENV->_post( 'search_text' );
$search_text = $search_text == pkGetLang( 'search_contents' ) ? '' : urlencode( $search_text );

$rorder = ( isset( $_REQUEST[ 'rorder' ] ) && intval( $_REQUEST[ 'rorder' ] ) == 1 ) ? 1 : 0;
$search_textoption = ( isset( $_REQUEST[ 'search_textoption' ] ) && intval( $_REQUEST[ 'search_textoption'
                                                                            ] ) == 1 ) ? 1 : 0;

$search_type = ( isset( $_REQUEST[ 'search_type' ] ) && is_array( $_REQUEST[ 'search_type' ] ) ) ? array_map( 'intval',$_REQUEST['search_type'] ) : array(0 => -1);
$search_cat = ( isset( $_POST[ 'search_cat' ] ) && is_array( $_POST[ 'search_cat' ] ) ) ? array_map( 'intval', $_POST['search_cat'] ) : array(0 => -1);

if ( pkGetConfig( 'captcha' ) && $ENV->_isset_post( 'search' ) && !$ENV->_isset_post( pkCAPTCHAVARNAME ) && !pkCaptchaCodeValid( NULL ) )
{
	#navbox used - redirect to the search form
	pkHeaderLocation( 'search', '', 'search_text=' . $search_text );
}

switch ( $mode )
{
	case 'result' :
		if ( $ACTION != 'view' && !pkCaptchaCodeValid( $ENV->_post( pkCAPTCHAVARNAME ) ) )
		{
			pkHeaderLocation( 'search', '', 'error=securitycode_invalid&search_text=' . urlencode( $search_text ) . '&search_textoption=' . $search_textoption . '&rorder=' . $rorder . '&' . pkArrayUrlencode( 'search_type', $search_type ) );
		}

		if ( ( empty( $_POST[ 'search_text' ] ) && !$SESSION->exists( 'save_rcontent' ) ) || ( isset( $_POST[ 'search'
		] ) && isset( $_POST[ 'search_text' ] ) && $ACTION == $_POST[ 'search' ] && empty( $_POST[ 'search_text' ] ) ) )
		{

			pkHeaderLocation( 'search', '', 'error=search_noresult&search_text=' . urlencode( $search_text ) . '&search_textoption=' . $search_textoption . '&rorder=' . $rorder . '&' . pkArrayUrlencode( 'search_type', $search_type ) );
		}

		if ( isset( $_REQUEST[ 'rorder' ] ) && intval( $_REQUEST[ 'rorder' ] ) == 1 )
		{
			$order = " ORDER by content_time ASC";
			$rorder = 1;
		}
		else
		{
			$order = " ORDER by content_time DESC";
			$rorder = 0;
		}

		$save_rcontent = ( $SESSION->exists( 'save_rcontent' ) && is_array( $SESSION->get( 'save_rcontent' ) ) ) ? $SESSION->get( 'save_rcontent' ) : NULL;
		$rcontent = '';

		if ( !empty( $search_text ) )
		{
			$search_text = trim( $ENV->_post( 'search_text' ) );
			$search_text = $search_text == pkGetLang( 'search_contents' ) ? '' : $search_text;
			$search_string = explode( " or ", strtolower( str_replace( "\+", " AND ", str_replace( "\-", " OR ", trim( $search_text ) ) ) ) );

			foreach ( $search_string as $k )
			{
				if ( stripos( 'or', $k ) === FALSE && strlen( $k ) < pkGetConfig( 'search_min_length' ) )
				{
					pkHeaderLocation( 'search', '', 'error=searchterm_too_short&search_text=' . urlencode( $search_text ) . '&search_textoption=' . $search_textoption . '&rorder=' . $rorder . '&' . pkArrayUrlencode( 'search_type', $search_type ) );
				}
			}

			unset( $search_text );
			$ic = 1;
			$searchtext = "(";

			if ( is_array( $search_string ) )
			{
				foreach ( $search_string as $i )
				{
					$i = str_replace( " ", " AND ", trim( $i ) );

					if ( stripos( " and ", $i ) !== FALSE )
					{
						$searchtext .= "(";
						$ii = explode( " and ", strtolower( $i ) );
						$iic = 1;

						if ( is_array( $ii ) )
						{
							foreach ( $ii as $iii )
							{
								$iii = $SQL->f( trim( $iii ) );

								if ( $search_textoption == 1 )
								{
									$searchtext .= "(content_title LIKE '%" . $iii . "%')";
								}
								else
								{
									$searchtext .= "(content_title LIKE '%" . $iii . "%' OR content_text LIKE '%" . $iii . "%')";
								}

								if ( count( $ii ) > $iic )
								{
									$searchtext .= " AND ";
									$iic++;
								}
							}
						}

						$searchtext .= ")";
					}
					else
					{
						$i = $SQL->f( trim( $i ) );

						if ( $search_textoption == 1 )
						{
							$searchtext .= "(content_title LIKE '%" . $i . "%')";
						}
						else
						{
							$searchtext .= "(content_title LIKE '%" . $i . "%' OR content_text LIKE '%" . $i . "%')";
						}
					}

					if ( count( $search_string ) > $ic )
					{
						$searchtext .= " OR ";
						$ic++;
					}

				}

				$searchtext .= ")";
				$sqlcommand = " FROM " . pkSQLTAB_CONTENT . " WHERE " . $searchtext;
			}

			unset( $search_type_string );

			if ( $search_type[ 0 ] != "-1" )
			{
				if ( $search_type[ 0 ] != "-1" && is_array( $search_type ) )
				{
					foreach ( $search_type as $st )
					{
						if ( $search_type_string )
						{
							$search_type_string .= " OR content_option='" . $st . "'";
						}
						else
						{
							$search_type_string = " AND (content_option='" . $st . "'";
						}
					}

					if ( $search_type_string )
					{
						$search_type_string .= ")";
					}

					$sqlcommand .= " " . $search_type_string;
				}
			}

			unset( $search_cat_string );

			if ( $search_cat[ 0 ] != "-1" )
			{
				if ( $search_cat[ 0 ] != "-1" && is_array( $search_cat ) )
				{
					foreach ( $search_cat as $sc )
					{
						if ( $search_cat_string )
						{
							$search_cat_string .= " OR content_cat='" . $sc . "'";
						}
						else
						{
							$search_cat_string = " AND (content_cat='" . $sc . "'";
						}
					}

					if ( $search_cat_string )
					{
						$search_cat_string .= ")";
					}

					$sqlcommand .= " " . $search_cat_string;
				}
			}

			if ( isset( $_POST[ 'search' ] ) || $ACTION == $_POST[ 'search' ] )
			{
				unset( $rcontent );

				$contentcat_cache = contentcats( );
				$getcontent = $SQL->query( "SELECT
					content_id,
					content_cat " . $sqlcommand . " AND
					content_status=1 AND 
					content_time<'" . pkTIME . "' AND
					(content_expire>'" . pkTIME . "' OR content_expire=0) " . $order . "
					LIMIT " . pkGetConfig( 'search_max' ) );
				while ( $content = $SQL->fetch_array( $getcontent ) )
				{
					$cat = $contentcat_cache[ 0 ][ $content[ 'content_cat' ] ];
					if ( getrights( $cat[ 'contentcat_rights' ] ) )
					{
						$rcontent[ ] = array(
							$content[ 'content_id' ], $content[ 'content_cat' ]
						);
					}
				}
			}
		}

		if ( !is_array( $rcontent ) && !is_array( $save_rcontent ) )
		{
			pkHeaderLocation( 'search', '', 'error=search_noresult&search_text=' . urlencode( $search_text ) . '&search_textoption=' . $search_textoption . '&rorder=' . $rorder . '&' . pkArrayUrlencode( 'search_type', $search_type ) );
		}

		if ( !$save_rcontent || ( $save_rcontent && $rcontent ) )
		{
			$SESSION->set( 'save_rcontent', $rcontent );
		}

		if ( !$rcontent && $save_rcontent )
		{
			$rcontent = $save_rcontent;
		}

		if ( isset( $_POST[ 'search' ] ) || $ACTION == $_POST[ 'search' ] ) //TODO Redirect problem
		{
			if ( count( $rcontent ) >= pkGetConfig( 'search_max' ) )
			{
				$link = pkHeaderLink( 'search', 'result', 'rorder=' . $rorder . '&entries=' . $entries, NULL, NULL, false );
				pkHeaderLocation( '', '', 'event=searchresult_limited&moveto=' . urlencode( $link ) );
			}

			pkHeaderLocation( 'search', 'result', 'rorder=' . $rorder . '&entries=0' );
		}

		$epp = 20;
		$entries = ( isset( $_REQUEST[ 'entries' ] ) && intval( $_REQUEST[ 'entries' ] ) > 0 ) ? intval( $_REQUEST[ 'entries' ] ) : 0;

		$resultcount = count( $rcontent );
		$sidelink = sidelinkfull( $resultcount, $epp, $entries, "include.php?path=search&mode=result&rorder=" . $rorder );
		$sidelink2 = sidelinkfull( $resultcount, $epp, $entries, "include.php?path=search&mode=result&rorder=" . $rorder, "headssmall" );

		$id_string = '' ;

		foreach ( $rcontent as $cid )
		{
			if ( $id_string )
			{
				$id_string .= " OR ";
			}

			$id_string .= " content_id='" . $cid[ 0 ] . "'";
		}

		$contentcat_cache = contentcats( );

		$getcontentinfo = $SQL->query( "SELECT
											content_cat,
											content_id,
											content_title,
											content_status,
											content_option
										FROM " . pkSQLTAB_CONTENT . "
										WHERE " . $id_string . " " . $order . "
										LIMIT " . $entries . "," . $epp );
		while ( $contentinfo = $SQL->fetch_array( $getcontentinfo ) )
		{
			$contentcat = $contentcat_cache[ 0 ][ $contentinfo[ 'content_cat' ] ];

			if ( !getrights( $contentcat[ 'contentcat_rights' ] ) )
			{
				continue;
			}

			$row = rowcolor( $row );
			$contentcat_title = pkEntities( $contentcat[ 'contentcat_name' ] );
			$contentinfo[ 'content_title' ] = pkEntities( $contentinfo[ 'content_title' ] );

			if ( $contentinfo[ 'content_option' ] == 1 )
			{
				$content_type = $lang[ 'article' ];
				$content_option = $contentinfo[ 'content_option' ];
			}
			elseif ( $contentinfo[ 'content_option' ] == 2 )
			{
				$content_type = $lang[ 'news' ];
				$content_option = $contentinfo[ 'content_option' ];
			}
			elseif ( $contentinfo[ 'content_option' ] == 3 )
			{
				$content_type = $lang[ 'link' ];
				$content_option = $contentinfo[ 'content_option' ];
			}
			elseif ( $contentinfo[ 'content_option' ] == 4 )
			{
				$content_type = $lang[ 'download' ];
				$content_option = $contentinfo[ 'content_option' ];
			}
			else
			{
				$content_type = $lang[ 'content' ];
			}

			eval( "\$searchresult_row.= \"" . pkTpl( "content/searchresult_row" ) . "\";" );

			unset( $contentcat_title );
		}

		eval( "\$site_body.= \"" . pkTpl( "content/searchresult" ) . "\";" );
		break;
	#END case result
	default :
		$SESSION->deset( 'save_rcontent' );

		if ( isset( $_REQUEST[ 'error' ] ) && $_REQUEST[ 'error' ] == 'searchterm_too_short' )
		{
			pkEvent( 'searchterm_too_short', 0 );
		}
		elseif ( isset( $_REQUEST[ 'error' ] ) && $_REQUEST[ 'error' ] == 'securitycode_invalid' )
		{
			pkEvent( 'securitycode_invalid', 0 );
		}
		elseif ( isset( $_REQUEST[ 'error' ] ) && $_REQUEST[ 'error' ] == 'search_noresult' )
		{
			pkEvent( 'search_noresult', 0 );
		}

		$cat_option = $search_textoption0 = $search_textoption1 = $rorder0 = $rorder1 = '';
		$contentcat_cache = contentcats( );
		$contentcat_cache = $contentcat_cache[ 0 ];

		if ( is_array( $contentcat_cache ) )
		{
			foreach ( $contentcat_cache as $contentcat )
			{
				if ( !getrights( $contentcat[ 'contentcat_rights' ] ) )
				{
					continue;
				}

				$cat_option .= '<option value="' . $contentcat[ 'contentcat_id' ] . '">' . pkEntities(
					$contentcat[ 'contentcat_name' ] ) . '</option>';
			}
		}

		$search_text = pkEntities( urldecode( $ENV->_get( 'search_text' ) ) );

		$search_textoption ? $search_textoption1 = ' checked="checked"' : $search_textoption0 = ' checked="checked"';
		$rorder ? $rorder1 = ' checked="checked"' : $rorder0 = ' checked="checked"';

		if ( isset( $search_type[ 0 ] ) && $search_type[ 0 ] == -1 )
		{
			$search_type_1 = ' selected="selected"';
			$search_type = array(
			);
		}

		foreach ( range( 0, 4 ) as $i )
		{
			$var = 'search_type' . $i;
			$$var = in_array( $i, $search_type ) ? ' selected="selected"' : '';
		}

		$captcha = pkCaptchaField( NULL, 2, 1 );

		eval( "\$site_body.= \"" . pkTpl( "content/search" ) . "\";" );
		break;
	#END case default
}
?>