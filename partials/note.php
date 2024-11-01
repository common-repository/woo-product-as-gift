<?php echo do_shortcode("[codup_ads_top]"); ?>
<div class="wcgw-gifts-wrapper-section">
    <form class="wcgw-gifts-form" method="post">
        <table class="form-table">
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label>Charge Fee for message</label>
                </th>
                <td>
                    <input name="wcgw-charge-fee-message"<?php echo esc_attr(get_option("wcgw-charge-fee-message")) ? " checked" : ""; ?> type="checkbox" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label>Charges for message</label>
                </th>
                <td>
                    <input name="wcgw-message-charges" value="<?php echo esc_attr( get_option("wcgw-message-charges")); ?>" type="text" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label>Change Label for "Add a message with gift"</label>
                </th>
                <td>
                    <input name="wcgw-add-message" type="text" value="<?php echo esc_attr(get_option("wcgw-add-message")); ?>" />
                </td>
            </tr>
        </table>
        <input type="submit" class="button button-primary" name="wcgw_note_submit" value="Apply">
    </form>
</div>