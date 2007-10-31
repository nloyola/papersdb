<?php ;

// $Id: sanity_checks.php,v 1.1 2007/10/30 21:24:09 aicmltec Exp $

/**
 * Main page for PapersDB.
 *
 * Main page for public access, provides a login, and a function that selects
 * the most recent publications added.
 *
 * @package PapersDB
 * @subpackage HTML_Generator
 */

ini_set("include_path", ini_get("include_path") . ":..");

/** Requries the base class and classes to access the database. */
require_once 'includes/pdHtmlPage.php';
require_once 'includes/pdPubList.php';

/**
 * Renders the whole page.
 *
 * @package PapersDB
 */
class sanity_checks extends pdHtmlPage {
    var $sub_pages = array(
        array('Pub Sanity Checks',      'pubSanityChecks.php'),
        array('Check Attachments',      'check_attachments.php'),
        array('Author Report',          'author_report.php'),
        array('Duplicate Publications', 'duplicatePubs.php'),
        array('Create Database',        'dbcreate_mysql.php')
        );

    function sanity_checks() {
        parent::__construct('sanity_checks');

        if ($this->loginError) return;

        echo '<h1>Sanity Checks</h1>';
        echo '<ul>';

        foreach ($this->sub_pages as $page_info) {
            echo '<li><a href="' . $page_info[1] . '">' . $page_info[0]
                . '</a></li>';
        }

        echo '</ul>';
    }
}

$page = new sanity_checks();
echo $page->toHtml();

?>