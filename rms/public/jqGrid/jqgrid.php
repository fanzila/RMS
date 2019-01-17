<?php 
//include the information needed for the connection to MySQL data base server. 
// we store here username, database and password 

require_once($_SERVER['DOCUMENT_ROOT']."/application/config/database.php");

// to the url parameter are added 4 parameters as described in colModel
// we should get these parameters to construct the needed query
// Since we specify in the options of the grid that we will use a GET method 
// we should use the appropriate command to obtain the parameters. 
// In our case this is $_GET. If we specify that we want to use post 
// we should use $_POST. Maybe the better way is to use $_REQUEST, which
// contain both the GET and POST variables. For more information refer to php documentation.
// Get the requested page. By default grid sets this to 1. 
$page = 0;
if(isset($_GET['page'])) $page = $_GET['page'];

// get how many rows we want to have into the grid - rowNum parameter in the grid 
$limit = 0;
if(isset($_GET['rows'])) $limit = $_GET['rows'];

// get index row - i.e. user click to sort. At first time sortname parameter -
// after that the index from colModel 
$sidx = 0; 
if(isset($_GET['sidx'])) $sidx = $_GET['sidx'];

// sorting order - at first time sortorder 
$sord = 0; 
if(isset($_GET['sord'])) $sord = $_GET['sord'];

// if we not pass at first time index use the first column for the index or what you want
if(!$sidx) $sidx =1; 

