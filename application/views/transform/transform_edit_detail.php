<?php $this->load->view('include/header'); ?>
<?php
$add = $this->pm->can_add;
$edit = $this->pm->can_edit;
$delete = $this->pm->can_delete;
$hide = $order->status == 1 ? 'hide' : '';
 ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    	<h4 class="title"><?php echo $this->title; ?></h4>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        	<button type="button" class="btn btn-xs btn-warning top-btn" onClick="editOrder('<?php echo $order->code; ?>')"><i class="fa fa-arrow-left"></i> กลับ</button>
      <?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
          <button type="button" class="btn btn-xs btn-success top-btn <?php echo $hide; ?>" id="btn-save-order" onclick="saveOrder()"><i class="fa fa-save"></i> บันทึก</button>
      <?php endif; ?>
        </p>
    </div>
</div>
<hr class="margin-bottom-15 padding-5" />
<?php $this->load->view('transform/transform_edit_header'); ?>

<!--  Search Product -->
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-8 padding-5 margin-bottom-10">
    <input type="text" class="form-control input-sm text-center" id="pd-box" placeholder="Model Code" autofocus />
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-4 padding-5 margin-bottom-10">
  	<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductGrid()">OK</button>
  </div>

	<div class="divider visible-xs"></div>
  <div class="col-lg-2-harf col-md-2-harf col-sm-4 col-xs-6 padding-5 margin-bottom-10">
    <input type="text" class="form-control input-sm text-center" id="item-code" placeholder="SKU Code">
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-2 padding-5 margin-bottom-10">
    <input type="number" class="form-control input-sm text-center" id="stock-qty" placeholder="Stock" disabled>
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-2 padding-5 margin-bottom-10">
    <input type="number" class="form-control input-sm text-center" id="input-qty" placeholder="Qty">
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 padding-5 margin-bottom-10">
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="addItemToOrder()">Add</button>
  </div>

  <div class="divider-hidden visible-sm visible-xs"></div>

  <div class="col-lg-1-harf col-lg-offset-1 col-md-1-harf col-md-offset-1 col-sm-2 col-xs-8 padding-5 margin-bottom-10">
    <input type="text" class="form-control input-sm text-center" id="so-code" placeholder="ใบสั่งขาย" value="<?php echo $order->so_code; ?>" <?php echo (empty($order->so_code) ? '' : 'disabled'); ?> />
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-4 padding-5 margin-bottom-10">
    <?php $load = empty($order->so_code) ? '' : 'hide'; ?>
    <?php $clear = empty($order->so_code) ? 'hide' : ''; ?>
  	<button type="button" class="btn btn-xs btn-primary btn-block <?php echo $load; ?>" id="btn-add-so" onclick="loadSO()">Add</button>
    <button type="button" class="btn btn-xs btn-warning btn-block <?php echo $clear; ?>" id="btn-clear-so" onclick="clearSO()">Clear</button>
  </div>
</div>
<hr class="margin-top-10 margin-bottom-0" />


<?php $this->load->view('transform/transform_detail');  ?>

<?php $this->load->view('orders/order_grid_modal'); ?>

<div class="modal fade" id="soModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width:800px; max-width:95vw;">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <center><h4 class="modal-title" id="so-title">ใบสั่งขาย</h4></center>
      </div>
      <div class="modal-body" style="max-width:94vw; min-height:300px; max-height:70vh; overflow:auto;">
        <table class="table table-striped table-bordered" style="table-layout: fixed; min-width:740px;">
          <thead>
            <th class="fix-width-40 text-center">#</th>
            <th class="fix-width-200 text-center">รหัส</th>
            <th class="min-width-200 text-center">สินค้า</th>
            <th class="fix-width-100 text-center">ราคา</th>
            <th class="fix-width-100 text-center">จำนวน</th>
            <th class="fix-width-100 text-center">เบิก</th>
          </thead>
          <tbody id="so-body">

          </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default top-btn" id="btn_close" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-yellow top-btn" onclick="getAll()">เบิกทั้งหมด</button>
				<button type="button" class="btn btn-purple top-btn" onclick="clearAll()">เคลียร์ทั้งหมด</button>
        <button type="button" class="btn btn-primary top-btn" onclick="addSoItem()">เพิ่มในรายการ</button>
       </div>
    </div>
  </div>
</div>

<script id="so-template" type="text/x-handlebarsTemplate">
  {{#each this}}
    <tr id="row-{{line_id}}">
      <td class="middle text-center">{{no}}</td>
      <td class="middle">{{product_code}}</td>
      <td class="middle">{{product_name}}</td>
      <td class="middle text-right">{{priceLabel}}</td>
      <td class="middle text-right">{{qtyLabel}}</td>
      <td class="middle text-right">
        <input type="text" class="form-control input-sm text-right so-qty"
          id="so-qty-{{line_id}}"
          data-lineid="{{line_id}}" data-ordercode="{{order_code}}" data-orderid="{{id_order}}"
          data-model="{{style_code}}" data-code="{{product_code}}" data-name="{{product_name}}"
          data-basecode="{{baseCode}}" data-qty="{{qty}}"
          data-cost="{{cost}}" data-price="{{price}}"
          data-vatcode="{{vat_code}}"  data-vatrate="{{vat_rate}}" data-iscount="{{is_count}}"
          value="" />
      </td>
    </tr>
  {{/each}}
</script>

<script src="<?php echo base_url(); ?>scripts/transform/transform.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transform/transform_add.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/transform/transform_detail.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/product_tab_menu.js?v=<?php echo date('YmdH'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_grid.js?v=<?php echo date('YmdH'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
