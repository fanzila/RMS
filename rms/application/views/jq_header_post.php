</head>
<body>
	<?php $dev = false; if($_SERVER['SERVER_NAME'] == 'rms.dev' || $_SERVER['SERVER_NAME'] == 'forkrms.dev') $dev = true; ?>
	<div data-role="page" id="pageid" class="nd2-no-menu-swipe">
		<?php if($index==1){
			include('adminpanel.html');
		}?>
		<?if($dev) { ?><div style="text-align: center;width:100%;background-color: #c3f59d;height:30px;border:2px solid #ccc;color:red">DEV MODE</div><br><? } ?>
		<div data-role="header" data-position="fixed" class="wow fadeIn">
			<?if($bu_id==1){?>
				<link rel="stylesheet" href="/public/droid2/css/nativedroid2.color.amber.css" />
			<?}else if($bu_id==2){?>
				<link rel="stylesheet" href="/public/droid2/css/nativedroid2.color.red.css" />
			<?}else if($bu_id==3){?>
				<link rel="stylesheet" href="/public/droid2/css/nativedroid2.color.orange.css" />
			<?}else if($bu_id==4){?>
				<link rel="stylesheet" href="/public/droid2/css/nativedroid2.color.blue.css" />
			<?}else if($bu_id==5){?>
				<link rel="stylesheet" href="/public/droid2/css/nativedroid2.color.lime.css" />
			<?}?>
			<?if($index==1){?>
				<a href="#adminpanel" class="ui-btn ui-btn-left wow fadeIn" data-wow-delay='0.8s'><i class="zmdi zmdi-menu"></i></a>
				<h1>
					<div class="row">
						<div class="col-xs">
							<div class="box"><?=$title?></div>
						</div>
						<div class="col-xs">
							<div class="box"><?=$bu_name?> | <?=$username?> <?if ($this->session->userdata('type')) { echo " - " . $this->session->userdata('type');}?></div>
						</div>
						<div class="col-xs-1">
							<div class="box"></div>
						</div>
					</div>
				</h1>
			<?}else if($index==2){?>
				<form action="#" method="POST">
					<select name="bus" class="ui-btn" onchange="this.form.submit()">
					<? foreach ($bus_list as $bu) { ?>
		  				<option value="<?=$bu->id?>" <? if($bu_id == $bu->id) echo "selected"; ?>><?=$bu->name?> | <?=$username?></option>
					<? } ?>
					</select>
				</form>
			<?}else{?>
				<a class="ui-btn ui-btn-left" rel="external" data-ajax="false" href="<?=$indexlocation?>"><i class="zmdi zmdi-arrow-back zmd-fw"></i></a>
					<h1>
				<div class="row">
					<div class="col-xs">
						<div class="box"><?=$title?></div>
					</div>
					<div class="col-xs">
						<div class="box"><?=$bu_name?> | <?=$username?></div>
					</div>
					<div class="col-xs-1">
						<div class="box"></div>
					</div>
				</div>
					</h1>
			<?}?>