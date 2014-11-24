<?php
return array(
	'debug' 			=> True,
    //use to encrypt cookie and other security related min character 8
    'secret_key'        => 'HfhdsknliUIHH89j9B3hnKNKJB',
    'backend_uri'       => Application::$BACKEND_URI,
    'charset' 			=> 'utf-8',
    'locale' 			=> 'en',  
    'tmp_dir'			=> Application::$ENGINE_PATH.DIRECTORY_SEPARATOR.'tmp',
    'log_dir'           => Application::$ENGINE_PATH.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.'logs'
);