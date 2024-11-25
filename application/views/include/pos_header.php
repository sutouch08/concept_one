
<!DOCTYPE html>
<html lang="th">
	<head>
		<meta charset="utf-8" />

		<title><?php echo $this->title; ?></title>
		<meta name="description" content="" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=10.0" />
		<link rel="shortcut icon" href="<?php echo base_url(); ?>assets/img/favicon.ico">

		<?php $this->load->view('include/header_include'); ?>

		<style>
			.ui-helper-hidden-accessible {
				display:none;
			}

			.ui-autocomplete {
		    max-height: 250px;
		    overflow-y: auto;
		    /* prevent horizontal scrollbar */
		    overflow-x: hidden;
			}

			.ui-widget {
				width:auto;
			}
	</style>
	</head>
	<body class="no-skin">
		<div id="loader" style="z-index:10000;">
        <div class="loader"></div>
		</div>
		<div id="loader-backdrop" style="position: fixed; width:100vw; height:100vh; background-color:white; opacity:0.3; display:none; z-index:9999;"></div>

		<!-- #section:basics/navbar.layout -->
		<div id="navbar" class="navbar navbar-default">
			<script type="text/javascript">
				var BASE_URL = '<?php echo base_url(); ?>';
				var HOME = '<?php echo $this->home . '/'; ?>';
			</script>
			<div class="navbar-container no-padding" id="navbar-container">

				<!-- #section:basics/sidebar.mobile.toggle -->
				<button type="button" class="navbar-toggle menu-toggler pull-left hide" id="menu-toggler" data-target="#sidebar">
					<span class="sr-only">Toggle sidebar</span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>

				<div class="navbar-header pull-left">
					<a href="<?php echo base_url(); ?>" class="navbar-brand">
						<small>
							<i class="fa fa-home"></i>
						</small>
					</a>
					<a href="#" class="navbar-brand">
						<small>
							<?php echo $this->title; ?>
						</small>
					</a>
				</div>

				<div class="navbar-buttons navbar-header pull-right" role="navigation" style="margin-right:5px;">
					<ul class="nav ace-nav">
						<li class="light-blue">
							<a data-toggle="dropdown" href="#" class="dropdown-toggle">
								<span class="user-info">
									<small>Welcome</small>
									<?php echo get_cookie('displayName'); ?>
								</span>
								<i class="ace-icon fa fa-caret-down"></i>
							</a>
							<ul class="user-menu dropdown-menu-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
								<li>
									<a href="JavaScript:void(0)" onclick="changeUserPwd('<?php echo get_cookie('uname'); ?>')">
										<i class="ace-icon fa fa-keys"></i>
										เปลี่ยนรหัสผ่าน
									</a>
								</li>
								<li class="divider"></li>
								<li>
									<a href="<?php echo base_url(); ?>users/authentication/pos_logout">
										<i class="ace-icon fa fa-power-off"></i>
										ออกจากระบบ
									</a>
								</li>
							</ul>
						</li>
					</ul>
				</div>

			</div><!-- /.navbar-container -->
		</div>

		<!-- /section:basics/navbar.layout -->
		<div class="main-container" id="main-container">
					<!-- /section:basics/sidebar -->
			<div class="main-content">
				<div class="main-content-inner">

					<div class="page-content" style="padding-bottom:0px;">

								<!-- PAGE CONTENT BEGINS -->
								<?php
								//--- if user don't have permission to access this page then deny_page;
									if($this->pm->can_view == 0)
									{
										$this->load->view('deny_page');
									}
								?>
