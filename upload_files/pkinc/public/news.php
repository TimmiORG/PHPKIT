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
pkLoadLang( 'content' );

$contentinfo_cache = array(
);

$entries = ( isset( $_REQUEST[ 'entries' ] ) && intval( $_REQUEST[ 'entries' ] ) > 0 ) ? intval(
	$_REQUEST[ 'entries' ] ) : 0;

$contentid = ( isset( $contentid ) && intval( $contentid ) > 0 ) ? intval( $contentid ) : 0;
$contentid = ( isset( $_REQUEST[ 'contentid' ] ) && $_REQUEST[ 'contentid' ] == 'new' ) ? 'new' : $contentid;
$contentid = ( isset( $_REQUEST[ 'contentid' ] ) && intval( $_REQUEST[ 'contentid' ] ) > 0 ) ? intval(
	$_REQUEST[ 'contentid' ] ) : $contentid;

$catid = ( isset( $_REQUEST[ 'catid' ] ) && intval( $_REQUEST[ 'catid' ] ) > 0 ) ? intval( $_REQUEST[ 'catid' ] ) : 0;

$sqlcommand = " WHERE " . pkSQLTAB_CONTENT . ".content_option='2' AND
	" . pkSQLTAB_CONTENT . ".content_status='1' AND
	" . pkSQLTAB_CONTENT . ".content_time<'" . pkTIME . "' AND
	(" . pkSQLTAB_CONTENT . ".content_expire>'" . pkTIME . "' OR
	" . pkSQLTAB_CONTENT . ".content_expire='0')";

if ( $contentid && $contentid != 'new' )
{
	$sqlcommand .= " AND " . pkSQLTAB_CONTENT . ".content_id='" . $SQL->i( $contentid ) . "'";
}

if ( $catid )
{
	$sqlcommand .= " AND " . pkSQLTAB_CONTENT . ".content_cat='" . $catid . "'";
}

$sqlcommand = " FROM " . pkSQLTAB_CONTENT . "
	LEFT JOIN " . pkSQLTAB_CONTENT_CATEGORY . " ON " . pkSQLTAB_CONTENT_CATEGORY . ".contentcat_id=" . pkSQLTAB_CONTENT . ".content_cat " . $sqlcommand . " AND
	(" . sqlrights( pkSQLTAB_CONTENT_CATEGORY . ".contentcat_rights" ) . ")";

$sqlcommand2 = "SELECT " . pkSQLTAB_CONTENT . ".* " . $sqlcommand;

$sqlcommand = "SELECT " . pkSQLTAB_CONTENT . ".* " . $sqlcommand . "
	ORDER by " . pkSQLTAB_CONTENT . ".content_time DESC
	LIMIT " . $entries . "," . ( ( $contentid ) ? 1 : pkGetConfig( 'content_epp2' ) );

$getcontentinfo = $SQL->query( $sqlcommand );
$sqlcommand = '';
while ( $contentinfo = $SQL->fetch_array( $getcontentinfo ) )
{
	$contentinfo_cache[ ] = $contentinfo;

	if ( $contentinfo[ 'content_autorid' ] )
	{
		if ( empty( $sqlcommand ) )
		{
			$sqlcommand = "SELECT user_nick, user_id FROM " . pkSQLTAB_USER . " WHERE user_id='" .
			              $contentinfo[ 'content_autorid' ] . "'";
		}
		else
		{
			$sqlcommand .= " OR user_id='" . $contentinfo[ 'content_autorid' ] . "'";
		}
	}
}

if ( $contentid && empty( $contentinfo_cache ) )
{
	pkEvent( 'article_not_available' );
	return;
}

if ( $sqlcommand )
{
	$getuserinfo = $SQL->query( $sqlcommand );
	while ( $userinfo = $SQL->fetch_array( $getuserinfo ) )
	{
		$userinfo_cache[ $userinfo[ 'user_id' ] ] = $userinfo;
	}
}

