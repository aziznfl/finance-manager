<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_TransactionV1 extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	function getWhereTransaction($accountKey = null) {
		if ($accountKey == null) {
			$accountKey = $this->session->userdata('user')->account_key;
		}
		return "(account_key = '".$accountKey."')";
	}

	function getCategories() {
		$this->db->where("category_id != 1");
		$this->db->order_by("parent_id", "ASC");
		$this->db->order_by("position", "ASC");
		return $this->db->get('category');
	}

	function getCategoriesInvestment() {
		return $this->db->get('category_investment');
	}

	//-------- Global --------//

	function addData($table, $data) {
		$this->db->insert($table, $data);
		return $this->db->insert_id();
	}

	function updateData($table, $data, $where) {
		$this->db->where($where);
		$this->db->update($table, $data);
		return $this->db->affected_rows();
	}

	function deleteData($table, $where) {
		$this->db->delete($table, $where);
		return $this->db->affected_rows();
	}

	//-------- Dashboard ---------//

	function getDashboardTransaction($accountKey = null) {
		$where = " WHERE type = 'outcome' AND transaction_date > DATE_ADD(NOW(), INTERVAL -1 YEAR) AND ".$this->getWhereTransaction($accountKey) ." ";

		$query = "
			SELECT a.year, a.month, total_transaction, total_investment
			FROM (
			    SELECT extract(year FROM transaction_date) year, extract(month FROM transaction_date) month, SUM(amount) as total_transaction
			    FROM transaction
			    ".$where."AND is_deleted = 0
			    GROUP BY extract(year FROM transaction_date), extract(month FROM transaction_date)
			) a
			LEFT JOIN (
			    SELECT extract(year FROM transaction_date) year, extract(month FROM transaction_date) month, SUM(amount) as total_investment
			    FROM transaction_investment
			    ".$where." AND category_id != 1
			    GROUP BY extract(year FROM transaction_date), extract(month FROM transaction_date)
			) b ON a.year = b.year AND a.month = b.month
		";
		return $this->db->query($query);
	}

	//-------- Transaction ---------//

	function getTotalPerMonthTransaction($accountKey = null) {
		$query = "
			SELECT EXTRACT(YEAR FROM transaction_date) AS year, EXTRACT(MONTH FROM transaction_date) AS month, SUM(amount) AS total_monthly, COUNT(transaction_id) as count_monthly
			FROM transaction
			WHERE ".$this->getWhereTransaction($accountKey)." AND type = 'outcome' AND is_deleted = 0
			GROUP BY EXTRACT(YEAR FROM transaction_date), EXTRACT(MONTH FROM transaction_date)
			ORDER BY transaction_date DESC
		";
		return $this->db->query($query);
	}

	function getCategoryTransaction($category_id, $apiKey) {
		$query = "
			SELECT CategoryHierarchy.* FROM (
			    SELECT transaction.*, category.category_name
			    FROM transaction
			    LEFT JOIN category ON category.category_id = transaction.category_id
			    WHERE category.category_id = ".$category_id."
			    UNION
			    SELECT transaction.*, category.category_name
			    FROM transaction
			    LEFT JOIN category ON category.category_id = transaction.category_id
			    WHERE category.parent_id = ".$category_id."
			) as CategoryHierarchy
			LEFT JOIN account ON account.account_id = CategoryHierarchy.account_id
			WHERE account.api_key = '".$apiKey."'
			ORDER BY transaction_date DESC
		";
		return $this->db->query($query);
	}

	function getLastTransaction($limit, $accountKey) {
		$query = "
			SELECT transaction.*, category.*
			FROM `transaction`
			LEFT JOIN category ON category.category_id = transaction.category_id
			WHERE account_key = '".$accountKey."'
			ORDER BY transaction_date DESC
			LIMIT ".$limit."
		";
		return $this->db->query($query);
	}

	function getTopTransaction($month, $year, $accountKey) {
		$where = "MONTH(transaction_date) = '".$month."' AND YEAR(transaction_date) = '".$year."' AND is_deleted = 0 AND ".$this->getWhereTransaction($accountKey);
		$query = "
			SELECT t.*, category.category_name
			FROM (
			SELECT 
			    (CASE WHEN category.parent_id = 1 THEN category.category_id ELSE category.parent_id END) as category_id, 
			    transaction_date, 
			    SUM(amount) as total,
			    (SUM(amount) * 100 / (SELECT SUM(amount) FROM transaction WHERE ".$where.")) as percentage
			FROM transaction
			JOIN category ON category.category_id = transaction.category_id
			WHERE ".$where."
			GROUP BY CASE WHEN category.parent_id = 1 THEN category.category_id ELSE category.parent_id END, extract(year from transaction_date), extract(month from transaction_date)
			ORDER BY total DESC) t
			LEFT JOIN category ON t.category_id = category.category_id
		";
		return $this->db->query($query);
	}

	function getMonthTransaction($month, $year, $category_id, $accountKey) {
		$this->db->join("category", "category.category_id = transaction.category_id", "left");
		$this->db->order_by("type", "DESC");
		$this->db->order_by("transaction_date", "DESC");
		$this->db->order_by("added_date", "DESC");
		$this->db->order_by("category.category_id", "ASC");
		$this->db->where("type", "outcome");
		$this->db->where("MONTH(transaction_date)", $month);
		$this->db->where("YEAR(transaction_date)", $year);
		$this->db->where($this->getWhereTransaction($accountKey));
		
		if ($category_id != 0) $this->db->where("(category.category_id = ".$category_id." OR category.parent_id = ".$category_id.")");

		return $this->db->get('transaction');
	}

	function getTransactions($lastTransaction = "", $accountKey) {
		$where = "account_key = '".$accountKey."'";
		if ($lastTransaction != "") { $where .= " and added_date > '".$lastTransaction."'"; }
		$query = "
			SELECT transaction.*
			FROM transaction
			WHERE ".$where."
			ORDER BY transaction_date ASC, category_id ASC
		";
		return $this->db->query($query);
	}

	//---------- Investment ----------//

	function getInvestment($accountKey) {
		$this->db->join("category_investment", "transaction_investment.category_id = category_investment.category_id", "left");
		$this->db->where($this->getWhereTransaction($accountKey));
		$this->db->order_by("transaction_date", "ASC");
		return $this->db->get('transaction_investment');
	}

	function getTotalInvestment($accountKey) {
		$query = "
			SELECT SUM(CASE WHEN type = 'outcome' THEN amount ELSE -amount END) as total_investment
			FROM transaction_investment 
			WHERE account_key = '".$accountKey."'
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
			    WHERE ".$this->getWhereTransaction()."
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
			WHERE ".$this->getWhereTransaction()."
			ORDER BY transaction_date DESC
		";
		return $this->db->query($query);
	}
}
?>