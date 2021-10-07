<?php

// Show custom data in "Inventari digital" admin ui

add_filter('manage_nodes_inventari_posts_columns', 'nodes_inventari_admin_ui_columns');

function nodes_inventari_admin_ui_columns($columns) {
    return [
        'cb' => $columns['cb'],
        'wps_post_id' => __('ID'),
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

    if ('sace' === $column) {
        echo get_post_meta($post_id, 'sace', true);
    }

    if ('serialnumber' === $column) {
        echo get_post_meta($post_id, 'serialnumber', true);
    }

    if ('wps_post_id' === $column) {
        echo $post_id;
    }

    if ('estat' === $column) {
        $url_base = 'edit.php?post_type=nodes_inventari';
        $estats = get_the_terms($post_id, 'nodes_estat_inventari');

        if (is_array($estats)) {
            $estats_list = '';
            foreach ($estats as $estat) {
                $estats_list .= " <a href='" . $url_base . "&nodes_estat_inventari=" . $estat->slug . "'>" . $estat->name . "</a>,";
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
                $ambits_list .= " <a href='" . $url_base . "&nodes_ambit_inventari=" . $ambit->slug . "'>" . $ambit->name . "</a>,";
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
                $origens_list .= " <a href='" . $url_base . "&nodes_origen_inventari=" . $origen->slug . "'>" . $origen->name . "</a>,";
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
                $ubicacio_list .= " <a href='" . $url_base . "&nodes_ubicacio_inventari=" . $ubi->slug . "'>" . $ubi->name . "</a>,";
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

// Pre-selected "Oberta"
add_filter('wp_terms_checklist_args', function ($args, $post_id) {

    if ($args['taxonomy'] !== 'nodes_estat_inventari') {
        return $args;
    }

    // Only do this for new posts, i.e. doesn't overwrite a post that has already been saved
    if (isset($_GET['post'])) {
        return $args;
    }

    $estat = get_term_by('name', 'Oberta', 'nodes_estat_inventari');
    $args['selected_cats'][] = $estat->term_id;

    return $args;

}, 10, 2);

add_filter('posts_join', 'nodes_inventari_search_join');

function nodes_inventari_search_join($join) {

    global $pagenow, $wpdb;

    // I want the filter only when performing a search on edit page of Custom Post Type named "nodes_inventari".
    if (is_admin() && 'edit.php' === $pagenow && 'nodes_inventari' === $_GET['post_type'] && !empty($_GET['s'])) {
        $join .= 'LEFT JOIN ' . $wpdb->postmeta . ' ON ' . $wpdb->posts . '.ID = ' . $wpdb->postmeta . '.post_id ';
    }

    return $join;

}

add_filter('posts_where', 'nodes_inventari_search_where');

function nodes_inventari_search_where($where) {

    global $pagenow, $wpdb;

    // I want the filter only when performing a search on edit page of Custom Post Type named "nodes_inventari".
    if (is_admin() && 'edit.php' === $pagenow && 'nodes_inventari' === $_GET['post_type'] && !empty($_GET['s'])) {
        $where = preg_replace(
            "/\(\s*" . $wpdb->posts . ".post_title\s+LIKE\s*(\'[^\']+\')\s*\)/",
            "(" . $wpdb->posts . ".post_title LIKE $1) OR (" . $wpdb->postmeta . ".meta_value LIKE $1)", $where);
        $where .= " GROUP BY {$wpdb->posts}.id"; // Solves duplicated results
    }

    return $where;

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
    if ('nodes_inventari' == $type) {
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
                    $value,
                    $value == $current_v ? ' selected="selected"' : '',
                    $label
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
                    $value,
                    $value == $current_v ? ' selected="selected"' : '',
                    $label
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
                    $value,
                    $value == $current_v ? ' selected="selected"' : '',
                    $label
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
                    $value,
                    $value == $current_v ? ' selected="selected"' : '',
                    $label
                );
            }
            ?>
        </select>
        <?php
    }

}

add_filter('parse_query', 'nodes_inventari_posts_filter');

/**
 * if submitted filter by post meta
 *
 * @param  (wp_query object) $query
 *
 * @return Void
 * @author Ohad Raz
 */
function nodes_inventari_posts_filter($query) {

    global $pagenow;
    $type = 'post';

    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    if ('nodes_inventari' == $type && is_admin() && $pagenow == 'edit.php'
        && isset($_GET['ADMIN_FILTER_FIELD_VALUE']) && $_GET['ADMIN_FILTER_FIELD_VALUE'] != '') {
        $query->query_vars['meta_key'] = 'nodes_estat_inventari';
        $query->query_vars['meta_value'] = $_GET['ADMIN_FILTER_FIELD_VALUE'];
    }

    if ('nodes_inventari' == $type && is_admin() && $pagenow == 'edit.php'
        && isset($_GET['ADMIN_FILTER_FIELD_VALUE']) && $_GET['ADMIN_FILTER_FIELD_VALUE'] != '') {
        $query->query_vars['meta_key'] = 'nodes_ubicacio_inventari';
        $query->query_vars['meta_value'] = $_GET['ADMIN_FILTER_FIELD_VALUE'];
    }

    if ('nodes_inventari' == $type && is_admin() && $pagenow == 'edit.php'
        && isset($_GET['ADMIN_FILTER_FIELD_VALUE']) && $_GET['ADMIN_FILTER_FIELD_VALUE'] != '') {
        $query->query_vars['meta_key'] = 'nodes_origens_inventari';
        $query->query_vars['meta_value'] = $_GET['ADMIN_FILTER_FIELD_VALUE'];
    }

}

add_filter('views_edit-nodes_inventari', 'nodes_inventari_quick_links_labels');

// TODO: Internacionalització
function nodes_inventari_quick_links_labels($views) {
    $views['trash'] = str_replace('Paperera', 'Arxivades', $views['trash']);
    $views['mine'] = str_replace('Els meus', 'Les meves', $views['mine']);

    return $views;
}
