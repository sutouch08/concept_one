<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12 padding-5 text-right">
		<button type="button" class="btn btn-sm btn-default top-btn" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button>
	<?php if(($this->pm->can_add OR $this->pm->can_edit) && $doc->status == 'P') : ?>
		<button type="button" class="btn btn-white btn-primary top-btn" onclick="save()"><i class="fa fa-save"></i>&nbsp; Save</button>
		<button type="button" class="btn btn-white btn-warning top-btn" onclick="goEdit('<?php echo $doc->code; ?>')"><i class="fa fa-pencil"></i>&nbsp; แก้ไข</button>
	<?php endif; ?>
	<?php if($this->pm->can_edit && $doc->status == 'C') : ?>
		<button type="button" class="btn btn-white btn-purple top-btn" onclick="rollback('<?php echo $doc->code; ?>')"><i class="fa fa-history"></i>&nbsp; ย้อนสถานะ</button>
	<?php endif; ?>
	<?php if($doc->status != 'D') : ?>
		<button type="button" class="btn btn-white btn-info top-btn" onclick="printPickOrders('<?php echo $doc->code; ?>')"><i class="fa fa-print"></i>&nbsp; Print Order</button>
		<button type="button" class="btn btn-white btn-info top-btn" onclick="printPickList('<?php echo $doc->code; ?>')"><i class="fa fa-print"></i>&nbsp; Print Pick List</button>
	<?php endif; ?>
  </div>
</div><!-- End Row -->
<hr class=""/>
<div class="row">
	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
		<input type="text" class="width-100 text-center" id="code" value="<?php echo $doc->code; ?>" disabled />
    <input type="hidden" id="id" value="<?php echo $doc->id; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>วันที่</label>
    <input type="text" class="width-100 text-center e" id="date" value="<?php echo thai_date($doc->date_add, FALSE); ?>" readonly disabled />
  </div>

	<div class="col-lg-4 col-md-5 col-sm-5 col-xs-12 padding-5">
		<label>คลังสินค้าต้นทาง</label>
		<select class="width-100 e" id="warehouse" disabled>
			<option value="">เลือกคลัง</option>
			<?php echo select_common_warehouse($doc->warehouse_code); ?>
		</select>
	</div>

	<div class="col-lg-3 col-md-3-harf col-sm-3-harf col-xs-8 padding-5">
		<label>ช่องทางขาย</label>
		<select class="width-100 e" id="channels" disabled>
			<option value="">เลือกช่องทางขาย</option>
			<?php echo select_channels($doc->channels_code); ?>
		</select>
	</div>

	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-4 padding-5">
		<label>สถานะ</label>
		<input type="text" class="width-100 text-center" value="<?php echo pick_list_status_text($doc->status); ?>" disabled />
	</div>

  <div class="col-lg-12 col-md-10 col-sm-10 col-xs-12 padding-5">
    <label>หมายเหตุ</label>
		<input type="text" class="width-100 e" id="remark" value="<?php echo $doc->remark; ?>" disabled/>
  </div>
</div>
<hr class="padding-5 margin-top-15">
<?php $this->load->view('inventory/pick_list/pick_list_details'); ?>

<script>
	$('#warehouse').select2();
	$('#channels').select2();

	function printPickOrders(code) {
	  let center = ($(document).width() - 800) /2;
	  let target = HOME + 'print_order_list/'+code;
		window.open(target, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
	}

	function printPickList(code) {
	  let center = ($(document).width() - 800) /2;
	  let target = HOME + 'print_pick_list/'+code;
		window.open(target, "_blank", "width=800, height=900. left="+center+", scrollbars=yes");
	}

	function unReleasePickList() {
		let code = $('#code').val();

		swal({
			title:'Unrelease',
			text:'ต้องการย้อนสถานะเอกสารหรือไม่ ?',
			type:'warning',
			html:true,
			showCancelButton:true,
			cancelButtonText:'No',
			confirmButtonText:'Yes',
			closeOnConfirm:true
		}, function() {
			load_in();

			setTimeout(() => {
				$.ajax({
					url:HOME + 'unrelease_pick_list/'+code,
					type:'POST',
					cache:false,
					success:function(rs) {
						load_out();

						if(rs.trim() === 'success') {
							window.location.reload();
						}
						else {
							beep();
							showError(rs);
						}
					},
					error:function(rs) {
						beep();
						showError(rs);
					}
				})
			}, 100)
		})
	}
</script>
<script src="<?php echo base_url(); ?>scripts/inventory/pick_list/pick_list.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/pick_list/pick_list_add.js?v=<?php echo date('Ymd'); ?>"></script>
<script src="<?php echo base_url(); ?>scripts/inventory/pick_list/pick_list_control.js?v=<?php echo date('Ymd'); ?>"></script>

<?php $this->load->view('include/footer'); ?>
