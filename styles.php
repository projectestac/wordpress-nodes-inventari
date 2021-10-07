<?php

add_action('admin_head', 'nodes_inventari_css');

function nodes_inventari_css() {

    echo '<style>';
    echo '.column-wps_post_id {width:70px !important}';
    echo '#A2A_SHARE_SAVE_meta {display:none}';
    echo '.post-type-nodes_inventari #addtag div.term-slug-wrap {display:none}';
    echo '.lbl-sace {display:block;width:300px}';
    echo '
        .identificadors {
            display: grid;
            grid-template-columns: 200px 400px;
            grid-template-rows: repeat( 2, 1fr );
            grid-auto-flow: column;
          }';
    echo '.lbl-sace {
            grid-column-start: 1;
            grid-column-end: 1;
            grid-row-start: 1;
            grid-row-end: 1;
          }';
    echo '.lbl-serialnumber {
            grid-column-start: 1;
            grid-column-end: 1;
            grid-row-start: 2;
            grid-row-end: 2;
          }';

    // Nodes1 bug
    echo '.post-type-nodes_inventari .tablenav select {min-width: inherit;}';

    if (!current_user_can('activate_plugins')) {
        echo '#nodes_estat_inventari_div {display:none}';
    }

    echo '</style>';

}
