<?php

if (!defined('ABSPATH')) {
    exit;
}

/* Export CSV */

function admin_inventari_list_add_export_button($which) {
    global $typenow;

    if ('nodes_inventari' === $typenow && 'top' === $which) {
        ?>
        <input type="submit" name="export_all_inventari" class="button export"
               value="<?php _e('Exporta'); ?>"/>
        <?php wp_nonce_field('nodes_export_inventari_' . get_current_user_id(), 'nodes_inventari_export_nonce'); ?>
        <?php
    }
}

add_action('manage_posts_extra_tablenav', 'admin_inventari_list_add_export_button', 20, 1);

// Neutralitza valors que Excel/LibreOffice/Sheets interpretarien com a fórmula (injecció CSV): els
// que comencen per tabulador o retorn de carro, i els que tenen =, +, - o @ com a primer caràcter
// visible, encara que hi hagi espais (també no separables) al davant, perquè alguns fulls de càlcul
// els ignoren.
function nodes_inventari_csv_safe($v) {
    if (is_string($v) && preg_match('/^(?:[\t\r]|(?:\s|\xC2\xA0)*[=+\-@])/', $v)) {
        return "'" . $v;
    }
    return $v;
}

function func_export_all_inventari() {

    if (!isset($_GET['export_all_inventari'])) {
        return;
    }

    // Cal poder crear entrades: és el permís que dona accés al llistat de l'inventari
    if (!is_user_logged_in() || !current_user_can('edit_posts')) {
        wp_die(
            __('No tens permisos per exportar.'),
            __('Accés denegat'),
            [
                'response'  => 403,
                'back_link' => true,
            ]
        );
    }

    check_admin_referer('nodes_export_inventari_' . get_current_user_id(), 'nodes_inventari_export_nonce');

    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    $arg = [
        'post_type' => 'nodes_inventari',
        'post_status' => ['publish', 'private', 'pending'],
        'posts_per_page' => -1,
    ];

        // Els professors (sense edit_others_posts) només exporten l'inventari que han creat ells
    if (!current_user_can('edit_others_posts')) {
        $arg['author'] = get_current_user_id();
    }

    global $post;
    $arr_post = get_posts($arg);

    if ($arr_post) {
        $previous_error_reporting = error_reporting(0);
        $data = date('d.m.Y');
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename="inventari-' . $data . '.csv"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $file = fopen('php://output', 'w');
        fwrite($file, "\xEF\xBB\xBF");

        fputcsv($file, [
            'ID',
            'Element',
            'Descripció',
            'SACE',
            'Núm.Sèrie',
            'Tipus',
            'Ubicació',
            'Estat',
            'Origen',
            'Visibilitat',
            'Autor'
        ]);

        foreach ($arr_post as $post) {
            setup_postdata($post);

            // Títol tal com està desat: get_the_title() aplica wptexturize i retorna entitats HTML (&#8217;)
            $element = $post->post_title;
            $descripcio = trim(strip_tags(get_the_content()));

            $sace = get_post_meta(get_the_ID(), 'sace', true);
            $serial_number = get_post_meta(get_the_ID(), 'serialnumber', true);

            // Tipus
            $amb = wp_get_post_terms(get_the_ID(), 'nodes_ambit_inventari', ['fields' => 'names']);
            if (!is_array($amb)) {
                $amb = [];
            }

            // Ubicacions
            $ubi = wp_get_post_terms(get_the_ID(), 'nodes_ubicacio_inventari', ['fields' => 'names']);
            if (!is_array($ubi)) {
                $ubi = [];
            }

            // Estats
            $sta = wp_get_post_terms(get_the_ID(), 'nodes_estat_inventari', ['fields' => 'names']);
            if (!is_array($sta)) {
                $sta = [];
            }

            // Orígens
            $ori = wp_get_post_terms(get_the_ID(), 'nodes_origen_inventari', ['fields' => 'names']);
            if (!is_array($ori)) {
                $ori = [];
            }

            // Visibilitat (publicat / privat / pendent) i autor
            $estats_pub = [
                'publish' => 'Publicat',
                'private' => 'Privat',
                'pending' => 'Pendent',
            ];
            $post_status = get_post_status(get_the_ID());
            $visibilitat = $estats_pub[$post_status] ?? $post_status;
            $autor = get_the_author_meta('display_name', $post->post_author);

            fputcsv($file, [
                get_the_ID(),
                nodes_inventari_csv_safe($element),
                nodes_inventari_csv_safe($descripcio),
                nodes_inventari_csv_safe($sace),
                nodes_inventari_csv_safe($serial_number),
                nodes_inventari_csv_safe(implode(',', $amb)),
                nodes_inventari_csv_safe(implode(',', $ubi)),
                nodes_inventari_csv_safe(implode(',', $sta)),
                nodes_inventari_csv_safe(implode(',', $ori)),
                nodes_inventari_csv_safe($visibilitat),
                nodes_inventari_csv_safe($autor)
            ]);
        }
        error_reporting($previous_error_reporting);
        exit();
    }

}

add_action('admin_init', 'func_export_all_inventari', 20);