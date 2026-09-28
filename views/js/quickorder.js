$(function(){

    createCart();

    bindEvents();

});

function createCart()
{
    $.ajax({

        url: dtmobilepos_ajax,

        type:'POST',

        dataType:'json',

        data:{
            ajax:1,
            action:'CreateCart'
        },

        success:function(json){

            console.log('CreateCart', json);

            if(!json.success){

                alert(json.message);

                return;

            }

            window.dtCartId = json.data.id_cart;

        },

        error:function(xhr){

            console.log(xhr.responseText);

            alert('CreateCart lỗi');

        }

    });
}

function bindEvents()
{
    $('#btn-search').off('click').on('click', function () {

        searchProduct();

    });

    $('#keyword').off('keypress').on('keypress', function (e) {

        if (e.which == 13) {

            searchProduct();

        }

    });
	
	$('#btn-create-order').off('click').on('click', function () {

		createOrder();

	});
	
	$('#btn-order-list').off('click').on('click', function () {
		goToOrderList();
	});
	
	$('#btn-apply-discount').off('click').on('click', function () {

		setDiscount();

	});
	$('#discount-amount').off('keypress').on('keypress', function (e) {

		if (e.which == 13) {

			setDiscount();

		}

	});
}

function goToOrderList()
{
    if (!window.dtCartId) {
        window.location.href = dtmobilepos_orders_url;
        return;
    }

    var $button = $('#btn-order-list');

    $button.prop('disabled', true);

    $.ajax({
        url: dtmobilepos_ajax,
        type: 'POST',
        dataType: 'json',
        data: {
            ajax: 1,
            action: 'DeleteCart',
            id_cart: window.dtCartId
        },
        success: function (json) {
            if (!json.success) {
                alert(json.message || 'Không thể xóa giỏ hàng.');
                $button.prop('disabled', false);
                return;
            }

            window.location.href = dtmobilepos_orders_url;
        },
        error: function (xhr) {
            console.log('DeleteCart error:', xhr.responseText);
            alert('Không thể kết nối để xóa giỏ hàng.');
            $button.prop('disabled', false);
        }
    });
}

function searchProduct()
{
    var keyword = $.trim($('#keyword').val());

    if (keyword == '') {

        return;

    }

    $('#search-result').html(
        '<div class="text-center"><i class="icon-refresh icon-spin"></i> Đang tìm...</div>'
    );

    $.ajax({

        url: dtmobilepos_ajax,

        type: 'POST',

        dataType: 'json',

       data: {
			ajax: 1,
			action: 'SearchProduct',
			keyword: keyword,
			id_cart: window.dtCartId || 0
		},

        success: function (json) {

            console.log(json);

            drawProducts(json);

        },

        error: function (xhr) {

            console.log(xhr.responseText);

        }

    });

}

function addProduct(idProduct)
{
    $.ajax({

        url: dtmobilepos_ajax,

        type: 'POST',

        dataType: 'json',

        data: {

            ajax: 1,

            action: 'AddProduct',

            id_cart: window.dtCartId || 0,

            id_product: idProduct,

            quantity: 1

        },

        success: function (json) {

            if (!json.success) {

                alert(json.message);

                return;

            }

           

            /**************
			refreshCart();
			**************/

			refreshSummary();
			searchProduct();
        },

        error: function () {

            alert('Lỗi AJAX AddProduct');

        }

    });

}

function refreshCart()
{
    $.ajax({

        url: dtmobilepos_ajax,

        type: 'POST',

        dataType: 'json',

        data: {

            ajax: 1,

            action: 'GetCart',

            id_cart: window.dtCartId

        },

        success: function (json) {

            console.log('Cart', json);

            // Tạm thời để test.
            // PATCH sau sẽ render giỏ hàng.

        },

        error: function () {

            alert('Lỗi GetCart');

        }

    });
}

function refreshSummary()
{
    $.ajax({

        url: dtmobilepos_ajax,

        type: 'POST',

        dataType: 'json',

        data: {

            ajax: 1,

            action: 'Summary',

            id_cart: window.dtCartId

        },

        success:function(json){

			if(!json.success){

				return;

			}

			var s = json.data;
			
			
			
			renderCart(s.products);

			$('#count-products').text(s.count_products);

			$('#count-quantity').text(s.count_quantity);

			$('#total-products').text(formatPrice(s.total_products));

			$('#total-shipping').text(formatPrice(s.total_shipping));

			$('#total-discounts').text(formatPrice(s.total_discounts));

			$('#total-paid').text(formatPrice(s.total_paid));

			

		},

        error: function () {

            alert('Lỗi Summary');

        }

    });
}

