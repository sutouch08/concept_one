<?php $this->load->view('include/header'); ?>
<div class="row">
	<div class="col-sm-12">
    	<center><h1><i class="fa fa-frown-o"></i></h1></center>
        <center><h3>Oops.. Something went wrong.</h3></center>
        <center><h4>Please go back and try to come back again.</h4></center>
		<?php if(!empty($error_message)) : ?>
				<center><h4><?php echo $error_message; ?></center>
		<?php endif; ?>
				<center><button type="button" class="btn btn-lg btn-warning" onclick="goBack()"><i class="fa fa-arrow-left"></i> กลับ</button></center>
    </div>
</div>
<script>
	function goBack(){
		window.location.href = "<?php echo $this->home; ?>";
	}
</script>

<?php $this->load->view('include/footer'); ?>
