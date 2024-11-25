<script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
<script>
  var HOME = '<?php echo $this->home; ?>';
  var BASE_URL = '<?php echo base_url(); ?>';
</script>
<div id="stat">
  <span style="float:left; margin-right:20px; font-size:32px;">Order Exported</span>
  <h4 id="stat-qty" style="font-size:32px; margin-bottom:10px;">0</h4>
</div>
<hr/>
<div id="result"></div>
<script src="<?php echo base_url(); ?>scripts/auto/auto_delivery_order.js?v=1"></script>
