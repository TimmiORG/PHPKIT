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
$contenttheme_info = $download_file = $file_size = $download_links = $content_teaser = $content_rating_info = $content_submit = $related_boxes = '';

if ( !isset( $contentid ) || !intval( $contentid ) > 0 )
{
	$contentid = ( isset( $_REQUEST[ 'contentid' ] ) && intval( $_REQUEST[ 'contentid' ] ) > 0 ) ? intval(
		$_REQUEST[ 'contentid' ] ) : 0;
}

if ( !$contentid )
{
	pkHeaderLocation( 'contentarchive', '', 'type=4' );
}

if ( isset( $_REQUEST[ 'download' ] ) && $_REQUEST[ 'download' ] == 'go' )
{
	#content item
	$query = $SQL->query( "SELECT
			content_cat, 
			content_altdat,
			content_status,
			content_id 
		FROM " . pkSQLTAB_CONTENT . "
		WHERE content_id='" . intval( $contentid ) . "'
		LIMIT 1" );
	$contentinfo = $SQL->fetch_assoc( $query );

	#content category (by content category id)
	$query = $SQL->query( "SELECT
			contentcat_rights
		FROM " . pkSQLTAB_CONTENT_CATEGORY . "
		WHERE contentcat_id='" . $contentinfo[ 'content_cat' ] . "'
		LIMIT 1" );
	$contentcatinfo = $SQL->fetch_assoc( $query );

	if ( $contentinfo[ 'content_status' ] != 1 )
	{
		pkEvent( 'download_not_available' );
		return;
	}

	if ( getrights( $config[ 'content_downloadstatus' ] ) != "true" || getrights( $contentcatinfo[ 'contentcat_rights'
	                                                                              ] ) != "true" )
	{
		pkEvent( 'access_refused' );
		return;
	}

	if ( $contentinfo[ 'content_altdat' ] != '' )
	{
		$SQL->query( "UPDATE " . pkSQLTAB_CONTENT . " SET content_views=content_views+1 WHERE content_id='" . $contentid . "'" );

		$dl = explode( "\n", trim( $contentinfo[ 'content_altdat' ] ) );

		if ( is_array( $dl ) )
		{
			$c = 0;

			if ( intval( $_REQUEST[ 'mirror' ] ) > 0 )
			{
				$mirror = intval( $_REQUEST[ 'mirror' ] );
			}
			else
			{
				unset( $mirror );
			}

			unset( $download );

			foreach ( $dl as $d )
			{
				$c++;
				$d = trim( $d );

				if ( ( $mirror == $c || !isset( $mirror ) ) && trim( $d ) != '' )
				{
					if ( filecheck( $config[ 'content_downloadpath' ] . '/' . $d ) )
					{
						$download = $config[ 'content_downloadpath' ] . '/' . $d;
					}
					elseif ( filecheck( $d ) )
					{
						$download = $d;
					}
					else
					{
						unset( $download );
					}

					if ( $download && $download != $config[ 'content_downloadpath' ] . '/' )
					{
						header( "location: " . $download );
						exit( );
					}
				}
			}

			if ( !$download && isset( $mirror ) )
			{
				pkHeaderLocation( 'download', '', 'contentid=' . $contentid . '&event=download_not_found' );
			}
			else
			{
				header( "location: " . $dl[ 0 ] );
				exit( );
			}
		}
	}

	pkHeaderLocation( 'download', '', 'contentid=' . $contentid . '&event=download_not_found' );
}

if ( !intval( $contentid ) > 0 )
{
	pkEvent( 'page_not_found' );
	return;
}

$contentinfo = $SQL->fetch_assoc( $SQL->query( "SELECT * FROM " . pkSQLTAB_CONTENT . " where content_id='" . $contentid . "'" ) );

if ( $contentinfo[ 'content_status' ] != 1 )
{
	pkEvent( 'download_not_available' );
	return;
}

if ( $contentinfo[ 'content_option' ] == 0 )
{
	pkHeaderLocation( 'content', '', 'contentid=' . $contentid );
}

if ( $contentinfo[ 'content_option' ] == 1 )
{
	pkHeaderLocation( 'content/articles', '', 'contentid=' . $contentid );
}

if ( $contentinfo[ 'content_option' ] == 2 )
{
	pkHeaderLocation( 'news', '', 'contentid=' . $contentid );
}

if ( $contentinfo[ 'content_option' ] == 3 )
{
	pkHeaderLocation( 'link', '', 'contentid=' . $contentid );
}

$contentcat_cache = contentcats( );
$contentcatinfo = $contentcat_cache[ 0 ][ $contentinfo[ 'content_cat' ] ];

if ( !getrights( $contentcatinfo[ 'contentcat_rights' ] ) )
{
	pkEvent( 'access_refused' );
	return;
}

pkLoadClass( $BBCODE, 'bbcode' );

if ( $userinfo = $SQL->fetch_array( $SQL->query( "SELECT user_id, user_nick FROM " . pkSQLTAB_USER . " WHERE user_id='" .
                                                 $contentinfo[ 'content_autorid' ] . "' LIMIT 1" ) ) )
{
	$userinfo[ 'user_nick' ] = pkEntities( $userinfo[ 'user_nick' ] );

	eval( "\$autor_info= \"" . pkTpl( "member_showprofil_textlink" ) . "\";" );
}
else
{
	$autor_info = pkEntities( $contentinfo[ 'content_autor' ] );
}

$CMS->site_title_set( $contentinfo[ 'content_title' ] );
#@TODO: Revise this - the title is from here html encoded 
$contentinfo[ 'content_title' ] = pkEntities( $contentinfo[ 'content_title' ] );

$content_time = formattime( $contentinfo[ 'content_time' ], '', 'date' );
$cat_name = pkEntities( $contentcatinfo[ 'contentcat_name' ] );

eval( "\$content_article_head=\"" . pkTpl( "content/download_textlink" ) . "\";" );

if ( !empty( $contentinfo[ 'content_teaser' ] ) )
{
	$teaser_dimension = @getimagesize( $contentcatinfo[ 'content_teaser' ] );

	eval( "\$content_teaser= \"" . pkTpl( "content/download_teaser" ) . "\";" );
}

if ( !empty( $contentcatinfo[ 'contentcat_symbol' ] ) && $contentcatinfo[ 'contentcat_symbol' ] != "blank.gif" )
{
	$catimage_dimension = @getimagesize( "images/catimages/" . $contentcatinfo[ 'contentcat_symbol' ] );

	eval( "\$content_catimage= \"" . pkTpl( "content/download_catimage" ) . "\";" );
}

$content_article_body = $BBCODE->parse(
	$contentinfo[ 'content_text' ], $contentinfo[ 'content_html' ], $contentinfo[ 'content_ubb' ],
	$contentinfo[ 'content_smilies' ], 1 );
$dl = explode( "\n", $contentinfo[ 'content_altdat' ] );

// reset
$download_file = $file_size = $download_links = '';

$c = 0;

if ( is_array( $dl ) )
{
	foreach ( $dl as $d )
	{
		$d = trim( $d );

		if ( !empty( $d ) )
		{
			$c++;

			if ( !empty( $download_file ) )
			{
				$download_file = basename( $d );
			}

			$sn = explode( "/", $d );

			if ( is_array( $sn ) )
			{
				if ( $sn[ 0 ] != 'http:' && $sn[ 0 ] != 'ftp:' )
				{
					$download_server = preg_replace( "/(.*)@/", "", basename( $_SERVER[ 'SERVER_NAME' ] ) );
				}
				else
				{
					$download_server = $sn[ 2 ];
				}
			}
		}

		if ( !empty( $file_size ) )
		{
			$file_size = FileSizeExt( $config[ 'content_downloadpath' ] . '/' . $d, "B" );
		}

		if ( $download_server != '' )
		{
			eval( "\$download_links.= \"" . pkTpl( "content/download_link" ) . "\";" );
		}

		unset( $download_server );
	}
}

if ( $file_size == '' )
{
	$file_size = FileSizeExt( '', "B", $contentinfo[ 'content_filesize' ] * 1024 );
}

if ( $file_size == '' || $file_size == "0" )
{
	$file_size = '&nbsp; - &nbsp;';
}

if ( $contentinfo[ 'content_comment_status' ] == 1 )
{
	$counter = $SQL->fetch_array( $SQL->query( "SELECT COUNT(*) FROM " . pkSQLTAB_COMMENT . " WHERE comment_cat='cont' AND comment_subid='" .
	                                           $contentinfo[ 'content_id' ] . "'" ) );

	eval( "\$content_comment= \"" . pkTpl( "content/download_comment_link" ) . "\";" );
}

if ( $contentinfo[ 'content_rating_status' ] == 1 )
{
	if ( $contentinfo[ 'content_rating_total' ] > 0 )
	{
		$content_rating_d = number_format( $contentinfo[ 'content_rating' ], 2, ",", "." );
		$content_rating_votes = $contentinfo[ 'content_rating_total' ];

		eval( "\$content_rating_info= \"" . pkTpl( "content/download_rating_info" ) . "\";" );
	}

	eval( "\$content_rate= \"" . pkTpl( "content/download_rating_link" ) . "\";" );
}

unset( $sqlcommand );

if ( !empty( $contentinfo[ 'content_related' ] ) )
{
	$related = explode( '-', $contentinfo[ 'content_related' ] );

	if ( is_array( $related ) )
	{
		foreach ( $related as $id )
		{
			if ( intval( $id ) > 0 )
			{
				if ( $sqlcommand )
				{
					$sqlcommand .= " OR content_id='" . intval( $id ) . "'";
				}
				else
				{
					$sqlcommand = "SELECT content_cat, content_id, content_title, content_option FROM " . pkSQLTAB_CONTENT . " WHERE content_time<'" . time( ) . "' AND content_status=1 AND (content_expire>'" . time( ) . "' OR content_expire=0) AND (content_id='" . intval( $id ) . "'";
				}
			}
		}

		if ( $sqlcommand )
		{
			$getrelated = $SQL->query( $sqlcommand . ")" );
			while ( $related = $SQL->fetch_array( $getrelated ) )
			{
				$related_cache[ ] = $related;
			}
		}
	}

	if ( is_array( $related_cache ) )
	{
		foreach ( $related_cache as $content )
		{
			$contentcatinfo = $contentcat_cache[ 0 ][ $content[ 'content_cat' ] ];

			if ( getrights( $contentcatinfo[ 'contentcat_rights' ] ) == "true" )
			{
				if ( $content[ 'content_option' ] == 1 )
				{
					eval( "\$related_articles.= \"" . pkTpl( "content/download_related_link" ) . "\";" );
				}
				elseif ( $content[ 'content_option' ] == 2 )
				{
					eval( "\$related_news.= \"" . pkTpl( "content/download_related_link" ) . "\";" );
				}
				elseif ( $content[ 'content_option' ] == 3 )
				{
					eval( "\$related_links.= \"" . pkTpl( "content/download_related_link" ) . "\";" );
				}
				elseif ( $content[ 'content_option' ] == 4 )
				{
					eval( "\$related_downloads.= \"" . pkTpl( "content/download_related_link" ) . "\";" );
				}
				else
				{
					eval( "\$related_text.= \"" . pkTpl( "content/download_related_link" ) . "\";" );
				}
			}
		}

		if ( $related_articles != '' )
		{
			eval( "\$related_boxes.= \"" . pkTpl( "content/download_related_box_articles" ) . "\";" );
		}

		if ( $related_news != '' )
		{
			eval( "\$related_boxes.= \"" . pkTpl( "content/download_related_box_news" ) . "\";" );
		}

		if ( $related_links != '' )
		{
			eval( "\$related_boxes.= \"" . pkTpl( "content/download_related_box_links" ) . "\";" );
		}

		if ( $related_downloads != '' )
		{
			eval( "\$related_boxes.= \"" . pkTpl( "content/download_related_box_downloads" ) . "\";" );
		}

		if ( $related_text != '' )
		{
			eval( "\$related_boxes.= \"" . pkTpl( "content/download_related_box_text" ) . "\";" );
		}
	}
}

$suggest_url = urlencode( 'path=download&contentid=' . $contentinfo[ 'content_id' ] );

eval( "\$site_body.= \"" . pkTpl( "content/download" ) . "\";" );
?>