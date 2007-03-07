<?php ;

// $Id: dbcreate_mysql.php,v 1.2 2007/02/20 04:24:38 loyola Exp $

/**
 * Creates the PapersDB database.
 *
 * @package PapersDB
 * @subpackage HTML_Generator
 */

ini_set("include_path", ini_get("include_path") . ":..");

/** Requries the base class and classes to access author information. */
require_once 'includes/pdHtmlPage.php';

/**
 *
 *
 * @package PapersDB
 */
class dbCreate extends pdHtmlPage {
    var $debug = 0;
    var $author_id = null;
    var $numNewInterests = 0;

    function dbCreate() {
        global $access_level;

        pubSessionInit();
        parent::pdHtmlPage('dbcreate');

        $db =& dbCreate();

        $this->tblAdditionalInfo($db);
        $this->tblAttachmentTypes($db);
        $this->tblAuthor($db);
        $this->tblAuthorInterest($db);
        $this->tblCatInfo($db);
        $this->tblCategory($db);
        $this->tblExtraInfo($db);
        $this->tblInfo($db);
        $this->tblinterest($db);
        $this->tblPointer($db);
        $this->tblPubAdd($db);
        $this->tblPubAuthor($db);
        $this->tblPubCat($db);
        $this->tblPubCatInfo($db);
        $this->tblPublication($db);
        $this->tblUser($db);
        $this->tblUserAuthor($db);
        $this->tblVenue($db);
        $this->tblVenueOccur($db);

        $db->close();
    }

    function createDatabase($db) {
        assert('is_object($db)');

        $q  = $db->query('CREATE DATABASE ' . DB_NAME);
        assert('$q');
    }

    function tblAdditionalInfo($db) {
        assert('is_object($db)');

        $q = $db->query('DROP TABLE IF EXISTS `additional_info`');
        $q = $db->query(
            'CREATE TABLE `additional_info` ('
            . '`add_id` int(10) unsigned NOT NULL auto_increment, '
            . '`type` varchar(100) default "",'
            . '`location` varchar(255) default NULL, '
            . 'PRIMARY KEY  (`add_id`))');
        assert('$q');
    }

    function tblAttachmentTypes($db) {
        assert('is_object($db)');
        $q = $db->query('DROP TABLE IF EXISTS `attachment_types`');
        $q = $db->query(
            'CREATE TABLE `attachment_types` ('
            . '`type` varchar(20) NOT NULL default "")');
        assert('$q');

        foreach (array('PDF', 'PS', 'DOC', 'TXT', 'Auxiliary Material') as $type)
            $arr[] = array('type' => $type);

        $q = $db->insert('attachment_types', $arr, 'dbcreate::tblAttachmentTypes');
        assert('$q');
    }

    function tblAuthor($db) {
        assert('is_object($db)');

        $q = $db->query('DROP TABLE IF EXISTS `author`');
        $q = $db->query(
            'CREATE TABLE `author` ('
            . '`title` varchar(255) default "", '
            . '`webpage` varchar(255) default "", '
            . '`author_id` int(10) unsigned NOT NULL auto_increment, '
            . '`name` varchar(255) NOT NULL default "", '
            . '`email` varchar(255) default NULL, '
            . '`organization` varchar(255) default NULL, '
            . 'PRIMARY KEY  (`author_id`))');
        assert('$q');
    }

    function tblAuthorInterest($db) {
        assert('is_object($db)');

        $q = $db->query('DROP TABLE IF EXISTS `author_interest`');
        $q = $db->query(
            'CREATE TABLE `author_interest` ('
            . '`author_id` int(10) unsigned NOT NULL default "0", '
            . '`interest_id` int(10) unsigned NOT NULL default "0", '
            . 'PRIMARY KEY  (`author_id`,`interest_id`))');
        assert('$q');
    }

    function tblCatInfo($db) {
        assert('is_object($db)');

        $q = $db->query('DROP TABLE IF EXISTS `cat_info`');
        $q = $db->query(
            'CREATE TABLE `cat_info` ('
            . '`cat_id` int(10) unsigned NOT NULL default "0", '
            . '`info_id` int(10) unsigned NOT NULL default "0", '
            . 'PRIMARY KEY  (`cat_id`,`info_id`) '
            . ')');
        assert('$q');
    }

