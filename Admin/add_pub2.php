<?php

/**
 * This is the form portion for adding or editing author information.
 *
 * @package PapersDB
 * @subpackage HTML_Generator
 */

/** Requries the base class and classes to access the database. */
require_once '../includes/defines.php';
require_once 'Admin/add_pub_base.php';
require_once 'includes/pdAuthInterests.php';
require_once 'includes/pdAuthor.php';

/**
 * This is just a stub, see javascript check_authors() for the real code
 */
function check_authors() {
    return true;
}

/**
 * Renders the whole page.
 *
 * @package PapersDB
 */
class add_pub2 extends add_pub_base {
    public $debug = 0;
    public $author_id = null;

    public function __construct() {
        parent::__construct();

        if ($this->loginError) return;
        $this->use_mootools = true;

        $this->pub =& $_SESSION['pub'];

        if (isset($this->pub->pub_id))
            $this->page_title = 'Edit Publication';

        $this->authors = pdAuthorList::create($this->db, null, null, true);

        $form = new HTML_QuickForm('add_pub2', 'post', '', '',
                                   array('onsubmit' => 'return check_authors("add_pub2");'));

        $form->addElement('header', null, 'Select from Authors in Database');

        $tooltip = 'Authors::The authors of the publication. Listed in the
same order as in the publication
&lt;p/&gt;
If an author is not already in the database press the &lt;b&gt;Add Author not
in DB&lt;/b&gt; button.';

        $form->addElement(
            'textarea', 'authors',
            "<div id=\"MYCUSTOMFLOATER\"  class=\"myCustomFloater\" style=\"position:absolute;top:200px;left:600px;background-color:#cecece;display:none;visibility:hidden\"><div class=\"myCustomFloaterContent\"></div></div>"
            . "<span class=\"Tips1\" title=\"$tooltip\">Authors</span>:",
            array('cols' => 60,
                  'rows' => 5,
                  'class' => 'wickEnabled:MYCUSTOMFLOATER',
                  'wrap' => 'virtual'));

        $form->addElement('static', null, null,
                          '<span class="small">'
                          . 'There are ' . count($this->authors)
                          . ' authors in the database. Type a partial name to '
                          . 'see a list of matching authors. Separate names '
                          . 'using commas.</span>');
        $form->addElement('submit', 'add_new_author', 'Add Author not in DB');

        // collaborations radio selections
        $tooltip = 'Collaborations::If the publication is a collaboration,
select the options that apply to this paper.';
        $form->addElement(
            'header', null,
            "<span class=\"Tips1\" title=\"$tooltip\">Collaborations</span>");
        $collaborations = pdPublication::collaborationsGet($this->db);

        foreach ($collaborations as $col_id => $description) {
            $radio_cols[] = HTML_QuickForm::createElement(
                'checkbox', 'paper_col[' . $col_id . ']', null, $description,
                1);
        }

        $form->addGroup($radio_cols, 'group_collaboration',
                        null, '<br/>', false);

        $pos = strpos($_SERVER['PHP_SELF'], 'papersdb');
        $url = substr($_SERVER['PHP_SELF'], 0, $pos) . 'papersdb';

        $buttons[] = HTML_QuickForm::createElement(
            'submit', 'prev_step', '<< Previous Step');
        $buttons[] = HTML_QuickForm::createElement(
            'button', 'cancel', 'Cancel',
            array('onclick' => "cancelConfirm();"));
        $buttons[] = HTML_QuickForm::createElement(
            'submit', 'next_step', 'Next Step >>');

        if ($this->pub->pub_id != '')
            $buttons[] = HTML_QuickForm::createElement(
                'submit', 'finish', 'Finish');

        $form->addGroup($buttons, 'buttons', '', '&nbsp;', false);

        $this->form =& $form;

        if ($form->validate()) {
            $this->processForm();
        }
        else {
            $this->renderForm();
        }
    }

    public function renderForm() {
        assert('isset($_SESSION["pub"])');

        $form =& $this->form;

        $defaults = array();

        if (count($this->pub->authors) > 0) {
            foreach ($this->pub->authors as $author)
                $auth_names[] = $author->firstname . ' ' . $author->lastname;
            $defaults['authors'] = implode(', ', $auth_names);
        }

        if (is_array($this->pub->collaborations)
            && (count($this->pub->collaborations) > 0)) {
            foreach ($this->pub->collaborations as $col_id) {
                $defaults['paper_col'][$col_id] = 1;
            }
        }

        $form->setDefaults($defaults);

        if (isset($this->pub->pub_id))
            echo '<h3>Editing Publication Entry</h3>';
        else
            echo '<h3>Adding Publication Entry</h3>';

        echo $this->pub->getCitationHtml('', false), '&nbsp;',
            getPubIcons($this->db, $this->pub, 0x1), '<p/>',
            add_pub_base::similarPubsHtml($this->db);

        $renderer =& $form->defaultRenderer();
        $form->accept($renderer);
        $this->renderer =& $renderer;
        $this->javascript();
    }

    public function processForm() {
        assert('isset($_SESSION["pub"])');

        $form =& $this->form;

        $values = $form->exportValues();

        if (empty($values['authors'])) {
            $this->pub->clearAuthors();
        }
        else {
            // need to retrieve author_ids for the selected authors
            $selAuthors = explode(', ', preg_replace('/\s\s+/', ' ',
                                                     $values['authors']));
            $author_ids = array();
            foreach ($selAuthors as $author) {
                if (empty($author)) continue;

                $result = array_search($author, $this->authors);
                if ($result !== false)
                    $author_ids[] = $result;
            }

            if (count($author_ids) > 0)
                $this->pub->addAuthor($this->db, $author_ids);
        }

        if (isset($values['paper_col'])
            && (count($values['paper_col']) > 0)) {
            $this->pub->collaborations = array_keys($values['paper_col']);
        }

        if ($this->debug) {
            debugVar('values', $values);
            debugVar('pub', $this->pub);
            return;
        }

        if (isset($values['add_new_author']))
            header('Location: add_author.php');
        else if (isset($values['prev_step']))
            header('Location: add_pub1.php');
        else if (isset($values['finish']))
            header('Location: add_pub_submit.php');
        else
            header('Location: add_pub3.php');
    }

    public function javascript() {
        $pos = strpos($_SERVER['PHP_SELF'], 'papersdb');
        $url = substr($_SERVER['PHP_SELF'], 0, $pos) . 'papersdb';

        // WICK/
        $this->js .= "\ncollection="
            . convertArrayToJavascript($this->authors, false)
            . ";\n\n";

        $this->js .=<<<JS_END
window.addEvent('domready', function() {
        var Tips1 = new Tips($$('.Tips1'));
    });
JS_END;

        $js_files = array('js/add_pub_cancel.js');

        foreach ($js_files as $js_file) {
            assert('file_exists($js_file)');
            $content = file_get_contents($js_file);

            $this->js .= str_replace(array('{host}', '{self}',
                                           '{new_location}'),
                                     array($_SERVER['HTTP_HOST'],
                                           $_SERVER['PHP_SELF'],
                                           $url),
                                     $content);
        }

        $this->addJavascriptFiles(array('../js/wick.js', '../js/check_authors.js'));
    }
}

$page = new add_pub2();
echo $page->toHtml();

?>
