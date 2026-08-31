<div id="dtmobilepos">

<div class="row">

    <!-- LEFT -->

    <div class="col-xs-12 col-md-6">

        <div class="panel">

            <h3>
                <i class="icon-search"></i>
                Tìm sản phẩm
            </h3>

            <div class="input-group">

                <input
                    id="keyword"
                    class="form-control"
                    autocomplete="off"
                    placeholder="Tên, mã hoặc EAN">

                <span class="input-group-btn">

                    <button
                        id="btn-search"
                        class="btn btn-primary">

                        <i class="icon-search"></i>

                    </button>

                </span>

            </div>

            <br>

            <div id="search-result"></div>

        </div>

    </div>

    <!-- RIGHT -->

    <div class="col-xs-12 col-md-6">

        <div class="panel">

            <h3>

                <i class="icon-shopping-cart"></i>

                Giỏ hàng

            </h3>

            <div id="cart-body">

                <div class="alert alert-info text-center">

                    Chưa có sản phẩm

                </div>

            </div>

        </div>

        <div class="panel">

            <table class="table">

                <tr>

                    <th>Tổng sản phẩm</th>

                    <td id="count-products">0</td>

                </tr>

                <tr>

                    <th>Tổng số lượng</th>

                    <td id="count-quantity">0</td>

                </tr>

                <tr>

                    <th>Tiền hàng</th>

                    <td id="total-products">0</td>

                </tr>

                <tr>

                    <th>Ship</th>

                    <td id="total-shipping">0</td>

                </tr>

                <tr>

                    <th>Giảm giá</th>

                    <td id="total-discounts">0</td>

                </tr>

                <tr>

                    <th>

                        <strong>Tổng</strong>

                    </th>

                    <td>

                        <strong id="total-paid">

                            0

                        </strong>

                    </td>

                </tr>

            </table>

        </div>

        <div class="panel">

            <h3>

                Thông tin đơn hàng

            </h3>

            <div class="form-group">

                <label>

                    Căn hộ

                </label>

                <input

                    id="apartment"

                    class="form-control"

                    placeholder="Ví dụ: Vin.A2.101">

            </div>

            <div class="form-group">

                <label>

                    Carrier

                </label>

                <select

                    id="id_carrier"

                    class="form-control">

                </select>

            </div>

        </div>

    </div>

</div>

<div id="footer-bar">

    <div class="row">

        <div class="col-xs-6">

            <h3 id="footer-total">

                0 đ

            </h3>

        </div>

        <div class="col-xs-6 text-right">

            <button

                id="btn-create-order"

                class="btn btn-success btn-lg">

                <i class="icon-check"></i>

                Tạo đơn

            </button>

        </div>

    </div>

</div>

</div>
<script>
var dtmobilepos_ajax = '{$ajax_url|escape:'javascript'}';
</script>