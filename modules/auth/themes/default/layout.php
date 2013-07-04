<?php echo Html::doctype('html5'); ?>
<html lang="<?php echo \Config::get('language'); ?>">
	<head>
		<meta charset="utf-8" />
		<meta name="description" content="" />
		<meta name="author" content="" />
		<title><?php echo $title; ?></title>

		<!-- Le styles -->
		<?php echo Asset::css('bootstrap.css'); ?>
		<?php echo Asset::js('bootstrap.js'); ?>
		<style type="text/css">
		body {
			padding-top: 40px;
			padding-bottom: 40px;
			background-color: #f5f5f5;
		}

		#content
		{
			padding: 19px 29px 29px;
			background-color: #fff;
			border: 1px solid #e5e5e5;
			-webkit-border-radius: 5px;
			-moz-border-radius: 5px;
			border-radius: 5px;
			-webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
			-moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
			box-shadow: 0 1px 2px rgba(0,0,0,.05);
		}

		.form-signin .form-signin-heading,
		.form-signin .checkbox {
			margin-bottom: 10px;
		}
		.form-signin input[type="text"],
		.form-signin input[type="password"] {
			font-size: 16px;
			height: auto;
			margin-bottom: 15px;
			padding: 7px 9px;
		}

		</style>
		<?php echo Asset::css('bootstrap-responsive.css'); ?>

		<!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
			<?php echo Asset::js('html5shiv.js'); ?>
		<![endif]-->

</head>
<body>
	<div class="container">
		<?php echo $partials["content"]; ?>
	</div>
</body>
</html>
