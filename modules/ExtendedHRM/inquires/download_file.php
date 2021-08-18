<?php
$fullPath = $_GET['fileSource'];

//print_r($fullPath);die;
if($fullPath) {
    $fsize = filesize($fullPath);
//print_r($fsize);die;	
	
    $path_parts = pathinfo($fullPath);
    $ext = strtolower($path_parts["extension"]);
    switch ($ext) {
        case "pdf":
        header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); // use 'attachment' to force a download
        header("Content-type: application/pdf"); // add here more headers for diff. extensions
        break;
        default;
        header("Content-type: application/octet-stream");
        header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
    }
    if($fsize) {//checking if file size exist
      header("Content-length: $fsize");
    }
    readfile($fullPath);
    exit;
}
?>