function setDiscount()
{
    var value = $.trim(
        $('#discount-amount').val()
    );

    /*
     * Nếu để trống thì coi như không giảm.
     */
    if (value === '') {
        value = '0';
    }

    /*
     * Chuyển sang số.
     */
    var amount = parseFloat(value);

    /*
     * Kiểm tra dữ liệu nhập.
     */
    if (isNaN(amount) || amount < 0) {

        alert('Số tiền giảm không hợp lệ.');

        $('#discount-amount').focus();

        return;
    }

    /*
     * Không cho số tiền giảm có quá nhiều chữ số thập phân.
     * Đây là tiền VNĐ nên chỉ dùng số nguyên.
     */
    amount = Math.round(amount);

    /*
     * Kiểm tra Cart hiện tại.
     */
    if (!window.dtCartId) {

        alert('Chưa có giỏ hàng.');

        return;
    }

    var $button = $('#btn-apply-discount');

    /*
     * Khóa nút trong lúc gửi AJAX.
     *
     * Rất quan trọng khi dùng trên mobile:
     * tránh người dùng chạm 2-3 lần liên tiếp
     * và tạo nhiều yêu cầu giảm giá.
     */
    $button.prop('disabled', true);

    $.ajax({

        url: dtmobilepos_ajax,

        type: 'POST',

        dataType: 'json',

        data: {

            ajax: 1,

            action: 'SetDiscount',

            id_cart: window.dtCartId,

            discount_amount: amount

        },

        success: function (json) {

            if (!json.success) {

                alert(
                    json.message ||
                    'Không thể áp dụng giảm giá.'
                );

                return;
            }

            /*
             * KHÔNG tự tính:
             *
             * total = total - amount
             *
             * Vì tổng tiền phải do Cart của
             * PrestaShop tính.
             */
            refreshSummary();

        },

        error: function (xhr) {

            console.log(
                'SetDiscount error:',
                xhr.responseText
            );

            alert(
                'Không thể kết nối để áp dụng giảm giá.'
            );

        },

        complete: function () {

            /*
             * Cho phép bấm lại sau khi AJAX xong.
             */
            $button.prop('disabled', false);

        }

    });
}

function renderCart(products)
{
    console.log(products);
	var html = '';

    if (!products || products.length === 0) {
        $('#cart-body').html('<div class="alert alert-info text-center">Chưa có sản phẩm</div>');
        return;
    }

    $.each(products, function(i, p) {

        html += '<div class="panel panel-default">';
		html += '<div class="panel-body">';
		html += '<div class="row">';

		html += '<div class="col-xs-3">';

		if (p.image) {

			html += '<img class="img-responsive img-thumbnail" src="' + p.image + '">';

		}

		html += '</div>';

		html += '<div class="col-xs-9">';

		html += '<strong>' + p.name + '</strong><br>';

		html += '<div class="btn-group" style="margin:6px 0;">';

		html += '<button class="btn btn-default btn-qty-minus"';
		html += ' data-id="' + p.id_product + '"';
		html += ' data-qty="' + p.quantity + '">-</button>';

		html += '<span class="btn btn-default disabled">' + p.quantity + '</span>';

		html += '<button class="btn btn-default btn-qty-plus"';
		html += ' data-id="' + p.id_product + '"';
		html += ' data-qty="' + p.quantity + '">+</button>';

		html += '</div><br>';

		html += 'Đơn giá : ' + formatPrice(p.unit_price_tax_incl) + '<br>';

		html += '<strong>' + formatPrice(p.total_price_tax_incl) + '</strong>';
		
		html += '<br><br>';

html += '<button';

html += ' class="btn btn-danger btn-remove-product"';

html += ' data-id="' + p.id_product + '"';

html += '>';

html += '<i class="icon-trash"></i> Xóa';

html += '</button>';

		html += '</div>';

		html += '</div>';
		html += '</div>';
		html += '</div>';

    });

    $('#cart-body').html(html);
}

