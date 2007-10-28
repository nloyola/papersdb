<?php ;

// $Id: index.php,v 1.42 2007/10/28 22:55:49 loyola Exp $

/**
 * Main page for PapersDB.
 *
 * Main page for public access, provides a login, and a function that selects
 * the most recent publications added.
 *
 * @package PapersDB
 * @subpackage HTML_Generator
 */

/** Requries the base class and classes to access the database. */
require_once 'includes/pdHtmlPage.php';
require_once 'includes/pdPubList.php';

/**
 * Renders the whole page.
 *
 * @package PapersDB
 */
class index extends pdHtmlPage {
    public function __construct() {
        parent::__construct('home');

        if ($this->loginError) return;

        $this->recentAdditions();
        $this->pubByYears();
    }

    private function recentAdditions() {
        $pub_list = new pdPubList($this->db, array('sort_by_updated' => true));

        if (!isset($pub_list->list)) return;

        echo '<h2>Recent Additions:</h2>';

        echo $this->displayPubList($pub_list, false, 6);
    }

    private function pubByYears() {
        $pub_years = new pdPubList($this->db, array('year_list' => true));

        if (!isset($pub_years->list)) return;

        $table = new HTML_Table(array('class' => 'nomargins',
                                      'width' => '60%'));

        $text = '';
        foreach (array_values($pub_years->list) as $item) {
            $text .= '<a href="list_publication.php?year=' . $item['year']
                . '">' . $item['year'] . '</a> ';
        }

        $table->addRow(array($text));

        echo '<h2>Publications by Year:</h2>'
            . $table->toHtml();
    }
}

$page = new index();
echo $page->toHtml();

?>
