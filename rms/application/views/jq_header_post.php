</head>
<body>
	<div data-role="page">
		<?php if($index==1){
			include('adminpanel.html');
		}?>
		<div data-role="header" data-position="fixed" class="wow fadeIn">
			<?php if($index==1){?>
				<a href="#adminpanel" class="ui-btn ui-btn-left wow fadeIn" data-wow-delay='0.8s'><i class="zmdi zmdi-menu"></i></a>
			<?}?>