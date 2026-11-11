<?php

if (!defined('ABSPATH')) {
    exit;
}

// Les fitxes de l'inventari només són visibles per a usuaris connectats
add_action('template_redirect', 'nodes_inventari_nomes_connectats');

function nodes_inventari_nomes_connectats() {

      $es_contingut_inventari = is_singular('nodes_inventari')
        || is_post_type_archive('nodes_inventari')
        || is_tax([
        'nodes_estat_inventari',
        'nodes_ambit_inventari',
        'nodes_origen_inventari',
        'nodes_ubicacio_inventari',
    ]);

    if ($es_contingut_inventari && !is_user_logged_in()) {
        auth_redirect();
    }
}

add_action('pre_get_posts', 'nodes_inventari_tax_archives_query');

function nodes_inventari_tax_archives_query($query) {

    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->is_tax([
        'nodes_estat_inventari',
        'nodes_ambit_inventari',
        'nodes_origen_inventari',
        'nodes_ubicacio_inventari',
    ])) {
        $query->set('post_type', 'nodes_inventari');
    }
}

// No llistar l'inventari als sitemaps públics
add_filter('wp_sitemaps_post_types', function ($post_types) {
    unset($post_types['nodes_inventari']);
    return $post_types;
});

add_filter('wp_sitemaps_taxonomies', function ($taxonomies) {
    unset(
        $taxonomies['nodes_estat_inventari'],
        $taxonomies['nodes_ambit_inventari'],
        $taxonomies['nodes_origen_inventari'],
        $taxonomies['nodes_ubicacio_inventari']
    );
    return $taxonomies;
});

add_filter('the_content', 'add_info_in_nodes_inventari', 1);

function add_info_in_nodes_inventari($content) {

    if (is_singular('nodes_inventari')) {

        $id = get_the_ID();

        $new_content = $content . '<hr />';
        $new_content .= '<ul>';

        // SACE
        // Escapar sempre la sortida de meta guardada per l'usuari
        $sace = get_post_meta($id, 'sace', true);
        if ($sace) {
            $new_content .= '<li><strong>SACE:</strong>' . '&nbsp;' . esc_html($sace) . '</li>';
        }

        // Número de sèrie
        $serialnumber = get_post_meta($id, 'serialnumber', true);
        if ($serialnumber) {
            $new_content .= '<li><strong>Número de sèrie:</strong>' . '&nbsp;' . esc_html($serialnumber) . '</li>';
        }

        // Tipus
        $terms = get_terms([
            'taxonomy' => 'nodes_ambit_inventari',
            'object_ids' => $id,
            'hide_empty' => false,
        ]);

        $new_content .= '<li><strong>Tipus:</strong>' . '&nbsp;';
        $nodes_ambit = '';
        foreach ($terms as $term) {
            $nodes_ambit .= esc_html($term->name) . ', ';
        }
        $new_content .= rtrim($nodes_ambit, ', ') . '</li>';

        // Ubicacions
        $terms = get_terms([
            'taxonomy' => 'nodes_ubicacio_inventari',
            'object_ids' => $id,
            'hide_empty' => false,
        ]);

        $new_content .= '<li><strong>Ubicació:</strong>' . '&nbsp;';
        $nodes_ubicacions = '';
        foreach ($terms as $term) {
            $nodes_ubicacions .= esc_html($term->name) . ', ';
        }
        $new_content .= rtrim($nodes_ubicacions, ', ') . '</li>';

        // Orígens
        $terms = get_terms([
            'taxonomy' => 'nodes_origen_inventari',
            'object_ids' => $id,
            'hide_empty' => false,
        ]);

        $new_content .= '<li><strong>Origen:</strong>' . '&nbsp;';
        $nodes_origens = '';
        foreach ($terms as $term) {
            $nodes_origens .= esc_html($term->name) . ', ';
        }
        $new_content .= rtrim($nodes_origens, ', ') . '</li>';

        // Estats
        $terms = get_terms([
            'taxonomy' => 'nodes_estat_inventari',
            'object_ids' => $id,
            'hide_empty' => false,
        ]);

        $new_content .= '<li><strong>Estats:</strong>' . '&nbsp;';
        $nodes_estats = '';
        foreach ($terms as $term) {
            $nodes_estats .= esc_html($term->name) . ', ';
        }
        $new_content .= rtrim($nodes_estats, ', ') . '</li>';

        // Informat per
        $new_content .= '<li><strong>Informat per:</strong>' . '&nbsp;' . esc_html(get_the_author()) . '</li>';

        // Data
        $new_content .= '<li><strong>Data:</strong>' . '&nbsp;' . esc_html(get_the_date()) . '</li>';
        $new_content .= '</ul>';

        return $new_content;
    }

    return $content;

}