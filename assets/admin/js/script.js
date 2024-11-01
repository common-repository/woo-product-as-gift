/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function () {
    jQuery("body").on("click", ".dashicons", function () {
        var icon = jQuery(this);
        if (jQuery(icon).hasClass("dashicons-plus") === true) {
            icon.prev().find("input").val("");
            icon.parent().clone(true).hide().appendTo(icon.parent().parent()).show(1000);
            icon.prev().remove();
            icon.parent().next().find("input").val("");
            var noImage = icon.parent().next().find("input[type='hidden']").attr("data-image");
            icon.parent().next().find("img").attr("src", noImage);
            icon.removeClass("dashicons-plus").addClass("dashicons-minus");
        } else {
            if (jQuery(icon).hasClass("last") === true) {
                icon.parent().prev().append('<p class="dashicons dashicons-plus"></p>');
                icon.parent().prev().find(".dashicons-minus").addClass("last");
            }
            icon.parent().hide(1000, function(){
                jQuery(this).remove();
            });
        }
    });
    jQuery(".upload_image_button").click(function () {
        var send_attachment_bkp = wp.media.editor.send.attachment;
        var button = jQuery(this);
        wp.media.editor.send.attachment = function (props, attachment) {
            console.log(attachment);
            jQuery(button).parent().prev().attr("src", attachment.url);
            jQuery(button).prev().val(attachment.id);
            wp.media.editor.send.attachment = send_attachment_bkp;
        }
        wp.media.editor.open(button);
        return false;
    });
});


