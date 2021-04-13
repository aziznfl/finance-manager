<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_Transaction extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	function getWhereTransaction() {
		return "(account_key = '".$this->session->userdata('user')->account_key."')";
	}

	function getCategories() {
		$this->db->where("category_id != 1");
		$this->db->order_by("parent_id", "ASC");
		$this->db->order_by("position", "ASC");
		return $this->db->get('category');
	}

	function get($tag) {
		$query = "
			SELECT a.date, a.amount, a.description
			FROM (
				SELECT transaction_date as date, amount, description, added_date FROM `transaction` WHERE tag = '".$tag."' AND ".$this->getWhereTransaction()."
				UNION
				SELECT transaction_date as date, (amount * -1) as amount, description, added_date FROM transaction_oop WHERE tag = '".$tag."' AND ".$this->getWhereTransaction()."
			) a
			ORDER BY a.date DESC
		";
		return $this->db->query($query);
	}

	function getAmountTag($tag) {
		$query = "
			SELECT SUM(a.amount) as total
			FROM (
				SELECT transaction_date as date, amount, tag FROM `transaction` WHERE tag = '".$tag."' AND ".$this->getWhereTransaction()."
				UNION
				SELECT transaction_date as date, (amount * -1) as amount, tag FROM transaction_oop WHERE tag = '".$tag."' AND ".$this->getWhereTransaction()."
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

	function deleteData($table, $where) {
		$this->db->delete($table, $where);
		return $this->db->affected_rows();
	}

	//-------- Transaction ---------//

	function getRecurringTransaction() {
		$this->db->join("category", "category.category_id = transaction_recurring.category_id", "left");
		$this->db->where($this->getWhereTransaction());
		return $this->db->get("transaction_recurring");
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