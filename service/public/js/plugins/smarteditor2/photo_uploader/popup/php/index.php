<?php
/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
//include_once("./_common.php");
@include_once("./JSON.php");

if( !function_exists('json_encode') ) {
    function json_encode($data) {
        $json = new Services_JSON();
        return( $json->encode($data) );
    }
}

@ini_set('gd.jpeg_ignore_warning', 1);

$ym = date('ym', time());

//$data_dir = G5_DATA_PATH.'/editor/'.$ym.'/';
//$data_url = G5_DATA_URL.'/editor/'.$ym.'/';
$data_dir = $_SERVER['DOCUMENT_ROOT'].'/ns/data/editor_images/';
$data_url = 'http://'.$_SERVER['HTTP_HOST'].'/ns/data/editor_images/';	

@mkdir($data_dir, 0755);
@chmod($data_dir, 0755);

require('UploadHandler.php');
$options = array(
    'upload_dir' => $data_dir,
    'upload_url' => $data_url,
    // This option will disable creating thumbnail images and will not create that extra folder.
    // However, due to this, the images preview will not be displayed after upload
    'image_versions' => array()
);

$upload_handler = new UploadHandler($options);