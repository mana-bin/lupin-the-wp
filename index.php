<?php
/*
Plugin Name: Lupin the WP
Plugin URI: https://github.com/mana-bin/lupin-the-wp
Description: プラグインを盗むプラグイン
Author: Nishio Manabu
Version: 1.0
Author URI: https://github.com/mana-bin
*/
class Lupin {
    function __construct() {
      add_action('admin_menu', array($this, 'add_index'));
    }
    function add_index() {
      add_menu_page(
        'プラグインを盗む', 
        'プラグインを盗む',  
        'level_0', "lupin", array($this, 'view_html'), '');
    }

    function create_zip($plugin_dir, $file){
      $zip = new ZipArchive();
      $res = $zip->open($file, ZipArchive::CREATE);
      if($res){ 
        $baseLen = mb_strlen($plugin_dir);
        $iterator = new RecursiveIteratorIterator(
          new RecursiveDirectoryIterator($plugin_dir, FilesystemIterator::SKIP_DOTS | FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO
          ), RecursiveIteratorIterator::SELF_FIRST
        );

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
    function view_html() {
      $plugins = scandir(WP_PLUGIN_DIR);
      $ignores = array(".", "..", ".DS_Store", "index.php");
    ?>
      <table class="form-table">
          <?php foreach($plugins as $val): ?>
          <?php if(in_array($val, $ignores)){continue;} ?>
          <tr>
              <th><?php echo $val; ?></th>
              <td>
                <form action="" method="post">
                  <input type="hidden" name="plugin_dir" value="<?php echo WP_PLUGIN_DIR."/".$val; ?>">
                  <input type="hidden" name="plugin" value="<?php echo $val; ?>">
                  <input type="hidden" name="donwload" value="true">
                  <input type="submit" class="button-primary" value="このプラグインを盗む">
                </form>
              </td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php }

}
$Lupin = new Lupin;

if($_POST["donwload"] == "true"){
  $plugin_dir = $_POST["plugin_dir"];
  $file_name  = $_POST["plugin"].".zip";
  $file       = dirname(__FILE__) . "/tmp/" . $file_name;
  $Lupin->create_zip($plugin_dir, $file);

  header('Content-Type: application/zip; name="' . $file_name . '"');
  header('Content-Disposition: attachment; filename="' . $file_name . '"');
  header('Content-Length: '.filesize($file));
  echo file_get_contents($file);
  unlink($file);
  exit();
}
