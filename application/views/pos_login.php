<!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
		<meta charset="utf-8" />
		<title>Login Page - <?php echo getConfig('COMPANY_NAME'); ?></title>
		<meta name="description" content="User login page" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" />

		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/bootstrap.css" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/font-awesome.css" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ace-fonts.css" />
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/ace.css" />

		<script src="<?php echo base_url(); ?>assets/js/ace-extra.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/jquery.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/jquery-ui-1.10.4.custom.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/bootstrap.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/ace/ace.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/sweet-alert.js"></script>
		<script src="<?php echo base_url(); ?>assets/js/handlebars-v3.js"></script>

		<script>	var BASE_URL = <?php echo base_url(); ?>	</script>
	</head>

	<body class="login-layout blur-login">
		<div class="main-container">
			<div class="main-content">
				<div class="row">
					<div class="col-sm-10 col-sm-offset-1">
						<div class="center">
							<h1>
								<span class="orange">POS</span>
								<span class="white" id="shop-name">Application</span>
							</h1>
							<h4 class="blue width-100 text-center" id="id-company-text">&copy; <?php echo getConfig('COMPANY_FULL_NAME');?></h4>
						</div>
						<div class="login-container">

							<div class="space-6"></div>

							<div class="position-relative">
								<div id="login-box" class="login-box visible widget-box no-border">
									<div class="widget-body">
										<div class="widget-main">
											<h4 class="header blue lighter bigger">

												Please Enter Your Information
											</h4>

											<div class="space-6"></div>
												<fieldset>
													<label class="block clearfix">
														<select class="form-control" name="user_name" id="uname"></select>
													</label>

													<label class="block clearfix">
														<span class="block input-icon input-icon-right">
															<input type="password" name="password" id="pwd" class="form-control" placeholder="Password" />
															<i id="pwd-btn" class="ace-icon fa fa-eye" onclick="showPwd()"></i>
														</span>
													</label>

													<div class="space"></div>

													<div class="clearfix">

														<label class="inline" id="rem-label">
															<input type="checkbox" name="remember" id="rem-box" class="ace" value="1" checked/>
															<span class="lbl"> Remember Me</span>
														</label>

														<input type="hidden" id="deviceId" value="" />
														<!-- Bypass robot-->
														<span style="display:none;" id="ipwd"></span>

														<button type="button" id="btn-login" class="width-35 pull-right btn btn-sm btn-primary">
															<i class="ace-icon fa fa-sign-in"></i>
															<span class="bigger-110">Login</span>
														</button>
													</div>

													<div class="space-4"></div>
													<div class="clearfix">
														<div class="space-4"></div>
														<div class="space-4"></div>
														<p class="text-center red" id="login-error">&nbsp;</p>
													</div>
												</fieldset>
										</div><!-- /.widget-main -->
									</div><!-- /.widget-body -->
								</div><!-- /.login-box -->
							</div><!-- /.position-relative -->
						</div>
					</div><!-- /.col -->
				</div><!-- /.row -->
			</div><!-- /.main-content -->
		</div><!-- /.main-container -->
	</body>

	<script id="user-drop-down" type="text/x-handlebarsTemplate">
		<option value="">Select User</option>
		{{#each this}}
			<option value="{{uname}}">{{uname}}</option>
		{{/each}}
	</script>

	<script id="no-user-drop-down" type="text/x-handlebarsTemplate">
		<option value="">No User<option>
	</script>

	<script src="<?php echo base_url(); ?>scripts/template.js?v=<?php echo date('Ymd'); ?>"></script>
	<script src="<?php echo base_url(); ?>scripts/pos_login.js?v=<?php echo date('Ymd'); ?>"></script>
</html>
