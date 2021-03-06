<?php

/**
 * Deletes a publication from the database.
 *
 * This page confirms that the user would like to delete the following
 * publication and then removes it from the database once confirmation has been
 * given.
 *
 * @package PapersDB
 * @subpackage HTML_Generator
 */

/** Requries the base class and classes to access the database. */
require_once '../includes/defines.php';
require_once 'includes/pdHtmlPage.php';
require_once 'includes/pdPublication.php';

/**
 * Renders the whole page.
 *
 * @package PapersDB
 */
class delete_publication extends pdHtmlPage {
    public $pub_id;

    public function __construct() {
        parent::__construct('delete_publication', 'Delete Publication',
                           'Admin/delete_pbublication.php');

        if ($this->loginError) return;

        $this->loadHttpVars();

        if (isset($this->pub_id) && !is_numeric($this->pub_id)) {
            $this->pageError = true;
            return;
        }

        $form =& $this->confirmForm('deleter');
        $form->addElement('hidden', 'pub_id', $this->pub_id);

        if ($form->validate()) {
            $values = $form->exportValues();

            $pub = new pdPublication();
            $result = $pub->dbLoad($this->db, $values['pub_id']);
            if (!$result) {
                $this->pageError = true;
                return;
            }

            $title = $pub->title;
            $pub->dbDelete($this->db);

            echo 'You have successfully removed the following ', 
            	'publication from the database: <p/><b>', $title, '</b>';
        }
        else {
            if ($this->pub_id == null) {
                echo 'No pub id defined';
                $this->pageError = true;
                return;
            }

            $pub = new pdPublication();
            $result = $pub->dbLoad($this->db, $this->pub_id);
            if (!$result) {
                $this->pageError = true;
                return;
            }

            $renderer =& $form->defaultRenderer();
            $form->accept($renderer);

            echo '<h3>Delete Publication</h3>Delete the following paper?<p/>', 
	            $pub->getCitationHtml();

            $this->form =& $form;
            $this->renderer =& $renderer;
        }
    }
}

$page = new delete_publication();
echo $page->toHtml();

?>