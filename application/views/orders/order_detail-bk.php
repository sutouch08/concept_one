<?php
	$add = $this->pm->can_add;
	$edit = $this->pm->can_edit;
	$delete = $this->pm->can_delete;
	?>
<form id="discount-form">
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
			<table class="table table-bordered" style="margin-bottom:0px;">
        <thead>
        	<tr class="font-size-12">
						<th class="fix-width-40 text-center"></th>
						<th class="fix-width-40 text-center">No.</th>
						<th class="fix-width-200">รหัสสินค้า</th>
						<th class="min-width-250">ชื่อสินค้า</th>
						<th class="fix-width-100 text-center">ราคา</th>
						<th class="fix-width-100 text-center">จำนวน</th>
						<th class="fix-width-120 text-center">ส่วนลด</th>
						<th class="fix-width-120 text-right">มูลค่า</th>
          </tr>
        </thead>
        <tbody id="detail-table">
          <?php   $no = 1;              ?>
          <?php   $total_qty = 0;       ?>
          <?php   $total_discount = 0;  ?>
          <?php   $total_amount = 0;    ?>
          <?php   $order_amount = 0;    ?>
					<?php $disabled = $order->state == 1 ? "" : "disabled"; ?>
          <?php if(!empty($details)) : ?>
          <?php   foreach($details as $rs) : ?>
            <?php 	$discount = $order->role == 'C' ? $rs->gp : discountLabel($rs->discount1, $rs->discount2, $rs->discount3); ?>
            <?php 	$discLabel = $order->role == 'C' ? $rs->gp .' %' : discountLabel($rs->discount1, $rs->discount2, $rs->discount3); ?>
            <tr class="font-size-10" id="row_<?php echo $rs->id; ?>">
							<input type="hidden" id="currentQty-<?php echo $rs->id; ?>" value="<?php echo $rs->qty; ?>">
              <input type="hidden" id="currentPrice-<?php echo $rs->id; ?>" value="<?php echo $rs->price; ?>">
              <input type="hidden" id="currentDisc-<?php echo $rs->id; ?>" value="<?php echo $discLabel; ?>">
							<input type="hidden" id="sellPrice-<?php echo $rs->id; ?>" value="<?php echo $rs->price; ?>">
							<input type="hidden" id="discAmount-<?php echo $rs->id; ?>" value="<?php echo $rs->discount_amount; ?>">
							<td class="middle text-right">
      				<?php if( $rs->is_count == 0 && ($edit OR $add) && $order->state < 8 && $edit_order) : ?>
      					<button type="button" class="btn btn-minier btn-warning" id="btn-show-price-<?php echo $rs->id; ?>" onclick="showNonCountPriceBox(<?php echo $rs->id; ?>)"><i class="fa fa-pencil"></i></button>
      					<button type="button" class="btn btn-minier btn-info hide" id="btn-update-price-<?php echo $rs->id; ?>" onclick="updateNonCountPrice(<?php echo $rs->id; ?>)"><i class="fa fa-save"></i></button>
      				<?php endif; ?>
              <?php if( ( $order->is_paid == 0 && $order->state != 2 && $order->is_expired == 0 ) && ($edit OR $add)) : ?>
								<?php if($order->state < 4 OR ($rs->is_count == 0 && $order->state != 8)) : ?>
              			<button type="button" class="btn btn-minier btn-danger" onclick="removeDetail(<?php echo $rs->id; ?>, '<?php echo $rs->product_code; ?>')">
											<i class="fa fa-trash"></i>
										</button>
								<?php endif; ?>
              <?php endif; ?>
              </td>
            	<td class="middle text-center no">
      					<?php echo $no; ?>
      				</td>

      				<td class="middle">
      					<?php echo $rs->product_code; ?>
      				</td>

              <td class="middle">
							<?php if($order->state == 1) : ?>
								<input type="text" class="form-control input-sm"
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
							<?php if( ($allowEditPrice && $order->state == 1) OR ($rs->is_count == 0 && $order->state < 8)  ) : ?>
				         <input type="number" class="form-control input-sm text-center line-price"
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
							<?php if($order->state == 1 OR ($rs->is_count == 0 && $order->state < 8)) : ?>
									<input type="number" class="form-control input-sm text-center line-qty"
										id="qty-<?php echo $rs->id; ?>"
										data-code="<?php echo $rs->product_code; ?>"
										data-id="<?php echo $rs->id; ?>"
										value="<?php echo round($rs->qty, 2); ?>"
										onchange="updateItem(<?php echo $rs->id; ?>)"
									/>
								<?php else : ?>
      						<?php echo number($rs->qty); ?>
								<?php endif; ?>
      				</td>

              <td class="middle text-center">
              <?php if( $order->state == 1 ) : ?>
                <input type="text" class="form-control input-sm text-center line-disc"
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
							<?php if($order->state == 1) : ?>
								<input type="text" class="form-control input-sm text-right"
								id="total-<?php echo $rs->id; ?>"
								value="<?php echo number($rs->total_amount, 2); ?>" readonly/>
							<?php else : ?>
								<?php echo number($rs->total_amount, 2); ?>
							<?php endif; ?>
      				</td>
          </tr>

      <?php			$total_qty += $rs->qty;	?>
      <?php 		$total_discount += $rs->discount_amount; ?>
      <?php 		$order_amount += $rs->qty * $rs->price; ?>
      <?php			$total_amount += $rs->total_amount; ?>
      <?php			$no++; ?>
          <?php   endforeach; ?>
          <?php else : ?>
            <tr>
              <td colspan="10" class="text-center"><h4>ไม่พบรายการ</h4></td>
            </tr>
          <?php endif; ?>

