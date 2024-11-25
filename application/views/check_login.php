<!doctype html>
<title>Please login</title>
<head>
	<script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
	<script> var BASE_URL = "<?php echo base_url(); ?>"; </script>
</head>
<body>

	<script>
		window.addEventListener('load', function() {
			let deviceId =
		})
	</script>
</body>
<article>
    <h1>We&rsquo;ll be back soon!</h1>
    <div>
        <p>Sorry for the inconvenience but we&rsquo;re performing some maintenance at the moment. If you need to you can always contact us, otherwise we&rsquo;ll be back online shortly!</p>
        <p>&mdash; The Team</p>
				<?php if($this->pm->can_add OR $this->pm->can_edit OR $this->pm->can_delete) : ?>
					<p style="float:right;"><button style="padding:15px;" onclick="openSystem()">OPEN SYSTEM</button></p>
					<script>
						function openSystem(){
							$.get(BASE_URL + 'setting/maintenance/open_system',function(rs){
								if(rs == 'success'){
									window.location.href = BASE_URL;
								}
							});
						}

						setInterval(function(){
							$.get(BASE_URL + 'setting/maintenance/check_open_system', function(rs){
								if(rs == 'open'){
									window.location.href = BASE_URL;
								}
							});
						}, 3000);
					</script>
				<?php endif; ?>
    </div>
</article>

<article>

</article>
