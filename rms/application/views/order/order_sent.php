<? $title = "Order Prepare"; include('jq_header.php'); ?>
<body>
	<div data-role="page">
		<div data-role="header">
			<a href="/order/" data-ajax="false" data-icon="home">back</a>
			<h1>Order Sent | <?=$bu_name?> | <?=$username?></h1>
		</div>
		<div data-role="content">
			Order(s) sent to : 
			<? foreach ($disp as $key) { ?>
				<?=$key?> | 
			<? } ?> 
		</div>
	</div>
	<? include('jq_footer.php'); ?>