function drawProducts(json)
{
    if (!json.success) {

        $('#search-result').html(
            '<div class="alert alert-warning">'+json.message+'</div>'
        );

        return;

    }

    var html='';

    $.each(json.data,function(i,p){

        html+='<div class="panel panel-default">';

        html+='<div class="panel-body">';

        html+='<div class="row">';

        html+='<div class="col-xs-3">';

        if(p.image){

            html+='<img class="img-responsive" src="'+p.image+'">';

        }

        html+='</div>';

        html+='<div class="col-xs-7">';

        html+='<strong>'+p.name+'</strong><br>';

        html+='Giá : '+formatPrice(p.price)+'<br>';

        html+='Kho : '+p.quantity;

        html+='</div>';

        html+='<div class="col-xs-2 text-right">';

        html+='<button';

        html+=' class="btn btn-success btn-add-product"';

        html+=' data-id="'+p.id_product+'">';

        html+='<i class="icon-plus"></i>';

        html+='</button>';

        html+='</div>';

        html+='</div>';

        html+='</div>';

        html+='</div>';

    });

    $('#search-result').html(html);

}

function formatPrice(price)
{
    return Number(price).toLocaleString('vi-VN')+' đ';
}

$(document).on(
    'click',
    '.btn-add-product',
    function () {

        addProduct($(this).data('id'));

    }
);

$(document).on('click', '.btn-qty-plus', function () {

    updateQuantity(
        $(this).data('id'),
        parseInt($(this).data('qty'), 10) + 1
    );

});

$(document).on('click', '.btn-qty-minus', function () {

    var qty = parseInt($(this).data('qty'), 10) - 1;

    if (qty < 1) {
        qty = 1;
    }

    updateQuantity(
        $(this).data('id'),
        qty
    );

});

function updateQuantity(idProduct, quantity)
{
    $.ajax({

        url: dtmobilepos_ajax,

        type: 'POST',

        dataType: 'json',

        data: {

            ajax: 1,

            action: 'UpdateQuantity',

            id_cart: window.dtCartId,

            id_product: idProduct,

            quantity: quantity

        },

        success: function (json) {

            if (!json.success) {

                alert(json.message);

                return;

            }

            refreshSummary();
			
        },

        error: function () {

            alert('Lỗi UpdateQuantity');

        }

    });
}

$(document).on(
    'click',
    '.btn-remove-product',
    function () {

        if (!confirm('Xóa sản phẩm này?')) {

            return;

        }

        removeProduct(
            $(this).data('id')
        );

    }
);

function removeProduct(idProduct)
{
    $.ajax({

        url: dtmobilepos_ajax,

        type:'POST',

        dataType:'json',

        data:{

            ajax:1,

            action:'RemoveProduct',

            id_cart:window.dtCartId,

            id_product:idProduct

        },

        success:function(json){

            if(!json.success){

                alert(json.message);

                return;

            }

            refreshSummary();
			searchProduct();
        },

        error:function(){

            alert('Lỗi RemoveProduct');

        }

    });
}

function createOrder()
{
    $.ajax({

        url: dtmobilepos_ajax,

        type: 'POST',

        dataType: 'json',

        data: {

            ajax: 1,

            action: 'CreateOrder',

            id_cart: window.dtCartId,

            apartment: $('#apartment').val()

        },

        success: function (json) {
			 console.log(json);
            if (!json.success) {

                alert(json.message);

                return;

            }

            alert('Đã tạo đơn hàng #' + json.data.id_order);

            resetPOS();

        },

        error: function (xhr) {

            //alert('Lỗi CreateOrder');
			 console.log(xhr.responseText);

    alert(xhr.responseText);

        }

    });
}

function resetPOS()
{
    $('#keyword').val('');

    $('#search-result').html('');

    $('#cart-body').html(
        '<div class="alert alert-info text-center">Chưa có sản phẩm</div>'
    );

    $('#count-products').text('0');

    $('#count-quantity').text('0');

    $('#total-products').text(formatPrice(0));

    $('#total-shipping').text(formatPrice(0));

    $('#total-discounts').text(formatPrice(0));

    $('#total-paid').text(formatPrice(0));

   

    createCart();

    $('#keyword').focus();
}