<?php
/**
 *
 *  Bootstraping the Web App
 *
 *  @license : MIT 
 *  @author  : Fajar Khairil
 */

require_once 'bootstrap.php';

\Unika\Bag::$BACKEND_URI = 'administrator';
\Unika\Bag::$BASE_URL = 'http://unikacmf.dev/';

$app = \Unika\Bag::instance();

require 'routes.php';

$app->run();