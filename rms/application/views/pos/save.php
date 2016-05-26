<div data-role="page" data-theme="a">
	<? if($keylogin) { ?>	
		<div data-role="header">
	&nbsp;&nbsp;&nbsp;
		</div>
		<? } ?>
	<div data-role="header">
		<? if(!$keylogin) { ?><a href="/admin/" data-role="button" data-inline="true" data-ajax="false" data-icon="home">Home</a><? } ?>
		<a href="/pos/" data-ajax="false" data-icon="home">Back</a>
		<h1>POS  | <?=$bu_name?> | <?=$username?></h1>
	</div>
	<div data-role="content" data-theme="a">
		F O R M I D A B L E! <br>
		Votre transaction à été enregistrée! <br>
		<? if($mov =='close') { ?> Insérer les billets, tickets restaurants, tickets CB et chèques dans une pochette caisse et notez dessus le <? } ?>
		Numéro : <b><?=$idtrans?></b>
	</div>
</div>