if ( is_array( $contentinfo_cache ) )
{
	$contentcat = contentcats( );
	$contentcat_cache = $contentcat[ '0' ];

	foreach ( $contentinfo_cache as $contentinfo )
	{
		$contentcatinfo = $contentcat_cache[ $contentinfo[ 'content_cat' ] ];
		$contentcat_name = pkEntities( $contentcatinfo[ 'contentcat_name' ] );

		if ( $contentinfo[ 'content_autorid' ] == 0 )
		{
			$autor_info = $newsinfo[ 'content_autor' ];
		}
		else
		{
			$userinfo = $userinfo_cache[ $contentinfo[ 'content_autorid' ] ];

			if ( $userinfo[ 'user_id' ] )
			{
				$userinfo[ 'user_nick' ] = pkEntities( $userinfo[ 'user_nick' ] );
				eval ( "\$autor_info= \"" . pkTpl( "member_showprofil_textlink" ) . "\";" );
			}
			else
			{
				$autor_info = pkEntities( $contentinfo[ 'content_autor' ] );
			}
		}

		$news_time = formattime( $contentinfo[ 'content_time' ] );
		$catimage_dimension = @getimagesize( "images/catimages/" . $contentcatinfo[ 'contentcat_symbol' ] );
		$type = 2;

		eval( "\$news_image= \"" . pkTpl( "content/cat_image_right" ) . "\";" );

		if ( !$contentid )
		{
			$news_text = explode( "<break>", $contentinfo[ 'content_text' ] );
			$news_text = $news_text[ 0 ];
		}
		else
		{
			$news_text = str_replace( '<break>', '', $contentinfo[ 'content_text' ] );
		}

		$news_text = $BBCODE->parse( $news_text, $contentinfo[ 'content_html' ], $contentinfo[ 'content_ubb' ],
		                             $contentinfo[ 'content_smilies' ], 1 );

		if ( !$contentid && stripos( "<break>", $contentinfo[ 'content_text' ] ) !== FALSE )
		{
			eval( "\$news_text.= \"" . pkTpl( "content/news_morelink" ) . "\";" );
		}

		$news_text = str_replace( "<break>", '', $news_text );

		if ( $contentinfo[ 'content_comment_status' ] == 1 )
		{
			list( $commentcount ) = $SQL->fetch_row( $SQL->query( "SELECT COUNT(*) FROM " . pkSQLTAB_COMMENT . " WHERE comment_subid='" . $contentinfo[ 'content_id'] . "' AND comment_cat='cont'" ) );

			$news_comments = pkGetSpecialLang( 'comment', $commentcount );
		}
		else
		{
			$news_comments = '';
		}

		$news_source = '';

		if ( !empty( $contentinfo[ 'content_altdat' ] ) )
		{
			$source = explode( "\n", $contentinfo[ 'content_altdat' ] );

			foreach ( $source as $link )
			{
				$link = trim( $link );

				if ( !empty( $link ) )
				{
					$news_source .= empty( $news_source ) ? '' : '<br />';
					$news_source .= $BBCODE->parse( $link, 1, 1, 0, 0 );
				}
			}

			if ( !empty( $news_source ) )
			{
				eval( "\$news_source=\"" . pkTpl( "content/news_source" ) . "\";" );
			}
		}

		$cont_title = pkEntities( $contentinfo[ 'content_title' ] );
		$suggest_url = urlencode( "path=news&contentid=" . $contentinfo[ 'content_id' ] );

		eval( "\$site_body.=\"" . pkTpl( "content/news" ) . "\";" );

		unset( $news_source );
		unset( $news_comments );
		unset( $news_text );
		unset( $news_text_cut );
	}
}

$page_link = '';
if ( $contentid )
{
	#single news
	#set site title
	$CMS->site_title_set( $contentinfo[ 'content_title' ] );
}
else
{
	#news overview
	$counter = $SQL->num_rows( $SQL->query( $sqlcommand2 ) );

	if ( $counter > $config[ 'content_epp2' ] )
	{
		$page_link = sidelinkfull( $counter, $config[ 'content_epp2'
		                                     ], $entries, "include.php?path=news&catid=" . $catid, "sitebodysmall" );
	}

	#set site title
	$title = pkGetConfigF( 'content_overview_title_news' );
	$title = empty( $title ) ? pkGetLang( 'content_overview_title_news' ) : $title;
	$CMS->site_title_set( $title, true );
}

#news overview footer navigation
$archive_news_title = pkGetConfigF( 'content_archive_title_news' );
$archive_news_title = empty( $archive_news_title ) ? pkGetLang( 'content_archive_title_news' ) : $archive_news_title;

$archive_news_link = pkLink( 'contentarchive', '', 'type=2' );

eval( "\$site_body.=\"" . pkTpl( "content/news_navigation" ) . "\";" );
?>