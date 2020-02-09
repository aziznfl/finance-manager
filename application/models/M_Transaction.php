<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Transaction extends CI_Model {

	function __construct() {
		parent::__construct();
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

	function getDashboardTransaction($type = "outcome") {
		$this->db->select("*");
		$this->db->select("SUM(amount) as total");
		$this->db->join("category", "category.category_id = transaction.category_id");
		$this->db->group_by("extract(year from transaction_date), extract(month from transaction_date)");
		$this->db->order_by("transaction_date", "ASC");
		$this->db->order_by("type", "DESC");
		$this->db->where("account_id", $this->session->userdata('user')->account_id);
		$this->db->where("type", $type);
		$query = $this->db->get('transaction');
		return $query->result_array();
	}

	function getFirstTransaction() {
		$this->db->order_by("transaction_date", "ASC");
		$this->db->limit(1);
		return $this->db->get('transaction');
	}

	function get($tag) {
		$query = "
			SELECT a.date, a.amount, a.description
			FROM (
				SELECT transaction_date as date, amount, description, added_date FROM `transaction` WHERE tag = '".$tag."'
				UNION
				SELECT transaction_date as date, (amount * -1) as amount, description, added_date FROM transaction_oop WHERE tag = '".$tag."'
			) a
			ORDER BY a.date DESC
		";
		return $this->db->query($query);
	}

	function getAmountTag($tag) {
		$query = "
			SELECT SUM(a.amount) as total
			FROM (
				SELECT transaction_date as date, amount, tag FROM `transaction` WHERE tag = '".$tag."'
				UNION
				SELECT transaction_date as date, (amount * -1) as amount, tag FROM transaction_oop WHERE tag = '".$tag."'
			) a
			GROUP BY a.tag
			ORDER BY a.date DESC
		";
		return $this->db->query($query);
	}

	//-------- Global --------//

	function addData($table, $data) {
		$this->db->insert($table, $data);
		return $this->db->affected_rows();
	}

	function updateData($table, $data, $where) {
		$this->db->where($where);
		$this->db->update($table, $data);
		return $this->db->affected_rows();
	}

	//-------- Transaction ---------//

	function getAllTransaction($limit, $type = "outcome") {
		// $this->db->limit($limit);
		$this->db->join("category", "category.category_id = transaction.category_id");
		$this->db->order_by("type", "DESC");
		$this->db->order_by("transaction_date", "DESC");
		$this->db->order_by("added_date", "DESC");
		$this->db->order_by("category.category_id", "ASC");
		$this->db->where("type", $type);
		$this->db->where("account_id", $this->session->userdata('user')->account_id);
		$this->db->limit($limit);
		return $this->db->get('transaction');
	}

	function getMonthTransaction($month, $year, $type = "outcome") {
		$this->db->join("category", "category.category_id = transaction.category_id", "left");
		$this->db->order_by("type", "DESC");
		$this->db->order_by("transaction_date", "DESC");
		$this->db->order_by("added_date", "DESC");
		$this->db->order_by("category.category_id", "ASC");
		$this->db->where("type", $type);
		$this->db->where("MONTH(transaction_date) = ".$month);
		$this->db->where("YEAR(transaction_date) = ".$year);
		$this->db->where("account_id", $this->session->userdata('user')->account_id);
		return $this->db->get('transaction');
	}

	function getTopTransaction($month, $year, $type = "outcome") {
		$where = "MONTH(transaction_date) = '".$month."' AND YEAR(transaction_date) = '".$year."'";
		$query = "
			SELECT category.category_id, category_name, SUM(amount) as total, (SUM(amount) * 100 / (
				SELECT SUM(amount) 
				FROM transaction 
				WHERE ".$where.")
			) as percentage
			FROM transaction
			JOIN category ON category.category_id = transaction.category_id
			WHERE ".$where."
			GROUP BY category_id
			ORDER BY total DESC
		";
		return $this->db->query($query);
	}

	function getTransaction($transaction_id) {
		$this->db->where("transaction_id", $transaction_id);
		return $this->db->get('transaction');
	}

	//-------- Investment --------//

	function getInvestment() {
		$this->db->join("category_investment", "transaction_investment.category_id = category_investment.category_id", "left");
		$this->db->where("account_id", $this->session->userdata('user')->account_id);
		$this->db->order_by("transaction_date", "ASC");
		return $this->db->get('transaction_investment');
	}

	function getTotalInvestment() {
		$query = "
			SELECT SUM(amount) as total_investment 
			FROM transaction_investment 
			WHERE account_id = ".$this->session->userdata('user')->account_id." && is_done = 0
		";
		return $this->db->query($query);
	}
}
?>