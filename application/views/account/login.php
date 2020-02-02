<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>AdminLTE 2 | Dashboard</title>
		<!-- Tell the browser to be responsive to screen width -->
		<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
		<!-- Bootstrap 3.3.7 -->
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bootstrap/css/bootstrap.min.css">
		<!-- Font Awesome -->
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/font-awesome/css/font-awesome.min.css">
		<!-- AdminLTE Skins. Choose a skin from the css/skins
		folder instead of downloading all of them to reduce the load. -->
		<link rel="stylesheet" href="<?php echo base_url(); ?>assets/admin-lte/css/skins/_all-skins.min.css">
		<!-- datatables -->
		<link rel="stylesheet" href="<?php echo base_url() ?>assets/datatables/datatables.min.css">

		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->

		<!-- Google Font -->
		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
	</head>
	<body style="background-color: #eaeaea;">


			<div style="width: 400px; height: auto; background-color: #dfdfdf; margin: 0; position: absolute; top: 50%; left: 50%; -ms-transform: translate(-50%, -50%); transform: translate(-50%, -50%); border: 1px solid #cacaca; border-radius: 4px; padding: 16px;">
				<div class="form">
					<div class="form-group">
						<label style="">Finance Manager</label>
					</div>
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon"><div class="glyphicon glyphicon-user"></div></div>
							<input type="text" name="username" class="form-control" placeholder="Username" />
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-addon"><div class="glyphicon glyphicon-lock"></div></div>
							<input type="password" name="password" class="form-control" placeholder="Password" />
						</div>
					</div>
					<button class="btn btn-primary form-control" onclick="signin()">Login</button>
				</div>
			</div>

			<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
			<script type="text/javascript">
				function signin() {
					var username = $('input[name="username"]').val();
					var password = $('input[name="password"]').val();
					password = CryptoJS.MD5(password).toString();
					
					$.ajax({
						type: 'POST',
						url: '<?php echo base_url('account/login'); ?>',
						data: {username: username, password: password},
						cache: false,
						success: function(result) {
							if (result) {
								window.location.replace("<?php echo base_url(); ?>");
							} else {
								console.log("error");
							}
						}
					});
				}
			</script>


	<!-- jQuery 3 -->
	<script src="<?php echo base_url(); ?>assets/jquery/jquery.min.js"></script>
	<!-- jQuery UI 1.11.4 -->
	<script src="<?php echo base_url(); ?>assets/jquery-ui/jquery-ui.min.js"></script>
	<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
	<script>
	$.widget.bridge('uibutton', $.ui.button);
	</script>
	<!-- Bootstrap 3.3.7 -->
	<script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
	<!-- highcharts -->
	<script src="<?php echo base_url(); ?>assets/highcharts/highcharts.js"></script>
	<!-- Slimscroll -->
	<script src="<?php echo base_url(); ?>assets/jquery-slimscroll/jquery.slimscroll.min.js"></script>
	<!-- datatables -->
	<script src="<?php echo base_url(); ?>assets/datatables/datatables.min.js"></script>
	<!-- AdminLTE App -->
	<script src="<?php echo base_url(); ?>assets/admin-lte/js/adminlte.min.js"></script>
	<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
	<script src="<?php echo base_url(); ?>assets/admin-lte/js/pages/dashboard.js"></script>
	<!-- AdminLTE for demo purposes -->
	<script src="<?php echo base_url(); ?>assets/admin-lte/js/demo.js"></script>

	<!-- add script of each module -->
	</body>
</html>