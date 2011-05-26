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

class pkSql
    {

    var $database_charset = 'utf8'; #here without hyphen
    var $database = '';
    var $sqlhost = '';
    var $sqluser = '';
    var $sqlpass = '';
    var $sqldataset = false;
    var $reportsqlerror = false;
    var $query; #last queryid
    var $querys = array( ); 
    var $querycounter = 0;
    var $querystring = '';
    var $maxConventionalNameLength = 64;
    var $maxConventionalNamePrefixLength = 30;
    var $servercon = '';
    var $dbselected = false;
	var $queryid = '';


    /**
     * Constructor.
     *
     * @param   void
     * @return  void
     */
    public function __construct()
        {
        if( !defined( 'pkSQLDATABASE' ) || !defined( 'pkSQLHOST' ) || !defined( 'pkSQLUSER' ) || !defined( 'pkSQLPASS' ) )
            {
            return false;
            }

        $this->set( pkSQLDATABASE, pkSQLHOST, pkSQLUSER, pkSQLPASS );
        }


    /**
     * Catcher for undefined members.
     * 
     * @param   string $name
     * @return  string
     */
    public function __get( $name )
        {
        switch( $name )
            {
            case '_queries':
                return $this->querys;
            }

        return NULL;
        }


    /**
     * Sets the required MySQL conection data.
     *
     * @param   string $database
     * @param   string $sqlhost
     * @param   string $sqluser
     * @param   string $sqlpass
     * @return  object|self
     */
    function set( $database, $sqlhost, $sqluser, $sqlpass )
        {
        $this->database = $database;
        $this->sqlhost = $sqlhost;
        $this->sqluser = $sqluser;
        $this->sqlpass = $sqlpass;
        $this->sqldataset = true;

        return $this;
        }


    /**
     * Connects to the database server and selects further the specified database.
     *
     * @param void
     * @return bool
     */
    function connect()
        {
        if( $this->servercon )
            {
            return true;
            }

        if( $this->sqldataset && !$this->servercon )
            {
            $this->servercon = pkDEVMODE ? mysql_connect( $this->sqlhost, $this->sqluser, $this->sqlpass ) : @mysql_connect( $this->sqlhost, $this->sqluser, $this->sqlpass );

            if( $this->servercon )
                {
                $this->query( "SET sql_mode=''" );
                $this->query( "SET NAMES '" . $this->database_charset . "'" );

                return $this->select_db() ? true : false;
                }
            }

        return false;
        }


    /**
     * Selects the specified database.
     *
     * @param   void
     * @return  bool
     */
    function select_db()
        {
        $database = $this->database;

        $this->dbselected = pkDEVMODE ? mysql_select_db( $database, $this->servercon ) : @mysql_select_db( $database, $this->servercon );

        return $this->dbselected;
        }


    /**
     * Returns a boolean if the server conection is established.
     *
     * @param   void
     * @return  bool
     */
    function connected()
        {
        return $this->servercon ? true : false;
        }


    /**
     * Returns a boolean if the database conection is established.
     *
     * @param   void
     * @return  bool
     */
    function dbselected()
        {
        return $this->dbselected ? true : false;
        }


    /**
     * Executes the given query.
     *
     * @param   string $querystring
     * @return  false|ressource
     */
    function query( $querystring='' )
        {
        $this->queryid = 0;
        $this->querystring = $querystring;


        list($a, $b) = explode( ' ', microtime() );

        if( !empty( $querystring ) )
            {
            $this->queryid = mysql_query( $querystring, $this->servercon );
            }

        list($c, $d) = explode( ' ', microtime() );
        $this->querys[] = array(
            'time' => ($c + $d) - ($a + $b),
            'string' => $querystring
        );

        if( !$this->queryid && $this->reportsqlerror )
            {
            $this->error();
            }

        return $this->queryid;
        }


    /**
     * Fetch a result row as an associative array and a numeric array.
     *
     * @param   ressource $resultsource
     * @return  false|array
     */
    function fetch_array( $resultsource='' )
        {
        if( $resultsource != '' )
            {
            if( $result = mysql_fetch_array( $resultsource ) )
                {
                return $result;
                }
            }

        return false;
        }


    /**
     * Fetch a result row as an associative array.
     *
     * @param   ressource $resultsource
     * @return  false|array
     */
    function fetch_assoc( $resultsource='' )
        {
        if( $resultsource != '' )
            {
            if( $result = mysql_fetch_assoc( $resultsource ) )
                {
                return $result;
                }
            }

        return false;
        }


    /**
     * Fetch a result row as a numeric array.
     *
     * @param   ressource $resultsource
     * @return  false|array
     */
    function fetch_row( $resultsource='' )
        {
        if( $resultsource != '' )
            {
            if( $result = mysql_fetch_row( $resultsource ) )
                {
                return $result;
                }
            }
        return false;
        }


    /**
     * Returns the numbers of rows selected by the given query result source.
     *
     * @param   ressource $resultsource
     * @return  false|integer
     */
    function num_rows( $resultsource='' )
        {
        if( $resultsource != '' )
            {
            if( $result = mysql_num_rows( $resultsource ) )
                {
                return $result;
                }
            }
        return false;
        }


    /**
     * Returns the number of affected rows by the last query.
     *
     * @param   void
     * @return  false|integer
     */
    function affected_rows()
        {
        return mysql_affected_rows();
        }


    /**
     * Returns the last inserted ID.
     *
     * Only useable after an INSERT.
     *
     * @param   void
     * @return  false|integer
     */
    function insert_id()
        {
        return mysql_insert_id();
        }


    /**
     * Returns the total number of queries performed.
     *
     * @param   void
     * @return  integer
     */
    function getquerycount()
        {
        return count( $this->querys );
        }


    /**
     * Fetch a result row as an associative array and a numeric array.
     *
     * @param   ressource $queryresults
     * @return  bool
     */
    function free_result( $queryresults = '' )
        {
        return @mysql_free_result( $queryresults );
        }


    /**
     * Returns the MySQL version number.
     *
     * @param   void
     * @return  false|string
     */
    function sqlversion()
        {
        $result = $this->query( "SELECT VERSION() AS mysql_version" );

        if( $result )
            {
            $row = $this->fetch_array( $result );

            return $row['mysql_version'];
            }

        return false;
        }


    /**
     * Calculates the total size of the database.
     *
     * @param   void
     * @return  false|string
     */
    function database_size()
        {
        $dbsize = array( 0, 0 );

        #fetch our own tables
        $tables = pkCfgData( 'sqltables' );

        #replace suffix with the real name
        foreach( $tables as $alias => $suffix )
            {
            $tables[$alias] = constant( $alias );
            }


        $result = $this->query( "SHOW TABLE STATUS FROM `" . $this->database . "`" );

        if( $result )
            {
            while( $data = $this->fetch_assoc( $result ) )
                {
                $dbsize[1]+= $data['Data_length'] + $data['Index_length'];


                if( in_array( $data['Name'], $tables ) )
                    {
                    $dbsize[0]+= $data['Data_length'] + $data['Index_length'];
                    }
                }

            return $dbsize;
            }

        return false;
        }


    /**
     * List all tables in the selected database.
     *
     * @param   void
     * @return  array
     */
    function list_tables()
        {
        $tablelist = array( );
        $ressource = $this->query( "SHOW TABLES FROM " . $this->f( $this->database ) );

        while( list($table) = $this->fetch_row( $ressource ) )
            {
            $tablelist[] = $table;
            }

        return $tablelist;
        }


    /**
     * Returns a table name
     *
     * @param   ressource $listresult
     * @param   integer $number
     * @return  string
     */
    function tablename( $listresult='', $number='' )
        {
        return mysql_tablename( $listresult, $number );
        }


    /**
     * Returns detailed informations about the given database table.
     *
     * @param   string $table
     * @param   string $option
     * @return  mixed
     */
    function table_status( $table='', $option='Type' )
        {
        if( !empty( $table ) && $table_status = $this->fetch_assoc( $this->query( "SHOW TABLE STATUS LIKE '" . $table . "'" ) ) )
            {
            if( empty( $option ) )
                {
                return $table_status;
                }

            if( isset( $table_status[$option] ) )
                {
                return $table_status[$option];
                }
            }

        return false;
        }


    /**
     * Checks if given table exists.
     *
     * @param   string $tablename
     * @return  bool
     */
    function table_exists( $tablename='' )
        {
        if( $tablename != '' )
            {
            $listresult = $this->list_tables();
            $counttables = count( $listresult );

            if( in_array( $tablename, $listresult ) )
                {
                return true;
                }
           }

        return false;
        }


    /**
     * Sets the error reporting for MySQL errors.
     *
     * @param   bool $set
     * @return  object|self
     */
    function sqlerrorreport( $set = true )
        {
        $this->reportsqlerror = $set ? true : false;

        return $this;
        }


    /**
     * Checks if the given string is useable as table or row name.
     *
     * @param   string $string
     * @param   bool $is_prefix
     * @return  bool
     */
    function isConventionalName( $string, $is_prefix = false )
        {
        if( $is_prefix && strlen( $string ) > $this->maxConventionalNamePrefixLength ) return false;

        return preg_match( '/^[a-z]([a-z0-9_\-]*)$/i', $string ) ? true : false;
        }


    /**
     * Prints an MySQL error.
     *
     * @param   bool $print
     * @return  string
     */
    function error( $print = true )
        {
        $error = mysql_error( $this->servercon );
        $errno = mysql_errno( $this->servercon );

        $string = '<table border="0" cellpadding="2" width="100%"><tr><td style="font: 12px verdana;" colspan="2" nowrap="nowrap"><b>MySQL-Database error</b></td></tr>' .
            '<tr><td style="font:12px verdana;white-space:nowrap;"><b>Time and Date:</b></td><td style="font:12px verdana;">' . pkTimeFormat() . '</td></tr>' .
            '<tr><td style="font:12px verdana;white-space:nowrap;"><b>MySQL error:</b></td><td style="font:12px verdana;">' . pkEntities( $error ) . '</td></tr>' .
            '<tr><td style="font:12px verdana;white-space:nowrap;"><b>MySQL error number:</b></td><td style="font:12px verdana;">' . pkEntities( $errno ) . '</td></tr>' .
            '<tr><td style="font:12px verdana;white-space:nowrap;"><b>MySQL query:</b></td><td width="100%"style="font:12px verdana;">' . str_replace( "\t", '', pkEntities( $this->querystring ) ) . '</td></tr></table>';

        if( $print )
            {
            echo $string;
            }

        return $string;
        }


    /**
     * Returns a complete list of performed queries.
     *
     * @param   void
     * @return  string
     */
    function getQueryList()
        {
        $list = '<table border="0" cellpadding="2" width="100%"><tr><td style="font: 12px verdana;" colspan="3" nowrap="nowrap"><b>MySQL-Querylist</b></td></tr>' .
            '<tr><td style="font:12px verdana;"><b>No.:</b></td><td style="font:12px verdana;" nowrap="nowrap"><b>Time:</b></td><td style="font:12px verdana;" nowrap="nowrap"><b>Query:</b></td></tr>';

        $i = 1;
        foreach( $this->querys as $key => $query )
            {
            $list.='<tr><td style="font:12px verdana;"><b>#' . ($i++) . '</b></td><td style="font:12px verdana;">' . $query['time'] . '</td><td style="font:12px verdana;">' . pkEntities( $query['string'] ) . '</td></tr>';
            }

        $list.='</table>';

        return $list;
        }


    /**
     * Converts the given value into an integer.
     *
     * @param   mixed $integer
     * @return  integer
     */
    function i( $integer )
        {
        return intval( $integer );
        }


    /**
     * Converts the given value into an integer great then 0.
     *
     * @param   mixed $id
     * @return  integer
     */
    function id( $id )
        {
        return intval( $id ) > 0 ? intval( $id ) : 0;
        }


    /**
     * Converts the given value into a boolean value.
     *
     * @param   mixed $bool
     * @return  integer
     */
    function b( $bool )
        {
        return $bool ? true : false;
        }


    /**
     * Escaps the given string to be used in queries.
     *
     * @param   string $string
     * @return  string
     */
    function f( $string )
        {
        return mysql_real_escape_string( $string, $this->servercon );
        }
    }
#END Class pksql
?>