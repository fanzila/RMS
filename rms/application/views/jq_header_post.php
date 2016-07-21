</head>
<body>
	<div data-role="page">
		<?php if($index==1){
			include('adminpanel.html');
		}?>
		<div data-role="header" data-position="fixed" class="wow fadeIn">
			<?php if($index==1){?>
				<a href="#adminpanel" class="ui-btn ui-btn-left wow fadeIn" data-wow-delay='0.8s'><i class="zmdi zmdi-menu"></i></a>
				<h1><div class="row">
					<div class="col-xs-2">
						<div class="box"><?=$title?></div>
					</div>
					<div class="col-xs-3">
						<div class="box"></div>
					</div>
					<div class="col-xs-2">
						<div class="box"><?=$username?></div>
					</div>
					<div class="col-xs-5">
						<div class="box"></div>
					</div>
				</div>
				</h1>
<!--<h1>?=$title?> | ?=$bu_name?> | ?=$username?></h1>
				<!--Partie Spé à ajouter-->
			<?}else if($index==2){?>
				<form action="#" method="POST">
					<select name="bus" class="ui-btn" onchange="this.form.submit()">
					<? foreach ($bus_list as $bu) { ?>
		  				<option value="<?=$bu->id?>" <? if($bu_id == $bu->id) echo "selected"; ?>><?=$bu->name?></option>
					<? } ?>
					</select>
				</form>
			<?}else{?>
				<a class="ui-btn ui-btn-left" rel="external" data-ajax="false" href="<?=$indexlocation?>"><i class="zmdi zmdi-arrow-back zmd-fw"></i></a>
				<h1><?=$title?> | <?=$bu_name?> | <?=$username?></h1>
			<?}?>