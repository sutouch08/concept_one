<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5">
    <h4 class="title"><?php echo $this->title; ?></h4>
  </div>
</div><!-- End Row -->
<hr class="padding-5"/>
<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>เลขที่อ้างอิง</label>
		<input type="text" class="form-control input-sm search-box" name="reference" value="<?php echo $reference; ?>"/>
	</div>
	<div class="col-lg-2 col-md-2 col-sm-2 col-xs-6 padding-5">
		<label>เลขที่ WO</label>
		<input type="text" class="form-control input-sm search-box" name="order_code" value="<?php echo $order_code; ?>"/>
	</div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Status</label>
		<select class="form-control input-sm filter" name="status">
			<option value="all">ทั้งหมด</option>
			<option value="S" <?php echo is_selected('S', $status); ?>>Success</option>
			<option value="E" <?php echo is_selected('E', $status); ?>>Failed</option>
			<option value="D" <?php echo is_selected('D', $status); ?>>Skip</option>
		</select>
	</div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
		<label>Action</label>
		<select class="form-control input-sm" name="action" onchange="getSearch()">
			<option value="all">All</option>
			<option value="A" <?php echo is_selected('A', $action); ?>>Add</option>
			<option value="U" <?php echo is_selected('U', $action); ?>>Update</option>
		</select>
	</div>

	<div class="col-lg-2 col-md-2-harf col-sm-3 col-xs-6 padding-5">
    <label>Date</label>
    <div class="input-daterange input-group">
      <input type="text" class="form-control input-sm width-50 text-center from-date" name="from_date" id="fromDate" value="<?php echo $from_date; ?>" />
      <input type="text" class="form-control input-sm width-50 text-center" name="to_date" id="toDate" value="<?php echo $to_date; ?>" />
    </div>
  </div>

	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block" onclick="getSearch()"><i class="fa fa-search"></i> Search</button>
  </div>
	<div class="col-lg-1 col-md-1-harf col-sm-1-harf col-xs-6 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>

</div>
<input type="hidden" name="search" value="1" />
</form>
<hr class="margin-top-15 padding-5"/>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 table-responsive padding-5">
		<table class="table table-striped border-1" style="min-width:1050px;">
			<thead>
				<tr>
					<th class="fix-width-40 middle text-center">#</th>
					<th class="fix-width-150 middle">วันที่</th>
					<th class="fix-width-200 middle">Reference</th>
					<th class="fix-width-200 middle">WO No.</th>
					<th class="fix-width-80 middle text-center">Action</th>
					<th class="fix-width-80 middle text-center">Status</th>
					<th class="min-width-150 middle">Message</th>
					<th class="fix-width-150">User</th>
				</tr>
			</thead>
			<tbody>
        <?php if( ! empty($logs)) : ?>
          <?php $no = $this->uri->segment(5) + 1; ?>
          <?php foreach($logs as $rs) : ?>
            <tr>
              <td class="middle text-center"><?php echo $no; ?></td>
              <td class="middle"><?php echo thai_date($rs->date_upd, TRUE, '/'); ?></td>
              <td class="middle"><?php echo $rs->reference; ?></td>
							<td class="middle"><?php echo $rs->order_code; ?></td>
							<td class="middle text-center"><?php echo $rs->action == 'U' ? 'Update' : 'Add'; ?></td>
              <td class="middle text-center"><?php echo $rs->status == 'E' ? 'Failed' : ($rs->status == 'D' ? 'Skip' : 'Success'); ?></td>
              <td class="middle"><?php echo $rs->message; ?></td>
							<td class="middle"><?php echo $rs->user; ?></td>
            </tr>
            <?php $no++; ?>
          <?php endforeach; ?>
        <?php endif; ?>
			</tbody>
		</table>
	</div>
</div>


<script>
	var HOME = "<?php echo $this->home; ?>";

	function goBack() {
		window.location.href = HOME;
	}

	function getSearch() {
		$('#searchForm').submit();
	}


	$('.search-box').keyup(function(e) {
		if(e.keyCode == 13) {
			getSearch();
		}
	})

	$('.filter').change(function() {
		getSearch();
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

	function viewDetail(id) {
		//--- properties for print
		var center    = ($(document).width() - 800)/2;
		var prop 			= "width=800, height=900. left="+center+", scrollbars=yes";

		var target  = HOME + '/view_detail/'+id+'?nomenu';
	 	window.open(target, '_blank', prop);
	}
</script>


<?php $this->load->view('include/footer'); ?>
