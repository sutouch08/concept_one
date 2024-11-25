<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-8 padding-5">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
  </div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-4 padding-5">
			<p class="pull-right top-p">
				<button type="button" class="btn btn-sm btn-primary" onclick="process()">Process</button>
			</p>
		</div>
</div><!-- End Row -->
<hr class=""/>

<?php echo $this->pagination->create_links(); ?>

<div class="row">
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padding-5 table-responsive">
    <table class="table table-striped border-1 dataTable" style="min-width:1360px;">
      <thead>
        <tr>
          <th class="fix-width-60 text-center">ลำดับ</th>
          <th class="fix-width-120">เลขที่เอกสาร </th>
          <th class="fix-width-60 text-center">สถานะ</th>
					<th class="min-width-100">หมายเหตุ</th>
        </tr>
      </thead>
      <tbody>
<?php if(!empty($orders))  : ?>
<?php $no = $this->uri->segment(5) + 1; ?>
<?php   foreach($orders as $rs)  : ?>

        <tr class="font-size-12" id="row-<?php echo $rs->id; ?>">
          <td class="middle text-center no"><?php echo $no; ?></td>
          <td class="middle"><?php echo $rs->code; ?></td>
					<td class="middle text-center" id="status-label-<?php echo $rs->id; ?>">
            <?php if($rs->status == 0) : ?>
              <span class="blue">NC</span>
            <?php elseif($rs->status == 3) : ?>
              <span class="red">ERROR</span>
						<?php elseif($rs->status == 1) : ?>
							<span class="green">สำเร็จ</span>
            <?php endif; ?>
          </td>
          <td class="middle">
            <?php
            if($rs->status == 3)
            {
              echo $rs->message;
            }
            ?>
          </td>
        </tr>
<?php  $no++; ?>
<?php endforeach; ?>
<?php else : ?>
      <tr>
        <td colspan="14" class="text-center"><h4>ไม่พบรายการ</h4></td>
      </tr>
<?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/wms/wms_temp_delivery.js?v=<?php echo date('Ymd'); ?>"></script>
<script>
	function process() {
		load_in();
		$.ajax({
			url:BASE_URL + "auto/auto_send_delivery/process",
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

</script>
<?php $this->load->view('include/footer'); ?>
