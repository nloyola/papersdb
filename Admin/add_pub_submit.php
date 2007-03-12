<?php ;

// $Id: add_pub_submit.php,v 1.13 2007/03/12 23:05:43 aicmltec Exp $

/**
 * This is the form portion for adding or editing author information.
 *
 * @package PapersDB
 * @subpackage HTML_Generator
 */

ini_set("include_path", ini_get("include_path") . ":..");

/** Requries the base class and classes to access the database. */
require_once 'includes/pdHtmlPage.php';
require_once 'includes/pdAuthInterests.php';
require_once 'includes/pdCatList.php';
require_once 'includes/pdAuthor.php';
require_once 'includes/pdExtraInfoList.php';
require_once 'includes/pdAttachmentTypesList.php';

/**
 * Renders the whole page.
 *
 * @package PapersDB
 */
class add_pub_submit extends pdHtmlPage {
    var $debug = 0;

    function add_pub_submit() {
        session_start();
        parent::pdHtmlPage(null, 'Publication Submitted',
                           'Admin/add_pub_submit.php', PD_NAV_MENU_NEVER);

        if ($this->loginError) return;

        if (!isset($_SESSION['state'])) {
            $this->pageError = true;
            return;
        }

        if ($_SESSION['state'] != 'pub_add') {
            $this->pageError = true;
            return;
        }

        $pub =& $_SESSION['pub'];
        $user =& $_SESSION['user'];

        if ($pub->pub_id != null) {
          $this->contentPre
            .= 'The change to the following publication has been submitted:<p/>';
        }
        else {
          $this->contentPre
            .= 'The following publication has been added to the database:<p/>';
        }

        $pub->submit = $user->name;

        $pub->dbSave($this->db);

        // deal with paper
        if (isset($_SESSION['paper'])) {
            if ($_SESSION['paper'] != 'none')
                $pub->paperSave($this->db, $_SESSION['paper']);
            else
                $pub->deletePaper($this->db);
        }

        if (isset($_SESSION['attachments'])
            && (count($_SESSION['attachments']) > 0))
            for ($i = 0; $i < count( $_SESSION['attachments']); $i++) {
                assert('isset($_SESSION["att_types"][$i])');

                $pub->attSave($this->db, $_SESSION['attachments'][$i],
                              $_SESSION['att_types'][$i]);
            }

        if (isset($_SESSION['removed_atts'])
             && (count($_SESSION['removed_atts']) > 0))
            foreach ($_SESSION['removed_atts'] as $filename)
                $pub->dbAttRemove($this->db, $filename);

        if ($this->debug) {
            $this->contentPost
                .= 'pub<pre>' . print_r($pub, true) . '</pre>';
        }

          $this->contentPre .= $pub->getCitationHtml()
              . $this->getPubIcons($pub);

        pubSessionInit();
        $this->db->close();
    }

}

$page = new add_pub_submit();
echo $page->toHtml();


?>
