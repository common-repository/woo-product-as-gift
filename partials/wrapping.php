<?php echo do_shortcode("[codup_ads_top]"); ?>
<div class="wcgw-gifts-wrapper-section">
    <form class="wcgw-gifts-form" method="post">
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    <label>Enable Gift Options</label>
                    <span class="help-notice" title="Enable gift options"></span>
                </th>
                <td>
                    <input name="wcgw-enable-gift-options"<?php echo get_option("wcgw-enable-gift-options") ? " checked" : ""; ?> type="checkbox" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label>Change Label for "Is this gift"</label>
                </th>
                <td>
                    <input name="wcgw-is-this-gift" value="<?php echo esc_attr(get_option("wcgw-is-this-gift")); ?>" type="text" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <label>Add wrapping style</label>
                </th>
                <td>
                    <?php
                    $styles = get_option("wcgw-wrapping-style");
                    $no_image = WC_GIFTS_URL . "assets/admin/img/no-image.jpg";
                    if ( !empty($styles) ) {
                        $styles = json_decode($styles, true);
                        $total_styles = count($styles);
                        $x = 0;
                        foreach ( $styles as $style ) {
                            $x++;
                            $class = "dashicons-minus";
                            if ( $x == $total_styles ) {
                                $class = "dashicons-plus";
                            }
                            ?>
                            <div class="wcgw-wrapping-style">
                                <div class="wcgw-wrapping-style-wrapper">
                                    <input name="wcgw-wrapping-style[name][]" value="<?php echo esc_attr($style["name"]); ?>" placeholder="Title" type="text" />
                                    <input name="wcgw-wrapping-style[price][]" value="<?php echo esc_attr($style["price"]); ?>" placeholder="Price" type="text" />
                                    <div class="upload">
                                        <?php
                                        if ( !empty($style["image"]) ) {
                                            $image = wp_get_attachment_thumb_url($style["image"]);
                                            ?>
                                            <img src="<?php echo esc_url($image); ?>" width="150" />
                                            <?php
                                        }
                                        else {
                                            ?>
                                            <img src="<?php echo esc_url($no_image);  ?>" width="150" />
                                            <?php
                                        }
                                        ?>
                                        <div>
                                            <input name="wcgw-wrapping-style[image][]" data-image='<?php echo esc_attr($no_image); ?>' value="<?php esc_attr($style["image"]); ?>" type="hidden" />
                                            <button type="button" class="upload_image_button button">Upload</button>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                if ( $x == $total_styles ) {
                                    ?>
                                    <p class="dashicons dashicons-minus last"></p>
                                    <?php
                                }
                                ?>
                                <p class="dashicons <?php esc_attr($class); ?>"></p>
                            </div>
                            <?php
                        }
                    }
                    else {
                        ?>
                        <div class="wcgw-wrapping-style">
                            <div class="wcgw-wrapping-style-wrapper">
                                <input name="wcgw-wrapping-style[name][]" value="" placeholder="Title" type="text" />
                                <input name="wcgw-wrapping-style[price][]" value="" placeholder="Price" type="text" />
                                <div class="upload">
                                    <img data-src="" src="<?php echo esc_url($no_image); ?>" width="150" />
                                    <div>
                                        <input name="wcgw-wrapping-style[image][]" value="" type="hidden" />
                                        <button type="button" class="upload_image_button button">Upload</button>
                                    </div>
                                </div>
                            </div>
                            <p class="dashicons dashicons-plus"></p>
                        </div>
                        <?php
                    }
                    ?>
                </td>
            </tr>
        </table>
        <input type="submit" class="button button-primary" name="wcgw_submit" value="Apply">
    </form>
</div>