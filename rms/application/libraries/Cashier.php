<?php

class Cashier {

	private function getSalesForProduct($id) {
		$CI = & get_instance(); 
		$CI->load->database();
		$qtty = 0;

		//get last_stock_update 
		$q_stock = "SELECT last_update_stock FROM sales_product WHERE id_pos = '".$id."'";
		$r_stock = $CI->db->query($q_stock) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
		$row_stock = $r_stock->result_array();
		$last_update = $row_stock[0]['last_update_stock'];
		
		//get sales product
		$q_sp = "SELECT sri.quantity AS quantity 
			FROM sales_receipt AS sr
			JOIN sales_receiptitem AS sri ON sri.receipt = sr.`id`
			WHERE sri.product = '".$id."'
			AND (sr.date_closed > '".$last_update."') 
			AND sr.canceled = 0";
		$r_sp = $CI->db->query($q_sp) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
		$row_sp = $r_sp->result_array();
		
		foreach ($row_sp as $key) {
			$qtty += 1*($key['quantity']/1000);
		}

		//get productaddon
		$q_spa = "SELECT sria.quantity FROM sales_productaddon AS spa
			JOIN sales_receiptitemaddon AS sria ON sria.productaddon = spa.id_pos
			JOIN sales_receiptitem AS sri ON sri.id = sria.receiptitem
			JOIN sales_receipt AS sr ON sr.`id` = sri.receipt
			JOIN sales_product AS sp ON sp.id_pos = spa.id_pos_product
			WHERE spa.id_pos_product = '".$id."'
			AND (sr.date_closed > '".$last_update."') 
			AND sr.canceled = 0";
		$r_spa = $CI->db->query($q_spa) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
		$row_spa = $r_spa->result_array();
		foreach ($row_spa as $keya) {
			$qtty += 1*($keya['quantity']);
		}

		return $qtty;
	}

	public function updateStock() {

		$CI = & get_instance(); 
		$CI->load->database();

		$q_pos_pdt = "SELECT * FROM sales_product WHERE deleted=0";
		$r_pos_pdt = $CI->db->query($q_pos_pdt) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
		$res_pos_pdt = $r_pos_pdt->result_array();

		foreach ($res_pos_pdt as $pos_pdt) {
			$CI->db->query("BEGIN") or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			
			$sales = $this->getSalesForProduct($pos_pdt['id_pos']);
			//echo "$sales for $pos_pdt[name]";
			
			$q_mapping = "SELECT * FROM products_mapping WHERE id_pos=$pos_pdt[id]";
			$r_mapping = $CI->db->query($q_mapping) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$res_mapping = $r_mapping->result_array();

			foreach ($res_mapping as $mapping) {				
				$CI->db->query("UPDATE products_stock SET qtty = qtty-($sales*$mapping[coef]), last_update_pos = NOW() WHERE id_product = $mapping[id_product]") or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			}
			
			$CI->db->query("UPDATE sales_product SET last_update_stock = NOW() WHERE id_pos = '".$pos_pdt['id_pos']."'") or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
			$CI->db->query("COMMIT") or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
		}

	}

	private function getDoneArchivesList() {

		$CI = & get_instance(); 
		$CI->load->database();

		//get pos_archives 
		$q_archives = "SELECT * FROM pos_archives";
		$r_archives = $CI->db->query($q_archives) or die('ERROR '.$this->db->_error_message().error_log('ERROR '.$this->db->_error_message()));
		$o_archives = $r_archives->result_array();
		$ret = array();
		foreach ($o_archives as $line) {
			$ret[$line['file']] = $line['file']; 
		}

		return $ret;
	}
	
