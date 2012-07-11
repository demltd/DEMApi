<?php

$paths = array(
    get_include_path(),
    '/var/lib/zend/',
);

defined('API_KEY')
    or define('API_KEY', '2a922c64f675dbba3ea047369042085e');

defined('API_SECRET')
    or define('API_SECRET', 'cc0dc08a0c6bc66d653609931ee23d36');

set_include_path(implode(PATH_SEPARATOR, $paths));

require_once dirname(__FILE__) . '/../v1/DEMAPI.php';