    function tblCategory($db) {
        assert('is_object($db)');

        $q = $db->query('DROP TABLE IF EXISTS `category`');
        $q = $db->query(
            'CREATE TABLE `category` ('
            . '`cat_id` int(10) unsigned NOT NULL auto_increment, '
            . '`category` varchar(255) NOT NULL default "", '
            . 'PRIMARY KEY  (`cat_id`) '
            . ')');
        assert('$q');

        foreach(array('In Conference', 'In Magazine', 'In Journal', 'In Workshop')
                as $cat)
            $arr[] = array('category' => $cat);

        $q = $db->insert('category', $arr, 'dbcreate::tblCategory');
        assert('$q');
    }

    function tblExtraInfo($db) {
        assert('is_object($db)');

        $q = $db->query('DROP TABLE IF EXISTS `extra_info`');
        $q = $db->query(
            'CREATE TABLE `extra_info` ('
            . '`name` varchar(50) NOT NULL default ""'
            . ')');
        assert('$q');

        foreach (array('Awarded Best Student Paper prize',
                       'Awarded Distinguished Paper prize',
                       'MSc thesis',
                       'Oral Presentation',
                       'Platform Presentation',
                       'Refereed Poster',
                       'Second Place Poster',
                       'lightly refereed',
                       'unrefereed',
                       'with Business',
                       'with Colleague',
                       'with External',
                       'with Medical',
                       'with PostDoc',
                       'with Student') as $info)
            $arr[] = array('name' => $info);

        $q = $db->insert('extra_info', $arr, 'dbcreate::tblExtraInfo');
        assert('$q');
    }


    function tblInfo($db) {
        assert('is_object($db)');

        $q = $db->query('DROP TABLE IF EXISTS `info`');
        $q = $db->query(
            'CREATE TABLE `info` ('
            . '`info_id` int(10) unsigned NOT NULL auto_increment, '
            . '`name` varchar(255) NOT NULL default "", '
            . 'PRIMARY KEY  (`info_id`) '
            . ')');
        assert('$q');

        foreach (array('Conference', 'Journal', 'Book Title', 'Publisher',
                       'Institution', 'Editor', 'Edition', 'School', 'Type',
                       'Volume', 'Number', 'Pages', 'URL') as $info)
            $arr[] = array('name' => $info);

        $q = $db->insert('info', $arr, "dbcreate::tblExtraInfo");
        assert('$q');
    }

    function tblinterest($db) {
        assert('is_object($db)');

        $q = $db->query('DROP TABLE IF EXISTS `interest`');
        $q = $db->query(
            'CREATE TABLE `interest` ('
            . '`interest_id` int(10) unsigned NOT NULL auto_increment, '
            . '`interest` varchar(255) NOT NULL default "", '
            . 'PRIMARY KEY  (`interest_id`) '
            . ')');
        assert('$q');
    }

    function tblPointer($db) {
        assert('is_object($db)');

        $q = $db->query('DROP TABLE IF EXISTS `pointer`');
        $q = $db->query(
            'CREATE TABLE `pointer` ('
            . '`pub_id` int(11) NOT NULL default "0", '
            . '`type` varchar(100) NOT NULL default "", '
            . '`name` varchar(100) NOT NULL default "", '
            . '`value` varchar(100) NOT NULL default "" '
            . ')');
        assert('$q');
    }

    function tblPubAdd($db) {
        assert('is_object($db)');

        $q = $db->query('DROP TABLE IF EXISTS `pub_add`');
        $q = $db->query(
            'CREATE TABLE `pub_add` ('
            . '`pub_id` int(10) unsigned NOT NULL default "0", '
            . '`add_id` int(10) unsigned NOT NULL default "0", '
            . 'PRIMARY KEY  (`pub_id`,`add_id`) '
            . ')');
        assert('$q');
    }

    function tblPubAuthor($db) {
        assert('is_object($db)');

        $q = $db->query('DROP TABLE IF EXISTS `pub_author`');
        $q = $db->query(
            'CREATE TABLE `pub_author` ('
            . '`rank` int(11) default "0", '
            . '`pub_id` int(10) unsigned NOT NULL default "0", '
            . '`author_id` int(10) unsigned NOT NULL default "0", '
            . 'PRIMARY KEY  (`pub_id`,`author_id`) '
            . ')');
        assert('$q');
    }

