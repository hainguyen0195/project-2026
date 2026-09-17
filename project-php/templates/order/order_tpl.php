<div class="container checkout-page-new">
    <form class="form-cart validation-cart" novalidate method="post" action="" enctype="multipart/form-data">
        <div class="wrap-cart">
            <?= $flash->getMessages("frontend") ?>

            <?php if (!empty($_SESSION['cart'])) { ?>
                <?php
                $cartProductCount = 0;
                foreach ($_SESSION['cart'] as $cartItem) {
                    $cartProductCount += (int) $cartItem['qty'];
                }
                $flashPayment = $flash->get('payments');
                ?>

                <div class="checkout-layout-new">
                    <section class="checkout-customer-new">
                        <h1 class="checkout-heading-new">THÔNG TIN KHÁCH HÀNG</h1>

                        <div class="checkout-fields-new">
                            <div class="input-cart checkout-field-new">
                                <label for="fullname">Họ và tên <span>*</span></label>
                                <input type="text" class="form-control text-sm" id="fullname" name="dataOrder[fullname]" placeholder="Nhập họ và tên" value="<?= (!empty($flash->has('fullname'))) ? $flash->get('fullname') : '' ?>" required>
                                <div class="invalid-feedback"><?= vuilongnhaphoten ?></div>
                            </div>

                            <div class="input-cart checkout-field-new">
                                <label for="phone">Số điện thoại <span>*</span></label>
                                <input type="number" class="form-control text-sm" id="phone" name="dataOrder[phone]" placeholder="Nhập số điện thoại" value="<?= (!empty($flash->has('phone'))) ? $flash->get('phone') : '' ?>" required>
                                <div class="invalid-feedback"><?= vuilongnhapsodienthoai ?></div>
                            </div>

                            <div class="input-cart checkout-field-new">
                                <label for="email">Địa chỉ email (tùy chọn)</label>
                                <input type="email" class="form-control text-sm" id="email" name="dataOrder[email]" placeholder="Nhập địa chỉ Email" value="<?= (!empty($flash->has('email'))) ? $flash->get('email') : '' ?>">
                                <div class="invalid-feedback"><?= vuilongnhapdiachiemail ?></div>
                            </div>

                            <div class="input-cart checkout-field-new">
                                <label for="city">Tỉnh/Thành phố <span>*</span></label>
                                <select class="select-city-cart form-select form-control text-sm" required id="city" name="dataOrder[city]">
                                    <option value="">Chọn tỉnh/thành phố</option>
                                    <?php foreach ($city as $k => $v) { ?>
                                        <option value="<?= $v['id'] ?>"><?= $v['name'] ?></option>
                                    <?php } ?>
                                </select>
                                <div class="invalid-feedback"><?= vuilongchontinhthanh ?></div>
                            </div>

                            <div class="input-cart checkout-field-new">
                                <label for="district">Quận huyện <span>*</span></label>
                                <select class="select-district-cart select-district form-select form-control text-sm" required id="district" name="dataOrder[district]">
                                    <option value="">Chọn quận/huyện</option>
                                </select>
                                <div class="invalid-feedback"><?= vuilongchonquanhuyen ?></div>
                            </div>

                            <div class="input-cart checkout-field-new">
                                <label for="ward">Xã/Phường/Thị trấn <span>*</span></label>
                                <select class="select-ward-cart select-ward form-select form-control text-sm" required id="ward" name="dataOrder[ward]">
                                    <option value="">Chọn xã/phường/thị trấn</option>
                                </select>
                                <div class="invalid-feedback"><?= vuilongchonphuongxa ?></div>
                            </div>

                            <div class="input-cart checkout-field-new checkout-field-wide-new">
                                <label for="address">Địa chỉ <span>*</span></label>
                                <input type="text" class="form-control text-sm" id="address" name="dataOrder[address]" placeholder="Tòa nhà, số nhà, tên đường" value="<?= (!empty($flash->has('address'))) ? $flash->get('address') : '' ?>" required>
                                <div class="invalid-feedback"><?= vuilongnhapdiachi ?></div>
                            </div>
                        </div>

                        <h2 class="checkout-subheading-new">Thông tin bổ sung</h2>
                        <div class="input-cart checkout-field-new checkout-note-new">
                            <label for="requirements">Ghi chú, thông tin xuất hóa đơn (tùy chọn)</label>
                            <textarea class="form-control text-sm" id="requirements" name="dataOrder[requirements]" placeholder="Vui lòng ghi chú thời gian giao hàng và/hoặc thông tin xuất hóa đơn VAT (tên công ty, MST, địa chỉ)."><?= (!empty($flash->has('requirements'))) ? $flash->get('requirements') : '' ?></textarea>
                        </div>

                        <h2 class="checkout-heading-new checkout-payment-heading-new">PHƯƠNG THỨC THANH TOÁN</h2>
                        <div class="information-cart checkout-payments-new">
                            <?php foreach ($payments_info as $key => $value) { ?>
                                <?php $paymentChecked = (!empty($flashPayment) && $flashPayment == $value['id']) || (empty($flashPayment) && $key === 0); ?>
                                <div class="payments-cart form-check">
                                    <input type="radio" class="form-check-input" id="payments-<?= $value['id'] ?>" name="dataOrder[payments]" value="<?= $value['id'] ?>" <?= $paymentChecked ? 'checked' : '' ?> required>
                                    <label class="payments-label form-check-label <?= $paymentChecked ? 'active' : '' ?>" for="payments-<?= $value['id'] ?>" data-payments="<?= $value['id'] ?>"><?= $value['name' . $lang] ?></label>
                                    <div class="payments-info payments-info-<?= $value['id'] ?> transition"><?= str_replace("\n", "<br>", $value['desc' . $lang]) ?></div>
                                </div>
                            <?php } ?>
                        </div>
                    </section>

                    <aside class="checkout-order-new">
                        <div class="checkout-products-new">
                            <?php foreach ($_SESSION['cart'] as $cartItem) {
                                $pid = $cartItem['productid'];
                                $quantity = (int) $cartItem['qty'];
                                $color = !empty($cartItem['color']) ? $cartItem['color'] : 0;
                                $size = !empty($cartItem['size']) ? $cartItem['size'] : 0;
                                $code = !empty($cartItem['code']) ? $cartItem['code'] : '';
                                $proinfo = $cart->getProductInfo($pid);
                                $pro_price = $proinfo['regular_price'];
                                $pro_price_new = $proinfo['sale_price'];
                                $pro_price_qty = $pro_price * $quantity;
                                $pro_price_new_qty = $pro_price_new * $quantity;
                            ?>
                                <article class="procart procart-<?= $code ?> checkout-product-new">
                                    <a class="checkout-product-image-new" href="<?= $proinfo[$sluglang] ?>" target="_blank" title="<?= $proinfo['name' . $lang] ?>">
                                        <?= $func->getImage(['sizes' => '110x110x2', 'upload' => UPLOAD_PRODUCT_L, 'image' => $proinfo['photo'], 'alt' => $proinfo['name' . $lang]]) ?>
                                    </a>

                                    <div class="checkout-product-info-new">
                                        <h3 class="name-procart"><a href="<?= $proinfo[$sluglang] ?>" target="_blank" title="<?= $proinfo['name' . $lang] ?>"><?= $proinfo['name' . $lang] ?></a></h3>
                                        <div class="properties-procart">
                                            <?php if ($color) {
                                                $color_detail = $d->rawQueryOne("select name$lang from #_color where type = ? and id = ? limit 0,1", [$proinfo['type'], $color]); ?>
                                                <span>Màu: <strong><?= $color_detail['name' . $lang] ?></strong></span>
                                            <?php } ?>
                                            <?php if ($size) {
                                                $size_detail = $d->rawQueryOne("select name$lang from #_size where type = ? and id = ? limit 0,1", [$proinfo['type'], $size]); ?>
                                                <span>Phân loại: <strong><?= $size_detail['name' . $lang] ?></strong></span>
                                            <?php } ?>
                                        </div>

                                        <div class="checkout-product-bottom-new">
                                            <div class="quantity-counter-procart quantity-counter-procart-<?= $code ?>">
                                                <span class="counter-procart-minus counter-procart">−</span>
                                                <input type="number" class="quantity-procart quantity-procat" min="1" value="<?= $quantity ?>" data-pid="<?= $pid ?>" data-code="<?= $code ?>">
                                                <span class="counter-procart-plus counter-procart">+</span>
                                            </div>

                                            <div class="price-procart">
                                                <?php if ($proinfo['sale_price']) { ?>
                                                    <p class="price-new-cart load-price-new-<?= $code ?>"><?= $func->formatMoney($pro_price_new_qty) ?></p>
                                                    <p class="price-old-cart load-price-<?= $code ?>"><?= $func->formatMoney($pro_price_qty) ?></p>
                                                <?php } else { ?>
                                                    <p class="price-new-cart load-price-<?= $code ?>"><?= $func->formatMoney($pro_price_qty) ?></p>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" class="del-procart" data-code="<?= $code ?>" aria-label="Xóa sản phẩm">×</button>
                                </article>
                            <?php } ?>
                        </div>

                        <div class="money-procart checkout-summary-new">
                            <div class="total-procart">
                                <p>Số lượng sản phẩm:</p>
                                <p><?= $cartProductCount ?></p>
                            </div>
                            <div class="total-procart">
                                <p>Tạm tính:</p>
                                <p class="total-price load-price-temp"><?= $func->formatMoney($cart->getOrderTotal()) ?></p>
                            </div>
                            <?php if ($config['order']['ship']) { ?>
                                <div class="total-procart">
                                    <p>Giao hàng</p>
                                    <p>Giao hàng miễn phí: <span class="load-price-ship">0đ</span></p>
                                </div>
                            <?php } ?>
                            <div class="total-procart checkout-total-new">
                                <p>Tổng</p>
                                <p class="total-price load-price-total"><?= $func->formatMoney($cart->getOrderTotal()) ?></p>
                            </div>
                        </div>

                        <div class="checkout-agreements-new">
                            <label><input type="checkbox" required> <span>Tôi đã đọc và đồng ý với các <a href="dieu-khoan" target="_blank">điều khoản, chính sách bán hàng</a> tại Website.</span></label>
                            <label><input type="checkbox" required> <span>Tôi đã đọc và đồng ý chính sách <a href="chinh-sach-bao-mat" target="_blank">bảo mật</a> tại Website.</span></label>
                        </div>

                        <input type="submit" class="btn btn-cart checkout-submit-new" name="thanhtoan" value="Đặt hàng ngay   →" disabled>
                    </aside>
                </div>
            <?php } else { ?>
                <a href="" class="empty-cart text-decoration-none d-block">
                    <i class="fa-duotone fa-cart-xmark"></i>
                    <p><?= khongtontaisanphamtronggiohang ?></p>
                    <span class="btn btn-warning"><?= vetrangchu ?></span>
                </a>
            <?php } ?>
        </div>
    </form>
</div>
