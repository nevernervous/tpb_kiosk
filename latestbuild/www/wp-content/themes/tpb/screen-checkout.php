<?php $cart = tpb_get_cart(); var_dump($cart); ?>
<div class="screen screen-checkout" data-screen="checkout">
	<div class="steps">
		<div class="step step-review is-active">
			<h2 class="title title-lg">
				<?php _e( 'Check out', 'tpb' ); ?>
			</h2>

			<?php if ( $cart ): ?>

				<form class="form-cart">
					<div class="order-head">
							<div class="col-item">
								<?php _e( 'Item', 'tpb' ); ?>
							</div>

							<div class="col-amount">
								<?php _e( 'Amount', 'tpb' ); ?>
							</div>


							<div class="col-qty">
								<?php _e( 'Qty', 'tpb' ); ?>
							</div>

							<div class="col-price">
								<?php _e( 'Price', 'tpb' ); ?>
							</div>
					</div><!-- .order-head -->

					<div class="scroller">
						<div class="order-lines">
							<?php $i=0; foreach( $cart['lines'] as $line ): ?>
							<div class="item-line qty-ancestor" data-product-id="<?php echo $line['product']->ID; ?>">
								<div class="col-item">
									<span class="item-name link-product" data-url="<?php echo get_the_permalink( $line['product']->ID ); ?>">
											<?php echo $line['product']->post_title; ?>
										</button>
									</span>
								</div>

								<div class="col-amount">
									<ul class="user-select select-amount <?php echo count($line['prices'])>1 ? 'selectable':''; ?>">
										<?php $cnt=0; foreach( $line['prices'] as $price ): ?>
										<li class="option <?php echo $cnt==$line['amount'] ? 'is-selected"':''; ?>" data-value="<?php echo $cnt; ?>" data-price="<?php echo $price->price; ?>">
											<div class="price">
												$<?php echo $price->price.($price->unit?'<small>/'.$price->unit.'</small>':''); ?>
											</div>
										</li>
										<?php $cnt++; endforeach; ?>
									</ul>
								</div>

								<div class="col-qty">
									<div class="item-qty">
										<div class="btn-user btn-qty btn-minus" data-target="cart-qty">
											-
											<span class="hit"></span>
										</div>

										<div class="qty">
											<input type="text" name="qty" value="<?php echo $line['qty']; ?>" class="input-qty" data-target="cart-price" />
										</div>

										<div class="btn-user btn-qty btn-plus" data-target="cart-qty">
											+
											<span class="hit"></span>
										</div>
									</div><!-- .product-qty -->
								</div>

								<div class="col-price">
									<span class="line-price">
										$<?php echo $line['line_price']; ?>
									</span>
								</div>

								<div class="col-del">
									<div class="btn-user btn-del">
										<span class="hit"></span>
									</div>
								</div>
							</div>
							<?php $i++; endforeach; ?>
						</div><!-- .order-lines -->
					</div><!-- .scroller -->

					<div class="order-total">
						<div class="label">
							<?php _e( 'Total', 'tpb' ); ?>
						</div><!-- .label -->

						<div class="total">
							$<?php echo $cart['total']; ?>
						</div><!-- .total -->
					</div><!-- .order-total -->

					<div class="cta">
						<div class="buttons">
							<div class="btn-user btn-text btn-text-lg btn-no btn-checkout-back">
								<?php _e( 'Continue shopping', 'tpb' ); ?>

								<span class="hit"></span>
							</div>

							<div class="btn-user btn-text btn-text-xl btn-yes btn-checkout-next">
								<?php _e( 'Confirm order', 'tpb' ); ?>

								<span class="hit"></span>
							</div>
						</div><!-- .buttons -->
					</div><!-- .cta -->
				</form><!-- .form-cart -->

			<?php else: ?>

			<?php _e( 'No product in cart', 'tpb' ); ?>

			<?php endif; ?>
		</div><!-- .step-review -->

		<div class="step step-login">
			<form class="form-checkout">
				<div class="field">
					<label for="user-name"><?php _e( 'Enter your name to place <br/>your order', 'tpb' ); ?></label>
					<input type="text" name="user_name" id="user-name" class="input-text" placeholder="<?php _e( 'What is your name?', 'tpb' ); ?>" data-osk-options="disableReturn disableTab" />
				</div><!-- .field -->

				<div class="cta">
					<div class="btn-user btn-text btn-text-xl btn-checkout-next">
						<?php _e( 'Checkout', 'tpb' ); ?>

						<span class="hit"></span>
					</div>
				</div><!-- .cta -->
			</form><!-- .form-checkout -->
		</div><!-- .step-login -->

		<div class="step step-thank-you">
			<div class="message">
				<div class="text">
					<?php echo sprintf( __( 'Thank you %s, you can go to the checkout line to proceed your order', 'tpb' ), '<span class="output-name"></span>' ); ?>
				</div><!-- .text -->
			</div><!-- .message -->

			<div class="cta">
				<div class="btn-user btn-text btn-text-xl btn-checkout-next btn-reset-session">
					<?php _e( 'Start a new session', 'tpb' ); ?>

					<span class="hit"></span>
				</div>
			</div><!-- .cta -->
		</div><!-- .step-thank-you -->
	</div><!-- .steps -->
</div><!-- .screen-checkout -->