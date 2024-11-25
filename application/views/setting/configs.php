<?php $this->load->view('include/header'); ?>
<?php
$company = $tab == 'company' ? 'active in' : '';
$document = $tab == 'document' ? 'active in' : '';
$order = $tab == 'order' ? 'active in' : '';
$inventory = $tab == 'inventory' ? 'active in' : '';
$sap = $tab == 'SAP' ? 'active in' : '';
$system = $tab == 'system' ? 'active in' : '';
$bookcode = $tab == 'boocode' ? 'active in' : '';
?>

<div class="row">
	<div class="col-lg-12">
    	<h4 class="title"><?php echo $this->title; ?></h4>
	</div>
</div>
<hr style="border-color:#CCC; margin-top: 15px; margin-bottom:0px;" />

<div class="row">
	<div class="col-sm-2 padding-5 margin-top-5">
		<ul id="myTab1" class="setting-tabs" style="margin-left:0px;">
			<!--  <li class="li-block active"><a href="#general" data-toggle="tab">ทั่วไป</a></li> -->
			<li class="li-block <?php echo $company; ?>" onclick="changeURL('company')"><a href="#company" data-toggle="tab">ข้อมูลบริษัท</a></li>
			<li class="li-block <?php echo $system; ?>" onclick="changeURL('system')"><a href="#system" data-toggle="tab">ระบบ</a></li>
			<li class="li-block <?php echo $inventory; ?>" onclick="changeURL('inventory')"><a href="#inventory" data-toggle="tab">คลังสินค้า</a></li>
			<li class="li-block <?php echo $order; ?>" onclick="changeURL('order')"><a href="#order" data-toggle="tab">ออเดอร์</a></li>
			<li class="li-block <?php echo $document; ?>" onclick="changeURL('document')"><a href="#document" data-toggle="tab">เลขที่เอกสาร</a></li>
			<li class="li-block <?php echo $bookcode; ?>" onclick="changeURL('bookcode')"><a href="#bookcode" data-toggle="tab">เล่มเอกสาร</a></li>
			<li class="li-block <?php echo $sap; ?>" onclick="changeURL('SAP')"><a href="#SAP" data-toggle="tab">ข้อมูล SAP</a></li>
	</ul>
</div>
<div class="col-sm-10" style="padding-top:15px; border-left:solid 1px #ccc; min-height:600px;">
	<div class="tab-content" style="border:0px;">

		<div class="tab-pane fade <?php echo $company; ?>" id="company">
			<?php $this->load->view('setting/setting_company'); ?>
		</div>

		<!---  ตั้งค่าระบบ  ----------------------------------------------------->
		<div class="tab-pane fade <?php echo $system; ?>" id="system">
			<?php $this->load->view('setting/setting_system'); ?>
		</div>

		<!---  ตั้งค่าออเดอร์  --------------------------------------------------->
		<div class="tab-pane fade <?php echo $order; ?>" id="order">
			<?php $this->load->view('setting/setting_order'); ?>
		</div>

		<!---  ตั้งค่าเอกสาร  --------------------------------------------------->
		<div class="tab-pane fade <?php echo $document; ?>" id="document">
			<?php $this->load->view('setting/setting_document'); ?>
		</div>

		<div class="tab-pane fade <?php echo $bookcode; ?>" id="bookcode">
			<?php $this->load->view('setting/setting_bookcode'); ?>
		</div>

		<div class="tab-pane fade <?php echo $sap; ?>" id="SAP">
			<?php $this->load->view('setting/setting_sap'); ?>
		</div>

		<div class="tab-pane fade <?php echo $inventory; ?>" id="inventory">
			<?php $this->load->view('setting/setting_inventory'); ?>
		</div>

	</div>
</div><!--/ col-sm-9  -->
</div><!--/ row  -->


<script src="<?php echo base_url(); ?>scripts/setting/setting.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/setting/setting_document.js?v=<?php echo date('Ymd'); ?>"></script>
<?php $this->load->view('include/footer'); ?>
