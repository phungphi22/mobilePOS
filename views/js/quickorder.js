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

            keyword: keyword

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

			$('#footer-total').text(formatPrice(s.total_paid));

		},

        error: function () {

            alert('Lỗi Summary');

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

    $('#footer-total').text(formatPrice(0));

    createCart();

    $('#keyword').focus();
}