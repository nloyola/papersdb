<?php

  // $Id: lib_dbfunctions.php,v 1.2 2006/05/19 17:42:40 aicmltec Exp $

  /**
   * \file
   *
   * \brief DB connection is only hard coded here and in lib_functions.
   *
   * \note IMPORTANT
   *
   * At present, the files are stored at:
   *	$FS_PATH/uploaded_files/{pub_id}
   *
   * The naming scheme is:
   *	{paper/additional}_{name_of_file}.file_extension
   */

require_once "defines.php";
require_once 'Database.php';

$relative_files_path = "uploaded_files/";
$absolute_files_path = FS_PATH . $relative_files_path;

$wgSiteName = "PapersDB";


/**
 * Creates a database object to operate on the database.
 */
function &dbCreate() {
    $db = Database::newFromParams(DB_SERVER, DB_USER, DB_PASSWD, DB_NAME);
    return $db;
}

/**
 * \todo this function should not be used anymore.
 *
 * \see dbCreate
 */
function connect_db() {

	$link = mysql_connect(DB_SERVER, DB_USER, DB_PASSWD)
		or die("Could not connect : " . mysql_error());
	mysql_select_db(DB_NAME) or die("Could not select database");

	return $link;
}

/**
 * \todo this function should not be used anymore.
 *
 * \see dbCreate
 */
function disconnect_db($link) {
	mysql_close($link);
}

/**
 * \todo this function should not be used anymore.
 *
 * \see dbCreate
 */
function query_db($query) {
	//$result = mysql_query($query) or die(back_button());
	// Use this line for debugging
	$result = mysql_query($query) or die("Query failed : " . mysql_error());
	return $result;
}

function wfDebug( $text, $logonly = false ) {
    print $text;
}

$wgProfiling = 0;

function wfProfileIn($str) {}
function wfProfileOut($str) { echo $str . "<br/>\n"; }
function wfLogDBError( $text ) {}
function wfGetSiteNotice() {}
function wfErrorExit() {}
function wfSetBit( &$dest, $bit, $state = true ) {}
function wfSuppressWarnings( $end = false ) {}
function wfRestoreWarnings() {}
function wfDebugDieBacktrace( $msg = '' ) { die($msg); }
function wfSetVar( &$dest, $source ) {}

?>