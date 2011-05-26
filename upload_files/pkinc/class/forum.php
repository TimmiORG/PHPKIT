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

class pkForum
{

	var $hash = -1;
	var $catidhash = array(
	);


	var $tree = array(
	);
	var $_key = 'tree'; //array-key for additional params
	var $_level = 'level'; //array-key for leveldepth
	var $_threadcount = 'threadcount'; //array-key - summary threadcount
	var $_postcount = 'postcount'; //array-key - summary threadcount
	var $_last_time = 'time'; //array-key
	var $_last_threadid = 'threadid'; //array-key
	var $_last_authorid = 'authorid'; //array-key
	var $_last_author = 'author'; //array-key
	var $_childs = 'childs';

	var $countCategories = 0;
	var $countThreads = 0;
	var $countPostings = 0;

	var $newthreads = array(
	);
	var $readedthreads = array(
	);

	function __construct( )
	{
		if( is_array( $this->hash ) )
		{
			return;
		}

		global $SQL, $pkFORUMNEWTHREADS, $pkFORUMREADEDTHREADS;

		$this->hash = array(
		);

		$this->usertime = pkGetUservalue( 'lastlog' );
		$this->newthreads =& $pkFORUMNEWTHREADS;
		$this->readedthreads =& $pkFORUMREADEDTHREADS;

		$result = $SQL->query( "SELECT
			forumcat_id,
			forumcat_subcat,
			forumcat_name,
			forumcat_description,
			forumcat_description_show,
			forumcat_status,
			forumcat_replys,
			forumcat_views,
			forumcat_threadcount,
			forumcat_postcount,
			forumcat_lastreply_threadid,
			forumcat_lastreply_time,
			forumcat_lastreply_autor,
			forumcat_lastreply_autorid,
			forumcat_posts,
			forumcat_threads,
			forumcat_threads_option,
			forumcat_option,
			forumcat_rrights,
			forumcat_wrights,
			forumcat_trights,
			forumcat_mods,
			forumcat_user		
			FROM " . pkSQLTAB_FORUM_CATEGORY . "
			ORDER BY forumcat_order ASC" );
		while( $cat = $SQL->fetch_assoc( $result ) )
		{
			$this->hash[ $cat[ 'forumcat_id' ] ] = $cat;
		}

		$this->__tree( );

		foreach( $this->tree as $catid => $nomatter )
		{
			if( $this->getCategoryRrights( $catid ) && !in_array( $catid, $this->catidhash ) )
			{
				$this->catidhash[ ] = $catid;
			}
		}

		$this->hash = array(
		);
	}


	function pkForum( )
	{
		$this->__construct( );
	}

	function __tree( $_parent = 0, $level = 0 )
	{
		foreach( $this->hash as $id => $cat )
		{
			if( $_parent != ( $parent = $cat[ 'forumcat_subcat' ] ) )
			{
				continue;
			}

			$this->countCategories++;
			$this->countThreads += $cat[ 'forumcat_threadcount' ];
			$this->countPostings += $cat[ 'forumcat_postcount' ];

			$this->tree[ $id ] = $cat;

			if( !$this->getCategoryRrights( $id ) )
			{
				unset( $this->tree[ $id ] );
				continue;
			}

			$this->catidhash[ $id ] = $id;

			$this->tree[ $id ][ $this->_level ] = $level;
			$this->tree[ $id ][ $this->_key ][ $this->_level ] = $level;
			$this->tree[ $id ][ $this->_key ][ $this->_threadcount ] = $cat[ 'forumcat_threadcount' ];
			$this->tree[ $id ][ $this->_key ][ $this->_postcount ] = $cat[ 'forumcat_postcount' ];

			$this->tree[ $id ][ $this->_key ][ $this->_last_time ] = $cat[ 'forumcat_lastreply_time' ];
			$this->tree[ $id ][ $this->_key ][ $this->_last_threadid ] = $cat[ 'forumcat_lastreply_threadid' ];
			$this->tree[ $id ][ $this->_key ][ $this->_last_authorid ] = $cat[ 'forumcat_lastreply_autorid' ];
			$this->tree[ $id ][ $this->_key ][ $this->_last_author ] = $cat[ 'forumcat_lastreply_autor' ];

			#update parent
			$this->__updateParent( $id, $parent );
			$this->__tree( $id, $level + 1 );
		}
	}

	function __updateParent( $id, $parent, $counter = 1 )
	{
		if( $parent == 0 || $id == 0 || !$this->getCategoryRrights( $parent ) )
		{
			return;
		}

		$this->tree[ $parent ][ $this->_key ][ $this->_childs ][ $id ] = $id;

		if( $counter )
		{
			$this->tree[ $parent ][ $this->_key ][ $this->_threadcount ] += $this->tree[ $id ][ $this->_key ][ $this->_threadcount ];
			$this->tree[ $parent ][ $this->_key ][ $this->_postcount ] += $this->tree[ $id ][ $this->_key ][ $this->_postcount ];
		}

		if( $this->tree[ $parent ][ $this->_key ][ $this->_last_time ] < $this->tree[ $id ][ $this->_key ][ $this->_last_time ] )
		{
			$this->tree[ $parent ][ $this->_key ][ $this->_last_time ] = $this->tree[ $id ][ $this->_key ][ $this->_last_time ];
			$this->tree[ $parent ][ $this->_key ][ $this->_last_threadid ] = $this->tree[ $id ][ $this->_key ][ $this->_last_threadid ];
			$this->tree[ $parent ][ $this->_key ][ $this->_last_authorid ] = $this->tree[ $id ][ $this->_key ][ $this->_last_authorid ];
			$this->tree[ $parent ][ $this->_key ][ $this->_last_author ] = $this->tree[ $id ][ $this->_key ][ $this->_last_author ];
		}

		$this->__updateParent( $parent, $this->tree[ $parent ][ 'forumcat_subcat' ], 0 );
	}


	function getCategoryRrights( $catid )
	{
		if( !array_key_exists( $catid, $this->tree ) )
		{
			return false;
		}

		$cat = $this->tree[ $catid ];

		if( getrights( $cat[ 'forumcat_rrights' ] ) || userrights(
			$cat[ 'forumcat_mods' ], $cat[ 'forumcat_rrights' ] ) || userrights(
			$cat[ 'forumcat_user' ], $cat[ 'forumcat_rrights' ] ) )
		{
			return true;
		}

		return false;
	}

	function getChilds( $catid )
	{
		$childs = array();
		if(isset($this->tree[ $catid ][ $this->_key ][ $this->_childs ]))
		{
			$childs = $this->tree[ $catid ][ $this->_key ][ $this->_childs ];
		}
		return is_array( $childs ) ? $childs : array();
	}

	function getCategories( )
	{
		return $this->catidhash;
	}

	function getCategory( $id )
	{
		return isset( $this->tree[ $id ] ) ? $this->tree[ $id ] : array(
		);
	}

	function getTree( )
	{
		return $this->tree;
	}

	function getCountCategories( )
	{
		return $this->countCategories;
	}

	function getCountThreads( )
	{
		return $this->countThreads;
	}

	function getCountPostings( )
	{
		return $this->countPostings;
	}

	function getCategoryThreadcount( $catid )
	{
		return $this->tree[ $catid ][ $this->_key ][ $this->_threadcount ];
	}

	function getCategoryPostcount( $catid )
	{
		return $this->tree[ $catid ][ $this->_key ][ $this->_postcount ];
	}

	function getCategoryNameF( $catid )
	{
		return array_key_exists( $catid, $this->tree ) ? pkEntities( $this->tree[ $catid ][ 'forumcat_name' ] ) : '';
	}

	function getUnreadedThreadids( )
	{
		$hash = array(
		);

		if( !is_array( $this->newthreads ) )
		{
			return $hash;
		}

		foreach( $this->newthreads as $catid => $threads )
		{
			if( !is_array( $threads ) )
			{
				continue;
			}

			foreach( $threads as $id => $time )
			{
				if( array_key_exists( $id, $this->readedthreads ) )
				{
					if( $threads[ $id ] <= $this->readedthreads[ $id ] )
					{
						continue;
					}
				}

				$hash[ ] = $id;
			}
		}

		return $hash;
	}

	function getUnreadedThreadtime( $catid, $threadid )
	{
		if( !is_array( $this->newthreads ) || !array_key_exists( $catid, $this->newthreads ) || !array_key_exists( $threadid, $this->newthreads[ $catid ] ) )
		{
			return 0;
		}

		$time = $this->newthreads[ $catid ][ $threadid ];

		$readedtime = ( is_array( $this->readedthreads ) && array_key_exists( $threadid, $this->readedthreads ) ) ? $this->readedthreads[ $threadid ] + 1 : $this->usertime; #+1 fix the stupid search query :P
		$time = $readedtime < $time ? $readedtime : $time;

		return $time;
	}

	function isUnreadedCategory( $catid )
	{
		if( !is_array( $this->newthreads ) )
		{
			return false;
		}

		if( array_key_exists( $catid, $this->newthreads ) )
		{
			foreach( $this->newthreads[ $catid ] as $threadid => $time )
			{
				if( $this->isUnreadedThread( $catid, $threadid, $time ) )
				{
					return true;
				}
			}
		}

		foreach( $this->getChilds( $catid ) as $id )
		{
			if( !array_key_exists( $id, $this->newthreads ) )
			{
				continue;
			}

			foreach( $this->newthreads[ $id ] as $threadid => $time )
			{
				if( $this->isUnreadedThread( $id, $threadid, $time ) )
				{
					return true;
				}
			}
		}

		return false;
	}

	function isUnreadedThread( $catid, $threadid, $replytime )
	{
		if( !is_array( $this->newthreads ) )
		{
			return false;
		}

		if( !array_key_exists( $catid, $this->newthreads ) )
		{
			return false;
		}

		$category = $this->newthreads[ $catid ];
		if( !array_key_exists( $threadid, $category ) )
		{
			return false;
		}

		if( array_key_exists( $threadid, $this->readedthreads ) )
		{
			if( $category[ $threadid ] <= $this->readedthreads[ $threadid ] )
			{
				return false;
			}
		}

		return true;
	}

	function setReaded( $catid, $threadid, $time )
	{
		global $SQL, $SESSION;

		if( $this->isUnreadedThread( $catid, $threadid, $time ) )
		{
			$SQL->query( "REPLACE " . pkSQLTAB_FORUM_THREAD_READED . " SET
				sid='" . $SQL->f( $SESSION->getid( ) ) . "',
				threadid='" . $SQL->i( $threadid ) . "',
				rtime='" . $SQL->i( $time ) . "'" );
		}
	}

	function unreadedPostings( $getthreadid )
	{
		if( !is_array( $this->newthreads ) )
		{
			return false;
		}

		foreach( $this->newthreads as $catid => $cat )
		{
			foreach( $cat as $threadid => $time )
			{
				if( $threadid == $getthreadid )
				{
					continue;
				}

				if( !array_key_exists( $threadid, $this->readedthreads ) || $this->readedthreads[ $threadid ] < $time )
				{
					return true;
				}
			}
		}

		return false;
	} #end newposts

	function getPosttime( )
	{
		return pkGetUservalue( 'lastlog' );
	}

	function getLayout( )
	{
		global $SESSION;

		return $SESSION->exists( 'forum_structur' ) ? $SESSION->get( 'forum_structur' ) : pkGetConfig( 'forum_structur' );
	}

	function setLayout( $set )
	{
		global $SESSION;

		$SESSION->set( 'forum_structur', $set );
	}
}

?>