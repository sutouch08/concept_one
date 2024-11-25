<?php $this->load->view('include/header'); ?>
<div class="row">
  <div class="col-sm-6">
    <h3 class="title"><?php echo $this->title; ?></h3>
  </div>
  <div class="col-sm-6">
    <p class="pull-right top-p">
      <button type="button" class="btn btn-sm btn-primary" id="btn-sync" onclick="syncData()"><i class="fa fa-retweet"></i> Sync Data</button>
      <button type="button" class="btn btn-sm btn-warning hide" id="btn-stop" onclick="stopSync()"><i class="fa fa-stop"></i> Stop Sync</button>
    </p>
  </div>
</div>
<hr>
<div class="row hide" id="progress">
  <div class="col-sm-8 margin-bottom-10">
    <h4 class="title" id="txt-label">Waiting for action</h4>
  </div>
  <div class="col-sm-8">
    <div class="progress pos-rel progress-striped" style="background-color:#CCC;" id="txt-percent" data-percent="0%">
			<div class="progress-bar progress-bar-primary" id="progress-bar" style="width: 0%;"></div>
		</div>
  </div>
</div>

<script src="<?php echo base_url(); ?>scripts/sync_products.js"></script>
<?php $this->load->view('include/footer'); ?>
