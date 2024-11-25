<?php
	$add = $this->pm->can_add;
	$edit = $this->pm->can_edit;
	$delete = $this->pm->can_delete;
	?>
<script src="<?php echo base_url(); ?>assets/js/jquery.autosize.js"></script>
<style>
	.form-group {
		margin-bottom: 5px;
	}
</style>

<?php if(($order->state == 1 && empty($order->BaseRef)) && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
<!--  Search Product -->
<div class="row">

	<div class="col-lg-2 col-md-4 col-sm-4 col-xs-12 padding-5">&nbsp;</div>
  <div class="col-lg-2-harf col-md-2-harf col-sm-4 col-xs-6 padding-5 margin-bottom-10">
		<label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm text-center" id="item-code" placeholder="SKU Code" autofocus>
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5 margin-bottom-10">
		<label>ราคา</label>
    <input type="number" class="form-control input-sm text-center" id="item-price" placeholder="ราคา" disabled>
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5 margin-bottom-10">
		<label>สต็อก</label>
    <input type="number" class="form-control input-sm text-center" id="stock-qty" placeholder="Stock" disabled>
    <input type="hidden" id="allow-over-stock" value="<?php echo getConfig('ORDER_OVER_STOCK'); ?>" />
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5 margin-bottom-10">
		<label>จอง</label>
    <input type="number" class="form-control input-sm text-center" id="reserv-qty" placeholder="จอง" disabled>
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5 margin-bottom-10">
		<label>คงเหลือ</label>
    <input type="number" class="form-control input-sm text-center" id="available-qty" placeholder="คงเหลือ" disabled>
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-4 padding-5 margin-bottom-10">
		<label>จำนวน</label>
    <input type="number" class="form-control input-sm text-center" id="input-qty" placeholder="Qty">
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-4 padding-5 margin-bottom-10">
		<label class="display-block not-show">B</label>
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="addItemToOrder()">Add</button>
  </div>
</div>

<?php endif; ?>

<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
			<table class="table table-bordered tableFixHead" style="min-width:1000px; margin-bottom:20px;">
        <thead>
        	<tr class="font-size-12">
						<th class="fix-width-40 text-center fix-header"></th>
						<th class="fix-width-40 text-center fix-header"></th>
						<th class="fix-width-40 text-center fix-header">Text</th>
						<th class="fix-width-200 fix-header">รหัสสินค้า</th>
						<th class="min-width-250 fix-header">ชื่อสินค้า</th>
						<th class="fix-width-100 text-center fix-header">ราคา</th>
						<th class="fix-width-100 text-center fix-header">จำนวน</th>
						<th class="fix-width-120 text-center fix-header">ส่วนลด</th>
						<th class="fix-width-120 text-right fix-header">มูลค่า</th>
          </tr>
        </thead>
        <tbody id="detail-table">
          <?php   $no = 1;              ?>
					<?php $disabled = $order->state == 1 ? (empty($order->BaseRef) ? "" : "disabled") : "disabled"; ?>
					<?php $active = FALSE; ?>

          <?php if(!empty($details)) : ?>
          <?php   foreach($details as $rs) : ?>
						<?php if( ($allowEditPrice && ($order->state == 1 && empty($order->BaseRef)) && ($this->pm->can_add OR $this->pm->can_edit)) OR (empty($order->BaseRef) && $rs->is_count == 0 && $order->state < 8)  ) : ?>
						<?php  		$active = TRUE; ?>
						<?php endif; ?>
            <?php 	$discount = $order->role == 'C' ? $rs->gp : discountLabel($rs->discount1, $rs->discount2, $rs->discount3); ?>
            <?php 	$discLabel = $order->role == 'C' ? $rs->gp .' %' : discountLabel($rs->discount1, $rs->discount2, $rs->discount3); ?>
            <tr class="font-size-12" id="row-<?php echo $rs->id; ?>">
							<input type="hidden" id="currentQty-<?php echo $rs->id; ?>" value="<?php echo $rs->qty; ?>">
              <input type="hidden" id="currentPrice-<?php echo $rs->id; ?>" value="<?php echo $rs->price; ?>">
              <input type="hidden" id="currentDisc-<?php echo $rs->id; ?>" value="<?php echo $discLabel; ?>">
							<input type="hidden" id="sellPrice-<?php echo $rs->id; ?>" value="<?php echo $rs->price; ?>">
							<input type="hidden" id="discAmount-<?php echo $rs->id; ?>" value="<?php echo $rs->discount_amount; ?>">
							<td class="middle text-center no">
      					<?php echo $no; ?>
      				</td>
							<td class="middle text-center">
              <?php if( ( $order->is_paid == 0 && $order->state != 2 && $order->is_expired == 0 ) && ($edit OR $add)) : ?>
								<?php if(($order->state == 1 && empty($order->BaseRef)) OR ($rs->is_count == 0 && $order->state != 8)) : ?>
										<a class="pointer" href="JavaScript:removeDetail('<?php echo $rs->id; ?>', '<?php echo $rs->product_code; ?>')">
											<i class="fa fa-trash fa-lg red"></i>
										</a>
								<?php endif; ?>
              <?php endif; ?>
              </td>
							<td class="middle text-center add-text">
								<?php $h = empty($rs->line_text) ? "" : "hide"; ?>
								<?php if(($order->state == 1 && empty($order->BaseRef)) && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
								<a class="pointer <?php echo $h; ?>" id="add-text-<?php echo $rs->id; ?>" href="javascript:insertTextRow(<?php echo $rs->id; ?>)" title="Insert text row">
									<i class="fa fa-plus-square-o fa-lg"></i>
								</a>
								<?php endif; ?>
      				</td>
      				<td class="middle">
      					<?php echo $rs->product_code; ?>
      				</td>

              <td class="middle">
							<?php if($active) : ?>
								<input type="text" class="form-control input-sm" style="border:0px;"
								id="pd-name-<?php echo $rs->id; ?>"
								data-code="<?php echo $rs->product_code; ?>"
								data-id="<?php echo $rs->id; ?>"
								value="<?php echo $rs->product_name; ?>"
								onchange="updateItem(<?php echo $rs->id; ?>)"
								/>
							<?php else : ?>
								<?php echo $rs->product_name; ?>
							<?php endif; ?>
      				</td>

              <td class="middle text-center">
							<?php if($active) : ?>
				         <input type="number" class="form-control input-sm text-center line-price" style="border:0px;"
								 	id="price-<?php echo $rs->id; ?>"
									name="price[<?php echo $rs->id; ?>]"
									data-code="<?php echo $rs->product_code; ?>"
									data-id="<?php echo $rs->id; ?>"
									value="<?php echo round($rs->price, 2); ?>"
									onchange="updateItem(<?php echo $rs->id; ?>)"
								/>
							<?php else : ?>
                <?php echo number($rs->price, 2); ?>
							<?php endif; ?>
              </td>

              <td class="middle text-center">
							<?php if($active) : ?>
									<input type="number" class="form-control input-sm text-center line-qty"
										style="border:0px;"
										id="qty-<?php echo $rs->id; ?>"
										data-code="<?php echo $rs->product_code; ?>"
										data-id="<?php echo $rs->id; ?>"
										data-vatcode="<?php echo $rs->vat_code; ?>"
										data-vatrate="<?php echo $rs->vat_rate; ?>"
										value="<?php echo round($rs->qty, 2); ?>"
										onchange="updateItem(<?php echo $rs->id; ?>)"
									/>
								<?php else : ?>
      						<?php echo number($rs->qty); ?>
								<?php endif; ?>
      				</td>

              <td class="middle text-center">
              <?php if($active) : ?>
                <input type="text" class="form-control input-sm text-center line-disc"
									style="border:0px;"
									id="disc-<?php echo $rs->id; ?>"
									name="disc[<?php echo $rs->id; ?>]"
									data-code="<?php echo $rs->product_code; ?>"
									data-id="<?php echo $rs->id; ?>"
									value="<?php echo $discount; ?>"
									onchange="updateItem(<?php echo $rs->id; ?>)"
								/>
							<?php else : ?>
								<span class="discount-label" id="disc_label_<?php echo $rs->id; ?>"><?php echo $discLabel; ?></span>
              <?php endif; ?>
              </td>

              <td class="middle text-right">
							<?php if($active) : ?>
								<input type="text" class="form-control input-sm text-right" style="border:0px;"
								id="total-<?php echo $rs->id; ?>"
								value="<?php echo number($rs->total_amount, 2); ?>" readonly/>
							<?php else : ?>
								<?php echo number($rs->total_amount, 2); ?>
							<?php endif; ?>
      				</td>
          </tr>

      <?php			$no++; ?>
				<?php if( ! empty($rs->line_text)) : ?>
					<tr class="font-size-12" id="text-row-<?php echo $rs->id; ?>" data-id="<?php echo $rs->id; ?>" class="rows">
				    <td class="text-center"></td>
				    <td class="text-center"></td>
				    <td class="text-center">
						<?php if(($order->state == 1 && empty($order->BaseRef))) : ?>
				      <a class="pointer" href="javascript:removeTextRow(<?php echo $rs->id; ?>)" title="Remove this row"><i class="fa fa-trash fa-lg red"></i></a>
						<?php endif; ?>
				    </td>
				    <td class="" colspan="2">
							<?php if(($order->state == 1 && empty($order->BaseRef))) : ?>
					      <textarea id="text-<?php echo $rs->id; ?>" style="min-height:30px; border:0px;"
									class="autosize autosize-transition form-control"
									onblur="updateLineText(<?php echo $rs->id; ?>)"><?php echo str_replace('"', '&quot;',$rs->line_text); ?></textarea>
							<?php else : ?>
								<?php echo str_replace('"', '&quot;', nl2br($rs->line_text)); ?>
							<?php endif; ?>
				    </td>
				    <td colspan="4"></td>
				  </tr>
				<?php endif; ?>
          <?php   endforeach; ?>
          <?php else : ?>
            <tr id="no-rows">
              <td colspan="9" class="text-center"><h4>ไม่พบรายการ</h4></td>
            </tr>
          <?php endif; ?>

        	</tbody>
        </table>
    </div>
</div>

<div class="row">
<?php $this->load->view('orders/order_edit_footer'); ?>
</div>
<!--  End Order Detail ----------------->

<?php $this->load->view('orders/order_template'); ?>

<script id="nodata-template" type="text/x-handlebars-template">
	<tr>
      <td colspan="9" class="text-center"><h4>ไม่พบรายการ</h4></td>
  </tr>
</script>
<script>
	$('#sale-id').select2();
	$('textarea.autosize').autosize();
</script>
