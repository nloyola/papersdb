<?php

/**
 * Given a author id number, this displays all the info about
 * the author.
 *
 * If the author has only a few publications it will display the title and link
 * to them. If the author has more then 6 then it will link to a seperate page
 * of a list of publications by that author.
 *
 * if a user is logged in, they have the option of editing or deleting the
 * author.
 *
 * @package PapersDB
 * @subpackage HTML_Generator
 */

/** Requries the base class and classes to access the database. */
require_once 'includes/defines.php';
require_once 'includes/pdHtmlPage.php';
require_once 'includes/pdAuthor.php';

/**
 * Renders the whole page.
 *
 * @package PapersDB
 */
class view_author extends pdHtmlPage {
    public $author_id;

    public function __construct() {
        parent::__construct('view_authors', 'Author Information',
                           'view_author.php');

        if ($this->loginError) return;

        $this->loadHttpVars(true, false);
        $this->use_mootools = true;

        // check if this author id is valid
        if (!isset($this->author_id) || !is_numeric($this->author_id)) {
            $this->pageError = true;
            return;
        }

        $auth = new pdAuthor();
        $auth->dbLoad($this->db, $this->author_id,
                      (pdAuthor::DB_LOAD_PUBS_MIN
                       | pdAuthor::DB_LOAD_INTERESTS));

        if (isset($_SERVER['HTTP_REFERER'])
            && (strpos($_SERVER['HTTP_REFERER'], 'Admin/add_author.php?author_id=') !== false)) {
            // the user added or changed an author
            echo "Your change has been sumitted.<br/><hr/>\n";
        }

        echo '<h3>', $auth->name;
        if ($this->access_level > 0) {
            echo $this->getAuthorIcons($auth, 0x6);
        }
        echo '</h3>',  $this->authorShow($auth);

        echo "<hr><a href='list_author.php?tab=" . $auth->name[0] . "'>Author List</a>";
    }

    public function authorShow($auth) {
        $result = '';

        $table = new HTML_Table(array('width' => '600',
                                      'border' => '0',
                                      'cellpadding' => '6',
                                      'cellspacing' => '0'));
        $table->setAutoGrow(true);

        $table->addRow(array('Name:', $auth->name));

        if (isset($auth->title) && (trim($auth->title) != "")) {
            $table->addRow(array('Title:', $auth->title));
        }

        $table->addRow(array('Email:',
                             "<a href='mailto:" . $auth->email . "'>"
                             . $auth->email . "</a>"));
        $table->addRow(array('Organization:', $auth->organization));

        $webpage = str_replace('http://', '', $auth->webpage);
        if (isset($auth->webpage) && !empty($webpage))
            $webpage = "<a href=\"" . $auth->webpage . "\" target=\"_blank\">"
                . $auth->webpage . "</a>";
        else
            $webpage = "none";

        $table->addRow(array('Webpage:', $webpage));

        $interestsStr = '';
        if (isset($auth->interests) && is_array($auth->interests)) {
            $interestsStr = implode('; ', array_values($auth->interests));
        }
        $table->addRow(array('Interest(s):', $interestsStr));

        if ($auth->totalPublications == 0) {
            $table->addRow(array('No publications by this author'),
                           array('colspan' => 2));
        }
        else if ($auth->totalPublications <= 6) {
            assert('is_array($auth->pub_list)');
            $headingCell = 'Publications:';

            $table->addRow(array($headingCell));
        }
        else {
            $table->addRow(
                array('Publications:',
                      '<a id="start" href="#">Show Publications by this author</a>'));
        }

        $table->updateColAttributes(0, array('class' => 'emph',
                                             'width' => '25%'));

        $result .= $table->toHtml();
        if (($auth->totalPublications > 0)
            && ($auth->totalPublications <= 6))
            $result .= displayPubList($this->db, $auth->pub_list);
        else
            $result .= "<div id=\"publist\">&nbsp;</div>";

        $this->css();
        $this->javascript();

        return $result;
    }

    private function css() {
        $this->css = '#publist.ajax-loading {
  background: url(images/spinner.gif) no-repeat center;
}';
    }

    private function javascript() {
        $js_file = 'js/view_author.js';
        assert('file_exists($js_file)');
        $content = file_get_contents($js_file);

        $this->js = str_replace('{author_id}', $this->author_id, $content);
    }
}

$page = new view_author();
echo $page->toHtml();

?>
