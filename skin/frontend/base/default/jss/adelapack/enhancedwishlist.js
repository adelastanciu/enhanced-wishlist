jQuery.noConflict();
jQuery(function ($) {
    $(document).ready(function () {
        var ajaxSucceeded = false;

        $('#modal-content').on('hide.bs.modal', function () {
            if (!ajaxSucceeded) {
                return false;
            }
        });

        $(".link-wishlist").on("click", function () {
            ajaxSucceeded = false;
            var _this = $(this),
                productId = _this.attr('data-product-id'),
                data = null,
                url = "";
            if (_this.hasClass('move-to-wishlist')) {
                url = _this.siblings("input").val()
            } else {
                url = _this.next(".wishlistUrl").val();
            }

            if (productId) {
                data = $("#product_addtocart_form_" + productId).serializeArray();
            } else {
                data = $("#product_addtocart_form").serializeArray();
            }

            var params = '&isAjax=1';
            $.each(data, function(key, value) {
                params += '&' + key + '=' + value;
            });

            $("#modal-content").modal("show");
            $("#waiting").show();
            $("#response").hide();

            $.ajax({
                url: url,
                dataType: "json",
                type: "post",
                data: params,
                success: function (data) {
                    ajaxSucceeded = true;
                    if (data.status == "REDIRECT") {
                        window.location = baseUrl + "customer/account/login/";
                        return;
                    }
                    $("#waiting").hide();
                    $("#response").html(data.message).show();
                }
            });
        });
    });

    $('#modal-content').on('click', '.close-modal', function () {
        $("#modal-content").modal("hide");
    });
});