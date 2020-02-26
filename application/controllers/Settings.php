<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MY_Controller {

    function __construct() {
        parent::__construct();

		if ($this->session->userdata('user') == '') {
			header("location: ".base_url('account'));
			exit;
		}

		$this->load->helper('form');
		$this->load->model('M_Transaction');
    }

    function category() {
		$result["categories"] = $this->listCategories();

        $result["add_footer"] = "
            <script>
                function manageCategories() {
                    var name = $('input[name=name]').val();
                    var parent = $('input[name=parent]').val();
                    console.log(name + '-' + parent);
                }
            </script>
        ";

    	$this->load->view('root/_header', $result);
    	$this->load->view('root/_menus');
    	$this->load->view('settings/category');
    	$this->load->view('root/_footer');
    }
}
?>