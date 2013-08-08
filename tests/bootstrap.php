<?php

set_include_path(dirname(__DIR__) . '/src:' . get_include_path());

$loader = require_once dirname(__DIR__) . '/vendor/autoload.php';
$loader->setUseIncludePath(true);
