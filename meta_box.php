<?php

if (!defined('ABSPATH')) {
    exit;
}

// Metabox Inventari
function nodes_inventari_add_metaboxes() {
    add_meta_box(
        'inventari_template',
        'Identificació (si cal)',
        'nodes_inventari_template',
        'nodes_inventari',
        'normal',
        'default'
    );
}

function nodes_inventari_template() {

    global $post;

    // Afegim 'noncename' per seguretat
    echo '<input type="hidden" name="inventari_noncename" id="inventari_noncename" value="' .
        wp_create_nonce(plugin_basename(__FILE__)) . '" />';

    // Recuperem les dades existents, si es que hi ha dades existents.
    $sace = get_post_meta($post->ID, 'sace', true);
    $serialnumber = get_post_meta($post->ID, 'serialnumber', true);

    // L'input que apareixerà quan creem un nou element
    echo '<div class="identificadors">';
    echo '<span class="lbl-sace"><label>SACE</label></span> <input width="300px" type="text" name="sace" value="' . esc_attr($sace) . '" />';
    echo '<span class="lbl-serialnumber"><label>Numero de sèrie</label></span> <input width="300px" type="text" name="serialnumber" value="' . esc_attr($serialnumber) . '" />';
    echo '</div>';

}

function nodes_inventari_save($post_id, $post) {

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post->ID;
    }

    if (!isset($_POST['inventari_noncename'])) {
        return $post->ID;
    }

    if (!wp_verify_nonce($_POST['inventari_noncename'], plugin_basename(__FILE__))) {
        return $post->ID;
    }

    if (!current_user_can('edit_post', $post->ID)) {
        return $post->ID;
    }

    if ($post->post_type !== 'nodes_inventari') {
        return $post->ID;
    }

    // Fix #5: sanejar sempre l'entrada de l'usuari abans de desar-la
    $inventari_meta['sace'] = sanitize_text_field($_POST['sace'] ?? '');
    $inventari_meta['serialnumber'] = sanitize_text_field($_POST['serialnumber'] ?? '');

    foreach ($inventari_meta as $key => $value) {
        // Check if it's a revision
        if ($post->post_type === 'revision') {
            return;
        }

        if (get_post_meta($post->ID, $key, false)) {
            // If it has value, update it
            update_post_meta($post->ID, $key, $value);
        } else {
            // If it hasn't value, create it
            add_post_meta($post->ID, $key, $value);
        }

        // If void, delete it
        if ($value === '') {
            delete_post_meta($post->ID, $key);
        }
    }

}

add_action('save_post', 'nodes_inventari_save', 1, 2);
