<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<?php if($this->_SuperAdmin) : ?>
			<button type="button" class="btn btn-sm btn-primary btn-100 pull-right margin-left-5" style="z-index:1; margin-top:-5px;" onclick="getStockSap('<?php echo $doc->code; ?>')">เปรียบเทียบสต็อก</button>
			<button type="button" class="btn btn-sm btn-info btn-100 pull-right margin-left-5" style="z-index:1; margin-top:-5px;" onclick="updateStock('<?php echo $doc->code; ?>')">ปรับยอดโอนตาม SAP</button>
		<?php endif; ?>
		<button type="button" class="btn btn-sm btn-danger btn-100 pull-right" style="z-index:1; margin-top:-5px;" onclick="removeChecked()">ลบรายการ</button>
		<div class="tabbable">
			<ul class="nav nav-tabs" id="myTab">
				<li class="active"><a data-toggle="tab" href="#transfer-table" aria-expanded="true">รายการโอนย้าย</a></li>
				<li class=""><a data-toggle="tab" href="#zone-table" aria-expanded="false">สินค้าในโซน</a></li>
			</ul>

			<div class="tab-content" style="padding:0px;">
				<div id="transfer-table" class="tab-pane fade active in" style="max-height:600px; overflow:auto;">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-0 table-responsive">
						<table class="table table-bordered" style="margin-bottom:0px; min-width:1000px;">
							<thead>
								<tr>
									<th class="fix-width-40 text-center">
										<label>
											<input type="checkbox" class="ace" onchange="checkAll($(this))" />
											<span class="lbl"></span>
										</label>
									</th>
									<th class="fix-width-40 text-center">ลำดับ</th>
									<th class="fix-width-200">รหัส</th>
									<th class="min-width-250">สินค้า</th>
									<th class="fix-width-200">ต้นทาง</th>
									<th class="fix-width-200">ปลายทาง</th>
									<th class="fix-width-100 text-center">จำนวน</th>
								<?php if($this->_SuperAdmin) : ?>
									<th class="fix-width-100 text-center">SAP</th>
								<?php endif; ?>
								</tr>
							</thead>

							<tbody id="transfer-list">
								<?php if(!empty($details)) : ?>
									<?php		$no = 1;						?>
									<?php   $total_qty = 0; ?>
									<?php		foreach($details as $rs) : 	?>
										<tr class="font-size-12" id="row-<?php echo $rs->id; ?>">
											<td class="middle text-center">
												<label>
													<input type="checkbox" class="ace chk"
													value="<?php echo $rs->id; ?>"
													data-id="<?php echo $rs->id; ?>"
													data-item="<?php echo $rs->product_code; ?>"
													data-fromzone="<?php echo $rs->from_zone; ?>"
													data-tozone="<?php echo $rs->to_zone; ?>"
													/>
													<span class="lbl"></span>
												</label>
											</td>
											<td class="middle text-center no">
												<?php echo $no; ?>
											</td>
											<!--- บาร์โค้ดสินค้า --->
											<td class="middle">
												<?php echo $rs->product_code; ?>
											</td>
											<!--- รหัสสินค้า -->
											<td class="middle">
												<?php echo $rs->product_name; ?>
											</td>
											<!--- โซนต้นทาง --->
											<td class="middle">
												<input type="hidden" class="row-zone-from" id="row-from-<?php echo $rs->id; ?>" value="<?php echo $rs->from_zone; ?>" />
												<?php echo $rs->from_zone_name; ?>
											</td>
											<td class="middle" id="row-label-<?php echo $rs->id; ?>">
												<?php 	echo $rs->to_zone_name; 	?>
											</td>

											<td class="middle text-center qty">
												<?php echo number($rs->qty); ?>
											</td>
										<?php if($this->_SuperAdmin) : ?>
											<td class="middle text-center">
												<input type="number" class="form-control input-sm text-right sap-stock" id="sap-stock-<?php echo $rs->id; ?>" value="<?php echo $rs->wms_qty; ?>" />
											</td>
										<?php endif; ?>
										</tr>
										<?php			$no++;			?>
										<?php     $total_qty += $rs->qty; ?>
									<?php		endforeach;			?>
									<tr>
										<td colspan="6" class="middle text-right"><strong>รวม</strong></td>
										<td class="middle text-center" id="total"><?php echo number($total_qty); ?></td>
									</tr>
								<?php	else : ?>
									<tr>
										<td colspan="8" class="text-center"><h4>ไม่พบรายการ</h4></td>
									</tr>
								<?php	endif; ?>
							</tbody>
						</table>

					</div>
				</div> <!-- Tab-pane -->

				<div id="zone-table" class="tab-pane fade" style="max-height:600px; overflow:auto;">
					<div class="divider-hidden"></div>

					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 padding-5">
					    <label>ต้นทาง</label>
					    <input type="text" class="form-control input-sm" id="from-zone" placeholder="ค้นหาชื่อโซน" autofocus />
					  </div>

					  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 padding-5">
					    <label>ปลายทาง</label>
					    <input type="text" class="form-control input-sm" id="to-zone" placeholder="ค้นหาชื่อโซน" />
					  </div>

					  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 padding-5">
					    <label>รหัสสินค้า</label>
					    <input type="text" class="form-control input-sm" id="item-code" placeholder="กรองด้วยรหัสสินค้า" />
					  </div>

					  <div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
					    <label class="display-block not-show">ok</label>
					    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductInZone()">แสดงสินค้า</button>
					  </div>
					</div>

					<div class="divider-hidden"></div>

					<form id="productForm">
			    	<table class="table table-striped table-bordered" style="margin-bottom:0px;">
			      	<thead>
								<tr>
									<th colspan="5" class="text-center">
										<h4 class="title" id="zoneName"></h4>
									</th>
								</tr>
			        	<tr>
			          	<th colspan="5">
										<div class="col-sm-6">
			              	<button type="button" class="btn btn-sm btn-info" onclick="selectAll()">เลือกทั้งหมด</button>
											<button type="button" class="btn btn-sm btn-warning" onclick="clearAll()">เคลียร์</button>
			              </div>
			              <div class="col-sm-6">
			                <p class="pull-right top-p">
			                  <button type="button" class="btn btn-sm btn-primary" onclick="addToTransfer()">ย้ายรายการที่เลือก</button>
			                </p>
			              </div>
			            </th>
			          </tr>

								<tr>
									<th class="fix-width-40 text-center">ลำดับ</th>
									<th class="fix-width-200">รหัส</th>
									<th class="min-width-200">สินค้า</th>
									<th class="fix-width-100 text-center">จำนวน</th>
									<th class="fix-width-100 text-center">ย้ายออก</th>
								</tr>
			          </thead>

			          <tbody id="zone-list"> </tbody>

			        </table>
			      </form>
				</div> <!-- Tab-pane -->
			</div> <!-- Tab content -->
		</div><!--- tabbable -->
  </div><!--- Col-lg-12 -->
