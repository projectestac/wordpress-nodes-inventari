<?php

/* Export CSV */

function admin_inventari_list_add_export_button($which) {
    global $typenow;

    if ('nodes_inventari' === $typenow && 'top' === $which) {
        ?>
        <input type="submit" name="export_all_inventari" class="button export"
               value="<?php _e('Exporta'); ?>"/>
        <?php
    }
}

add_action('manage_posts_extra_tablenav', 'admin_inventari_list_add_export_button', 20, 1);

function func_export_all_inventari() {

    if (isset($_GET['export_all_inventari'])) {
        $arg = [
            'post_type' => 'nodes_inventari',
            'post_status' => 'publish',
            'posts_per_page' => -1,
        ];

        global $post;
        $arr_post = get_posts($arg);

        if ($arr_post) {
            $data = date('d.m.Y');
            header('Content-type: text/csv');
            header('Content-Disposition: attachment; filename="inventari-' . $data . '.csv"');
            header('Pragma: no-cache');
            header('Expires: 0');

            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Element',
                'Descripció',
                'SACE',
                'Núm.Sèrie',
                'Tipus',
                'Ubicació',
                'Estat',
                'Origen'
            ]);

            foreach ($arr_post as $post) {
                setup_postdata($post);

                $element = get_the_title();
                $descripcio = trim(strip_tags(get_the_content()));

                $sace = get_post_meta(get_the_ID(), 'sace', true);
                $serial_number = get_post_meta(get_the_ID(), 'serialnumber', true);

                // Tipus
                $ambits = get_the_terms(get_the_ID(), 'nodes_ambit_inventari', '', ',');
                $amb = [];

                if (!empty($ambits)) {
                    foreach ($ambits as $ambit) {
                        $amb[] = $ambit->name;
                    }
                }

                // Ubicacions
                $ubicacions = get_the_terms(get_the_ID(), 'nodes_ubicacio_inventari', '', ',');
                $ubi = [];

                if (!empty($ubicacions)) {
                    foreach ($ubicacions as $ubicacio) {
                        $ubi[] = $ubicacio->name;
                    }
                }

                // Estats
                $estats = get_the_terms(get_the_ID(), 'nodes_estat_inventari', '', ',');
                $sta = [];

                if (!empty($estats)) {
                    foreach ($estats as $estat) {
                        $sta[] = $estat->name;
                    }
                }

                // Orígens
                $origens = get_the_terms(get_the_ID(), 'nodes_origen_inventari', '', ',');
                $ori = [];

                if (!empty($origens)) {
                    foreach ($origens as $origen) {
                        $ori[] = $origen->name;
                    }
                }

                fputcsv($file, [
                    $element,
                    $descripcio,
                    $sace,
                    $serial_number,
                    implode(',', $amb),
                    implode(',', $ubi),
                    implode(',', $sta),
                    implode(',', $ori)
                ]);
            }

            exit();
        }
    }

}

add_action('init', 'func_export_all_inventari');