    function tblPubCat($db) {
        assert('is_object($db)');

        $q = $db->query('DROP TABLE IF EXISTS `pub_cat`');
        $q = $db->query(
            'CREATE TABLE `pub_cat` ('
            . '`pub_id` int(10) unsigned NOT NULL default "0", '
            . '`cat_id` int(10) unsigned NOT NULL default "0", '
            . 'PRIMARY KEY  (`pub_id`,`cat_id`) '
            . ')');
        assert('$q');
    }

    function tblPubCatInfo($db) {
        assert('is_object($db)');

        $q = $db->query('DROP TABLE IF EXISTS `pub_cat_info`');
        $q = $db->query(
            'CREATE TABLE `pub_cat_info` ('
            . '`pub_id` int(10) unsigned NOT NULL default "0", '
            . '`cat_id` int(10) unsigned NOT NULL default "0", '
            . '`info_id` int(10) unsigned NOT NULL default "0", '
            . '`value` varchar(255) default NULL, '
            . 'PRIMARY KEY  (`pub_id`,`cat_id`,`info_id`) '
            . ')');
        assert('$q');
    }

    function tblPublication($db) {
        assert('is_object($db)');

        $q = $db->query('DROP TABLE IF EXISTS `publication`');
        $q = $db->query(
            'CREATE TABLE `publication` ('
            . '`pub_id` int(10) unsigned NOT NULL auto_increment, '
            . '`title` varchar(255) NOT NULL default "", '
            . '`paper` varchar(255) NOT NULL default "", '
            . '`abstract` blob, '
            . '`keywords` varchar(255) default NULL, '
            . '`published` date default NULL, '
            . '`venue_id` int(11) default "0", '
            . '`venue` blob, '
            . '`extra_info` blob, '
            . '`submit` varchar(100) default "", '
            . '`user` blob, '
            . '`updated` date default NULL, '
            . 'PRIMARY KEY  (`pub_id`)'
            . ')');
        assert('$q');
    }

    function tblUser($db) {
        assert('is_object($db)');

        $q = $db->query('DROP TABLE IF EXISTS `user`');
        $q = $db->query(
            'CREATE TABLE `user` ('
            . '`search` varchar(100) default "", '
            . '`comments` varchar(100) default "", '
            . '`verified` tinyint(1) NOT NULL default "0", '
            . '`access_level` int(11) NOT NULL default "0", '
            . '`email` varchar(100) NOT NULL default "", '
            . '`name` varchar(100) NOT NULL default "", '
            . '`password` varchar(40) NOT NULL default "", '
            . '`login` varchar(100) NOT NULL default "", '
            . 'PRIMARY KEY  (`login`) '
            . ')');
        assert('$q');
    }

    function tblUserAuthor($db) {
        assert('is_object($db)');

        $q = $db->query('DROP TABLE IF EXISTS `user_author`');
        $q = $db->query(
            'CREATE TABLE `user_author` ('
            . '`login` varchar(100) NOT NULL default "", '
            . '`author_id` int(11) NOT NULL default "0", '
            . 'PRIMARY KEY  (`login`,`author_id`) '
            . ')');
        assert('$q');
    }

    function tblVenue($db) {
        assert('is_object($db)');

        $q = $db->query('DROP TABLE IF EXISTS `venue`');
        $q = $db->query(
            'CREATE TABLE `venue` ('
            . '`venue_id` int(11) NOT NULL auto_increment, '
            . '`title` varchar(100) default "", '
            . '`name` varchar(100) default "", '
            . '`url` varchar(100) default "", '
            . '`type` varchar(100) default "", '
            . '`data` varchar(100) default "", '
            . '`editor` varchar(100) default "", '
            . '`date` date default "0000-00-00", '
            . 'PRIMARY KEY  (`venue_id`) '
            . ')');
        assert('$q');
    }

    function tblVenueOccur($db) {
        assert('is_object($db)');

        $q = $db->query('DROP TABLE IF EXISTS `venue_occur`');
        $q = $db->query(
            'CREATE TABLE `venue_occur` ('
            . '`venue_occur_id` int(11) NOT NULL auto_increment, '
            . '`venue_id` int(11) NOT NULL default "0", '
            . '`location` varchar(100) default "", '
            . '`date` date NOT NULL default "0000-00-00", '
            . '`url` varchar(100) NOT NULL default "", '
            . 'PRIMARY KEY  (`venue_occur_id`), '
            . 'KEY `venue_id` (`venue_id`) '
            . ')');
        assert('$q');
    }
}

session_start();
$access_level = check_login();
$page = new dbCreate();
echo $page->toHtml();


?>