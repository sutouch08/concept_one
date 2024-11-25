<?php $this->load->view('include/header');  ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<h3 class="title"><?php echo $this->title; ?></h3>
	</div>
</div>
<hr class="margin-bottom-15 padding-5" />
<!--  Search Product -->
<div class="row">
  <div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 padding-5">
		<label>คลัง</label>
    <select class="form-control input-sm" name="warehouse" id="warehouse">
      <option value="">เลือกคลัง</option>
      <?php echo select_sell_warehouse(); ?>
    </select>
  </div>
	<div class="col-lg-2 col-md-2 col-sm-3 col-xs-8 padding-5">
		<label>รุ่นสินค้า</label>
    <input type="text" class="form-control input-sm text-center" id="pd-box" placeholder="ค้นรหัสสินค้า" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-2 col-xs-4 padding-5">
		<label class="display-block not-show">btn</label>
  	<button type="button" class="btn btn-xs btn-primary btn-block" onclick="getProductGrid()"><i class="fa fa-tags"></i> แสดงสินค้า</button>
  </div>
</div>
<hr class="margin-top-15 margin-bottom-0" />
<!--- Category Menu ---------------------------------->
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<div class="widget-box widget-color-blue collapsed" onclick="toggleCate()" id="cate-widget">
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

<hr style='border-color:#CCC; margin-top: 0px; margin-bottom:0px;' />
<div class='row'>
	<div class='col-sm-12'>
		<div class='tab-content' style="min-height:1px; padding:0px; border:0px;">
		<?php echo getProductTabs(); ?>
		</div>
	</div>
</div>
<!-- End Category Menu ------------------------------------>


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
			 </div>
		</div>
	</div>
</div>
</form>


<input type="hidden" name="view" id="view" value="1">

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
<script src="<?php echo base_url(); ?>scripts/orders/orders.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/product_tab_menu.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/orders/order_grid.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
