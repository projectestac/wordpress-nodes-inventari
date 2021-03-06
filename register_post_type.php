<?php

function nodes_inventari_register_post_type() {

    // CPT: nodes_inventari
    // TODO: Internacionalitzaci√≥

    $labels = [
        'name' => __('Inventari digital', 'nodes'),
        'singular_name' => __('Element', 'nodes'),
        'all_items' => __('Tots els elements', 'nodes'),
        'add_new' => __('Afegeix un element', 'nodes'),
        'add_new_item' => __('Afegeix un element nou', 'nodes'),
        'edit_item' => __('Edita', 'nodes'),
        'new_item' => __('Element nou', 'nodes'),
        'view_item' => __('Visualitza', 'nodes'),
        'search_items' => __('Cerca', 'nodes'),
        'not_found' => __('No s\'han trobat elements digitals', 'nodes'),
        'not_found_in_trash' => __('No hi ha elements digitals a la paperera', 'nodes'),
    ];

    $args = [
        'labels' => $labels,
        'public' => true,
        'show_in_rest' => false,
        'has_archive' => false,
        'hierarchical' => false,
        'publicly_queryable' => false,
        'query_var' => false,
        'hierarchical' => false,
        'menu_icon' => 'dashicons-list-view',
        'supports' => [
            'title',
            'editor',
            'thumbnail'
        ],
        'rewrite' => ['slug' => 'inventari-digital'],
        'register_meta_box_cb' => 'add_inventari_metaboxes',
    ];

    register_post_type('nodes_inventari', $args);

}

function add_nodes_inventari_to_query($query) {
    if (is_home() && $query->is_main_query()) {
        $query->set('post_type', ['post', 'nodes_inventari']);
    }

    return $query;
}

// TAXONOMIES

function nodes_inventari_register_taxonomy() {

    // Estat de l'element
    $labels = [
        'name' => __('Estat de l\'element', 'nodes'),
        'singular_name' => __('Estat', 'nodes'),
        'search_items' => __('Cerca als estats', 'nodes'),
        'all_items' => __('Tots els estats', 'nodes'),
        'edit_item' => __('Edita l\'estat', 'nodes'),
        'update_item' => __('Actualitza l\'estat', 'nodes'),
        'add_new_item' => __('Afegeix un estat nou', 'nodes'),
        'new_item_name' => __('Nom de l\'estat nou', 'nodes'),
        'menu_name' => __('Estats', 'nodes'),
    ];

    $args = [
        'labels' => $labels,
        'hierarchical' => true,
        'sort' => true,
        'args' => ['orderby' => 'term_order'],
        'rewrite' => ['slug' => 'estats_inventari'],
        'show_admin_column' => true,
        'show_in_rest' => true,
    ];

    register_taxonomy('nodes_estat_inventari', ['nodes_inventari'], $args);

    // TAXONOMY √Ämbit
    $labels = [
        'name' => __('Tipus', 'nodes'),
        'singular_name' => __('Tipus', 'nodes'),
        'search_items' => __('Cerca als tipus', 'nodes'),
        'all_items' => __('Tots els tipus', 'nodes'),
        'edit_item' => __('Edita el tipus', 'nodes'),
        'update_item' => __('Actualitza el tipus', 'nodes'),
        'add_new_item' => __('Afegeix un tipus nou', 'nodes'),
        'new_item_name' => __('Nom del tipus nou', 'nodes'),
        'menu_name' => __('Tipus', 'nodes'),
    ];

    $args = [
        'labels' => $labels,
        'hierarchical' => true,
        'sort' => true,
        'args' => ['orderby' => 'term_order'],
        'rewrite' => ['slug' => 'ambit_inventari'],
        'show_admin_column' => true,
        'show_in_rest' => true,
    ];

    register_taxonomy('nodes_ambit_inventari', ['nodes_inventari'], $args);

    // TAXONOMY Origen
    $labels = [
        'name' => __('Or√≠gens', 'nodes'),
        'singular_name' => __('Origen', 'nodes'),
        'search_items' => __('Cerca als or√≠gens', 'nodes'),
        'all_items' => __('Tots els or√≠gens', 'nodes'),
        'edit_item' => __('Edita l\'origen', 'nodes'),
        'update_item' => __('Actualitza l\'origen', 'nodes'),
        'add_new_item' => __('Afegeix un origen nou', 'nodes'),
        'new_item_name' => __('Nou de l\'origen nou', 'nodes'),
        'menu_name' => __('Or√≠gens', 'nodes'),
    ];

    $args = [
        'labels' => $labels,
        'hierarchical' => true,
        'sort' => true,
        'args' => ['orderby' => 'term_order'],
        'rewrite' => ['slug' => 'origen_inventari'],
        'show_admin_column' => true,
        'show_in_rest' => true

    ];

    register_taxonomy('nodes_origen_inventari', ['nodes_inventari'], $args);

    // TAXONOMY Ubicaci√≥
    $labels = [
        'name' => __('Ubicacions', 'nodes'),
        'singular_name' => __('Ubicaci√≥', 'nodes'),
        'search_items' => __('Cerca a les ubicacions', 'nodes'),
        'all_items' => __('Totes les ubicacions', 'nodes'),
        'edit_item' => __('Edita la ubicaci√≥', 'nodes'),
        'update_item' => __('Actualitza la ubicaci√≥', 'nodes'),
        'add_new_item' => __('Afegeix una ubicaci√≥ nova', 'nodes'),
        'new_item_name' => __('Nom de la ubicaci√≥ nova', 'nodes'),
        'menu_name' => __('Ubicacions', 'nodes'),
    ];

    $args = [
        'labels' => $labels,
        'hierarchical' => true,
        'sort' => true,
        'args' => ['orderby' => 'term_order'],
        'rewrite' => ['slug' => 'ubicacio_inventari'],
        'show_admin_column' => true,
        'show_in_rest' => true

    ];

    register_taxonomy('nodes_ubicacio_inventari', ['nodes_inventari'], $args);

}

