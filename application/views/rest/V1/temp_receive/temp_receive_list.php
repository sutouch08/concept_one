<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
		<div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
			<p class="pull-right top-p">
				<?php if($this->_SuperAdmin) : ?>
				<button type="button" class="btn btn-sm btn-primary" onclick="process()">Process</button>
				<?php endif; ?>
			</p>
		</div>
</div><!-- End Row -->
<hr class=""/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label>เลขที่เอกสาร</label>
    <input type="text" class="form-control input-sm search" name="code"  value="<?php echo $code; ?>" />
  </div>
	<div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>เลขที่อ้างอิง</label>
    <input type="text" class="form-control input-sm search" name="reference"  value="<?php echo $reference; ?>" />
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label>ประเภท</label>
    <select class="form-control input-sm" name="type" onchange="getSearch()">
			<option value="all">ทั้งหมด</option>
			<option value="RT" <?php echo is_selected('RT', $type); ?>>RT</option>
			<option value="RN" <?php echo is_selected('RN', $type); ?>>RN</option>
			<option value="SM" <?php echo is_selected('SM', $type); ?>>SM</option>
			<option value="WR" <?php echo is_selected('WR', $type); ?>>WR</option>
			<option value="WW" <?php echo is_selected('WW', $type); ?>>WW</option>
			<option value="RC" <?php echo is_selected('RC', $type); ?>>RC</option>
		</select>
  </div>

  <div class="col-lg-1-harf col-md-1-harf col-sm-1-harf col-xs-3 padding-5">
    <label>สถานะ</label>
    <select class="form-control input-sm" name="status" onchange="getSearch()">
      <option value="all">ทั้งหมด</option>
      <option value="1" <?php echo is_selected('1', $status); ?>>เข้าแล้ว</option>
      <option value="0" <?php echo is_selected('0', $status); ?>>ยังไม่เข้า</option>
      <option value="3" <?php echo is_selected('3', $status); ?>>Error</option>
			<option value="2" <?php echo is_selected('2', $status); ?>>Closed</option>
    </select>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>Received Date</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="received_from_date" id="receivedFromDate" value="<?php echo $received_from_date; ?>">
      <input type="text" class="form-control input-sm width-50 text-center" name="received_to_date" id="receivedToDate" value="<?php echo $received_to_date; ?>">
    </div>
  </div>

	<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6 padding-5">
    <label>วันที่เข้า temp</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>">
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>">
    </div>
  </div>

  <div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1 col-sm-1-harf col-xs-3 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
</div>
<hr class="margin-top-15 padding-5">
</form>
<?php echo $this->pagination->create_links(); ?>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <p class="pull-right">
      สถานะ : ว่างๆ = ปกติ, &nbsp;
      <span class="red">ERROR</span> = เกิดข้อผิดพลาด, &nbsp;
      <span class="blue">NC</span> = ยังไม่เข้า IX
    </p>
  </div>
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1 dataTable" style="min-width:1300px;">
      <thead>
        <tr>
          <th class="fix-width-40 text-center">ลำดับ</th>
					<th class="fix-width-150">Received Date </th>
          <th class="fix-width-120">เลขที่เอกสาร </th>
					<th class="fix-width-120">เลขที่อ้างอิง </th>
          <th class="fix-width-150">เข้า Temp</th>
          <th class="fix-width-150">เข้า IX</th>
          <th class="fix-width-60 text-center">สถานะ</th>
					<th class="fix-width-200">หมายเหตุ</th>
					<th class="fix-width-100">Closed by</th>
					<th class="fix-width-150"></th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($orders))  : ?>
<?php $no = $this->uri->segment(5) + 1; ?>
<?php   foreach($orders as $rs)  : ?>

        <tr class="font-size-12" id="row-<?php echo $rs->id; ?>">
          <td class="middle text-center"><?php echo $no; ?></td>
					<td class="middle"><?php echo (empty($rs->received_date) ? "" : thai_date($rs->received_date, TRUE)); ?></td>
          <td class="middle"><?php echo $rs->code; ?></td>
					<td class="middle"><?php echo $rs->reference; ?></td>
          <td class="middle" ><?php echo thai_date($rs->temp_date, TRUE); ?></td>

          <td class="middle">
						<?php
							if($rs->status != 0 && !empty($rs->ix_date))
							{
								echo thai_date($rs->ix_date, TRUE);
							}
					 	?>
				 	</td>
					<td class="middle text-center" id="status-label-<?php echo $rs->id; ?>">
            <?php if($rs->status == 0) : ?>
              <span class="blue">NC</span>
						<?php elseif($rs->status == 2) : ?>
							<span class="blue">Closed</span>
            <?php elseif($rs->status == 3) : ?>
              <span class="red">ERROR</span>
						<?php elseif($rs->status == 1) : ?>
							<span class="green">สำเร็จ</span>
            <?php endif; ?>
          </td>
          <td class="middle">
            <?php
            if($rs->status == 3 OR $rs->status == 2)
            {
              echo $rs->message;
            }
            ?>
          </td>
					<td class="middle" id="closed-by-<?php echo $rs->id; ?>"><?php echo $rs->closed_by; ?></td>
					<td class="middle text-right">
						<button type="button" class="btn btn-minier btn-info" title="Details" onclick="getDetails(<?php echo $rs->id; ?>)">
							<i class="fa fa-eye"></i>
						</button>
					<?php if($this->_SuperAdmin && $rs->status != 1) : ?>
						<?php if($rs->status != 2) : ?>
						<button type="button" class="btn btn-minier btn-warning" id="close-btn-<?php echo $rs->id; ?>" title="Close" onclick="closeOrder(<?php echo $rs->id;?>, '<?php echo $rs->code; ?>')">
							<i class="fa fa-times"></i>
						</button>
						<?php endif; ?>
						<button type="button" class="btn btn-minier btn-primary" onclick="doReceive(<?php echo $rs->id; ?>)">
							เอาเข้าทันที
						</button>
						<button type="button" class="btn btn-minier btn-danger" title="Delete" onclick="getDelete(<?php echo $rs->id; ?>, '<?php echo $rs->code; ?>')">
							<i class="fa fa-trash"></i>
						</button>

					<?php endif; ?>
					</td>
        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="10" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/wms/wms_temp_receive.js?v=<?php echo date('Ymd'); ?>"></script>