	private function insertArchives($file) {

		$CI = & get_instance(); 
		$CI->load->database();

		$dir	= $CI->hmw->getParam('pos_archive_dir');
		$path	= $dir."/".$file;
		$db		= new SQLite3($path);

		$result_check = $db->query("SELECT * FROM sqlite_master WHERE name ='ARCHIVEDRECEIPT' and type='table'");
		$result_check_ar = $result_check->fetchArray(SQLITE3_ASSOC);

		if(!empty($result_check_ar)) {

			//RECEIPT
			$result_receipt = $db->query('SELECT * FROM ARCHIVEDRECEIPT');
			while($row_receipt=$result_receipt->fetchArray(SQLITE3_ASSOC)){
				if(!empty($row_receipt['SEQUENTIAL_ID'])) {
					$q_receipt = "INSERT IGNORE INTO sales_receipt SET id='".$row_receipt['ID']."', sequential_id=".$row_receipt['SEQUENTIAL_ID'].", owner='".$row_receipt['OWNER']."', date_created='".$row_receipt['DATE_CREATED']."', date_closed='".$row_receipt['DATE_CLOSED']."', canceled='".$row_receipt['CANCELLED']."', amount_total=".$row_receipt['AMOUNT_TOTAL']; 
					$r_receipt = $CI->db->query($q_receipt) or die($this->db->_error_message());
				}
			}

			//RECEIPTITEM
			$result_receiptitem = $db->query('SELECT * FROM ARCHIVEDRECEIPTITEM');
			while($row_receiptitem=$result_receiptitem->fetchArray(SQLITE3_ASSOC)){
				$q_receiptitem = "INSERT IGNORE INTO sales_receiptitem SET id=".$row_receiptitem['ID'].", receipt='".$row_receiptitem['ARCHIVEDRECEIPT']."', product='".$row_receiptitem['PRODUCT']."', quantity=".$row_receiptitem['QUANTITY'];
				$r_receiptitem = $CI->db->query($q_receiptitem) or die($this->db->_error_message());
			}

			//RECEIPTITEMADDON
			$result_receiptitemaddon = $db->query('SELECT * FROM ARCHIVEDRECEIPTITEMADDON');
			while($row_receiptitemaddon=$result_receiptitemaddon->fetchArray(SQLITE3_ASSOC)){
				$q_receiptitemaddon = "INSERT IGNORE INTO sales_receiptitemaddon SET id=".$row_receiptitemaddon['ID'].", receiptitem=".$row_receiptitemaddon['ARCHIVEDRECEIPTITEM'].", productaddon='".$row_receiptitemaddon['PRODUCTADDON']."', quantity=".$row_receiptitemaddon['QUANTITY']; 
				$r_receiptitemaddon = $CI->db->query($q_receiptitemaddon) or die($this->db->_error_message());
			}
		}
		//update pos_archive
		$q_archives = "INSERT INTO pos_archives SET file ='".$file."'";
		$r_archives = $CI->db->query($q_archives) or die($this->db->_error_message());


	}

	private function syncArchivesDb() {

		$CI = & get_instance(); 
		$CI->load->database();

		$dir	= $CI->hmw->getParam('pos_archive_dir');
		$files	= scandir($dir, 0);
		$line2	= null;

		$archives_list = $this->getDoneArchivesList();

		foreach ($files as $line) {
			if($line[0] == 2 ) {
				//search if file already done
				$key = array_key_exists($line, $archives_list);
				if($key == null) {
					$this->insertArchives($line);
				}
			}
		}

	}

	private function syncSalesDb() {

		$CI = & get_instance(); 
		$CI->load->database();

		//get sqlite3 data from cashpad db
		$file	= $CI->hmw->getParam('pos_db_dir');
		$db		= new SQLite3($file);

		//PRODUCT
		$result_product = $db->query('SELECT * FROM PRODUCT');
		while($row_product=$result_product->fetchArray(SQLITE3_ASSOC)){
	
			$q_product = "INSERT INTO sales_product SET id_pos='".$row_product['ID']."',  name='".addslashes($row_product['NAME'])."', category='".$row_product['CATEGORY']."', deleted=".$row_product['DELETED']." ON DUPLICATE KEY UPDATE name='".addslashes($row_product['NAME'])."', category='".$row_product['CATEGORY']."', deleted=".$row_product['DELETED'];
			
			$r_product = $CI->db->query($q_product) or die($this->db->_error_message());
		}

		//PRODUCTADDON
		$result_productaddon = $db->query('SELECT * FROM PRODUCTADDON');
		while($row_productaddon=$result_productaddon->fetchArray(SQLITE3_ASSOC)){
			$q_productaddon = "INSERT INTO sales_productaddon SET id_pos='".$row_productaddon['ID']."',  property_name='".addslashes($row_productaddon['PROPERTY_NAME'])."', category='".$row_productaddon['CATEGORY']."', id_pos_product='".$row_productaddon['PRODUCT']."', deleted=".$row_productaddon['DELETED']." ON DUPLICATE KEY UPDATE property_name='".addslashes($row_productaddon['PROPERTY_NAME'])."', category='".$row_productaddon['CATEGORY']."', id_pos_product='".$row_productaddon['PRODUCT']."', deleted=".$row_productaddon['DELETED'];
			$r_productaddon = $CI->db->query($q_productaddon) or die($this->db->_error_message());
		}

		//RECEIPT
		$result_receipt = $db->query('SELECT * FROM RECEIPT');
		while($row_receipt=$result_receipt->fetchArray(SQLITE3_ASSOC)){
			if(!empty($row_receipt['SEQUENTIAL_ID'])) {
				$q_receipt = "INSERT IGNORE INTO sales_receipt SET id='".$row_receipt['ID']."', sequential_id=".$row_receipt['SEQUENTIAL_ID'].", owner='".$row_receipt['OWNER']."', date_created='".$row_receipt['DATE_CREATED']."', date_closed='".$row_receipt['DATE_CLOSED']."', canceled='".$row_receipt['CANCELLED']."', amount_total=".$row_receipt['AMOUNT_TOTAL']; 
				$r_receipt = $CI->db->query($q_receipt) or die($this->db->_error_message());
			}
		}

		//RECEIPTITEM
		$result_receiptitem = $db->query('SELECT * FROM RECEIPTITEM');
		while($row_receiptitem=$result_receiptitem->fetchArray(SQLITE3_ASSOC)){
			$q_receiptitem = "INSERT IGNORE INTO sales_receiptitem SET id=".$row_receiptitem['ID'].", receipt='".$row_receiptitem['RECEIPT']."', product='".$row_receiptitem['PRODUCT']."', quantity=".$row_receiptitem['QUANTITY'];
			$r_receiptitem = $CI->db->query($q_receiptitem) or die($this->db->_error_message());
		}

		//RECEIPTITEMADDON
		$result_receiptitemaddon = $db->query('SELECT * FROM RECEIPTITEMADDON');
		while($row_receiptitemaddon=$result_receiptitemaddon->fetchArray(SQLITE3_ASSOC)){
			$q_receiptitemaddon = "INSERT IGNORE INTO sales_receiptitemaddon SET id=".$row_receiptitemaddon['ID'].", receiptitem=".$row_receiptitemaddon['RECEIPTITEM'].", productaddon='".$row_receiptitemaddon['PRODUCTADDON']."', quantity=".$row_receiptitemaddon['QUANTITY']; 
			$r_receiptitemaddon = $CI->db->query($q_receiptitemaddon) or die($this->db->_error_message());
		}

	}

