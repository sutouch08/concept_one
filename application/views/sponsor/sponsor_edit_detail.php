<?php $this->load->view('include/header'); ?>
<?php
$add = $this->pm->can_add;
$edit = $this->pm->can_edit;
$delete = $this->pm->can_delete;
$hide = $order->status == 1 ? 'hide' : '';
 ?>
<div class="row">
	<div class="col-sm-6 col-xs-6 padding-5">
    	<h4 class="title"><?php echo $this->title; ?></h4>
    </div>
    <div class="col-sm-6 col-xs-6 padding-5">
    	<p class="pull-right top-p">
        	<button type="button" class="btn btn-sm btn-warning" onClick="editOrder('<?php echo $order->code; ?>')"><i class="fa fa-arrow-left"></i> กลับ</button>
      <?php if($this->pm->can_add OR $this->pm->can_edit) : ?>
          <button type="button" class="btn btn-sm btn-success <?php echo $hide; ?>" id="btn-save-order" onclick="saveOrder()"><i class="fa fa-save"></i> บันทึก</button>
      <?php endif; ?>
        </p>
    </div>
</div>
<hr class="margin-bottom-15 padding-5" />
<?php $this->load->view('sponsor/sponsor_edit_header'); ?>

<!--  Search Product -->
<div class="row">
  <div class="col-lg-2 col-md-2-harf col-sm-2-harf col-xs-8 padding-5 margin-bottom-10">
    <input type="text" class="form-control input-sm text-center" id="pd-box" placeholder="Model Code" autofocus />
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-4 padding-5 margin-bottom-10">
  	<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductGrid()">OK</button>
  </div>

	<div class="divider visible-xs"></div>
  <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"> &nbsp; </div>
  <div class="col-lg-2-harf col-md-2-harf col-sm-3 col-xs-6 padding-5 margin-bottom-10">
    <input type="text" class="form-control input-sm text-center" id="item-code" placeholder="SKU Code">
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 padding-5 margin-bottom-10">
    <input type="number" class="form-control input-sm text-center" id="stock-qty" placeholder="Stock" disabled>
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 padding-5 margin-bottom-10">
    <input type="number" class="form-control input-sm text-center" id="input-qty" placeholder="Qty">
  </div>
  <div class="col-lg-1 col-md-1 col-sm-1 col-xs-2 padding-5 margin-bottom-10">
    <button type="button" class="btn btn-xs btn-primary btn-block" onclick="addItemToOrder()">Add</button>
  </div>
</div>
<hr class="margin-top-10 margin-bottom-0 visible-lg" />
<!--- Category Menu ---------------------------------->
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<div class="widget-box widget-color-blue2 collapsed" onclick="toggleCate()" id="cate-widget">
			<div class="widget-header widget-header-small">
				<h6 class="widget-title">Categories</h6>
			</div>
			<div class="widget-body">
				<div class="widget-main">
					<ul class='nav navbar-nav' role='tablist' style="float:none;">
					<?php echo productTabMenu('order'); ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

<hr class=""/>
<div class='row'>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<div class='tab-content' style="min-height:1px; padding:0px; border:0px;">
		<?php echo getProductTabs(); ?>
		</div>
	</div>
</div>
<!-- End Category Menu ------------------------------------>

<?php $this->load->view('sponsor/sponsor_detail');  ?>

<form id="orderForm">
<div class="modal fade" id="orderGrid" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" id="modal" style="max-width:95%;">
		<div class="modal-content">
  			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="modalTitle">title</h4>
        <center><span style="color: red;">ใน ( ) = ยอดคงเหลือทั้งหมด   ไม่มีวงเล็บ = สั่งได้ทันที</span></center>
			 </div>
			 <div class="modal-body">
         <div class="row">
           <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive" id="modalBody">

           </div>
         </div>
       </div>
			 <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
				<button type="button" class="btn btn-primary" onClick="addToOrder()" >เพิ่มในรายการ</button>
			 </div>
		</div>
	</div>
</div>
</form>


<script>
	function toggleCate() {
		if($('#cate-widget').hasClass('collapsed')) {
			$('#cate-widget').removeClass('collapsed');
		}
		else {
			$('#cate-widget').addClass('collapsed');
		}
	}
</script>

<script src="<?php echo base_url(); ?>scripts/sponsor/sponsor.js?v=<?php echo date('YmdHis'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/sponsor/sponsor_add.js?v=<?php echo date('YmdHis'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/product_tab_menu.js?v=<?php echo date('YmdHis'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_grid.js?v=<?php echo date('YmdHis'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
