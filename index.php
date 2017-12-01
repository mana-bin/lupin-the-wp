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
                <form action="<?php echo plugins_url("lupin")."/download.php"; ?>" method="post">
                  <input type="hidden" name="plugin_dir" value="<?php echo WP_PLUGIN_DIR."/".$val; ?>">
                  <input type="hidden" name="plugin" value="<?php echo $val; ?>">
                  <input type="submit" class="button-primary" value="このプラグインを盗む">
                </form>
              </td>
          </tr>
        <?php endforeach; ?>
      </table>
    <?php }
}
$Lupin = new Lupin;