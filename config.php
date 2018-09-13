<?php
// HTTP
define('HTTP_SERVER', 'https://'.$_SERVER['HTTP_HOST'].'/');

// HTTPS
define('HTTPS_SERVER', 'https://'.$_SERVER['HTTP_HOST'].'/');

// DIR
define('DIR_APPLICATION', $_SERVER['DOCUMENT_ROOT'].'/catalog/');
define('DIR_SYSTEM', $_SERVER['DOCUMENT_ROOT'].'/system/');
define('DIR_IMAGE', $_SERVER['DOCUMENT_ROOT'].'/image/');
define('DIR_STORAGE', $_SERVER['DOCUMENT_ROOT'].'/../storage/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/theme/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');



switch ($_SERVER['HTTP_HOST']):
    case 'dnepr-toys.loc':
        define('DB_DRIVER', 'mysqli');
        define('DB_HOSTNAME', 'localhost');
        define('DB_USERNAME', 'root');
        define('DB_PASSWORD', '');
        define('DB_DATABASE', 'dtoys_new');
        define('DB_PORT', '3306');
        define('DB_PREFIX', 'oc_');
        break;
    default:
        define('DB_DRIVER', 'mysqli');
        define('DB_HOSTNAME', 'localhost');
        define('DB_USERNAME', 'dneprtoyscom_dnepr_toys');
        define('DB_PASSWORD', 'DwPce*fglg)o');
        define('DB_DATABASE', 'dneprtoyscom_new');
        define('DB_PORT', '3306');
        define('DB_PREFIX', 'oc_');
        break;
endswitch;


// DB
//define('DB_DRIVER', 'mysqli');
//define('DB_HOSTNAME', 'localhost');
//define('DB_USERNAME', 'dneprtoyscom_dnepr_toys');
//define('DB_PASSWORD', 'DwPce*fglg)o');
//define('DB_DATABASE', 'dneprtoyscom_new');
//define('DB_PORT', '3306');
//define('DB_PREFIX', 'oc_');