	public function posInfo($action, $param = null) {

		$CI = & get_instance(); 
		$CI->load->database();
		$CI->load->library("hmw");

		$file	= $CI->hmw->getParam('pos_db_dir');
		$db		= new SQLite3($file);

		switch($action) {

			case 'salesUpdate':

			$this->syncSalesDb();
			$this->syncArchivesDb();

			break;

			case 'cashfloat':
			$sql1 	= "SELECT SUM(AMOUNT) AS FLOAT1 FROM CASHMOVEMENT WHERE METHOD='7DD4A3FB-ADC2-49D9-9EDE-01129023FE37'";
			$result1 = $db->query($sql1);
			$res1	= $result1->fetchArray(SQLITE3_ASSOC);

			$sql2 	= "SELECT SUM(AMOUNT) AS FLOAT2 FROM RECEIPTPAYMENT WHERE METHOD='7DD4A3FB-ADC2-49D9-9EDE-01129023FE37'";
			$result2 = $db->query($sql2);
			$res2	= $result2->fetchArray(SQLITE3_ASSOC);

			$sql3 	= "SELECT CASH_FLOAT_IN AS FLOAT3 FROM CASHFLOAT";
			$result3 = $db->query($sql3);
			$res3	= $result3->fetchArray(SQLITE3_ASSOC);

			$ret 	= ($res1['FLOAT1']+$res2['FLOAT2']+$res3['FLOAT3'])/1000;
			return $ret;
			break;

			case 'updateUsers':
			foreach ($CI->hmw->getUsers() as $key) {
				$sql 	= "SELECT ID FROM USER WHERE lower(NAME)='".strtolower($key->username)."'";
				$result = $db->query($sql);
				$res	= $result->fetchArray(SQLITE3_ASSOC);
				if(is_array($res)) {
					$sqlu = "UPDATE users SET pos_id = '".$res['ID']."' WHERE id = $key->id";
					$resu = $CI->db->query($sqlu);
				}
			}
			break;

			case 'getUsers':
			$dir	= $CI->hmw->getParam('pos_archive_dir');
			$path	= $dir."/".$param['closing_file'];
			$dbar	= new SQLite3($path);
			$sqlar 	= "SELECT DISTINCT(USER) FROM ARCHIVEDRECEIPTPAYMENT";
			$result = $dbar->query($sqlar);
			$res 	= array();
			while($row=$result->fetchArray(SQLITE3_ASSOC)){
				$q = "SELECT username FROM users WHERE pos_id = '".$row['USER']."'";
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

	public function calc($action) {

		$CI = & get_instance(); 
		$CI->load->database();

		switch($action) {

			case 'safe_current_cash_amount':
			$q = "SELECT SUM(amount_user) AS amount FROM pos_payments AS pp JOIN pos_movements AS pm ON pp.id_movement = pm.id WHERE pm.movement IN ('safe_in','safe_out') AND pp.id_payment = 1";
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
	public function getClosureData($datein = null, $file = null)
	{
		$CI = & get_instance(); 
		$CI->load->library("hmw");

		//$datenow	= @date('Y').@date('m').@date('d').'T000000';
		if(isset($datein)) $datein = $datein.'T000000';

		if(!isset($file))  $file = $this->getPosArchivesFileName($datein);

		if(empty($file)) {
			return null;
		}

		$dir	= $CI->hmw->getParam('pos_archive_dir');
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
			$val_method = $this->getPaymentMethodName($res_method);
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
	public function getPosArchivesFileName($datein = null)
	{

		$CI = & get_instance(); 
		$CI->load->library("hmw");

		if(isset($datein)) $dateseek = $this->getPosArchivesDatetime($datein);

		$dir	= $CI->hmw->getParam('pos_archive_dir');
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

	public function getPaymentMethodName($id) 
	{
		$CI = & get_instance(); 
		$CI->load->database();

		$req = "SELECT `name`,`id` FROM pos_payments_type WHERE pos_id='".$id."' LIMIT 1";
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