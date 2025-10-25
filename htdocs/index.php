<?php
pm_Context::init('plesk-guardian');
require_once pm_Context::getPlibDir() . 'library/autoload.php';
$application = new pm_Application();
$application->run(); 