// connect to the MySQL database server  
$mysqli = new mysqli($db['pasteque']['hostname'], $db['pasteque']['username'], $db['pasteque']['password'], $db['pasteque']['database']);
if ($mysqli->connect_errno) {
	echo "Echec lors de la connexion à MySQL : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

if($_GET['query'] == 'product_by_date') $req = "SELECT pdt.ID AS ID, pdt.NAME AS NAME, count(pdt.NAME) AS NUM  
	FROM PAYMENTS p 
	JOIN RECEIPTS r ON r.ID = p.RECEIPT 
	JOIN TICKETS t ON t.ID = p.RECEIPT 
	JOIN TICKETLINES tl ON tl.TICKET = p.RECEIPT 
	JOIN PRODUCTS pdt ON pdt.id = tl.PRODUCT 
	WHERE r.DATENEW BETWEEN '$_GET[date_from] 00:00:00' AND '$_GET[date_to] 23:59:59' GROUP BY pdt.NAME ";


if($_GET['query'] == 'top_sales_last_days') $req = "SELECT pdt.ID AS ID, pdt.NAME AS NAME, count(pdt.NAME) AS NUM  
	FROM PAYMENTS p 
	JOIN RECEIPTS r ON r.ID = p.RECEIPT 
	JOIN TICKETS t ON t.ID = p.RECEIPT 
	JOIN TICKETLINES tl ON tl.TICKET = p.RECEIPT 
	JOIN PRODUCTS pdt ON pdt.id = tl.PRODUCT 
WHERE r.DATENEW > (NOW() - INTERVAL 10 DAY) GROUP BY pdt.NAME ";


if($_GET['query'] == 'to_last_days') $req = "SELECT p.ID AS ID, YEAR(r.DATENEW) AS Y, MONTH(r.DATENEW) AS M, DAY(r.DATENEW) AS D, DAYNAME(r.DATENEW) AS DAY, ROUND(SUM(TOTAL)) AS TOTAL, COUNT(p.ID) AS NBTICKET 
	FROM PAYMENTS p 
	JOIN RECEIPTS r ON r.ID = p.RECEIPT 
WHERE r.DATENEW > (NOW() - INTERVAL 29 DAY) 
AND p.RECEIPT IN (SELECT DISTINCT(p.RECEIPT) 
FROM PAYMENTS p 
JOIN RECEIPTS r ON r.ID = p.RECEIPT 
JOIN TICKETS t ON t.ID = p.RECEIPT 
JOIN TICKETLINES tl ON tl.TICKET = p.RECEIPT 
JOIN PRODUCTS pdt ON pdt.id = tl.PRODUCT) 
GROUP BY DAY(r.DATENEW) ";

if($_GET['query'] == 'burgers_last_days') $req = "SELECT  p.ID AS ID,  COUNT(*) AS NB,  YEAR(r.DATENEW) AS Y,  MONTH(r.DATENEW) AS M,  DAY(r.DATENEW) AS D,  DAYNAME(r.DATENEW) AS DAY,  r.DATENEW   
FROM PAYMENTS p  
	JOIN RECEIPTS r ON r.ID = p.RECEIPT  
	JOIN TICKETS t ON t.ID = p.RECEIPT   
	JOIN TICKETLINES tl ON tl.TICKET = p.RECEIPT   
	JOIN PRODUCTS pdt ON pdt.id = tl.PRODUCT  
	JOIN CATEGORIES ctg ON pdt.CATEGORY = ctg.ID    
WHERE r.DATENEW > (NOW() - INTERVAL 30 DAY)  
AND ctg.NAME = 'BURGERS' 
GROUP BY DAY(r.DATENEW) ";

if($_GET['query'] == 'to_by_month') $req = "SELECT p.ID AS ID, YEAR(r.DATENEW) AS YEAR, MONTH(r.DATENEW) AS MONTH, ROUND(SUM(TOTAL)) AS TOTAL, COUNT(p.ID) AS NBTICKET FROM PAYMENTS p JOIN RECEIPTS r ON r.ID = p.RECEIPT WHERE YEAR(r.DATENEW) = 2014 AND p.RECEIPT IN (SELECT DISTINCT(p.RECEIPT) FROM PAYMENTS p JOIN RECEIPTS r ON r.ID = p.RECEIPT JOIN TICKETS t ON t.ID = p.RECEIPT JOIN TICKETLINES tl ON tl.TICKET = p.RECEIPT JOIN PRODUCTS pdt ON pdt.id = tl.PRODUCT) GROUP BY MONTH(r.DATENEW) ";

// calculate the number of rows for the query. We need this for paging the result 
$res = $mysqli->query($req); 
$count = $res->num_rows; 

// calculate the total pages for the query 
if( $count > 0 && $limit > 0) { 
	$total_pages = ceil($count/$limit); 
} else { 
	$total_pages = 0; 
} 

// if for some reasons the requested page is greater than the total 
// set the requested page to total page 
if ($page > $total_pages) $page=$total_pages;

// calculate the starting position of the rows 
$start = $limit*$page - $limit;

// if for some reasons start position is negative set it to 0 
// typical case is that the user type 0 for the requested page 
if($start <0) $start = 0; 

// the actual query for the grid data 
$result = $mysqli->query($req." ORDER BY $sidx $sord LIMIT $start , $limit");

// we should set the appropriate header information. Do not forget this.
header("Content-type: text/xml;charset=utf-8");

$s = "<?xml version='1.0' encoding='utf-8'?>";
$s .=  "<rows>";
$s .= "<page>".$page."</page>";
$s .= "<total>".$total_pages."</total>";
$s .= "<records>".$count."</records>";

$line = 0;

if($_GET['query'] == 'product_by_date') {
	while($row = $result->fetch_array()) {
		$s .= "<row id='". $row['ID']."'>";    
		$s .= "<cell>". $row['NAME']."</cell>";        
		$s .= "<cell>". $row['NUM']."</cell>";
		$s .= "</row>";
	}
}

if($_GET['query'] == 'top_sales_last_days') {
	while($row = $result->fetch_array()) {
		$s .= "<row id='". $row['ID']."'>";    
		$s .= "<cell>". $row['NAME']."</cell>";        
		$s .= "<cell>". $row['NUM']."</cell>";
		$s .= "</row>";
	}
}

if($_GET['query'] == 'to_last_days') {
	while($row = $result->fetch_array()) {
		$s .= "<row id='". $row['ID']."'>";    
		$s .= "<cell>". $row['Y']."</cell>";        
		$s .= "<cell>". $row['M']."</cell>";
		$s .= "<cell>". $row['D']."</cell>";
		$s .= "<cell>". $row['DAY']."</cell>";
		$s .= "<cell>". $row['TOTAL']."</cell>";
		$s .= "<cell>". $row['NBTICKET']."</cell>";
		$s .= "</row>";
	}
}

if($_GET['query'] == 'burgers_last_days') {
	while($row = $result->fetch_array()) {
		$s .= "<row id='". $row['ID']."'>";    
		$s .= "<cell>". $row['Y']."</cell>";        
		$s .= "<cell>". $row['M']."</cell>";
		$s .= "<cell>". $row['D']."</cell>";
		$s .= "<cell>". $row['DAY']."</cell>";
		$s .= "<cell>". $row['NB']."</cell>";
		$s .= "</row>";
	}
}

if($_GET['query'] == 'to_by_month') {
	while($row = $result->fetch_array()) {
		$s .= "<row id='". $row['ID']."'>";    
		$s .= "<cell>". $row['YEAR']."</cell>";        
		$s .= "<cell>". $row['MONTH']."</cell>";
		$s .= "<cell>". $row['TOTAL']."</cell>";
		$s .= "<cell>". $row['NBTICKET']."</cell>";
		$s .= "<cell>". round($row['TOTAL']/$row['NBTICKET'],2)."</cell>";
		$s .= "</row>";
	}
}

$s .= "</rows>"; 

echo $s;
?>