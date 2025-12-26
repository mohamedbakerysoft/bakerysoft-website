<html lang="en">

<head>
	<title>Bakery Soft Company | Track system</title>
</head>

<body style="background:#E1E1E1">
	<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap/latest/css/bootstrap.css" />
	<style type="text/css">
		.price.panel-red>.panel-heading {
			color: #fff;
			background-color: #D04E50;
			border-color: #FF6062;
			border-bottom: 1px solid #FF6062;
		}

		.price.panel-red>.panel-body {
			color: #fff;
			background-color: #EF5A5C;
		}

		.price .list-group-item {
			border-bottom-: 1px solid rgba(250, 250, 250, .5);
		}

		.panel.price .list-group-item:last-child {
			border-bottom-right-radius: 0px;
			border-bottom-left-radius: 0px;
		}

		.panel.price .list-group-item:first-child {
			border-top-right-radius: 0px;
			border-top-left-radius: 0px;
		}

		.price .panel-footer {
			color: #fff;
			border-bottom: 0px;
			background-color: rgba(0, 0, 0, .1);
			box-shadow: 0px 3px 0px rgba(0, 0, 0, .3);
		}

		.panel.price .btn {
			box-shadow: 0 -1px 0px rgba(50, 50, 50, .2) inset;
			border: 0px;
		}
	</style>
	<?php
	$paypalUrl = 'https://www.paypal.com/cgi-bin/webscr';
	$paypalId = 'php.power.arts@gmail.com';
	?>

	<div class="container text-center">
		<br />
		<h2><strong>Bakery Soft Company</strong></h2>
		<br />
		<div class="row">
			<div class="col-xs-6 col-sm-6 col-md-3 col-md-offset-4 col-lg-12">

				<!-- PRICE ITEM -->
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" name="frmPayPal1">
					<div class="panel price panel-red">
						<input type="hidden" name="business" value="php.power.arts@gmail.com">
						<input type="hidden" name="cmd" value="_xclick">
						<input type="hidden" name="item_name" value="It Solution Stuff">
						<input type="hidden" name="item_number" value="2">
						<input type="hidden" name="no_shipping" value="1">
						<input type="hidden" name="currency_code" value="USD">
						<input type="hidden" name="cancel_return" value="http://demo.itsolutionstuff.com/paypal/cancel.php">
						<input type="hidden" name="return" value="http://demo.itsolutionstuff.com/paypal/success.php">
						
						<div class="panel-heading  text-center">
							<h3 style="padding: 10px;">Support Us to build Track system Plan</h3>
						</div>
						<div class="panel-body text-center">
							<input hidden type="text" class="form-control" name="amount" value="30">
							<p class="lead" style="font-size:40px"><strong>$30</strong></p>
						</div>
						<ul class="list-group list-group-flush text-center">
							<li class="list-group-item"><i class="icon-ok text-danger"></i> Personal use</li>
							<li class="list-group-item"><i class="icon-ok text-danger"></i> Unlimited projects</li>
							<li class="list-group-item"><i class="icon-ok text-danger"></i> 27/7 support</li>
						</ul>
						<div class="panel-footer">
							<button class="btn btn-lg btn-block btn-danger" href="#">Support/reserve Your BTrack System Release !</button>
						</div>
					</div>
				</form>
				<!-- /PRICE ITEM -->

			</div>
		</div>
	</div>
</body>

</html>