// Set predefined terms
function nodes_inventari_set_terms() {

    if (get_option('Activated_inventari') != 'nodes_inventari') {
        return;
    }

    wp_insert_term(
        'Fora de manteniment',
        'nodes_estat_inventari',
        [
            'description' => 'Equip sense servei de manteniment',
            'slug' => 'fora-manteniment'
        ]
    );

    wp_insert_term(
        'En manteniment',
        'nodes_estat_inventari',
        [
            'description' => 'Equip amb servei de manteniment actiu',
            'slug' => 'en-manteniment'
        ]
    );

    wp_insert_term(
        'Fora de garantia',
        'nodes_estat_inventari',
        [
            'description' => 'Equip sense garantia vigent',
            'slug' => 'fora-garantia'
        ]
    );

    wp_insert_term(
        'En garantia',
        'nodes_estat_inventari',
        [
            'description' => 'Equip amb garantia vigent',
            'slug' => 'en-garantia'
        ]
    );

    wp_insert_term(
        'En reparaci√≥',
        'nodes_estat_inventari',
        [
            'description' => 'L\'element est√† en el servei t√®cnic',
            'slug' => 'en-reparacio'
        ]
    );

    wp_insert_term(
        'En √ļs',
        'nodes_estat_inventari',
        [
            'description' => 'L\'Element est√† en √ļs, no disponible',
            'slug' => 'en-us'
        ]
    );

    wp_insert_term(
        'Disponible',
        'nodes_estat_inventari',
        [
            'description' => 'L\'Element est√† disponible',
            'slug' => 'disponible'
        ]
    );

    // Tipus

    wp_insert_term(
        'Impressora',
        'nodes_ambit_inventari',
        [
            'description' => 'Impressora, impressora 3D',
            'slug' => 'impressora'
        ]
    );

    wp_insert_term(
        'Projector',
        'nodes_ambit_inventari',
        [
            'description' => 'Projector',
            'slug' => 'projector'
        ]
    );

    wp_insert_term(
        'Panell interactiu / PDI',
        'nodes_ambit_inventari',
        [
            'description' => 'Panell interactiu o PDI',
            'slug' => 'panell-pdi'
        ]
    );

    wp_insert_term(
        'Port√†til',
        'nodes_ambit_inventari',
        [
            'description' => 'Port√†til',
            'slug' => 'portatil'
        ]
    );

    wp_insert_term(
        'Tablet',
        'nodes_ambit_inventari',
        [
            'description' => 'Tauleta',
            'slug' => 'tablet'
        ]
    );

    wp_insert_term(
        'Ordinador de sobretaula',
        'nodes_ambit_inventari',
        [
            'description' => 'Ordinador de sobretaula',
            'slug' => 'ordinador-sobretaula'
        ]
    );

    wp_insert_term(
        'M√≤bil',
        'nodes_ambit_inventari',
        [
            'description' => 'Tel√®fon m√≤bil',
            'slug' => 'mobil'
        ]
    );

    wp_insert_term(
        'Pantalla',
        'nodes_ambit_inventari',
        [
            'description' => 'Pantalla de monitor',
            'slug' => 'pantalla'
        ]
    );

    wp_insert_term(
        'Element WIFI',
        'nodes_ambit_inventari',
        [
            'description' => 'Punt d\'acc√©s, repetidor, Mifis, etc',
            'slug' => 'wifi'
        ]
    );

    // Or√≠gens
    wp_insert_term(
        'Cat√†leg TIC',
        'nodes_origen_inventari',
        [
            'description' => 'Cat√†leg TIC d\'autoservei',
            'slug' => 'cataleg-tic'
        ]
    );

}
