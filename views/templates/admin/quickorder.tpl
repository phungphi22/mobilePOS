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
<!-- Begin Upgrade -->
	<div class="panel">
		<h3>
			<i class="icon-tag"></i>
			Giảm giá
		</h3>

		<div class="form-group">
			<label for="discount-amount">
				Số tiền giảm
			</label>

			<div class="input-group">
				<input
					type="number"
					id="discount-amount"
					class="form-control"
					min="0"
					step="1"
					value=""
					placeholder="Ví dụ: 50000">

				<span class="input-group-btn">
					<button
						type="button"
						id="btn-apply-discount"
						class="btn btn-primary">
						<i class="icon-check"></i>
						Áp dụng
					</button>
				</span>
			</div>

			<p class="help-block">
				Nhập số tiền giảm bằng VNĐ.
				Nhập 0 để bỏ giảm giá.
			</p>
		</div>
	</div>
<!-- End Upgrade -->
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

           

        </div>

    </div>

</div>

<div id="footer-bar">

    <div class="footer-actions">

        <button
            id="btn-order-list"
            type="button"
            class="btn btn-default btn-lg">
            <i class="icon-list"></i>
            Danh sách đơn
        </button>

        <button
            id="btn-create-order"
            type="button"
            class="btn btn-success btn-lg">
            <i class="icon-check"></i>
            Tạo đơn
        </button>

    </div>

</div>

</div>
<script>
var dtmobilepos_ajax = '{$ajax_url|escape:'javascript'}';
var dtmobilepos_orders_url = '{$orders_url|escape:'javascript'}';
</script>