</div><!--- row -->

<script id="zoneTemplate" type="text/x-handlebars-template">
{{#each this}}
	{{#if nodata}}
		<tr>
			<td colspan="6" class="text-center">
				<h4>ไม่พบสินค้าในโซน</h4>
			</td>
		</tr>
	{{else}}
		<tr id="zone-row-{{no}}">
			<td class="text-center zone-no">{{ no }}</td>
		  <td>{{ product_code }}</td>
		  <td>{{ product_name }}</td>
		  <td class="text-center qty-label" id="qty-label-{{no}}">{{ qty }}</td>
		  <td class="text-center">
		  	<input type="number" class="form-control input-sm text-center input-qty" max="{{qty}}" id="{{product_code}}" data-no="{{no}}" data-limit="{{qty}}" data-sku="{{product_code}}" />
		  </td>
		</tr>
	{{/if}}
{{/each}}
</script>



<script id="transferTableTemplate" type="text/x-handlebars-template">
{{#each this}}
	{{#if nodata}}
	<tr>
		<td colspan="8" class="text-center"><h4>ไม่พบรายการ</h4></td>
	</tr>
	{{else}}
		{{#if @last}}
			<tr>
				<td colspan="6" class="text-right"><strong>รวม</strong></td>
				<td class="middle text-center" id="total">{{ total }}</td>
				<td></td>
			</tr>
		{{else}}
		<tr class="font-size-12" id="row-{{id}}">
			<td class="middle text-center">
				<label>
					<input type="checkbox" class="ace chk" value="{{id}}" />
					<span class="lbl"></span>
				</label>
			</td>
			<td class="middle text-center no">{{ no }}</td>
			<td class="middle">{{ product_code }}</td>
			<td class="middle">{{ product_name }}</td>
			<td class="middle">{{ from_zone }}</td>
			<td class="middle">{{{ to_zone }}}</td>
			<td class="middle text-center qty">{{ qty }}</td>
		</tr>
		{{/if}}
	{{/if}}
{{/each}}
</script>