<script>
	function process() {
		load_in();
		$.ajax({
			url:BASE_URL + "auto/wms_auto_receive/do_receive",
			type:'GET',
			success:function(rs) {
				load_out();
				if(rs == 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(function(){
						window.location.reload();
					}, 1200);
				}
				else {
					swal({
						title:'Error',
						type:'error',
						text:rs,
						html:true
					});
				}
			},
			error:function(xhr, status, error) {
				load_out();
				swal({
					title:'Error',
					type:'error',
					text:xhr.responseText,
					html:true
				})
			}
		})
	}


	function doReceive(id)
	{
		load_in();
		$.ajax({
			url:BASE_URL + "auto/wms_auto_receive/do_receive/"+id,
			type:'GET',
			success:function(rs) {
				load_out();
				if(rs == 'success') {
					swal({
						title:'Success',
						type:'success',
						timer:1000
					});

					setTimeout(function(){
						window.location.reload();
					}, 1200);
				}
				else {
					swal({
						title:'Error',
						type:'error',
						text:rs,
						html:true
					});
				}
			},
			error:function(xhr, status, error) {
				load_out();
				swal({
					title:'Error',
					type:'error',
					text:xhr.responseText,
					html:true
				})
			}
		})
	}

	function getDelete(id, code) {
		swal({
			title:"Are you sure ?",
			text:'ต้องการลบ '+code+' หรือไม่ ?',
			type:'warning',
			showCancelButton: true,
			confirmButtonColor: '#DD6855',
			confirmButtonText: 'ใช่ ลบเลย',
			cancelButtonText: 'ยกเลิก',
			closeOnConfirm: false
		}, function() {
			doDelete(id);
		});
	}


	function doDelete(id){
		$.ajax({
			url:HOME + "delete/"+id,
			type:'POST',
			cache:false,
			success:function(rs) {
				if(rs == 'success') {
					swal({
						title:'Deleted',
						type:'success',
						timer:1000
					});

					setTimeout(function(){
						window.location.reload();
					}, 1200);
				}
				else {
					swal({
						title:'Error',
						text:rs,
						type:'error'
					})
				}
			}
		})
	}


	function closeOrder(id, code) {
		swal({
			title:"Are you sure ?",
			text:'ต้องการปิดรารการ '+code+' หรือไม่ ?',
			type:'warning',
			showCancelButton: true,
			confirmButtonColor: '#DD6855',
			confirmButtonText: 'ดำเนินการ',
			cancelButtonText: 'ยกเลิก',
			closeOnConfirm: false
		}, function() {
			close_temp(id);
		});
	}


	function close_temp(id){
		$.ajax({
			url:HOME + "close_temp/"+id,
			type:'POST',
			cache:false,
			success:function(rs) {
				if(isJson(rs)) {
					swal({
						title:'Closed',
						type:'success',
						timer:1000
					});

					var arr = $.parseJSON(rs);

					$('#status-label-'+id).html('<span class="blue">Closed</span>');
					$('#closed-by-'+id).text(arr.closed_by);
					$('#close-btn-'+id).remove();
				}
				else {
					swal({
						title:'Error',
						text:rs,
						type:'error'
					})
				}
			}
		})
	}




	$("#fromDate").datepicker({
	  dateFormat:'dd-mm-yy',
	  onClose:function(sd){
	    $("#toDate").datepicker('option', 'minDate', sd);
	  }
	});


	$("#toDate").datepicker({
	  dateFormat:'dd-mm-yy',
	  onClose:function(sd){
	    $("#fromDate").datepicker('option', 'maxDate', sd);
	  }
	});

	$("#receivedFromDate").datepicker({
	  dateFormat:'dd-mm-yy',
	  onClose:function(sd){
	    $("#receivedToDate").datepicker('option', 'minDate', sd);
	  }
	});


	$("#receivedToDate").datepicker({
	  dateFormat:'dd-mm-yy',
	  onClose:function(sd){
	    $("#receivedFromDate").datepicker('option', 'maxDate', sd);
	  }
	});


</script>
<?php $this->load->view('include/footer'); ?>
