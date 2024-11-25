<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 padding-5 hidden-xs">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
	<div class="col-xs-12 padding-5 visible-xs">
		<h3 class="title-xs"><?php echo $this->title; ?></h3>
	</div>
  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 padding-5">
  	<p class="pull-right top-p">
    <?php if($this->pm->can_add) : ?>
      <button type="button" class="btn btn-sm btn-success btn-top" onclick="addNew()"><i class="fa fa-plus"></i> เพิมใหม่</button>
    <?php endif; ?>
    </p>
  </div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>

  <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>คลังต้นทาง</label>
		<select class="width-100 filter" name="from_warehouse" id="from-warehouse">
			<option value="all">ทั้งหมด</option>
			<?php echo select_warehouse($from_warehouse); ?>
    </select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>คลังปลายทาง</label>
		<select class="width-100 filter" name="to_warehouse" id="to-warehouse">
			<option value="all">ทั้งหมด</option>
			<?php echo select_warehouse($to_warehouse); ?>
    </select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
		<label>พนักงาน</label>
		<select class="width-100 filter" name="user" id="user">
			<option value="all">ทั้งหมด</option>
			<?php echo select_user($user); ?>
		</select>
	</div>

	<div class="col-lg-1-harf col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm" name="status" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="-1" <?php echo is_selected('-1', $status); ?>>Draft</option>
			<option value="0" <?php echo is_selected('0', $status); ?>>รออนุมัติ</option>
			<option value="1" <?php echo is_selected('1', $status); ?>>บันทึกแล้ว</option>
			<option value="2" <?php echo is_selected('2', $status); ?>>ยกเลิก</option>
			<option value="5" <?php echo is_selected('5', $status); ?>>หมดอายุ</option>
		</select>
  </div>

	<div class="col-lg-1 col-md-2 col-sm-2 col-xs-6 padding-5">
    <label>การอนุมัติ</label>
    <select class="form-control input-sm" name="is_approve" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected('0', $is_approve); ?>>รออนุมัติ</option>
			<option value="1" <?php echo is_selected('1', $is_approve); ?>>อนุมัติแล้ว</option>
			<option value="3" <?php echo is_selected('3', $is_approve); ?>>ไม่อนุมัติ</option>
			<option value="-1" <?php echo is_selected('-1', $is_approve); ?>>ไม่ต้องอนุมัติ</option>
		</select>
  </div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="fromDate" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="toDate" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>

	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
		<label>SAP</label>
		<select name="sap" class="form-control input-sm" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="0" <?php echo is_selected('0', $sap); ?>>ยังไม่เข้า</option>
			<option value="1" <?php echo is_selected('1', $sap); ?>>เข้าแล้ว</option>
		</select>
	</div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-4 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
		<p  class="pull-right top-p">
			ว่างๆ = ปกติ, &nbsp;
			<span class="red bold">NC</span> = สินค้าไม่ครบ, &nbsp;
			<span class="orange bold">DF</span> = ยังไม่บันทึก, &nbsp;
			<span class="blue bold">AP</span> = รออนุมัติ, &nbsp;
			<span class="orange bold">WC</span> = รอยืนยัน, &nbsp;
			<span class="purple bold">OP</span> = อยู่ที่ WMS, &nbsp;
			<span class="red bold">CN</span> = ยกเลิก, &nbsp;
			<span class="red bold">NE</span> = ยังไม่ส่งออก, &nbsp;
			<span class="dark bold">EXP</span> = หมดอายุ &nbsp;
		</p>
	</div>
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
		<table class="table table-striped border-1" style="min-width:1000px;">
			<thead>
				<tr>
					<th class="fix-width-100 middle"></th>
					<th class="fix-width-50 middle text-center">ลำดับ</th>
					<th class="fix-width-100 middle text-center">วันที่</th>
					<th class="fix-width-120 middle">เลขที่เอกสาร</th>
					<th class="fix-width-100 middle">SAP NO</th>
					<th class="fix-width-150 middle">ต้นทาง</th>
					<th class="fix-width-150 middle">ปลายทาง</th>
					<th class="fix-width-40 middle text-center">สถานะ</th>
					<th class="fix-width-60 middle text-center">อนุมัติ</th>
					<th class="fix-width-150 middle">พนักงาน</th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($docs)) : ?>
          <?php $no = $this->uri->segment(4) + 1; ?>
          <?php foreach($docs as $rs) : ?>
						<?php $color = $rs->valid == 0 ? 'color:red;' : 'color:black;'; ?>
            <tr id="row-<?php echo $rs->code; ?>" style="<?php echo $color; ?> <?php echo statusBackgroundColor($rs->is_expire, $rs->status); ?>">
							<td class="middle">
								<button type="button" class="btn btn-minier btn-info" onclick="goDetail('<?php echo $rs->code; ?>')"><i class="fa fa-eye"></i></button>
								<?php if(($rs->status == -1 OR $rs->status == 0 )&& $rs->is_expire == 0 && $this->pm->can_edit) : ?>
									<button type="button" class="btn btn-minier btn-warning" onclick="goEdit('<?php echo $rs->code; ?>')"><i class="fa fa-pencil"></i></button>
								<?php endif; ?>
								<?php if(($rs->status == -1 OR $rs->status == 0 OR $rs->status == 3 OR $rs->status == 4) && $this->pm->can_delete) : ?>
									<button type="button" class="btn btn-minier btn-danger" onclick="goDelete('<?php echo $rs->code; ?>', <?php echo $rs->status; ?>)"><i class="fa fa-trash"></i></button>
								<?php endif; ?>
							</td>
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle text-center"><?php echo thai_date($rs->date_add); ?></td>
              <td class="middle"><?php echo $rs->code; ?></td>
							<td class="middle"><?php echo $rs->inv_code; ?></td>
              <td class="middle"><?php echo $rs->from_warehouse_name; ?></td>
              <td class="middle"><?php echo $rs->to_warehouse_name; ?></td>
              <td class="middle text-center">
								<?php if($rs->is_expire == 1) : ?>
									<span class="dark">EXP</span>
								<?php else : ?>
									<?php if($rs->status == 2) : ?>
										<span class="red">CN</span>
									<?php endif; ?>
									<?php if($rs->status == -1) : ?>
										<span class="orange">DF</span>
									<?php endif; ?>
									<?php if($rs->status == 0) : ?>
										<span class="blue">AP</span>
									<?php endif; ?>
									<?php if($rs->status == 3) : ?>
										<span class="purple">OP</span>
									<?php endif; ?>
									<?php if($rs->status == 4) : ?>
										<span class="orange">WC</span>
									<?php endif; ?>
									<?php if($rs->status == 1 && $rs->is_export == 0) : ?>
										<span class="red">NE</span>
									<?php endif; ?>
									<?php if($rs->status == 1 && $rs->valid == 0) : ?>
										<span class="red">NC</span>
									<?php endif; ?>
								<?php endif; ?>
							</td>
							<td class="middle text-center">
								<?php if($rs->must_approve == 1) : ?>
									<?php if($rs->is_approve == 1) : ?>
										<span class="green">Y</span>
									<?php elseif($rs->is_approve == 3) : ?>
										<span class="red">R</span>
									<?php else : ?>
										<span class="orange">P</span>
									<?php endif; ?>
								<?php endif; ?>
							</td>
							<td class="middle"><?php echo $rs->display_name; ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>

<?php $this->load->view('cancle_modal'); ?>

<script src="<?php echo base_url(); ?>scripts/transfer/transfer.js?v=<?php echo date('Ymd'); ?>"></script>
<script>
$('#from-warehouse').select2();
$('#to-warehouse').select2();
$('#user').select2();

function sendToSAP(code)
{
	load_in();
	$.ajax({
		url:HOME + 'export_transfer/' + code,
		type:'POST',
		cache:false,
		success:function(rs){
			load_out();
			if(rs == 'success'){
				swal({
					title:'Success',
					text:'ส่งข้อมูลไป SAP เรียบร้อยแล้ว',
					type:'success',
					timer:1000
				});
				$('#row-'+code).remove();
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
