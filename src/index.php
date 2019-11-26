<?php
ob_start();
session_start();
require(dirname(__FILE__) . '/includes/tim_example_queries.php');
require(dirname(__FILE__) . '/config/config.php');
require(dirname(__FILE__) . '/classes/MySmarty.class.php');
require(dirname(__FILE__) . '/classes/TimPars.class.php');
require(dirname(__FILE__) . '/classes/TimLabel.class.php');
require(dirname(__FILE__) . '/classes/TimQuery.class.php');
require(dirname(__FILE__) . '/classes/TimWrite.class.php');
require(dirname(__FILE__) . '/includes/functions.php');
require(dirname(__FILE__) . "/tweaks/tweak_queries.php");

//error_reporting(0);
$URI = $_SERVER["REQUEST_URI"];
if (isset($_GET["hsid"])) {
    login($_GET['hsid']);
    $parts = explode('?', $_SERVER["REQUEST_URI"]);
    $URI = $parts[0];
    header("Location: " . $parts[0]);
    ob_end_flush();
}

$segments = explode('/', $URI);
if (count($segments) < 3) {
    $page = "home";
} else {
    $page = $segments[2];
}

switch ($page) {
    case 'browse':
        if (isset($segments[3])) {
            switch ($segments[3]) {
               /* case 'set':
                    if (isset($segments[4])) {
                        show_object($segments[4]);
                    } else {
                        browse();
                    }
                    break;*/
                case 'list':
                    if (isset($segments[4]) && isset($segments[5])) {
                        if (isset($segments[6])) {
                            show_list($segments[4], $segments[5], $segments[6]);
                        } else {
                            show_list($segments[4], $segments[5]);
                        }
                    } else {
                        browse();
                    }
                    break;
                case 'metadata':
                    if (isset($segments[4])) {
                        show_metadata($segments[4]);
                    } else {
                        browse();
                    }
                    break;
                default:
                    browse();
                    break;
            }
        } else {
            browse();
        }
        break;
    case "show":
        if (isset($segments[3]) && isset($segments[4]) && isset($segments[5])) {
            switch ($segments[4]) {
                //case "clusius_Persons":
                //    show_person($segments[5]);
                //    break;
                default:
                    if (isset($segments[5])) {
                        show_entity($segments[3], $segments[4], $segments[5]);
                    } else {
                        go_home();
                    }

                    break;
            }
        } else {
            browse();
        }
        break;
    case 'create':
        if (isset($segments[3]) && isset($segments[4])) {
            create_entity($segments[3], $segments[4]);
        } else {
            go_home();
        }
        break;
    case 'edit':
        if (isset($segments[3]) && isset($segments[4])) {
            switch ($segments[3]) {
                case 'metadata':
                    edit_metadata_nw($segments[4]);
                    break;
                case 'settings':
                    edit_settings($segments[4], $segments[5]);
                    break;
                default:
                    edit_entity($segments[3], $segments[4], $segments[5]);
                    break;
            }
        } else {
            go_home();
        }
        break;
    case 'publish':
        if (isset($segments[3])) {
            publish_dataset($segments[3]);
        } else {
            go_home();
        }
        break;
    case 'drop':
        if (isset($segments[3]) && isset($segments[4])) {
            switch ($segments[3]) {
                case 'dataset':
                    drop_dataset($segments[4]);
                    go_home();
                    break;
                case 'collection':
                    go_home();
                    break;
                case 'item':
                    if (isset($segments[4]) && isset($segments[5]) && isset($segments[6])) {
                        drop_item($segments[4], $segments[5], $segments[6]);
                    } else {
                        go_home();
                    }
                    break;
                default:
                    go_home();
            }
        }
        break;
    case 'test':
        check_input();
        break;
    case 'submit':
        set_collection_settings();
        break;
    case 'login':
        login();
        break;
    case 'logout':
        logout();
        break;
    default:
        browse();
        break;
}