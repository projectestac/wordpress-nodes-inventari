<?php

if (!defined('ABSPATH')) {
    exit;
}

// Show custom data in "Inventari digital" admin ui

add_filter('manage_nodes_inventari_posts_columns', 'nodes_inventari_admin_ui_columns');

function nodes_inventari_admin_ui_columns($columns) {
    return [
        'cb' => $columns['cb'],
        'wps_post_id' => __('ID'),
        'imatge' => __('Imatge', 'nodes'),
        'title' => __('Title'),
        'ambit' => __('Tipus', 'nodes'),
        'ubicacio' => __('Ubicació', 'nodes'),
        'estat' => __('Estat', 'nodes'),
        'sace' => __('SACE', 'nodes'),
        'serialnumber' => __('N.Sèrie', 'nodes'),
        'origen' => __('Origen', 'nodes'),
    ];
}

add_action('manage_nodes_inventari_posts_custom_column', 'nodes_inventari_column', 10, 2);

function nodes_inventari_column($column, $post_id) {

    // Escapar sempre la sortida de meta guardada per l'usuari
    if ('sace' === $column) {
        echo esc_html(get_post_meta($post_id, 'sace', true));
    }

    if ('serialnumber' === $column) {
        echo esc_html(get_post_meta($post_id, 'serialnumber', true));
    }

    if ('wps_post_id' === $column) {
        echo (int) $post_id;
    }

    if ('imatge' === $column) {
        if (has_post_thumbnail($post_id)) {
            echo get_the_post_thumbnail($post_id, [40, 40], ['style' => 'width:40px;height:40px;object-fit:cover;border-radius:3px;']);
        } else {
            echo '—';
        }
    }

    if ('estat' === $column) {
        $url_base = 'edit.php?post_type=nodes_inventari';
        $estats = get_the_terms($post_id, 'nodes_estat_inventari');

        if (is_array($estats)) {
            $estats_list = '';
            foreach ($estats as $estat) {
                $estats_list .= " <a href='" . esc_url($url_base . "&nodes_estat_inventari=" . $estat->slug) . "'>" . esc_html($estat->name) . "</a>,";
            }
            echo rtrim($estats_list, ','); // remove last comma
        }
    }

    if ('ambit' === $column) {
        $url_base = 'edit.php?post_type=nodes_inventari';
        $ambits = get_the_terms($post_id, 'nodes_ambit_inventari');

        if (is_array($ambits)) {
            $ambits_list = '';
            foreach ($ambits as $ambit) {
                $ambits_list .= " <a href='" . esc_url($url_base . "&nodes_ambit_inventari=" . $ambit->slug) . "'>" . esc_html($ambit->name) . "</a>,";
            }
            echo rtrim($ambits_list, ','); // remove last comma
        }
    }

    if ('origen' === $column) {
        $url_base = 'edit.php?post_type=nodes_inventari';
        $origens = get_the_terms($post_id, 'nodes_origen_inventari');

        if (is_array($origens)) {
            $origens_list = '';
            foreach ($origens as $origen) {
                $origens_list .= " <a href='" . esc_url($url_base . "&nodes_origen_inventari=" . $origen->slug) . "'>" . esc_html($origen->name) . "</a>,";
            }
            echo rtrim($origens_list, ','); // remove last comma
        }
    }

    if ('ubicacio' === $column) {
        $url_base = 'edit.php?post_type=nodes_inventari';
        $ubicacions = get_the_terms($post_id, 'nodes_ubicacio_inventari');

        if (is_array($ubicacions)) {
            $ubicacio_list = '';
            foreach ($ubicacions as $ubi) {
                $ubicacio_list .= " <a href='" . esc_url($url_base . "&nodes_ubicacio_inventari=" . $ubi->slug) . "'>" . esc_html($ubi->name) . "</a>,";
            }
            echo rtrim($ubicacio_list, ','); // remove last comma
        }
    }

}

// Define sortable
add_filter('manage_edit-nodes_inventari_sortable_columns', 'nodes_inventari_sortable_columns');

function nodes_inventari_sortable_columns($columns) {
    $columns['wps_post_id'] = 'ID';
    return $columns;
}

// Cerca de l'admin: inclou els camps SACE i número de sèrie
// La condició compartida pels tres filtres de sota
function nodes_inventari_is_cpt_search($query) {
    global $pagenow;

    return is_admin()
        && 'edit.php' === $pagenow
        && $query->is_main_query()
        && isset($_GET['post_type'])
        && 'nodes_inventari' === $_GET['post_type']
        && !empty($_GET['s']);
}

add_filter('posts_join', 'nodes_inventari_search_join', 10, 2);

function nodes_inventari_search_join($join, $query) {
    global $wpdb;

    if (nodes_inventari_is_cpt_search($query)) {
        $join .= " LEFT JOIN {$wpdb->postmeta}
                   ON {$wpdb->posts}.ID = {$wpdb->postmeta}.post_id
                   AND {$wpdb->postmeta}.meta_key IN ('sace', 'serialnumber') ";
    }

    return $join;
}

