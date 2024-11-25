<script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
<script>
  var HOME = '<?php echo $this->home; ?>';
  var BASE_URL = '<?php echo base_url(); ?>';
</script>

<div id="result"></div>
<script>

$(document).ready(function() {
  doExport();
});


function doExport(){
  $.ajax({
    url:HOME + '/do_export',
    type:'POST',
    cache:false,
    success:function(rs){
      $('#result').text(rs);
      window.close();
    }
  });
}
</script>
