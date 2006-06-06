<?php ;

/**
 * \file
 *
 * \brief
 */

require_once 'includes/check_login.php';

$page_info = array(
    'add_publication'
    => array('Add Publication', 'Admin/add_publication.php', 2),
    'add_author'
    => array('Add Author', 'Admin/add_author.php', 2),
    'add_venue'
    => array('Add Venue', 'Admin/add_venue.php', 2),
    'edit_user'
    => array('Edit User', 'Admin/edit_user.php', 2),
    'advanced_search'
    => array('Advanced Search', 'advanced_search.php', 1),
    'all_publications'
    => array('All Publications', 'list_publication.php', 1),
    'all_authors'
    => array('All Authors', 'list_author.php', 1),
    'logout'
    => array('Logout', 'logout.php', 2),
    'login'
    => array('Login or Register', 'login.php', 0)
    );

function htmlHeader($page, $title, $redirect = '') {
    global $page_info;

    echo "<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='en' "
        . "lang='en'>\n"
        . "<head>\n"
        . "<title>". $title . "</title>\n"
        . "<meta http-equiv='Content-Type' "
        . "content='text/html; charset=iso-8859-1' />\n";

    if ($redirect != '') {
        echo "<meta http-equiv='refresh' content='5;url=" . $redirect
            . "' />\n";
    }

    if (($page != '') && isset($page_info[$page])
        && (strstr($page_info[$page][1], '/')))
        $cssFile = '../style.css';
    else
        $cssFile = 'style.css';

    echo '<link rel="stylesheet" href="' . $cssFile . '" /></head>';
}

function navMenu($page = '') {
    global $logged_in, $page_info;

    $url_prefix = '';
    if (isset($page_info[$page])) {
        if (strstr($page_info[$page][1], '/'))
            $url_prefix = '../';
    }

    foreach ($page_info as $name => $info) {
        if (($logged_in && ($info[2] > 0))
            || (!$logged_in && ($info[2] <= 1))) {
            if ($name == $page) {
                $options[$info[0]] = '';
            }
            else if (($page != '') && (strstr($page_info[$page][1], '/')))
                $options[$info[0]] = $url_prefix . $info[1];
            else
                $options[$info[0]] = $info[1];
        }
    }

    echo '<div id="nav">'
        . '<h2>navigation</h2>'
        . '<ul>';

    if (is_array($options))
        foreach ($options as $key => $value) {
            if ($value == '')
                echo '<li>' . $key . '</li>';
            else
                echo '<li><a href="' . $value . '">' . $key . '</a></li>';
        }

    $form = quickSearchFormCreate();
    $renderer =& new HTML_QuickForm_Renderer_QuickHtml();
    $form->accept($renderer);

    echo "</ul>\n"
        . $renderer->toHtml($renderer->elementToHtml('search') . ' '
                            . $renderer->elementToHtml('Quick'))
        . "</div>";
}

?>