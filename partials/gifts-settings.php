<?php
 echo do_shortcode("[codup_ads_top]"); ?>
<div class="wc-gifts-wrapper">
    <h2>Woocommerce Gifts Settings</h2>
    <?php
    $active_tab = "";
    if ( filter_input(INPUT_GET, 'page') == "wc-gift-settings" ) {
        if ( filter_input(INPUT_GET, 'tab') ) {
            $active_tab = wp_kses_post($_GET['tab']);
        }
    }

    require_once 'nav.php';
    switch ( $active_tab ) {
        case 'note':
            require_once 'note.php';
            break;
        default :
            require_once 'wrapping.php';
            break;
    }
    ?>
</div>