<?php 	$netAmount = ( $total_amount - $order->bDiscAmount ) + $order->shipping_fee + $order->service_fee;	?>
						<tr class="font-size-12">
							<td colspan="4" rowspan="4" style="white-space:normal;">
								หมายเหตุ :
								<?php echo $order->remark; ?>
							</td>
							<td colspan="2" style="border-left:solid 1px #CCC;"><b>จำนวนรวม</b></td>
							<td colspan="2" class="text-right bolder" id="total-qty" style="font-weight:bold;"><?php echo number($total_qty); ?></td>
						</tr>
						<tr class="font-size-12">
							<td colspan="2" style="border-left:solid 1px #CCC;"><b>มูลค่ารวม</b></td>
							<td colspan="2" class="text-right" id="total-td" style="font-weight:bold;"><?php echo number($order_amount, 2); ?></td>
						</tr>
						<tr class="font-size-12">
							<td colspan="2" style="border-left:solid 1px #CCC;"><b>ส่วนลดรวม</b></td>
							<td colspan="2" class="text-right" id="discount-td" style="font-weight:bold;"><?php echo number($total_discount, 2); ?></td>
						</tr>
						<tr class="font-size-12">
							<td colspan="2" style="border-left:solid 1px #CCC;"><b>สุทธิ</b></td>
							<td colspan="2" class="text-right" style="font-weight:bold;" id="netAmount-td"><?php echo number( $netAmount, 2); ?></td>
						</tr>
        	</tbody>
        </table>
    </div>
</div>
<!--  End Order Detail ----------------->
</form>
<!-- order detail template ------>
<script id="detail-table-template" type="text/x-handlebars-template">
{{#each this}}
	{{#if @last}}
    <tr class="font-size-12">
    	<td colspan="4" rowspan="4" style="white-space:normal;">
				หมายเหตุ : {{remark}}
			</td>
      <td colspan="2" style="border-left:solid 1px #CCC;"><b>จำนวนรวม</b></td>
      <td colspan="2" class="text-right" id="total-qty" style="font-weight:bold;">{{ total_qty }}</td>
    </tr>

    <tr class="font-size-12">
      <td colspan="2" style="border-left:solid 1px #CCC;"><b>มูลค่ารวม</b></td>
      <td colspan="2" class="text-right" id="total-td" style="font-weight:bold;">{{ order_amount }}</td>
    </tr>

    <tr class="font-size-12">
      <td colspan="2" style="border-left:solid 1px #CCC;"><b>ส่วนลดรวม</b></td>
      <td colspan="2" class="text-right" id="discount-td" style="font-weight:bold;">{{ total_discount }}</td>
    </tr>

    <tr class="font-size-12">
      <td colspan="2" style="border-left:solid 1px #CCC;"><b>สุทธิ</b></td>
      <td colspan="2" class="text-right" id="netAmount-td" style="font-weight:bold;">{{ net_amount }}</td>
    </tr>
	{{else}}
		<tr class="font-size-10" id="row_{{ id }}">
			<input type="hidden" id="currentQty-{{id}}" value="{{qty}}">
			<input type="hidden" id="currentPrice-{{qty}}" value="{{price}}">
			<input type="hidden" id="currentDisc-{{qty}}" value="{{discount}}">
			<input type="hidden" id="sellPrice-{{qty}}" value="{{price}}">
			<input type="hidden" id="discAmount-{{qty}}" value="{{discount_amount}}">
			<td class="middle text-right">
				<?php if( $edit OR $add ) : ?>
					<button type="button" class="btn btn-xs btn-danger" onclick="removeDetail({{ id }}, '{{ productCode }}')"><i class="fa fa-trash"></i></button>
				<?php endif; ?>
			</td>
			<td class="middle text-center">{{ no }}</td>
			<td class="middle">{{productCode}}</td>
			<td class="middle">
				<input type="text" class="form-control input-sm"
				id="pd-name-{{id}}"
				data-code="{{productCode}}"
				data-id="{{id}}"
				value="{{productName}}"
				onchange="updateItem({{id}})"
				/>
			</td>
			<td class="middle text-center">
				<input type="number" class="form-control input-sm text-center line-price"
				id="price-{{id}}"
				data-code="{{productCode}}"
				data-id="{{id}}"
				value="{{price}}"
				onchange="updateItem({{id}})"
				/>
			</td>
			<td class="middle text-center">
				<input type="number" class="form-control input-sm text-center line-qty"
				id="qty-{{id}}"
				data-code="{{productCode}}"
				data-id="{{id}}"
				value="{{qty}}"
				onchange="updateItem({{id}})"
				/>
			</td>
			<td class="middle text-center">
				<input type="text" class="form-control input-sm text-center line-disc"
				id="disc-{{id}}"
				data-code="{{productCode}}"
				data-id="{{id}}"
				value="{{discount}}"
				onchange="updateItem({{id}})"
				/>
			</td>
			<td class="middle text-right">
				<input type="text" class="form-control input-sm text-right"
				id="total-{{id}}"
				value="{{amount}}" readonly/>
			</td>
		</tr>
	{{/if}}
{{/each}}
</script>

<script id="nodata-template" type="text/x-handlebars-template">
	<tr>
      <td colspan="11" class="text-center"><h4>ไม่พบรายการ</h4></td>
  </tr>
</script>
