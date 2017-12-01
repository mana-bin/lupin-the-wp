<?php 
$plugin_dir = $_POST["plugin_dir"];
$file_name  = $_POST["plugin"].".zip";
$file       = dirname(__FILE__) . "/tmp/" . $file_name;

function zipDirectory($plugin_dir, $file){
  $zip = new ZipArchive();
  $res = $zip->open($file, ZipArchive::CREATE);
  if($res){ 
    $baseLen = mb_strlen($plugin_dir);
    $iterator = new RecursiveIteratorIterator(
      new RecursiveDirectoryIterator($plugin_dir, FilesystemIterator::SKIP_DOTS | FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO
      ), RecursiveIteratorIterator::SELF_FIRST
    );

    $list = array();
    foreach($iterator as $pathname => $info){
      $localpath = mb_substr($pathname, $baseLen);
      if( $info->isFile() ){
        $zip->addFile($pathname, $localpath);
      } else {
        $res = $zip->addEmptyDir($localpath);
      }
    }

    $zip->close();
  } else {
    return false;
  }
}

zipDirectory($plugin_dir, $file);

header('Content-Type: application/zip; name="' . $file_name . '"');
header('Content-Disposition: attachment; filename="' . $file_name . '"');
header('Content-Length: '.filesize($file));
echo file_get_contents($file);
unlink($file);
exit();