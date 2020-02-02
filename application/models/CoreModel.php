<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CoreModel extends CI_Model {

	function getMenus() {
		$this->db->order_by("ordering", "ASC");
		$result = $this->db->get('menu');

		$arrayMenu[0] = array();
		$arrayMenu[1] = array();
		foreach($result->result_array() as $menu) {
			if ($menu['parent_id'] == 0 && $menu['layer'] == 0) {
				array_push($arrayMenu[0], $menu);
			} else {
				array_push($arrayMenu[1], $menu);
			}
		}

		return $arrayMenu;
	}
}
