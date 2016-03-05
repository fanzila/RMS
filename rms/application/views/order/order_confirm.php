<? $title = "Order Prepare"; include('jq_header.php'); ?>
<body>
	<div data-role="page">
		<div data-role="header">
			<a href="/order/" data-ajax="false" data-icon="home">back</a>
			<h1>Order Confirm</h1>
		</div>
		<div data-role="content">
			Order(s) have been saved to draft.
			<form onsubmit="return validate(this);" id="order" name="order" method="post" action="/order/sendOrder/" data-ajax="false">
					<ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
						<? $i = 0; foreach ($order as $key => $var) { ?> 
						<li><a data-ajax="false" target="_blank" href="/order/downloadOrder/<?=$var['id']?>">Order <?=$var['id']?> for <?=$var['sup_email']?> <? if(!empty($var['cc_email'])) { ?> in cc <?=$var['cc_email']?><? } ?></a></li>
						<input type="hidden" name="<?=$i?>_ID" value="<?=$var['id']?>">
						<input type="hidden" name="<?=$i?>_EMAIL" value="<?=$var['sup_email']?>">
						<input type="hidden" name="<?=$i?>_EMAILCC" value="<?=$var['cc_email']?>">
						<input type="hidden" name="<?=$i?>_IDORDER" value="<?=$var['idorder']?>">
						<input type="hidden" name="<?=$i?>_SUP" value="<?=$var['sup_name']?>">
						<input type="hidden" name="<?=$i?>_SUPID" value="<?=$var['sup_id']?>">
						<input type="hidden" name="<?=$i?>_USER" value="<?=$var['user']?>">
						<input type="hidden" name="<?=$i?>_USERID" value="<?=$var['userid']?>">
						<input type="hidden" name="<?=$i?>_COMT" value="<?=$var['comt']?>">
						<? $i++; } ?>
					</ul>
					<ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
						<li><input type="submit" name="save" onClick="" value="SEND"></li>
					</ul>
			</form>
		</div>
	</div>
	<script>
	function validate(form) {
	        return confirm('Do you really want to send the order(s)?\n->NO cancellation will be possible!<-');
	}
	</script>
	<? include('jq_footer.php'); ?>