<?php

define('LIBS_DIR', __DIR__ . '/../../libs/');
define('APP_DIR', __DIR__ . '/../../app/');
define('TMP_DIR', __DIR__ . '/../../temp/');
define('TESTS_DIR', __DIR__ . '/../../tests/');
define('TESTS_FRAMEWORK', __DIR__ . '/framework/');

require_once LIBS_DIR . '/Nette/loader.php';
require_once LIBS_DIR . '/NetteTestCase/framework/TestCase.php';
require_once LIBS_DIR . '/NetteTestCase/framework/TestCaseSelen.php';

