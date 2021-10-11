<?php

function nodes_inventari_register_post_type() {

    // CPT: nodes_inventari
    // TODO: Internacionalització

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

    // TAXONOMY Àmbit
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
        'name' => __('Orígens', 'nodes'),
        'singular_name' => __('Origen', 'nodes'),
        'search_items' => __('Cerca als orígens', 'nodes'),
        'all_items' => __('Tots els orígens', 'nodes'),
        'edit_item' => __('Edita l\'origen', 'nodes'),
        'update_item' => __('Actualitza l\'origen', 'nodes'),
        'add_new_item' => __('Afegeix un origen nou', 'nodes'),
        'new_item_name' => __('Nou de l\'origen nou', 'nodes'),
        'menu_name' => __('Orígens', 'nodes'),
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

    // TAXONOMY Ubicació
    $labels = [
        'name' => __('Ubicacions', 'nodes'),
        'singular_name' => __('Ubicació', 'nodes'),
        'search_items' => __('Cerca a les ubicacions', 'nodes'),
        'all_items' => __('Totes les ubicacions', 'nodes'),
        'edit_item' => __('Edita la ubicació', 'nodes'),
        'update_item' => __('Actualitza la ubicació', 'nodes'),
        'add_new_item' => __('Afegeix una ubicació nova', 'nodes'),
        'new_item_name' => __('Nom de la ubicació nova', 'nodes'),
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
        'En reparació',
        'nodes_estat_inventari',
        [
            'description' => 'L\'element està en el servei tècnic',
            'slug' => 'en-reparacio'
        ]
    );

    wp_insert_term(
        'En ús',
        'nodes_estat_inventari',
        [
            'description' => 'L\'Element està en ús, no disponible',
            'slug' => 'en-us'
        ]
    );

    wp_insert_term(
        'Disponible',
        'nodes_estat_inventari',
        [
            'description' => 'L\'Element està disponible',
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
        'Portàtil',
        'nodes_ambit_inventari',
        [
            'description' => 'Portàtil',
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
        'Mòbil',
        'nodes_ambit_inventari',
        [
            'description' => 'Telèfon mòbil',
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
            'description' => 'Punt d\'accés, repetidor, Mifis, etc',
            'slug' => 'wifi'
        ]
    );

    // Orígens
    wp_insert_term(
        'Catàleg TIC',
        'nodes_origen_inventari',
        [
            'description' => 'Catàleg TIC d\'autoservei',
            'slug' => 'cataleg-tic'
        ]
    );

}
