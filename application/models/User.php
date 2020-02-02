<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Model {

	function __construct() {
		parent::__construct();
	}

	function login($username, $password) {
		$this->db->where('email', $username);
		$this->db->where('password', $password);
		return $this->db->get('account');
	}
}
?>