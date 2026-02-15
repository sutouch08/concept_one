<?php $this->load->view('include/header'); ?>
<?php $isAdmin = $this->_SuperAdmin; ?>
<script src="<?php echo base_url(); ?>assets/js/dropzone.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.colorbox.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/css/dropzone.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/colorbox.css" />
<?php $this->load->view('orders/style'); ?>

<div class="row">
	<div class="col-lg-2 col-md-2 col-sm-2 padding-5 hidden-xs">
    <h3 class="title" style="margin-top:6px;"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 text-center visible-xs" style="background-color:#eee;">
		<h3 class="margin-top-0"><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12 padding-5">
    	<p class="pull-right top-p text-right" >
				<button type="button" class="btn btn-xs btn-warning top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
				<?php if($order->is_term == 0 && $order->status == 1 && $order->TotalBalance > 0 && ($this->pm->can_add OR $this->pm->can_edit)) : ?>
				<button type="button" class="btn btn-xs btn-info top-btn" onclick="payOrder()">แจ้งชำระเงิน</button>
				<?php endif; ?>
				<?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
				<!--<button type="button" class="btn btn-xs btn-grey top-btn" onClick="inputDeliveryNo()">บันทึกการจัดส่ง</button>-->
				<?php endif; ?>
				<button type="button" class="btn btn-xs btn-purple top-btn" onclick="getSummary()">สรุปข้อมูล</button>
				<button type="button" class="btn btn-xs btn-success top-btn" onclick="showCustomerModal()">ข้อมูลลูกค้า</button>
				<button type="button" class="btn btn-xs btn-default top-btn hidden-xs" onclick="printOrderSheet()"><i class="fa fa-print"></i> พิมพ์</button>
				<button type="button" class="btn btn-xs btn-info top-btn hidden-xs" onclick="printOrderSheetBarcode()"><i class="fa fa-barcode"></i> พิมพ์</button>
      </p>
    </div>
</div><!-- End Row -->
<hr/>
<?php $this->load->view('orders/order_edit_header'); ?>
<?php $this->load->view('orders/order_panel'); ?>
<?php $this->load->view('orders/order_detail'); ?>
<?php $this->load->view('orders/order_online_modal'); ?>
<?php $this->load->view('orders/order_grid_modal'); ?>
<?php $this->load->view('order_invoice/customer_modal'); ?>
<?php $this->load->view('order_invoice/address_modal'); ?>

<div class="modal fade" id="soGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:950px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <center style="margin-bottom:10px;"><h4 class="modal-title" id="so-title"></h4></center>
      </div>
      <div class="modal-body" style="max-width:94vw; min-height:300px; max-height:70vh; overflow:auto;">
        <table class="table table-striped table-bordered" style="font-size:11px; table-layout: fixed; min-width:900px;">
          <thead>
            <th class="fix-width-40 text-center">#</th>
            <th class="fix-width-150 text-center">Item</th>
            <th class="min-width-200 text-center">Description</th>
						<th class="fix-width-80 text-center">Sell Price</th>
            <th class="fix-width-80 text-center">Qty</th>
            <th class="fix-width-80 text-center">Open</th>
            <th class="fix-width-80 text-center">Commited</th>
            <th class="fix-width-80 text-center">Available</th>
            <th class="fix-width-100 text-center">Order</th>
          </thead>
          <tbody id="so-table">

          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default top-btn" data-dismiss="modal">ยกเลิก</button>
				<button type="button" class="btn btn-yellow top-btn" onclick="takeAll()">เลือกทั้งหมด</button>
				<button type="button" class="btn btn-purple top-btn" onclick="clearAll()">เคลียร์ทั้งหมด</button>
        <button type="button" class="btn btn-primary top-btn" id="btn-add-so" onclick="addSoItem()">สร้างออเดอร์</button>
       </div>
    </div>
  </div>
</div>


<script id="so-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr id="row-{{id}}">
      <td class="middle text-center">{{no}}</td>
      <td class="middle">{{product_code}}</td>
      <td class="middle">{{product_name}}</td>
			<td class="middle text-right">{{sell_price}}</td>
      <td class="middle text-right">{{qty}}</td>
      <td class="middle text-right">{{OpenQty}}</td>
      <td class="middle text-right">{{commit_qty}}</td>
      <td class="middle text-right">{{available}}</td>
      <td class="middle text-right">
        <input type="number" class="form-control input-sm text-right so-qty"
          id="so-qty-{{id}}"
          data-id="{{id}}"
          data-code="{{product_code}}"
          data-name="{{product_name}}"
          data-style="{{style_code}}"
          data-basecode="{{order_code}}"
          data-baseline="{{id}}"
          data-baseentry="{{id_order}}"
          data-openqty="{{OpenQty}}"
          data-qty="{{qty}}"
          data-commit="{{commit_qty}}"
          data-available="{{available}}"
					data-cost="{{cost}}"
          data-price="{{price}}"
					data-sellprice="{{sell_price}}"
					data-discprcnt="{{discount_label}}"
					data-discamount="{{discount_amount}}"
          data-avgbilldisc="{{avgBillDiscAmount}}"
          data-vatcode="{{vat_code}}"
          data-vatrate="{{vat_rate}}"
					data-vattype="{{vat_type}}"
					data-unit="{{unit_code}}"
          data-iscount="{{is_count}}"
          value="{{available}}" />
      </td>
    </tr>
  {{/each}}
</script>

<input type="hidden" id="auz" value="<?php echo getConfig('ALLOW_UNDER_ZERO'); ?>">


<script src="<?php echo base_url(); ?>assets/js/clipboard.min.js"></script>
<script src="<?php echo base_url(); ?>scripts/orders/orders.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_add.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_online.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_order.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/print/print_address.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_grid.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_image.js?v=<?php echo date('YmdH'); ?>"></script>


<?php $this->load->view('include/footer'); ?>
