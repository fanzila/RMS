		</div>
		<div data-role="content">
			Order(s) have been saved to draft.
			<form onsubmit="return validate(this);" id="order" name="order" method="post" action="/order/sendOrder/" data-ajax="false">
					<ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
						<li><a data-ajax="false" target="_blank" href="/order/downloadOrder/<?=$info['idorder']?>_<?=strtoupper($info['supplier']['name'])?>">Order <?=$info['idorder']?> for <?=$info['supplier']['name']?>: <?=$info['supplier']['contact_order_email']?> <? if(!empty($info['cc_email'])) { ?> in cc <?=$info['cc_email']?><? } ?></a></li>
					</ul>
					<a data-ajax="false" target="_blank" href="/order/downloadOrder/<?=$info['idorder']?>_<?=strtoupper($info['supplier']['name'])?>"><img src="/order/pdfPreview/<?=$filename?>" style="border: 1px solid #e9e9e9;" width="612" height="792"></a>
					<?if (isset($info['valid_number']) AND $info['valid_number'] == true) {?>
						<label><input type="checkbox" name="SMSSupplier">Envoyer un SMS de confirmation au numéro:  <?=$info['supplier']['contact_order_tel']?></label>
						<?}?>
					<ul data-role="listview" data-inset="true" data-split-theme="a" data-divider-theme="a">
						<li><input type="submit" name="send" onClick="" value="SEND"></li>
					</ul>
					<input type="hidden" name="idorder" value="<?=$info['idorder']?>">
			</form>
		</div>
	</div>
	<script>
	function validate(form) {
	        return confirm('Do you really want to send the order?\n->NO cancellation will be possible!<-');
	}
	</script>