add_filter('posts_where', 'nodes_inventari_search_where', 10, 2);

function nodes_inventari_search_where($where, $query) {
    global $wpdb;

    if (nodes_inventari_is_cpt_search($query)) {
        $where = preg_replace(
            "/\(\s*" . $wpdb->posts . ".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(" . $wpdb->posts . ".post_title LIKE $1) OR ({$wpdb->postmeta}.meta_value LIKE $1)",
            $where
        );
    }

    return $where;
}

add_filter('posts_groupby', 'nodes_inventari_search_groupby', 10, 2);

function nodes_inventari_search_groupby($groupby, $query) {
    global $wpdb;

    if (nodes_inventari_is_cpt_search($query)) {
        $groupby = "{$wpdb->posts}.ID";
    }

    return $groupby;
}

// Dropdown filters with custom fields 

add_action('restrict_manage_posts', 'nodes_inventari_admin_posts_filter_restrict_manage_posts');

/**
 * First create the dropdown
 * @return void
 * @author Ohad Raz
 */
function nodes_inventari_admin_posts_filter_restrict_manage_posts() {

    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    // Only add filter to post type you want
    if ('nodes_inventari' === $type) {
        //change this to the list of values you want to show
        //in 'label' => 'value' format

        $terms = get_terms([
            'taxonomy' => 'nodes_estat_inventari',
            'hide_empty' => false,
        ]);
        $values = [];

        foreach ($terms as $term) {
            $values[$term->name] = $term->slug;
        }

        ?>
        <select name="nodes_estat_inventari">
            <option value=""><?php _e('Tots els estats', 'nodes'); ?></option>
            <?php
            $current_v = $_GET['nodes_estat_inventari'] ?? '';
            foreach ($values as $label => $value) {
                printf
                (
                    '<option value="%s"%s>%s</option>',
                    esc_attr($value),
                    $value === $current_v ? ' selected="selected"' : '',
                    esc_html($label)
                );
            }
            ?>
        </select>

        <?php
        $terms = get_terms([
            'taxonomy' => 'nodes_ubicacio_inventari',
            'hide_empty' => false,
        ]);
        $values = [];
        foreach ($terms as $term) {
            $values[$term->name] = $term->slug;
        }
        ?>

        <select name="nodes_ubicacio_inventari">
            <option value=""><?php _e('Totes les ubicacions', 'nodes'); ?></option>
            <?php
            $current_v = $_GET['nodes_ubicacio_inventari'] ?? '';
            foreach ($values as $label => $value) {
                printf
                (
                    '<option value="%s"%s>%s</option>',
                    esc_attr($value),
                    $value === $current_v ? ' selected="selected"' : '',
                    esc_html($label)
                );
            }
            ?>
        </select>

        <?php
        $terms = get_terms([
            'taxonomy' => 'nodes_ambit_inventari',
            'hide_empty' => false,
        ]);
        $values = [];

        foreach ($terms as $term) {
            $values[$term->name] = $term->slug;
        }
        ?>

        <select name="nodes_ambit_inventari">
            <option value=""><?php _e('Tots els tipus', 'nodes'); ?></option>
            <?php
            $current_v = $_GET['nodes_ambit_inventari'] ?? '';
            foreach ($values as $label => $value) {
                printf
                (
                    '<option value="%s"%s>%s</option>',
                    esc_attr($value),
                    $value === $current_v ? ' selected="selected"' : '',
                    esc_html($label)
                );
            }
            ?>
        </select>

        <?php
        $terms = get_terms([
            'taxonomy' => 'nodes_origen_inventari',
            'hide_empty' => false,
        ]);
        $values = [];

        foreach ($terms as $term) {
            $values[$term->name] = $term->slug;
        }
        ?>

        <select name="nodes_origen_inventari">
            <option value=""><?php _e('Tots els origens', 'nodes'); ?></option>
            <?php
            $current_v = $_GET['nodes_origen_inventari'] ?? '';
            foreach ($values as $label => $value) {
                printf
                (
                    '<option value="%s"%s>%s</option>',
                    esc_attr($value),
                    $value === $current_v ? ' selected="selected"' : '',
                    esc_html($label)
                );
            }
            ?>
        </select>
        <?php
    }

}


/**
 * if submitted filter by post meta
 *
 * @param  (wp_query object) $query
 *
 * @return Void
 * @author Ohad Raz
 */


add_filter('views_edit-nodes_inventari', 'nodes_inventari_quick_links_labels');

// TODO: Internacionalització
function nodes_inventari_quick_links_labels($views) {

    if (isset($views['trash'])) {
        $views['trash'] = str_replace('Paperera', 'Arxivades', $views['trash']);
    }

    if (isset($views['mine'])) {
        $views['mine'] = str_replace('Els meus', 'Les meves', $views['mine']);
    }

    return $views;

}