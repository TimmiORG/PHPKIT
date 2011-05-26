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
$contenttheme_info = $type = $content_rating_info = $page = $article_pagelink = $content_teaser = $content_comment = $related_boxes = '';

pkLoadClass( $BBCODE, 'bbcode' );

$modehash = array(
	'new'
);

if ( isset( $mode ) && $mode == 'new' )
{
}
else
{
	$mode = ( isset( $_REQUEST[ 'mode' ] ) && in_array( $_REQUEST[ 'mode' ], $modehash ) ) ? $_REQUEST[ 'mode' ] : NULL;
}

switch ( $mode )
{
	case 'new' :
		$contentcat_cache = contentcats( );

		$getcontentinfo = $SQL->query( "SELECT
				" . pkSQLTAB_CONTENT . ".content_id,
				" . pkSQLTAB_CONTENT . ".content_cat,
				" . pkSQLTAB_CONTENT . ".content_title,
				" . pkSQLTAB_CONTENT . ".content_time,
				" . pkSQLTAB_CONTENT . ".content_text
			FROM " . pkSQLTAB_CONTENT . "
				LEFT JOIN " . pkSQLTAB_CONTENT_CATEGORY . " ON " . pkSQLTAB_CONTENT_CATEGORY . ".contentcat_id=" . pkSQLTAB_CONTENT . ".content_cat
			WHERE " . pkSQLTAB_CONTENT . ".content_option=1 AND
				" . pkSQLTAB_CONTENT . ".content_status=1 AND
				(" . pkSQLTAB_CONTENT . ".content_expire>'" . pkTIME . "' OR
				" . pkSQLTAB_CONTENT . ".content_expire='0') AND
				" . pkSQLTAB_CONTENT . ".content_time<'" . pkTIME . "' AND
				" . sqlrights( "" . pkSQLTAB_CONTENT_CATEGORY . ".contentcat_rights" ) . "
			ORDER by " . pkSQLTAB_CONTENT . ".content_time DESC
			LIMIT 5" );
		while ( $contentinfo = $SQL->fetch_array( $getcontentinfo ) )
		{
			$row = rowcolor( $row );
			$contentcatinfo = $contentcat_cache[ 0 ][ $contentinfo[ 'content_cat' ] ];
			$contentcat_title = pkEntities( $contentcatinfo[ 'contentcat_name' ] );
			$content_teaser;
			$content_time = formattime( $contentinfo[ 'content_time' ] );

			$content_title = pkEntities( $contentinfo[ 'content_title' ] );

			$content_text = $BBCODE->parse(
				$contentinfo[ 'content_header' ] . " " . $contentinfo[ 'content_text' ], 1, 1, 1, 1 );
			$content_text = strip_tags( $content_text );
			$content_text = mb_substr( $content_text, 0, 250, pkGetLang( '__CHARSET__' ) );

			$content_catid = $contentinfo[ 'content_cat' ];
			$category_link = pkLink( 'contentarchive', '', 'type=1&catid=' . $content_catid );

			eval( "\$articlesnew_row.= \"" . pkTpl( "content/articlesnew_row" ) . "\";" );
		}

		eval( "\$site_body.= \"" . pkTpl( "content/articlesnew" ) . "\";" );
		break;
	#END case new
	default :
		$contentid = ( !$contentid && isset( $_REQUEST[ 'contentid' ] ) && intval( $_REQUEST[ 'contentid'
		                                                                           ] ) > 0 ) ? intval(
			$_REQUEST[ 'contentid' ] ) : $contentid;

		if ( !$contentid || !$contentid > 0 )
		{
			pkHeaderLocation( 'contentarchive', '', 'type=1' );
		}

		$contentinfo = $SQL->fetch_assoc( $SQL->query( "SELECT * FROM " . pkSQLTAB_CONTENT . " where content_id='" . $contentid . "' LIMIT 1" ) );

		$contentcat_cache = contentcats( );
		$contentcatinfo = $contentcat_cache[ 0 ][ $contentinfo[ 'content_cat' ] ];

		if ( !getrights( $contentcatinfo[ 'contentcat_rights' ] ) )
		{
			pkEvent( 'access_refused' );
			return;
		}

		if ( !$contentinfo[ 'content_status' ] == 1 )
		{
			pkEvent( 'article_not_available' );
			return;
		}

		if ( $contentinfo[ 'content_option' ] == 0 )
		{
			pkHeaderLocation( 'content', '', 'contentid=' . $contentid );
		}

		if ( $contentinfo[ 'content_option' ] == 2 )
		{
			pkHeaderLocation( 'news', '', 'contentid=' . $contentid );
		}

		if ( $contentinfo[ 'content_option' ] == 3 )
		{
			pkHeaderLocation( 'link', '', 'contentid=' . $contentid );
		}

		if ( $contentinfo[ 'content_option' ] == 4 )
		{
			pkHeaderLocation( 'download', '', 'contentid=' . $contentid );
		}

		//preset
		$sqlcommand = '';
		$related_cache = array();

		if ( $contentinfo[ 'content_related' ] != '' )
		{
			$related = explode( '-', $contentinfo[ 'content_related' ] );

			if ( is_array( $related ) )
			{
				foreach ( $related as $id )
				{
					if ( intval( $id ) > 0 )
					{
						if ( !empty($sqlcommand) )
						{
							$sqlcommand .= " OR content_id='" . $id . "'";
						}
						else
						{
							$sqlcommand = "SELECT
									content_cat,
									content_id,
									content_title,
									content_option 
								FROM " . pkSQLTAB_CONTENT . "
								WHERE content_time<'" . pkTIME . "'
									AND content_status=1 
									AND (content_expire>'" . pkTIME . "'
										OR content_expire=0) 
									AND (content_id='" . $id . "'";
						}
					}
				}

				if ( !empty($sqlcommand) )
				{
					$getrelated = $SQL->query( $sqlcommand . ")" );
					while ( $related = $SQL->fetch_array( $getrelated ) )
					{
						$related_cache[ ] = $related;
					}
				}
			}
		}

		if ( !isset( $_REQUEST[ 'page' ] ) )
		{
			$SQL->query( "UPDATE " . pkSQLTAB_CONTENT . " SET content_views=content_views+1 WHERE content_id='" .
			             $contentinfo[ 'content_id' ] . "'" );
		}

		if ( intval( $contentinfo[ 'content_autorid' ] ) > 0 )
		{
			$userinfo = $SQL->fetch_array( $SQL->query( "SELECT user_id, user_nick FROM " . pkSQLTAB_USER . " WHERE user_id='" .
			                                            $contentinfo[ 'content_autorid' ] . "' LIMIT 1" ) );
		}

		if ( intval( $contentinfo[ 'content_themeid' ] ) > 0 )
		{
			$contentthemeinfo = $SQL->fetch_array( $SQL->query( "SELECT * FROM " . pkSQLTAB_CONTENT_THEME . " WHERE contenttheme_id='" .
			                                                    $contentinfo[ 'content_themeid' ] . "' LIMIT 1" ) );
		}

		$content_time = formattime( $contentinfo[ 'content_time' ], '', 'date' );
		$content_title = pkEntities( $contentinfo[ 'content_title' ] );
		$contentcat_name = pkEntities( $contentcatinfo[ 'contentcat_name' ] );

		if ( !empty($userinfo) && $userinfo[ 'user_id' ] > 0 && $userinfo[ 'user_nick' ] != '' )
		{
			$userinfo[ 'user_nick' ] = pkEntities( $userinfo[ 'user_nick' ] );
			eval( "\$autor_info= \"" . pkTpl( "content/articles_autor" ) . "\";" );
		}
		else
		{
			$autor_info = pkEntities( $contentinfo[ 'content_autor' ] );
		}

		eval( "\$content_article_head= \"" . pkTpl( "content/articles_textlink_head" ) . "\";" );

		if ( trim( $contentinfo[ 'content_teaser' ] ) != '' )
		{
			$teaser_dimension = @getimagesize( $contentcatinfo[ 'content_teaser' ] );

			eval( "\$content_teaser= \"" . pkTpl( "content/articles_teaser" ) . "\";" );
		}

		if ( $contentcatinfo[ 'contentcat_symbol' ] != 'blank.gif' && !empty( $contentcatinfo[ 'contentcat_symbol' ] ) )
		{
			$catimage_dimension = @getimagesize( "images/catimages/" . $contentcatinfo[ 'contentcat_symbol' ] );

			eval( "\$content_catimage= \"" . pkTpl( "content/cat_image_left" ) . "\";" );
		}

		if ( stripos( '<break>', $contentinfo[ 'content_text' ] ) !== false )
		{
			$page = ( isset( $_REQUEST[ 'page' ] ) && intval( $_REQUEST[ 'page' ] ) > 0 ) ? intval(
				$_REQUEST[ 'page' ] ) : 1;

			$content_article = explode( '<break>', $contentinfo[ 'content_text' ] );
			$page_count = count( $content_article );
			$p = $page - 1;

			if ( $p < 0 || $p > $page_count )
			{
				$p = 0;
			}

			$content_article = $content_article[ $p ];
			$counter = 0;

			$article_pagelink = pagelink( $page_count, 1, $page, pkLink( 'article', '', 'contentid=' .
			                                                                            $contentinfo[ 'content_id'
			                                                                            ] ) );
		}
		else
		{
			$content_article = $contentinfo[ 'content_text' ];
		}

		$content_article_body = $BBCODE->parse( $content_article, $contentinfo[ 'content_html' ],
		                                        $contentinfo[ 'content_ubb' ], $contentinfo[ 'content_smilies' ], 1 );

		/*DOC-include zu Quelle ändern*/
		if ( !empty( $contentinfo[ 'content_altdat' ] ) )
		{
			$i = explode( "\n", $contentinfo[ 'content_altdat' ] );
			foreach ( $i as $d )
			{
				if ( !empty( $d ) && filecheck( $d ) )
				{
					$content_article_body .= implode( "", file( $d ) );
				}
			}
		}

		if ( getrights( $config[ 'content_submit1' ] ) == "true" )
		{
			eval( "\$content_submit= \"" . pkTpl( "content/articles_submit_link" ) . "\";" );
		}

		if ( $contentinfo[ 'content_comment_status' ] == 1 )
		{
			list( $content_comment_count ) = $SQL->fetch_row( $SQL->query( "SELECT COUNT(*) FROM " . pkSQLTAB_COMMENT . " WHERE comment_cat='cont' AND comment_subid='" .
			                                                               $contentinfo[ 'content_id' ] . "'" ) );

			eval( "\$content_comment= \"" . pkTpl( "content/articles_comment_link" ) . "\";" );
		}

		if ( $contentinfo[ 'content_rating_status' ] == 1 )
		{
			if ( $contentinfo[ 'content_rating_total' ] > 0 )
			{
				$content_rating_d = number_format( $contentinfo[ 'content_rating' ], 2, ",", "." );
				$content_rating_votes = $contentinfo[ 'content_rating_total' ];

				eval( "\$content_rating_info= \"" . pkTpl( "content/articles_rating_info" ) . "\";" );
			}

			eval( "\$content_rate= \"" . pkTpl( "content/articles_rating_link" ) . "\";" );
		}

		//preset
		$related_articles = $related_news = $related_links = $related_downloads = $related_text = '';

		if ( is_array( $related_cache ) )
		{
			foreach ( $related_cache as $content )
			{
				$contentcatinfo = $contentcat_cache[ 0 ][ $content[ 'content_cat' ] ];

				if ( $content[ 'content_option' ] == 1 )
				{
					eval( "\$related_articles.= \"" . pkTpl( "content/articles_related_link" ) . "\";" );
				}
				elseif ( $content[ 'content_option' ] == 2 )
				{
					eval( "\$related_news.= \"" . pkTpl( "content/articles_related_link" ) . "\";" );
				}
				elseif ( $content[ 'content_option' ] == 3 )
				{
					eval( "\$related_links.= \"" . pkTpl( "content/articles_related_link" ) . "\";" );
				}
				elseif ( $content[ 'content_option' ] == 4 )
				{
					eval( "\$related_downloads.= \"" . pkTpl( "content/articles_related_link" ) . "\";" );
				}
				else
				{
					eval( "\$related_text.= \"" . pkTpl( "content/articles_related_link" ) . "\";" );
				}
			}

			if ( $related_articles != '' )
			{
				eval( "\$related_boxes.= \"" . pkTpl( "content/articles_related_box_articles" ) . "\";" );
			}

			if ( $related_news != '' )
			{
				eval( "\$related_boxes.= \"" . pkTpl( "content/articles_related_box_news" ) . "\";" );
			}

			if ( $related_links != '' )
			{
				eval( "\$related_boxes.= \"" . pkTpl( "content/articles_related_box_links" ) . "\";" );
			}

			if ( $related_downloads != '' )
			{
				eval( "\$related_boxes.= \"" . pkTpl( "content/articles_related_box_downloads" ) . "\";" );
			}

			if ( $related_text != '' )
			{
				eval( "\$related_boxes.= \"" . pkTpl( "content/articles_related_box_text" ) . "\";" );
			}
		}

		$suggest_url = urlencode( 'path=article&contentid=' . $contentinfo[ 'content_id' ] . '&page=' . $page );

		#set site title
		$CMS->site_title_set( $contentinfo[ 'content_title' ] );

		eval( "\$site_body.=\"" . pkTpl( "content/articles" ) . "\";" );
		break;
	#END default
}
?>