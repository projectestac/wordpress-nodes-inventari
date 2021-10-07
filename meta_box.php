<?php

// Metabox Inventari
function add_inventari_metaboxes() {
    add_meta_box(
        'inventari_template',
        'Identificació (si cal)',
        'inventari_template',
        'nodes_inventari',
        'normal',
        'default'
    );
}

function inventari_template() {

    global $post;

    // Afegim 'noncename' per seguretat
    echo '<input type="hidden" name="inventari_noncename" id="inventari_noncename" value="' .
        wp_create_nonce(plugin_basename(__FILE__)) . '" />';

    // Recuperem les dades existents, si es que hi ha dades existents.
    $sace = get_post_meta($post->ID, 'sace', true);
    $serialnumber = get_post_meta($post->ID, 'serialnumber', true);

    // L'input que apareixerà quan creem un nou element
    echo '<div class="identificadors">';
    echo '<span class="lbl-sace"><label>SACE</label></span> <input width="300px" type="text" name="sace" value="' . $sace . '" />';
    echo '<span class="lbl-serialnumber"><label>Numero de sèrie</label></span> <input width="300px" type="text" name="serialnumber" value="' . $serialnumber . '" />';
    echo '</div>';

}

function save_inventari($post_id, $post) {

    if (!wp_verify_nonce($_POST['inventari_noncename'], plugin_basename(__FILE__))) {
        return $post->ID;
    }

    if (!current_user_can('edit_post', $post->ID)) {
        return $post->ID;
    }

    $inventari_meta['sace'] = $_POST['sace'];
    $inventari_meta['serialnumber'] = $_POST['serialnumber'];

    foreach ($inventari_meta as $key => $value) {
        // Check if it's a revision
        if ($post->post_type == 'revision') {
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
        if (!$value) {
            delete_post_meta($post->ID, $key);
        }
    }

}

add_action('save_post', 'save_inventari', 1, 2);
