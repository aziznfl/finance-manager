<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_TransactionV1 extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	function getWhereTransaction($accountKey) {
		return "(account_key = '".$accountKey."')";
	}

	//-------- Transaction ---------//

	function getTotalPerMonthTransaction($accountKey) {
		$query = "
			SELECT EXTRACT(YEAR FROM transaction_date) AS year, EXTRACT(MONTH FROM transaction_date) AS month, SUM(amount) AS total_monthly, COUNT(transaction_id) as count_monthly
			FROM transaction
			WHERE ".$this->getWhereTransaction($accountKey)." AND type = 'outcome' AND is_deleted = 0
			GROUP BY EXTRACT(YEAR FROM transaction_date), EXTRACT(MONTH FROM transaction_date)
			ORDER BY transaction_date DESC
		";
		return $this->db->query($query);
	}

	//--------- Debts ---------//

	function getDebtsBalance() {
		$query = "
			SELECT *
			FROM (
			    SELECT to_who, SUM(
			        IF (type = 'debts' OR type = 'transfer_from', -amount, amount)
			    ) AS balance
			    FROM debts
			    WHERE ".$this->getWhereTransaction($this->session->userdata('user')->account_key)."
			    GROUP BY to_who
			    ORDER BY transaction_date ASC
			) AS debts_view
			WHERE debts_view.balance != 0
		";
		return $this->db->query($query);
	}

	function getDebtsList() {
		$query = "
			SELECT * FROM `debts`
			WHERE ".$this->getWhereTransaction($this->session->userdata('user')->account_key)."
			ORDER BY transaction_date DESC
		";
		return $this->db->query($query);
	}
}
?>