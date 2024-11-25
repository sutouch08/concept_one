<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-6">
    <h3 class="title">
      <?php echo $this->title; ?>
    </h3>
    </div>
    <div class="col-sm-6">
    	<p class="pull-right top-p">

      </p>
    </div>
</div><!-- End Row -->
<hr class=""/>

<form id="searchForm" method="post" action="<?php echo current_url(); ?>">
<div class="row">
  <div class="col-sm-2 padding-5 first">
    <label>รหัสสินค้า</label>
    <input type="text" class="form-control input-sm search" id="item_code" name="item_code"  value="<?php echo $item_code; ?>" />
  </div>

  <div class="col-sm-2 padding-5">
    <label>โซน</label>
    <input type="text" class="form-control input-sm search" id="zone_code" name="zone_code" value="<?php echo $zone_code; ?>" />
  </div>

	<div class="col-sm-2 padding-5">
    <label>โซน System</label>
    <select class="form-control input-sm" id="show_system" name="show_system">
			<option value="no" <?php echo is_selected('no', $show_system); ?>>ไม่แสดง</option>
			<option value="yes" <?php echo is_selected('yes', $show_system); ?>>แสดง</option>
		</select>
  </div>
	
  <div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="submit" class="btn btn-xs btn-primary btn-block"><i class="fa fa-search"></i> ค้นหา</button>
  </div>
	<div class="col-sm-1 padding-5">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-warning btn-block" onclick="clearFilter()"><i class="fa fa-retweet"></i> Reset</button>
  </div>
  <div class="col-sm-1 col-1-harf padding-5 last">
    <label class="display-block not-show">buton</label>
    <button type="button" class="btn btn-xs btn-purple btn-block" onclick="doExport()"><i class="fa fa-file-excel-o"></i> Download</button>
  </div>
</div>
<hr class="margin-top-15">
</form>
<form id="exportFrom" method="post" action="<?php echo $this->home; ?>/export">
  <input type="hidden" id="item" name="item">
  <input type="hidden" id="zone" name="zone">
	<input type="hidden" id="system" name="system">
  <input type="hidden" id="token" name="token" value="<?php echo uniqid(); ?>">
</form>
<?php echo $this->pagination->create_links(); ?>
<div class="row">
  <div class="col-sm-12">
    <table class="table table-striped border-1">
      <tr>
        <th class="width-5 text-center">ลำดับ</th>
        <th class="width-25">สินค้า</th>
        <th class="width-20">รหัสโซน</th>
        <th class="width-40">ชื่อโซน</th>
        <th class="width-10 text-center">คงเหลือ</th>
      </tr>
      <tbody>
    <?php if( !empty($data)) : ?>
    <?php $no = $this->uri->segment(4) + 1; ?>
    <?php foreach($data as $rs) : ?>
      <tr class="font-size-12">
        <td class="text-center no"><?php echo $no; ?></td>
        <td>
          <?php echo $rs->ItemCode; ?>
          <?php
            if(!empty($rs->U_OLDCODE))
            {
              echo " | ".$rs->U_OLDCODE;
            }
            ?>
        </td>
        <td><?php echo $rs->BinCode; ?></td>
        <td><?php echo $rs->Descr; ?></td>
    		<td class="text-center"><?php echo number($rs->OnHandQty); ?></td>
      </tr>
    <?php  $no++; ?>
    <?php endforeach; ?>
    <?php else : ?>
      <tr>
        <td colspan="5" class="text-center">--- ไม่พบข้อมูล ---</td>
      </tr>
    <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<script>

  function getSearch()
  {
    $('#searchForm').submit();
  }

  function clearFilter()
  {
    $.get(BASE_URL + 'inventory/consign_stock/clear_filter', function(){
      window.location.href = "<?php echo $this->home; ?>";
    });
  }

  $('.search').keyup(function(e){
    if(e.keyCode == 13){
      var item = $('#item_code').val();
      var zone = $('#zone_code').val();
      if(item.length > 0 || zone.length > 0){
        getSearch();
      }
    }
  })


  function doExport(){
    var item = $('#item_code').val();
    var zone = $('#zone_code').val();
		var system = $('#show_system').val();
    var token = $('#token').val();
    if(item.length > 0 || zone.length > 0)
    {
      $('#item').val(item);
      $('#zone').val(zone);
			$('#system').val(system);
      get_download(token);
      $('#exportFrom').submit();
    }
  }
</script>

<?php $this->load->view('include/footer'); ?>
