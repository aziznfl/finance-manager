<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_TransactionV1 extends CI_Model {

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

	function getTotalPerMonthTransaction($apiKey) {
		$query = "
			SELECT EXTRACT(YEAR FROM transaction_date) AS year, EXTRACT(MONTH FROM transaction_date) AS month, SUM(amount) AS total_monthly, COUNT(transaction_id) as count_monthly
			FROM transaction
			LEFT JOIN account ON account.account_id = transaction.account_id
			WHERE api_key = '".$apiKey."' AND type = 'outcome' AND is_deleted = 0
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
}
?>