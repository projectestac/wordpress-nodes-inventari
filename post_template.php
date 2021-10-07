<?php

add_filter('the_content', 'add_info_in_nodes_inventari', 1);

function add_info_in_nodes_inventari($content) {

    if (is_singular('nodes_inventari')) {

        $id = get_the_ID();

        $new_content = $content . '<hr />';
        $new_content .= '<ul>';

        // SACE
        $sace = get_post_meta($id, 'sace', true);
        if ($sace) {
            $new_content .= '<li><strong>SACE:</strong>'. '&nbsp;'  . $sace . '</li>';
        }

        // Número de sèrie
        $serialnumber = get_post_meta($id, 'serialnumber', true);
        if ($serialnumber) {
            $new_content .= '<li><strong>Número de sèrie:</strong>'. '&nbsp;'  . $serialnumber . '</li>';
        }

        // Tipus
        $terms = get_terms([
            'taxonomy' => 'nodes_ambit_inventari',
            'object_ids' => $id,
            'hide_empty' => false,
        ]);

        $new_content .= '<li><strong>Tipus:</strong>'. '&nbsp;' ;
        $nodes_ambit = '';
        foreach ($terms as $term) {
            $nodes_ambit .= $term->name . ', ';
        }
        $new_content .= rtrim($nodes_ambit, ', ') . '</li>';

        // Ubicacions
        $terms = get_terms(array(
            'taxonomy' => 'nodes_ubicacio_inventari',
            'object_ids' => $id,
            'hide_empty' => false,
        ));

        $new_content .= '<li><strong>Ubicació:</strong>'. '&nbsp;' ;
        $nodes_ubicacions = '';
        foreach ($terms as $term) {
            $nodes_ubicacions .= $term->name . ', ';
        }
        $new_content .= rtrim($nodes_ubicacions, ', ') . '</li>';

        // Orígens
        $terms = get_terms([
            'taxonomy' => 'nodes_origens_inventari',
            'object_ids' => $id,
            'hide_empty' => false,
        ]);

        $new_content .= '<li><strong>Origen:</strong>'. '&nbsp;' ;
        $nodes_origens = '';
        foreach ($terms as $term) {
            $nodes_origens .= $term->name . ', ';
        }
        $new_content .= rtrim($nodes_origens, ', ') . '</li>';

        // Estats
        $terms = get_terms([
            'taxonomy' => 'nodes_estat_inventari',
            'object_ids' => $id,
            'hide_empty' => false,
        ]);

        $new_content .= '<li><strong>Estats:</strong>'. '&nbsp;' ;
        $nodes_estats = '';
        foreach ($terms as $term) {
            $nodes_estats .= $term->name . ', ';
        }
        $new_content .= rtrim($nodes_estats, ', ') . '</li>';

        // Informat per
        $new_content .= '<li><strong>Informat per:</strong>'. '&nbsp;'  . get_the_author() . '</li>';

        // Data
        $new_content .= '<li><strong>Data:</strong>'. '&nbsp;'  . get_the_date() . '</li>';
        $new_content .= '</ul>';

        return $new_content;
    }

    return $content;

}
