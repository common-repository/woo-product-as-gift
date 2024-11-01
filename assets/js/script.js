/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function () {
    jQuery("input[name='wcgw-apply-gift-options']").on('click', function () {
        action = "is_order_is_gift";
        var data = {
            'action': action,
        };
        jQuery.post(admin_url, data, function (response) {
            location.reload();
        });
    });
});


