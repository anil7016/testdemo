<?php
define('SITE_URL', "http://" . $_SERVER['SERVER_NAME'] . "890/tms/");
define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'] . "/tms/");
define('UPLOADS_ROOT', DOCUMENT_ROOT . "uploads/");

//db constants
define('DB_SERVER', 'localhost'); //please enter mysql server name
define('DB_USERNAME', 'root'); // please enter mysql user name
define('DB_PASSWORD', 'admin'); // please enter mysql user password
define('DB_DATABASE', 'kanhawhp_tms'); // please enter your database name

define('DB_PREFIX', 'tms_');
define('LOGOUT_TIME', 1800);
define('DECIMAL', 2);
define('MIN_COMPONENTS_TO_DISPLAY', 4);
define('TAX_DECIMAL', 5);
define('DATE_FORMAT', "d-m-Y");
define('DEFAULT_CURRENCY', '$');
define('DEFAULT_AREA_UNIT', 'sqmt');
define('FORMATS', "pdf,docx,doc");
define('IMG_FORMATS', "jpg,jpeg,gif,png");
define('DEFAULT_COUNTRY_ID', 'IN');
date_default_timezone_set('Asia/Kolkata');
?>