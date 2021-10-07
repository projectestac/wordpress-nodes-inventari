<?php

/**
 * @package Nodes_Inventari_digital
 * @version 1.0.0
 */
/**
 * Plugin Name: Nodes: Inventari digital
 * Plugin URI: https://agora.xtec.cat/nodes/inventari_digital
 * Description: Extensió per la gestió de l'inventari digital d'un centre educatiu.
 * Author: Xavier Meler
 * Version: 1.0.0
 * Author URI: https://dossier.xtec.cat/jmeler/
 */

include plugin_dir_path(__FILE__) . 'register_post_type.php';
include plugin_dir_path(__FILE__) . 'meta_box.php';
include plugin_dir_path(__FILE__) . 'admin_ui_list.php';
include plugin_dir_path(__FILE__) . 'post_template.php';
include plugin_dir_path(__FILE__) . 'export_csv.php';
include plugin_dir_path(__FILE__) . 'styles.php';
