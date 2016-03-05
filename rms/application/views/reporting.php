<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>HANK reporting</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />

	<link rel="stylesheet" type="text/css" media="screen" href="/public/jqGrid/ui.jqgrid.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="/public/jqGrid/jquery-ui-custom.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="/public/jqGrid/ui.multiselect.css" />

	<script src="/public/jquery-1.11.3.min.js" type="text/javascript"></script>
	<script src="/public/jqGrid/jquery-ui-custom.min.js" type="text/javascript"></script>
	<script src="/public/jqGrid/jquery.layout.js" type="text/javascript"></script>
	<script src="/public/jqGrid/grid.locale-en.js" type="text/javascript"></script>
	<script type="text/javascript">
	$.jgrid.no_legacy_api = true;
	$.jgrid.useJSON = true;

	$(function() {
		$( "#datepicker_from" ).datepicker({ dateFormat: 'yy-mm-dd' });
	});

	$(function() {
		$( "#datepicker_to" ).datepicker({ dateFormat: 'yy-mm-dd' });
	});

	</script>
	<script src="/public/jqGrid/ui.multiselect.js" type="text/javascript"></script>
	<script src="/public/jqGrid/jquery.jqGrid.js" type="text/javascript"></script>
	<script src="/public/jqGrid/jquery.tablednd.js" type="text/javascript"></script>
	<script src="/public/jqGrid/jquery.contextmenu.js" type="text/javascript"></script>

	<style type="text/css">

	::selection{ background-color: #E13300; color: white; }
	::moz-selection{ background-color: #E13300; color: white; }
	::webkit-selection{ background-color: #E13300; color: white; }

	body {
		background-color: #fff;
		margin: 40px;
		font: 13px/20px normal Helvetica, Arial, sans-serif;
		color: #4F5155;
	}

	a {
		color: #003399;
		background-color: transparent;
		font-weight: normal;
	}

	h1 {
		color: #444;
		background-color: transparent;
		border-bottom: 1px solid #D0D0D0;
		font-size: 19px;
		font-weight: normal;
		margin: 0 0 14px 0;
		padding: 14px 15px 10px 15px;
	}

	code {
		font-family: Consolas, Monaco, Courier New, Courier, monospace;
		font-size: 12px;
		background-color: #f9f9f9;
		border: 1px solid #D0D0D0;
		color: #002166;
		display: block;
		margin: 14px 0 14px 0;
		padding: 12px 10px 12px 10px;
	}

	#body{
		margin: 0 15px 0 15px;
	}

	p.footer{
		text-align: right;
		font-size: 11px;
		border-top: 1px solid #D0D0D0;
		line-height: 32px;
		padding: 0 10px 0 10px;
		margin: 20px 0 0 0;
	}

	#container{
		margin: 10px;
		border: 1px solid #D0D0D0;
		-webkit-box-shadow: 0 0 8px #D0D0D0;
	}
	</style>
</head>
<body>

	<?php

include($_SERVER['DOCUMENT_ROOT']."/application/config/database.php");

$id_tva_10 = '002';
$id_tva_20 = '004';

$mysqli = new mysqli($db['pasteque']['hostname'], $db['pasteque']['username'], $db['pasteque']['password'], $db['pasteque']['database']);
if ($mysqli->connect_errno) {
	echo "Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

$req_last_ticket = "SELECT DATENEW FROM RECEIPTS ORDER BY DATENEW DESC LIMIT 1";
$res_last_ticket = $mysqli->query($req_last_ticket);
$row_last_ticket = $res_last_ticket->fetch_array();
?>

<script type="text/javascript">

$(function () {
	$("#top_sales_last_days").jqGrid({
		url: "/public/jqGrid/jqgrid.php?query=top_sales_last_days",
		datatype: "xml",
		mtype: "GET",
		colNames: ["NAME", "NUM"],
		colModel: [
		{ name: "NAME", width: 250 },
		{ name: "NUM", width: 90 }
		],
		pager: "#top_sales_last_days",
		rowNum: 50,
		rowList: [10, 20, 30, 1000],
		sortname: "NUM",
		sortorder: "desc",
		viewrecords: true,
		gridview: true,
		autoencode: true,
		//autowidth: true,
		height: 'auto',
		emptyrecords: "No records to view",
		caption: "TOP VENTE 10 DERNIERS JOURS"
	}); 

});

$(function () {
	$("#to_last_days").jqGrid({
		url: "/public/jqGrid/jqgrid.php?query=to_last_days",
		datatype: "xml",
		mtype: "GET",
		colNames: ["Y", "M", "D", "DAY", "TOTAL", "NBTICKET"],
		colModel: [
		{ name: "Y", width: 40 },
		{ name: "M", width: 40 },
		{ name: "D", width: 40 },
		{ name: "DAY", width: 70 },
		{ name: "TOTAL", width: 50 },
		{ name: "NBTICKET", width: 110 }
		],
		pager: "#to_last_days",
		rowNum: 50,
		rowList: [10, 20, 30, 1000],
		sortname: "D",
		sortorder: "desc",
		viewrecords: true,
		gridview: true,
		height: 'auto',
		//autowidth: true,
		emptyrecords: "No records to view",
		autoencode: true,
		caption: "CA EUR TTC 30 DERNIERS JOURS"


	});

});

$(function () {
	$("#burgers_last_days").jqGrid({
		url: "/public/jqGrid/jqgrid.php?query=burgers_last_days",
		datatype: "xml",
		mtype: "GET",
		colNames: ["Y", "M", "D", "DAY", "NB"],
		colModel: [
		{ name: "Y", width: 40 },
		{ name: "M", width: 40 },
		{ name: "D", width: 40 },
		{ name: "DAY", width: 80 },
		{ name: "NB", width: 150 }
		],
		pager: "#burgers_last_days",
		rowNum: 50,
		rowList: [10, 20, 30, 1000],
		sortname: "D",
		sortorder: "desc",
		viewrecords: true,
		gridview: true,
		height: 'auto',
		//autowidth: true,
		emptyrecords: "No records to view",
		autoencode: true,
		caption: "NB BURGER /JOUR 30 DERNIERS JOURS"
	});

});

$(function () {
	$("#to_by_month").jqGrid({
		url: "/public/jqGrid/jqgrid.php?query=to_by_month",
		datatype: "xml",
		mtype: "GET",
		colNames: ["YEAR", "MONTH", "TOTAL", "NBTICKET", "AVG_TICKET"],
		colModel: [
		{ name: "YEAR", width: 78 },
		{ name: "MONTH", width: 78 },
		{ name: "TOTAL", width: 78 },
		{ name: "NBTICKET", width: 80 },
		{ name: "AVG_TICKET", width: 100 }
		],
		pager: "#to_by_month",
		rowNum: 50,
		rowList: [10, 20, 30, 1000],
		sortname: "MONTH",
		sortorder: "ASC",
		viewrecords: true,
		gridview: true,
		height: 'auto',
		//autowidth: true,
		emptyrecords: "No records to view",
		autoencode: true,
		caption: "CA /MOIS EUR TTC"
	});

});

$(function () {
	$("#product_by_date").jqGrid({
		url: "/public/jqGrid/jqgrid.php?query=product_by_date&date_from=<?php echo $this->input->post('date_from'); ?>&date_to=<?php echo $this->input->post('date_to'); ?>",
		datatype: "xml",
		mtype: "GET",
		colNames: ["NAME", "NUM"],
		colModel: [
		{ name: "NAME", width: 250 },
		{ name: "NUM", width: 90 }
		],
		pager: "#product_by_date",
		rowNum: 50,
		rowList: [10, 20, 30, 1000],
		sortname: "NUM",
		sortorder: "desc",
		viewrecords: true,
		gridview: true,
		autoencode: true,
		//autowidth: true,
		height: 'auto',
		emptyrecords: "No records to view",
		caption: "VENTE PAR DATE"
	}); 

});
</script>

<div id="container">
	<h1>Reporting</h1>

	<div id="body">
		<table border=0>
			<tr>
				<td height="30" colspan="3">
					<b>Last ticket : <?=$row_last_ticket['DATENEW']?></b>
				</td>
			</tr>
			<tr>
				<td valign="top">
					<table id="top_sales_last_days"><tr><td></td></tr></table>
					<div id="top_sales_last_days"></div>
					<br />
					<table id="to_by_month"><tr><td></td></tr></table>
					<div id="to_by_month"></div>
				</td>
				<td valign="top">	
					<table id="to_last_days"><tr><td></td></tr></table> 
					<div id="to_last_10_days"></div>
					<br />
					<table id="burgers_last_days"><tr><td></td></tr></table> 
					<div id="burgers_last_days"></div>
				</td>
				<td valign="top" width="330">
					<form name="product_by_date" method="post" action="/reporting">
						Date from: <input size="10" value="<?php echo $this->input->post('date_from'); ?>" name="date_from" type="text" id="datepicker_from"> Date to: <input size="10" value="<?php echo $this->input->post('date_to'); ?>" name="date_to" type="text" id="datepicker_to"> <input type="submit" value="Go">
					</form>
					<hr />
					<?php 
					$date_from = $this->input->post('date_from');
					if(!empty($date_from)) { ?>
						<?php 
						$from	= $this->input->post('date_from');
						$to		= $this->input->post('date_to');		
						$req_stats = "SELECT ROUND(SUM(TOTAL)) AS TOTAL, COUNT(r.ID) AS NBTICKET 
							FROM PAYMENTS p 
							JOIN RECEIPTS r ON r.ID = p.RECEIPT 
							WHERE r.DATENEW BETWEEN '$from 00:00:00' AND '$to 23:59:59' 
						AND p.RECEIPT IN (SELECT DISTINCT(p.RECEIPT) 

						FROM PAYMENTS p 
						JOIN RECEIPTS r ON r.ID = p.RECEIPT 
						JOIN TICKETS t ON t.ID = p.RECEIPT 
						JOIN TICKETLINES tl ON tl.TICKET = p.RECEIPT 
						JOIN PRODUCTS pdt ON pdt.id = tl.PRODUCT
					)";
				$res_stats = $mysqli->query($req_stats) or die('SQL ERROR: '. $mysqli->error);
				$row_stats = $res_stats->fetch_array();

				$req_nb_pdt_bycat = "SELECT cat.NAME AS CATEGORY, COUNT(pdt.ID) AS NB
					FROM PAYMENTS p 
					JOIN RECEIPTS r ON r.ID = p.RECEIPT 
					JOIN TICKETLINES tl ON tl.TICKET = p.RECEIPT 
					JOIN PRODUCTS pdt ON pdt.ID = tl.PRODUCT
					JOIN CATEGORIES cat ON cat.ID = pdt.CATEGORY
					WHERE r.DATENEW BETWEEN '$from 00:00:00' AND '$to 23:59:59' 
				AND p.RECEIPT IN (SELECT DISTINCT(p.RECEIPT) 
				FROM PAYMENTS p 
				JOIN RECEIPTS r ON r.ID = p.RECEIPT 
				JOIN TICKETS t ON t.ID = p.RECEIPT 
				JOIN TICKETLINES tl ON tl.TICKET = p.RECEIPT 
			WHERE p.TOTAL >= 0) GROUP BY cat.NAME";

		$req_payment_type = "SELECT SUM(p.TOTAL) AS TOTAL, p.PAYMENT AS PAYMENT_TYPE, YEAR(r.DATENEW) AS YEAR, MONTH(r.DATENEW) AS MONTH  
			FROM PAYMENTS p  
			JOIN RECEIPTS r ON r.ID = p.RECEIPT  
			WHERE r.DATENEW BETWEEN '$from 00:00:00' AND '$to 23:59:59'   
		AND p.RECEIPT IN (SELECT DISTINCT(p.RECEIPT)  
		FROM PAYMENTS p  
		JOIN RECEIPTS r ON r.ID = p.RECEIPT  
		JOIN TICKETS t ON t.ID = p.RECEIPT  
		JOIN TICKETLINES tl ON tl.TICKET = p.RECEIPT  
	JOIN PRODUCTS pdt ON pdt.id = tl.PRODUCT) 
	GROUP BY p.PAYMENT;";

$req_ca_taxe_1 = "SELECT ROUND(SUM(tl.PRICE)) AS TOTAL FROM TICKETLINES tl JOIN TICKETS t ON tl.TICKET = t.ID JOIN RECEIPTS r ON r.ID = t.ID JOIN PRODUCTS p ON p.ID = tl.PRODUCT WHERE r.DATENEW BETWEEN '$from 00:00:00' AND '$to 23:59:59' AND tl.PRICE > 0 AND tl.TAXID = '002'";

$req_ca_taxe_2 = "SELECT ROUND(SUM(tl.PRICE)) AS TOTAL FROM TICKETLINES tl JOIN TICKETS t ON tl.TICKET = t.ID JOIN RECEIPTS r ON r.ID = t.ID JOIN PRODUCTS p ON p.ID = tl.PRODUCT WHERE r.DATENEW BETWEEN '$from 00:00:00' AND '$to 23:59:59' AND tl.PRICE > 0 AND tl.TAXID = '004' AND p.NAME NOT LIKE 'EXTRA-%'";

$req_ca_taxe_2b = "SELECT ROUND(SUM(tl.PRICE)) AS TOTAL FROM TICKETLINES tl JOIN TICKETS t ON tl.TICKET = t.ID JOIN RECEIPTS r ON r.ID = t.ID JOIN PRODUCTS p ON p.ID = tl.PRODUCT WHERE r.DATENEW BETWEEN '$from 00:00:00' AND '$to 23:59:59' AND tl.PRICE > 0 AND tl.TAXID = '004' AND p.NAME like 'EXTRA-%'";

$req_distrib = "SELECT p.NAME, count(*) AS TOTAL FROM TICKETLINES tl JOIN TICKETS t ON tl.TICKET = t.ID JOIN RECEIPTS r ON r.ID = t.ID JOIN PRODUCTS p ON p.ID = tl.PRODUCT WHERE r.DATENEW BETWEEN '$from 00:00:00' AND '$to 23:59:59' AND tl.PRICE = 0 AND (p.NAME LIKE '%TAKE-AWAY%' OR p.NAME LIKE '%EAT-HERE%') GROUP BY p.NAME";

$res_ca_taxe_1 = $mysqli->query($req_ca_taxe_1) or die('SQL ERROR: '. $mysqli->error);
$row_ca_taxe_1 = $res_ca_taxe_1->fetch_array();
$res_ca_taxe_2 = $mysqli->query($req_ca_taxe_2) or die('SQL ERROR: '. $mysqli->error);
$row_ca_taxe_2 = $res_ca_taxe_2->fetch_array();
$res_ca_taxe_2b = $mysqli->query($req_ca_taxe_2b) or die('SQL ERROR: '. $mysqli->error);
$row_ca_taxe_2b = $res_ca_taxe_2b->fetch_array();

$tva2b		= round($row_ca_taxe_2b['TOTAL']/0.3);
$totaltva2	= $row_ca_taxe_2['TOTAL']+$tva2b;
?>
<?="CA EUR TTC: $row_stats[TOTAL] | NBTICKET: $row_stats[NBTICKET]"; ?>
<hr />
<?php
if($row_stats['NBTICKET'] > 0) {
	$avg = round($row_stats['TOTAL']/$row_stats['NBTICKET'],2)
	?>
	AVG TICKET | <?= "$avg
	<br />
	CA EUR HT TVA 10%: $row_ca_taxe_1[TOTAL]
	<br /> 
	CA EUR HT TVA 20%: $row_ca_taxe_2[TOTAL] + $tva2b (menu) = $totaltva2";?>
	<br />
	TOTAL TVA DUE: <?php echo $row_stats['TOTAL']-($row_ca_taxe_1['TOTAL']+$totaltva2); ?>€
	<hr />
	<b>CA PAR TYPE DE PAIEMENT</b><br />
	<?php
	$res_payment_type = $mysqli->query($req_payment_type) or die('SQL ERROR: '. $mysqli->error);
	while($row_payment_type = $res_payment_type->fetch_array()) {
		echo $row_payment_type['PAYMENT_TYPE'].': '.round($row_payment_type['TOTAL']).' <br /> ';    
	}
	?>
	<hr />
	<?php
	$res_distrib = $mysqli->query($req_distrib) or die('SQL ERROR: '. $mysqli->error);
	while($row_distrib = $res_distrib->fetch_array()) {
		echo $row_distrib['NAME'].': '.$row_distrib['TOTAL'].' <br /> ';    
	}
	?>
	<hr />
	<b>VENTES PAR CATEGORIES</b><br />
	<?php
	$res_nb_pdt_bycat = $mysqli->query($req_nb_pdt_bycat) or die('SQL ERROR: '. $mysqli->error);
	while($row_nb_pdt_bycat = $res_nb_pdt_bycat->fetch_array()) {
		echo $row_nb_pdt_bycat['CATEGORY'].': '.$row_nb_pdt_bycat['NB'].' <br /> ';    
	}
}
?>	
<br />
<table id="product_by_date"><tr><td></td></tr></table>
<div id="product_by_date"></div>
<? } ?>
</td>
</tr>
</table>	
</div>
</div>
</body>
</html>

<?php //$last_line = system('sudo /root/scripts/get_bank_balance', $retval); ?>