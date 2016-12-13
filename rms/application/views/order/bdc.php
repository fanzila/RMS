<html>
<head>
<style type="text/css">
html {
	font-size: 100%;
}
body {
	font-size: 1em;
	line-height: 1.3;
	font-family: sans-serif /*{global-font-family}*/;
}
.table_bdc {
	border: 1px solid silver;
	padding: 6px;
}
.smallfont {
	font-size: 0.8em;
}
.delivery_title {
	font-size: 0.9em;
}
.delivery {
	font-size: 0.7em;
	padding-top: -10px;
	text-align: justify;
}
</style>
</head>
<center><h3>BON DE COMMANDE<br />N°<?=$info['idorder']?></h3></center>
	<table width="100%">
		<tr>
			<td width="60%" valign="top">
<table class="smallfont" border="0" width="100%">
	<tr><td><?=nl2br($info['buinfo']->delivery_header)?></td></tr>
	<tr><td>Horaire de livraison : <?=$info['supplier']['comment_delivery_info']?></td></tr>
	<tr><td>Contact pour cette commande : <?=$info['user']->username?></td></tr>
	<tr><td><a href="mailto:<?=$info['user']->email?>"><?=$info['user']->email?></a> - Tel : <a href="phone:"><?=$info['user']->phone?></a></td></tr>
</table>
</td><td width="40%" style="border-left: 1px solid silver;" valign="top">
<table class="smallfont" border="0" width="100%">
	<tr><td>Date : <?=$info['date']?></td></tr>
	<tr><td>Société : <?=$info['supplier']['name']?> <br /> 
		Email : <a href="mailto:<?=$info['supplier']['contact_order_email']?>"><?=$info['supplier']['contact_order_email']?></a> <br />
		Tel : <?=$info['supplier']['contact_order_tel']?></td></tr>
	<tr><td>Franco : <?=$info['supplier']['carriage_paid']?></td></tr>
	<?if(isset($info['supplier']['comment_order'])) {?> <tr><td><?=$info['supplier']['comment_order']?></td></tr> <? } ?>
	<?if(isset($info['supplier']['comment_delivery'])) {?> <tr><td><?=$info['supplier']['comment_delivery']?></td></tr> <? } ?>
</table>
</td>
</tr>
</table>
<br />
<? if(!empty($info['comment'])) { ?><table width="100%"><tr class="table_bdc"><td class="table_bdc" valign="top">Commentaire : <b><font color="red"> <?=$info['comment']?></font></b></td></tr></table><? } ?>
<br />
<table width="100%">
<tr>
	<td class="table_bdc" align="center"><b>Désignation</b></td>
	<td class="table_bdc" align="center"><b>Code Art.</b></td>
<?if(!$info['supplier']['simple_order_form']) { ?><td class="table_bdc" align="center"><font color="#7c7c7c"><b>Colisage</b></font></td> <? } ?>
	<td class="table_bdc" align="center"><b>Unité de vente</b></td>
<?if(!$info['supplier']['simple_order_form']) { ?><td class="table_bdc" align="center"><b>prix unitaire H.T.</b></td><? } ?>
	<td class="table_bdc" align="center"><b>Quantité</b></td>
<?if(!$info['supplier']['simple_order_form']) { ?><td class="table_bdc" align="center"><b>Sous total H.T.</b></td><? } ?>

</tr>
<? foreach ($order['pdt'] as $key => $var) { ?>
<tr>
	<td class="table_bdc"><?=$var['name']?></td>
	<td class="table_bdc" align="center"><?=$info['pdtinfo'][$key]['supplier_reference']?></td>
	<?if(!$info['supplier']['simple_order_form']) { ?><td class="table_bdc" align="center"><font color="#7c7c7c"><?=$info['pdtinfo'][$key]['packaging']?></font></td><? } ?>
	<td class="table_bdc" align="center"><?=$info['pdtinfo'][$key]['unit_name']?></td>
	<?if(!$info['supplier']['simple_order_form']) { ?><td class="table_bdc" align="center"><?=$info['pdtinfo'][$key]['price']/1000?>€</td><? } ?>
	<td class="table_bdc" align="center"><?=$var['qtty']?></td>
	<?if(!$info['supplier']['simple_order_form']) { ?><td class="table_bdc" align="center"><?=($info['pdtinfo'][$key]['price']*$var['qtty'])/1000?>€</td><? } ?>
</tr>
<? } ?>
<?if(!$info['supplier']['simple_order_form']) { ?><tr><td class="table_bdc" align="left" colspan="7">Total H.T. : <?=$order['pricetotal']/1000?>€</td></tr><? } ?>
</table>
<span class="smallfont">Afin de faciliter votre paiement, merci de bien vouloir reporter ce numéro de BDC : <?=$info['idorder']?> sur vos factures et BL.</span>
<p class="delivery_title"><b>Conditions de livraison</b><br />
<b><font color="red">Informez votre transporteur</font></b></p>
<p class="delivery"><?=nl2br($info['buinfo']->delivery_info)?></p>

   <script type="text/php">
    if ( isset($pdf) ) { 
        $pdf->page_script('
            if ($PAGE_COUNT > 1) {
                $font = Font_Metrics::get_font("Arial, Helvetica, sans-serif", "normal");
                $size = 12;
                $pageText = Page . " " . $PAGE_NUM . " sur " . $PAGE_COUNT;
                $y = 15;
                $x = 520;
                $pdf->text($x, $y, $pageText, $font, $size);
            } 
        ');
    }
</script>
</html>
