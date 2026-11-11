<?php
if (!defined('ABSPATH')) {
    exit;
}
/**
 * @package Nodes_Inventari_digital
 * @version 1.1.0
 */
/**
 * Plugin Name: Nodes: Inventari digital
 * Plugin URI: https://agora.xtec.cat/nodes/inventari_digital
 * Description: Extensió per la gestió de l'inventari digital d'un centre educatiu.
 * Author: Xavier Meler, Òscar Benítez
 * Version: 1.1.0
 */

require_once plugin_dir_path(__FILE__) . 'register_post_type.php';
require_once plugin_dir_path(__FILE__) . 'meta_box.php';
require_once plugin_dir_path(__FILE__) . 'admin_ui_list.php';
require_once plugin_dir_path(__FILE__) . 'post_template.php';
require_once plugin_dir_path(__FILE__) . 'export_csv.php';
require_once plugin_dir_path(__FILE__) . 'styles.php';

add_action('init', 'nodes_inventari_register_post_type');
add_action('init', 'nodes_inventari_register_taxonomy');
register_activation_hook(__FILE__, 'nodes_inventari_activate');
add_action('init', 'nodes_inventari_set_terms');
add_action('init', 'nodes_inventari_end_activation');

function nodes_inventari_activate() {
    add_option('Activated_inventari', 'nodes_inventari');
    flush_rewrite_rules();
}

function nodes_inventari_end_activation() {
    if (get_option('Activated_inventari') == 'nodes_inventari') {
        delete_option('Activated_inventari');
    }
}
