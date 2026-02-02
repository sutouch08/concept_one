<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-sm-6 col-sm-6 col-xs-12 padding-5 padding-top-5">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5 text-right">
		<?php if($this->pm->can_add) : ?>
			<button type="button" class="btn btn-white btn-success top-btn" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
		<?php endif; ?>
  </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
	<div class="row">
		<div class="col-lg-1-harf col-md-2-harf col-sm-2-harf col-xs-6 padding-5">
			<label>เลขที่เอกสาร</label>
			<input type="text" class="width-100 search" name="code"  value="<?php echo $code; ?>" />
		</div>

		<div class="col-lg-3 col-md-4-harf col-sm-4-harf col-xs-6 padding-5">
			<label>คลัง</label>
			<select class="width-100 filter" name="warehouse" id="warehouse">
				<option value="all">ทั้งหมด</option>
				<?php echo select_common_warehouse($warehouse); ?>
			</select>
		</div>

		<div class="col-lg-2 col-md-3-harf col-sm-3-harf col-xs-6 padding-5">
			<label>ช่องทางขาย</label>
			<select class="width-100 filter" name="channels" id="channels">
				<option value="all">ทั้งหมด</option>
				<?php echo select_channels($channels); ?>
			</select>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
			<label>สถานะ</label>
			<select class="form-control input-sm" name="status" onchange="getSearch()">
				<option value="all">ทั้งหมด</option>
				<option value="P" <?php echo is_selected('P', $status); ?>>Pending</option>
				<option value="C" <?php echo is_selected('C', $status); ?>>Closed</option>
				<option value="D" <?php echo is_selected('D', $status); ?>>Canceled</option>
			</select>
		</div>

		<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
			<label>วันที่</label>
			<div class="input-group width-100">
				<input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
				<input type="text" class="form-control input-sm width-50 text-center" name="from_date" id="toDate" value="<?php echo $to_date; ?>" />
			</div>
		</div>

		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="submit" class="btn btn-xs btn-primary btn-block">Search</button>
		</div>
		<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
			<label class="display-block not-show">buton</label>
			<button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()">Reset</button>
		</div>
	</div>
	<input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15">
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped table-hover border-1" style="min-width:750px;">
			<thead>
				<tr class="font-size-11">
					<th class="fix-width-100 middle"></th>
					<th class="fix-width-40 middle">สถานะ</th>
					<th class="fix-width-40 middle text-center">ลำดับ</th>
					<th class="fix-width-100 middle text-center">วันที่</th>
					<th class="fix-width-120 middle">เลขที่เอกสาร</th>
					<th class="fix-width-150 middle">ช่องทางขาย</th>
					<th class="min-width-100 middle">คลัง</th>
					<th class="fix-width-100 middle">พนักงาน</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($list)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
					<?php $chs = channels_array(); ?>
          <?php foreach($list as $rs) : ?>
						<?php $channelsName = empty($rs->channels_code) ? NULL : (empty($chs[$rs->channels_code]) ? NULL : $chs[$rs->channels_code]); ?>
            <tr class="font-size-11" id="row-<?php echo $rs->code; ?>">
							<td class="middle">
								<button type="button" class="btn btn-minier btn-info" onclick="viewDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
								<?php if($rs->status == 'P' && $this->pm->can_edit) : ?>
									<button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
								<?php endif; ?>
								<?php if($this->pm->can_delete) : ?>
									<button type="button" class="btn btn-minier btn-danger" onclick="goCancel('<?php echo $rs->code; ?>')"><i class="fa fa-trash"></i></button>
								<?php endif; ?>
							</td>
							<td class="middle text-center">
								<?php if($rs->status == 'C') : ?>
									<span class="green">Closed</span>
								<?php elseif($rs->status == 'P') : ?>
									<span class="orange">Pending</span>
								<?php elseif($rs->status == 'R') : ?>
									<span class="purple">Released</span>
								<?php elseif($rs->status == 'Y') : ?>
									<span class="purple">Picking</span>
								<?php elseif($rs->status == 'D') : ?>
									<span class="red">Canceled</span>
								<?php endif; ?>
							</td>
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo thai_date($rs->date_add); ?></td>
              <td class="middle"><?php echo $rs->code; ?></td>
							<td class="middle"><?php echo $channelsName; ?></td>
              <td class="middle"><?php echo $rs->warehouse_code; ?></td>
              <td class="middle"><?php echo $rs->user; ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
				<?php else : ?>
					<tr><td colspan="9" class="text-center">-- No Data --</td></tr>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/inventory/pick_list/pick_list.js?v=<?php echo date('Ymd'); ?>"></script>
<script>
	$('#warehouse').select2();
	$('#zone').select2();
	$('#channels').select2();
	$('#user').select2();
	$('#sender').select2();

	function export_to_sap(code)
	{
		load_in();
		$.ajax({
			url:HOME + 'export_pick_list/' + code,
			type:'POST',
			cache:false,
			success:function(rs){
				load_out();
				if(rs == 'success'){
					$('#row-'+code).remove();
					swal({
						title:'Success',
						text:'ส่งข้อมูลไป SAP เรียบร้อยแล้ว',
						type:'success',
						timer:1000
					});
				}else{
					swal({
						title:'Error!',
						text:rs,
						type:'error'
					});
				}
			}
		});
	}
</script>

<?php $this->load->view('include/footer'); ?>
