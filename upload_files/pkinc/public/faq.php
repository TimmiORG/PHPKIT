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
$faq_list = $faq_question = $faq_answer = $faq_question = $faq_question = $faq_question = $faq_head = $faq_body = $faq_catselect = '';

pkLoadLang( 'faq' );

$catid = ( isset( $_REQUEST[ 'catid' ] ) && intval( $_REQUEST[ 'catid' ] ) > 0 ) ? intval( $_REQUEST[ 'catid' ] ) : 0;
$faqcat_cache = array(
);
$selected_all = '';

$getfaqcat = $SQL->query( "SELECT faqcat_id, faqcat_title FROM " . pkSQLTAB_FAQ_CATEGORY . " ORDER by faqcat_title ASC" );
while ( $faqcat = $SQL->fetch_array( $getfaqcat ) )
{
	$faqcat_cache[ $faqcat[ 'faqcat_id' ] ] = $faqcat;
}

if ( is_array( $faqcat_cache ) )
{
	foreach ( $faqcat_cache as $faqcat )
	{
		$faqcat_title = pkEntities( $faqcat[ 'faqcat_title' ] );

		if ( count( $faqcat_cache ) == 1 )
		{
			$catid = $faqcat[ 'faqcat_id' ];
		}

		if ( $catid == $faqcat[ 'faqcat_id' ] )
		{
			$selected = ' selected="selected"';

			if ( count( $faqcat_cache ) > 1 )
			{
				eval( "\$faq_head=\"" . pkTpl( "faq/faq_full_head" ) . "\";" );
			}
		}
		else
		{
			$selected = '';
		}

		if ( count( $faqcat_cache ) > 1 )
		{
			eval( "\$faqcat_option.=\"" . pkTpl( "faq/faq_cat_option" ) . "\";" );
		}
	}
}

if ( $catid != 0 )
{
	pkLoadClass( $BBCODE, 'bbcode' );

	$getfaqinfo = $SQL->query( "SELECT
		*
		FROM " . pkSQLTAB_FAQ . "
		WHERE faq_answer!='-' AND 
			faq_catid='" . $catid . "'
		ORDER BY faq_order ASC, faq_title ASC" );
	while ( $faqinfo = $SQL->fetch_array( $getfaqinfo ) )
	{
		$faqinfo[ 'faq_title' ] = pkEntities( $faqinfo[ 'faq_title' ] );

		$faq_question_text = $BBCODE->parse( $faqinfo[ 'faq_question' ], 1, 1, 1, 1 );

		if ( !empty( $faq_question_text ) )
		{
			eval( "\$faq_question=\"" . pkTpl( "faq/faq_question" ) . "\";" );
		}

		$faq_answer_text = $BBCODE->parse( $faqinfo[ 'faq_answer' ], 1, 1, 1, 1 );

		if ( $faq_answer_text != '' )
		{
			eval( "\$faq_list.= \"" . pkTpl( "faq/faq_list" ) . "\";" );
			eval( "\$faq_answer.= \"" . pkTpl( "faq/faq_answer" ) . "\";" );
		}

		$faqinfo[ 'faq_title' ] = pkEntities( $faqinfo[ 'faq_title' ] );

		$faq_question = '';
	}

	if ( empty( $faq_list ) )
	{
		eval( "\$faq_list=\"" . pkTpl( "faq/faq_cat_infopage_empty" ) . "\";" );
	}

	eval( "\$faq_body.=\"" . pkTpl( "faq/faq_body" ) . "\";" );
}
else
{
	if ( is_array( $faqcat_cache ) )
	{
		foreach ( $faqcat_cache as $faqcat )
		{
			unset( $faq_list );
			$catid = $faqcat[ 'faqcat_id' ];
			$faqcat_title = pkEntities( $faqcat[ 'faqcat_title' ] );

			$getfaqinfo = $SQL->query( "SELECT * FROM " . pkSQLTAB_FAQ . " WHERE faq_answer!='-' AND faq_catid='" . $catid . "' ORDER by faq_title ASC" );
			while ( $faqinfo = $SQL->fetch_array( $getfaqinfo ) )
			{
				$faqinfo[ 'faq_title' ] = pkEntities( $faqinfo[ 'faq_title' ] );

				if ( $faqinfo[ 'faq_answer' ] != '' )
				{
					eval( "\$faq_list.= \"" . pkTpl( "faq/faq_full_list" ) . "\";" );
				}
			}

			eval( "\$faq_head= \"" . pkTpl( "faq/faq_full_head" ) . "\";" );
			eval( "\$faq_body.= \"" . pkTpl( "faq/faq_full_body" ) . "\";" );
		}
	}
}

if ( count( $faqcat_cache ) > 1 )
{
	eval( "\$faq_catselect=\"" . pkTpl( "faq/faq_catselect" ) . "\";" );
}

#set site title
$CMS->site_title_set( pkGetLang( 'faq_page_title' ), true );

eval( "\$site_body.= \"" . pkTpl( "faq/faq" ) . "\";" );
?>