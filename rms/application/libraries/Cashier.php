<?php 

class Cashier {
	private function debugFile($txt) { 
		$file = 'orderdebug.txt';
		$current = file_get_contents($file);
		$current .= "$txt\r\n";
		file_put_contents($file, $current);
	}
	
	private function getSalesForProduct($id, $id_bu) {
		$CI = & get_instance(); 
		$CI->load->database();
		$qtty = 0;
		$debug = false;
		
		//get sales product
		$q_sp = "SELECT sri.quantity AS quantity, sr.period_id as period_id, sri.product AS product
			FROM sales_receipt AS sr
			JOIN sales_receiptitem AS sri ON sri.receipt = sr.`id`
			WHERE sri.product = '".$id."'
			AND sr.id_bu = $id_bu
			AND sr.done = 0 
			AND sr.date_closed != '0000-00-00' 
			AND sr.canceled = 0";
			
			
		$r_sp = $CI->db->query($q_sp) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
		$row_sp = $r_sp->result_array();
		
		foreach ($row_sp as $key) {
			$qtty += 1*($key['quantity']/1000);
			if($debug AND $qtty > 0) $this->debugFile(@date('Y-m-d H:i:s')." - SP: Found $qtty sales for product $key[product] in receipt $key[period_id] for BU: $id_bu"); 
		}
		//get productaddon
		$q_spa = "SELECT sria.quantity AS quantity, sr.period_id as period_id, sp.id_pos AS product 
		FROM sales_productaddon AS spa
			JOIN sales_receiptitemaddon AS sria ON sria.productaddon = spa.id_pos
			JOIN sales_receiptitem AS sri ON sri.id = sria.receiptitem
			JOIN sales_receipt AS sr ON sr.`id` = sri.receipt
			JOIN sales_product AS sp ON sp.id_pos = spa.id_pos_product
			WHERE spa.id_pos_product = '".$id."'
			AND sr.done = 0 
			AND sr.id_bu = $id_bu
			AND sr.date_closed != '0000-00-00' 
			AND sr.canceled = 0";
		$r_spa = $CI->db->query($q_spa) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
		$row_spa = $r_spa->result_array();
		foreach ($row_spa as $keya) {
			$qtty += 1*($keya['quantity']); 
			if($debug AND $qtty > 0) $this->debugFile(@date('Y-m-d H:i:s')." - SPA: Found $qtty sales for product $keya[product] in receipt  $keya[period_id] for BU: $id_bu");
		}
		
		return $qtty;
	}
	public function updateStock($id_bu) {
		$CI = & get_instance(); 
		$CI->load->database();
		$debug = false;
		
		$q_pos_pdt = "SELECT * FROM sales_product WHERE deleted=0 AND id_bu = $id_bu";
		$r_pos_pdt = $CI->db->query($q_pos_pdt) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
		$res_pos_pdt = $r_pos_pdt->result_array();
		$CI->db->query("BEGIN") or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message())); 
		foreach ($res_pos_pdt as $pos_pdt) {
			
			$sales = $this->getSalesForProduct($pos_pdt['id_pos'], $id_bu);
			if($debug AND $sales > 0) { 
				$this->debugFile(@date('Y-m-d H:i:s')." - Found $sales sales for $pos_pdt[name] for BU: $id_bu"); 
			}
			
			$q_mapping = "SELECT coef, id_product  FROM products_mapping WHERE id_pos=$pos_pdt[id] AND id_bu = $id_bu";
			$r_mapping = $CI->db->query($q_mapping) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$res_mapping = $r_mapping->result_array();
			foreach ($res_mapping as $mapping) {	
				if($sales > 0) {		
					$CI->db->query("UPDATE products_stock SET qtty = qtty-($sales*$mapping[coef]), last_update_pos = NOW() WHERE id_product = $mapping[id_product] AND id_bu = $id_bu") or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
					if($debug) $this->debugFile(@date('Y-m-d H:i:s')." - Mapping coef: $mapping[coef] - update for id_product : $mapping[id_product] set qtty = qtty-".$sales*$mapping['coef']." for BU: $id_bu");
				}
			}
		}
		$CI->db->query("UPDATE sales_receipt SET done = 1 WHERE date_closed != '0000-00-00' AND id_bu = $id_bu") or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
        $CI->db->query("COMMIT") or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
		if($debug) $this->debugFile(@date('Y-m-d H:i:s')." - UPDATE sales_receipt SET done = 1 WHERE date_closed != '0000-00-00' && COMMIT for BU: $id_bu"); 
	}
	private function getDoneArchivesList($id_bu) {
		$CI = & get_instance(); 
		$CI->load->database();
		//get pos_archives 
		$q_archives = "SELECT * FROM pos_archives WHERE id_bu = $id_bu";
		$r_archives = $CI->db->query($q_archives) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
		$o_archives = $r_archives->result_array();
		$ret = array();
		foreach ($o_archives as $line) {
			$ret[$line['file']] = $line['file']; 
		}
		return $ret;
	}
	
	private function insertArchives($file, $id_bu) {
		$CI = & get_instance(); 
		$CI->load->database();
		$dir	= $this->getPosArchivesDir($id_bu);
		$path	= $dir."/".$file;
		$db		= new SQLite3($path);
		$result_check = $db->query("SELECT * FROM sqlite_master WHERE name ='ARCHIVEDRECEIPT' and type='table'");
		$result_check_ar = $result_check->fetchArray(SQLITE3_ASSOC);
		if(!empty($result_check_ar)) {
			//RECEIPT
			$result_receipt = $db->query('SELECT * FROM ARCHIVEDRECEIPT');
			while($row_receipt=$result_receipt->fetchArray(SQLITE3_ASSOC)){
				if(!empty($row_receipt['SEQUENTIAL_ID'])) {
					$q_receipt = "INSERT IGNORE INTO sales_receipt SET id='".$row_receipt['ID']."', sequential_id=".$row_receipt['SEQUENTIAL_ID'].", owner='".$row_receipt['OWNER']."', date_created='".$row_receipt['DATE_CREATED']."', date_closed='".$row_receipt['DATE_CLOSED']."', canceled='".$row_receipt['CANCELLED']."', period_id='".$row_receipt['PERIOD_ID']."', amount_total=".$row_receipt['AMOUNT_TOTAL'].", id_bu = ".$id_bu; 
					$r_receipt = $CI->db->query($q_receipt) or die($this->db->_error_message());
				}
			}
			//RECEIPTITEM
			$result_receiptitem = $db->query('SELECT * FROM ARCHIVEDRECEIPTITEM');
			while($row_receiptitem=$result_receiptitem->fetchArray(SQLITE3_ASSOC)){
				$q_receiptitem = "INSERT IGNORE INTO sales_receiptitem SET id=".$row_receiptitem['ID'].", receipt='".$row_receiptitem['ARCHIVEDRECEIPT']."', product='".$row_receiptitem['PRODUCT']."', quantity=".$row_receiptitem['QUANTITY'].", id_bu =". $id_bu;
				$r_receiptitem = $CI->db->query($q_receiptitem) or die($this->db->_error_message());
			}
			//RECEIPTITEMADDON
			$result_receiptitemaddon = $db->query('SELECT * FROM ARCHIVEDRECEIPTITEMADDON');
			while($row_receiptitemaddon=$result_receiptitemaddon->fetchArray(SQLITE3_ASSOC)){
				$q_receiptitemaddon = "INSERT IGNORE INTO sales_receiptitemaddon SET id=".$row_receiptitemaddon['ID'].", receiptitem=".$row_receiptitemaddon['ARCHIVEDRECEIPTITEM'].", productaddon='".$row_receiptitemaddon['PRODUCTADDON']."', quantity=".$row_receiptitemaddon['QUANTITY'].", id_bu = $id_bu"; 
				
				$r_receiptitemaddon = $CI->db->query($q_receiptitemaddon) or die($this->db->_error_message());
			}
			
			//CASHMOVEMENT
			$result_cashmovement = $db->query('SELECT * FROM ARCHIVEDCASHMOVEMENT');
			while($row_cashmovement=$result_cashmovement->fetchArray(SQLITE3_ASSOC)){
				if(empty($row_cashmovement['TYPE'])) $row_cashmovement['TYPE'] = 0;
				if(empty($row_cashmovement['CUSTOMER'])) $row_cashmovement['CUSTOMER'] = null;
				$q_cashmovement = "INSERT IGNORE INTO sales_cashmovements SET id_pos='".$row_cashmovement['ID']."', `date`='".$row_cashmovement['DATE']."', user='".$row_cashmovement['USER']."', amount=".$row_cashmovement['AMOUNT'].", method='".$row_cashmovement['METHOD']."', type=".$row_cashmovement['TYPE'].", description='".$row_cashmovement['DESCRIPTION']."', `archive`='".$file."', customer='".$row_cashmovement['CUSTOMER']."', id_bu = $id_bu"; 
				$r_cashmovement = $CI->db->query($q_cashmovement) or die($this->db->_error_message());
			}
		}
		//update pos_archive
		$q_archives = "INSERT INTO pos_archives SET file ='".$file."', id_bu = $id_bu";
		$r_archives = $CI->db->query($q_archives) or die($this->db->_error_message());
	}
	private function syncArchivesDb($id_bu) {
		$CI = & get_instance(); 
		$CI->load->database();
		$dir	= $this->getPosArchivesDir($id_bu);
		$files	= scandir($dir, 0);
		$line2	= null;
		$archives_list = $this->getDoneArchivesList($id_bu);
		foreach ($files as $line) {
			if($line[0] == 2 ) {
				//search if file already done
				$key = array_key_exists($line, $archives_list);
				if($key == null) {
					$this->insertArchives($line, $id_bu);
				}
			}
		}
	}
	private function syncSalesDb($id_bu) {
		$CI = & get_instance(); 
		$CI->load->database();
		//get sqlite3 data from cashpad db
		$file	= $this->getPosDbDir($id_bu);
		$db		= new SQLite3($file);
		//PRODUCT
		$result_product = $db->query('SELECT * FROM PRODUCT');
		
		while($row_product=$result_product->fetchArray(SQLITE3_ASSOC)){
			$q_product = "INSERT INTO sales_product SET id_pos='".$row_product['ID']."',  name='".addslashes($row_product['NAME'])."', category='".$row_product['CATEGORY']."', deleted=".$row_product['DELETED'].", id_bu = $id_bu ON DUPLICATE KEY UPDATE name='".addslashes($row_product['NAME'])."', category='".$row_product['CATEGORY']."', deleted=".$row_product['DELETED'];
			
			$r_product = $CI->db->query($q_product) or die($this->db->_error_message());
		}
		//PRODUCTADDON
		$result_productaddon = $db->query('SELECT * FROM PRODUCTADDON');
		while($row_productaddon=$result_productaddon->fetchArray(SQLITE3_ASSOC)){
			$q_productaddon = "INSERT INTO sales_productaddon SET id_pos='".$row_productaddon['ID']."',  property_name='".addslashes($row_productaddon['PROPERTY_NAME'])."', category='".$row_productaddon['CATEGORY']."', id_pos_product='".$row_productaddon['PRODUCT']."', deleted=".$row_productaddon['DELETED'].", id_bu = $id_bu ON DUPLICATE KEY UPDATE property_name='".addslashes($row_productaddon['PROPERTY_NAME'])."', category='".$row_productaddon['CATEGORY']."', id_pos_product='".$row_productaddon['PRODUCT']."', deleted=".$row_productaddon['DELETED'];
			$r_productaddon = $CI->db->query($q_productaddon) or die($this->db->_error_message());
		}
		//RECEIPT
		$result_receipt = $db->query('SELECT * FROM RECEIPT WHERE DATE_CLOSED  IS NOT NULL');
		while($row_receipt=$result_receipt->fetchArray(SQLITE3_ASSOC)){
			if(!empty($row_receipt['SEQUENTIAL_ID'])) {
				$q_receipt = "INSERT INTO sales_receipt SET 
				id='".$row_receipt['ID']."', 
				sequential_id=".$row_receipt['SEQUENTIAL_ID'].", 
				owner='".$row_receipt['OWNER']."', 
				date_created='".$row_receipt['DATE_CREATED']."', 
				period_id='".$row_receipt['PERIOD_ID']."', 
				date_closed='".$row_receipt['DATE_CLOSED']."', 
				canceled='".$row_receipt['CANCELLED']."', 
				amount_total=".$row_receipt['AMOUNT_TOTAL'].", 
				id_bu = $id_bu 
				ON DUPLICATE KEY UPDATE 
				owner='".$row_receipt['OWNER']."', 
				date_closed='".$row_receipt['DATE_CLOSED']."', 
				canceled='".$row_receipt['CANCELLED']."', 
				amount_total=".$row_receipt['AMOUNT_TOTAL']; 
				
				$r_receipt = $CI->db->query($q_receipt) or die($this->db->_error_message());
			}
		}
		//RECEIPTITEM
		$result_receiptitem = $db->query('SELECT ri.ID, ri.RECEIPT, ri.PRODUCT, ri.QUANTITY FROM RECEIPTITEM AS ri JOIN RECEIPT AS r ON r.ID = ri.RECEIPT WHERE r.DATE_CLOSED IS NOT NULL');
		while($row_receiptitem=$result_receiptitem->fetchArray(SQLITE3_ASSOC)){
			$q_receiptitem = "INSERT IGNORE INTO sales_receiptitem SET id=".$row_receiptitem['ID'].", receipt='".$row_receiptitem['RECEIPT']."', product='".$row_receiptitem['PRODUCT']."', quantity=".$row_receiptitem['QUANTITY'].", id_bu = $id_bu";
			$r_receiptitem = $CI->db->query($q_receiptitem) or die($this->db->_error_message());
		}
		//RECEIPTITEMADDON
		$result_receiptitemaddon = $db->query('SELECT ria.RECEIPTITEM, ria.ID, ria.PRODUCTADDON, ria.QUANTITY FROM RECEIPTITEMADDON AS ria JOIN RECEIPTITEM AS ri ON ria.RECEIPTITEM = ri.ID JOIN RECEIPT AS r ON ri.RECEIPT = r.ID WHERE r.DATE_CLOSED IS NOT NULL');
		while($row_receiptitemaddon=$result_receiptitemaddon->fetchArray(SQLITE3_ASSOC)){
			$q_receiptitemaddon = "INSERT IGNORE INTO sales_receiptitemaddon SET id=".$row_receiptitemaddon['ID'].", receiptitem=".$row_receiptitemaddon['RECEIPTITEM'].", productaddon='".$row_receiptitemaddon['PRODUCTADDON']."', quantity=".$row_receiptitemaddon['QUANTITY'].", id_bu = $id_bu";
			$r_receiptitemaddon = $CI->db->query($q_receiptitemaddon) or die($this->db->_error_message());
		}
		
		//CUSTOMER
		$result_customer = $db->query('SELECT * FROM CUSTOMER');
		while($row_customer=$result_customer->fetchArray(SQLITE3_ASSOC)){
			$q_customer = "INSERT INTO sales_customers SET pos_id='".$row_customer['ID']."',  lastname='".addslashes($row_customer['LASTNAME'])."', firstname='".$row_customer['FIRSTNAME']."', zipcode='".addslashes($row_customer['ZIPCODE'])."', city='".addslashes($row_customer['CITY'])."', country='".addslashes($row_customer['COUNTRY'])."', email='".addslashes($row_customer['EMAIL'])."', phone='".addslashes($row_customer['PHONE'])."', loyalty_points=".$row_customer['LOYALTY_POINTS'].", account=".$row_customer['ACCOUNT'].", balance=".$row_customer['BALANCE'].", date_created='".$row_customer['DATE_CREATED']."', date_last_seen='".$row_customer['DATE_LAST_SEEN']."', deleted=".$row_customer['DELETED'].", id_bu= $id_bu ON DUPLICATE KEY UPDATE lastname='".addslashes($row_customer['LASTNAME'])."', firstname='".$row_customer['FIRSTNAME']."', zipcode='".addslashes($row_customer['ZIPCODE'])."', city='".addslashes($row_customer['CITY'])."', country='".addslashes($row_customer['COUNTRY'])."', email='".addslashes($row_customer['EMAIL'])."', phone='".addslashes($row_customer['PHONE'])."', loyalty_points=".$row_customer['LOYALTY_POINTS'].", account=".$row_customer['ACCOUNT'].", balance=".$row_customer['BALANCE'].", date_created='".$row_customer['DATE_CREATED']."', date_last_seen='".$row_customer['DATE_LAST_SEEN']."', deleted=".$row_customer['DELETED'].", id_bu=".$id_bu;
			
			$r_customer = $CI->db->query($q_customer) or die($this->db->_error_message());
		}
	}
	public function posInfo($action, $param = null) {
		$CI = & get_instance(); 
		$CI->load->database();
		$CI->load->library("hmw");
		$file	= $this->getPosDbDir($param['id_bu']);
		if(empty($file)) exit('No db found');
		$db	= new SQLite3($file);
		
		$getBuInfo = $CI->hmw->getBuInfo($param['id_bu']);
		$id_pos_cash_method = $getBuInfo->id_pos_cash_method;
		
		switch($action) {
			
			case 'salesUpdate':
			$this->syncSalesDb($param['id_bu']);
			$this->syncArchivesDb($param['id_bu']);
			break;
			case 'cashfloat':
			$sql1 	= "SELECT SUM(AMOUNT) AS FLOAT1 FROM CASHMOVEMENT WHERE METHOD='".$id_pos_cash_method."'";
			$result1 = $db->query($sql1);
			$res1	= $result1->fetchArray(SQLITE3_ASSOC);
			$sql2 	= "SELECT SUM(AMOUNT) AS FLOAT2 FROM RECEIPTPAYMENT WHERE METHOD='".$id_pos_cash_method."'";
			$result2 = $db->query($sql2);
			$res2	= $result2->fetchArray(SQLITE3_ASSOC);
			$sql3 	= "SELECT CASH_FLOAT_IN AS FLOAT3 FROM CASHFLOAT";
			$result3 = $db->query($sql3);
			$res3	= $result3->fetchArray(SQLITE3_ASSOC);
			$ret 	= ($res1['FLOAT1']+$res2['FLOAT2']+$res3['FLOAT3'])/1000;
			return $ret;
			break;
			
			case 'getLiveMovements':
			$sql 	= "SELECT cm.DATE, u.NAME AS USERNAME, c.LASTNAME AS CLASTNAME, c.FIRSTNAME AS CFIRSTNAME,  cm.AMOUNT, pm.NAME AS PAYMENTNAME, cm.DESCRIPTION, cm.CUSTOMER  FROM CASHMOVEMENT AS cm 
				LEFT JOIN USER AS u ON cm.USER = u.ID
				LEFT JOIN PAYMENTMETHOD AS pm ON cm.METHOD = pm.ID
				LEFT JOIN CUSTOMER AS c ON cm.CUSTOMER = c.ID";
			$result = $db->query($sql);
			$lines = array();
			while($row=$result->fetchArray(SQLITE3_ASSOC)){
				$lines[] = $row;
			}
			return $lines;
			break;
			
			case 'updateUsers':
			foreach ($CI->hmw->getUsers() as $key) {
				$sql 	= "SELECT ID FROM USER WHERE lower(NAME)='".strtolower($key->username)."'";
				$result = $db->query($sql);
				$res	= $result->fetchArray(SQLITE3_ASSOC);
				if(is_array($res)) {
					$sqlu = "INSERT INTO users_pos SET id_pos = '".$res['ID']."', id_user = ".$key->id.", id_bu = $param[id_bu] ON DUPLICATE KEY UPDATE id_pos = '".$res['ID']."'";
					$resu = $CI->db->query($sqlu);
				}
			}
			break;
			
			case 'getMovements':
			$q_mov = "SELECT sc.`date`, u.`username`, sc.`user`, sc.amount, sc.method, sc.description, sc.customer, ppt.`name` AS method_name, sc2.`firstname` AS customer_first_name, sc2.`lastname` AS customer_last_name 
				FROM sales_cashmovements AS sc 
				LEFT JOIN users_pos AS up ON up.id_pos = sc.user
				LEFT JOIN users AS u ON u.id = up.id_user 
				LEFT JOIN pos_payments_type AS ppt ON ppt.pos_id = sc.method 
				LEFT JOIN sales_customers AS sc2 ON sc2.pos_id = sc.customer
				WHERE archive = '".$param['closing_file']."' AND up.id_bu = ".$param['id_bu'];
			$r_mov = $CI->db->query($q_mov) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			return $r_mov->result_array();
			break;
			
			case 'getUsers':
			$dir	= $this->getPosArchivesDir($param['id_bu']);
			$path	= $dir."/".$param['closing_file'];
			
			$dbar	= new SQLite3($path);
			$sqlar 	= "SELECT DISTINCT(USER) FROM ARCHIVEDRECEIPTPAYMENT";
			$result = $dbar->query($sqlar);
			$res 	= array();
			while($row=$result->fetchArray(SQLITE3_ASSOC)){
				$q = "SELECT username FROM users AS u 
				LEFT JOIN users_pos AS up ON u.id = up.id_user
				WHERE up.id_pos = '".$row['USER']."'";
				$r = $CI->db->query($q) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
				$o = $r->result_array();
				if($o) { 
					$res[] = $o['0']['username']; 
				} else {
					$res[] = $row['USER'];
				}
			}
			return $res;
			break;
		}
	}
	public function calc($action, $id_bu) {
		$CI = & get_instance(); 
		$CI->load->database();
		switch($action) {
			case 'safe_current_cash_amount':
			$q = "SELECT SUM(amount_user) AS amount FROM pos_payments AS pp JOIN pos_movements AS pm ON pp.id_movement = pm.id WHERE pm.movement IN ('safe_in','safe_out') AND pp.id_payment = 1 AND pm.id_bu = $id_bu";
			$r = $CI->db->query($q) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$o = $r->result_object();
			$ret = $o[0]->amount;
			if(empty($ret)) $ret = 0;
			return $ret;
			break;
			
			case 'safe_current_tr_num':
			$q = "SELECT SUM(amount_user) AS amount FROM pos_payments AS pp JOIN pos_movements AS pm ON pp.id_movement = pm.id WHERE pm.movement IN ('safe_in','safe_out') AND pp.id_payment = 3 AND pm.id_bu = $id_bu";
			$r = $CI->db->query($q) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$o = $r->result_object();
			$ret = $o[0]->amount;
			if(empty($ret)) $ret = 0;
			return $ret;
			break;
		}
	}
	//cd /var/www/hank/HMW/hmw && php index.php pos getClosureData
	//datein format YYYYMMDD
	public function getClosureData($datein = null, $file = null, $id_bu = null)
	{
		$CI = & get_instance(); 
		$CI->load->library("hmw");
		//$datenow	= @date('Y').@date('m').@date('d').'T000000';
		if(isset($datein)) $datein = $datein.'T000000';
		if(!isset($file))  $file = $this->getPosArchivesFileName($datein, $id_bu);
		if(empty($file)) {
			return null;
		}
		$dir	= $this->getPosArchivesDir($id_bu);
		$path	= $dir."/".$file;
		$db = new SQLite3($path);
		$sql = "select SUM(AMOUNT) AS SUM, METHOD from ARCHIVEDRECEIPTPAYMENT AS rp GROUP BY rp.METHOD";
		$result = $db->query($sql);
		$row = array(); 
		$i = 0; 
		while($res = $result->fetchArray(SQLITE3_ASSOC)) { 
			if(!isset($res['SUM'])) continue; 
			$res_method = strtoupper($res['METHOD']);
			$method = 'PAYMENT_METHOD_NOT_FOUND_'.$res_method;
			$row[$i]['SUM'] = $res['SUM']/1000;
			$val_method = $this->getPaymentMethodName($res_method, $id_bu);
			if(isset($val_method)) $method = strtoupper($val_method['name']);
			$row[$i]['METHOD'] = $method;
			$row[$i]['IDMETHOD'] = $val_method['id'];
			$i++; 
		} 
		$sqlid		= "select SEQUENTIAL_ID AS SEQID from ARCHIVE ORDER BY SEQUENTIAL_ID DESC LIMIT 1";
		$resultid	= $db->query($sqlid);
		$archiveid	= $resultid->fetchArray(SQLITE3_ASSOC);
		$ret 			= array();
		$ret['file']	= $file;
		$ret['ca']		= $row;
		$ret['seqid']	= $archiveid['SEQID'];
		return $ret;	
	}
	//datein format YYYYMMDDT000000
	public function getPosArchivesFileName($datein = null, $id_bu = null)
	{
		$CI = & get_instance(); 
		$CI->load->library("hmw");
		if(isset($datein)) $dateseek = $this->getPosArchivesDatetime($datein);
		$dir	= $this->getPosArchivesDir($id_bu);
		if(empty($dir)) exit('No db found');
		$files	= scandir($dir, 0);
		$line2	= null;
		foreach ($files as $line) {
			if($line[0] == 2 ) {
				$ex			= explode('.', $line);
				$date		= $this->getPosArchivesDatetime($ex[0]);
				$day 		= $date['Y']."-".$date['m']."-".$date['dd'];
				if(isset($datein)) $dayseek	= $dateseek['Y']."-".$dateseek['m']."-".$dateseek['dd'];
				if(isset($datein)) {
					if($day == $dayseek) $line2 = $line;
				}
			}
		}
		if(isset($datein) AND empty($line2)) return null;
		if(isset($line2)) $line = $line2;
		return $line;
	}
	
	private function getPosArchivesDir($id_bu) {
		$CI = & get_instance(); 
		$CI->db->select('bus.pos_archives_dir');
		$CI->db->where('bus.id', $id_bu);
		$query = $CI->db->get("bus");
		$res = $query->result();
		return trim($res[0]->pos_archives_dir);
	}
	private function getPosDbDir($id_bu) {
		$CI = & get_instance();
		$CI->db->select('bus.pos_db_dir');
		$CI->db->where('bus.id', $id_bu);
		$query = $CI->db->get("bus");
		$res = $query->result();
		return trim($res[0]->pos_db_dir);
	}
	public function getPosArchivesDatetime($datex) {
		$ex			= explode('T', $datex);
		$date 		= array();
		$date['Y']	= substr($ex[0], 0, 4);
		$date['m']	= substr($ex[0], 4, 2);
		$date['dd']	= substr($ex[0], 6, 2);
		$date['hh']	= substr($ex[1], 0, 2);
		$date['mn']	= substr($ex[1], 2, 2);
		$date['ss']	= substr($ex[1], 4, 2);
		$date['tt']	= $date['Y']."-".$date['m']."-".$date['dd']." ".$date['hh'].":".$date['mn'].":".$date['ss'];
		return $date;
	}
	public function getPaymentMethodName($id, $id_bu) 
	{
		$CI = & get_instance(); 
		$CI->load->database();
		$req = "SELECT `name`,`id` FROM pos_payments_type WHERE pos_id= '".$id."' AND id_bu = $id_bu LIMIT 1";
		$res = $CI->db->query($req) or die($this->mysqli->error);
		$ret = $res->result_array();
		return $ret[0];
	}
	public function clean_number($num) {
		$t1 = str_replace ( ',' , '.' , $num);
		$t2 = trim($t1);
		$t3 = preg_replace("/[^0-9,.]/", "", $t2);
		return $t3;
	}
}
?>