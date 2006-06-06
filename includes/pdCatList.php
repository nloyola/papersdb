<?php ;

// $Id: pdCatList.php,v 1.8 2006/06/06 21:11:12 aicmltec Exp $

/**
 * \file
 *
 * \brief Storage and retrieval of publication categories to / from the
 * database.
 *
 *
 */

/**
 *
 * \brief Class for storage and retrieval of publication categories to / from
 * the database.
 */
class pdCatList {
    var $list;

    /**
     * Constructor.
     */
    function pdCatList($obj = NULL) {
        if (!is_null($obj))
            $this->load($obj);
    }

    /**
     * Loads a specific publication from the database.
     *
     * Use flags to load individual tables
     */
    function dbLoad(&$db, $flags = 0) {
        $q = $db->select('category', array('cat_id', 'category'), '',
                         "pdCatList::dbLoad");
        $r = $db->fetchObject($q);
        while ($r) {
            $this->list[] = $r;
            $r = $db->fetchObject($q);
        }
    }
}

?>
