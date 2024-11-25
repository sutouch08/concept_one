<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label>เลขที่ออเดอร์</label>
		<input type="text" class="form-control input-sm search-box" name="reference" value="<?php echo $reference; ?>"/>
	</div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label>บริษัท</label>
		<input type="text" class="form-control input-sm search-box" name="company" value="<?php echo $company; ?>"/>
	</div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label>ชื่อผู้ลูกค้า</label>
		<input type="text" class="form-control input-sm search-box" name="name" value="<?php echo $name; ?>"/>
	</div>

	<div class="col-sm-1 col-1-harf col-xs-6 padding-5">
		<label>เบอร์โทร</label>
		<input type="text" class="form-control input-sm search-box" name="phone" value="<?php echo $phone; ?>"/>
	</div>

	<div class="col-sm-2 col-xs-6 padding-5">
    <label>วันที่</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>
	<div class="col-sm-1 col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-sm-1 col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>

</div>
</form>
<hr class="margin-top-15 padding-5"/>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-sm-12 col-xs-12 padding-5">
		<table class="table table-striped border-1">
			<thead>
				<tr>
					<th class="width-5 middle text-center">#</th>
					<th class="width-8 middle">วันที่</th>
					<th class="width-20 middle">เลขที่ออเดอร์</th>
					<th class="width-15 middle">บริษัท</th>
					<th class="width-15 middle">ชื่อลูกค้า</th>
					<th class="width-10 middle">เบอร์โทร</th>
					<th class="width-8 middle text-right">จำนวน</th>
					<th class="width-10 middle text-right">มูลค่า</th>
					<th class="middle"></th>
				</tr>
			</thead>
			<tbody>
        <?php if(!empty($data)) : ?>
          <?php $no = $this->uri->segment(5) + 1; ?>
					<?php $ci =& get_instance(); ?>
          <?php foreach($data as $rs) : ?>
            <tr>
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle"><?php echo thai_date($rs->tempDate, FALSE, '/'); ?></td>
              <td class="middle"><?php echo $rs->reference; ?></td>
              <td class="middle"><?php echo (empty($rs->sCompany) ? $rs->bCompany : $rs->sCompany); ?></td>
              <td class="middle"><?php echo (empty($rs->sFirstName) ? $rs->bFirstName.' '.$rs->bLastName : $rs->sFirstName.' '.$rs->sLastName); ?></td>
							<td class="middle"><?php echo (empty($rs->sPhone) ? $rs->bPhone : $rs->sPhone); ?></td>
							<td class="middle text-right"><?php echo number($ci->get_order_qty($rs->id)); ?></td>
							<td class="middle text-right"><?php echo number($ci->get_order_amount($rs->id, 2)); ?></td>
              <td class="middle text-right">
              	<button type="button" class="btn btn-mini btn-info" onclick="viewDetail(<?php echo $rs->id; ?>)"><i class="fa fa-eye"></i> รายละเอียด</button>
              </td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>


<script>
	var HOME = "<?php echo current_url(); ?>/";
	function goBack() {
		window.location.href = HOME;
	}

	function getSearch() {
		$('#searchForm').submit();
	}


	function viewDetail(id) {
		var prop 			= "width=1100, height=900. left="+center+", scrollbars=yes";
	  var center 	= ($(document).width() - 1100)/2;
		var target 	= HOME + 'view_detail/'+id+'?nomenu';
		window.open(target, "_blank", prop );
	}



	$('.search-box').keyup(function(e) {
		if(e.keyCode == 13) {
			getSearch();
		}
	})


	function clearFilter() {
		$.get(HOME+'/clear_filter', function(){
			goBack();
		})
	}


	$("#fromDate").datepicker({
		dateFormat: 'dd-mm-yy',
		onClose: function(ds){
			$("#toDate").datepicker("option", "minDate", ds);
		}
	});

	$("#toDate").datepicker({
		dateFormat: 'dd-mm-yy',
		onClose: function(ds){
			$("#fromDate").datepicker("option", "maxDate", ds);
		}
	});
</script>


<?php $this->load->view('include/footer'); ?>
