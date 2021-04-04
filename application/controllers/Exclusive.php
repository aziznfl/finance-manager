<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exclusive extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('M_TransactionV1');

		$accountKey = $this->getHeaderFromUrl('currentUser');
        if (!$this->M_TransactionV1->checkHeaders($accountKey)) {
			header("location: ".base_url('account/logoutUserSettings'));
			exit;
		}
	}

	//-------- Transaction ---------//

	// return JSON
	function insertTransactionNewFlow() {
		$response = $this->getResponseUrl();
		
		$data['category_id'] = $response->categoryId;
		$data['transaction_date'] = $response->date;
		$data['amount'] = $response->amount;
		$data['description'] = $response->description;
		$data['tag'] = $response->tag;
		$data['location'] = $response->location->name;
		$data['coordinate'] = $response->location->coordinate;
		if (isset($response->picture)) {
			$data['picture'] = $response->picture;
		}

		// get from headers
		$data['account_key'] = $this->input->get_request_header('currentUser', true);

		// set date time
		$addedDate = $this->input->post('addedDate');
		if ($addedDate != "" || $addedDate != null) {
			$data['added_date'] = $addedDate;
		}
		$timestamp = time();
		$data["transaction_identify"] = "FMTR".$timestamp;

		// insert transaction to database
		$transactionId = $this->M_TransactionV1->addData("transaction", $data);

		// set transaction list
		if (isset($response->items)) {
			foreach($response->items as $item) {
				$arr["transaction_id"] = $transactionId;
				$arr["name"] = $item->name;
				$arr["price"] = $item->price;
				$arr["quantity"] = $item->qty;

				// insert transaction list to database
				$this->M_TransactionV1->addData("transaction_list", $arr);
			}
		}

		$result = array("status_code" => 300, "status_text" => "sukses");

		// return JSON
		echo json_encode($result);
	}

	// return JSON
	function getTransactionFromIdentify() {
		$transactionIdentify = $this->input->get('transactionIdentify');
		$accountKey = $this->getHeaderFromUrl('currentUser');
		$data = $this->M_TransactionV1->getTransactionById($transactionIdentify, $accountKey)->row();
		
		// get transaction
		$response['transactionDate'] = $data->transaction_date;
		$response['transactionId'] = $data->transaction_id;
		$response['transactionIdentify'] = $data->transaction_identify;
		$response['addedDate'] = $data->added_date;
		$response['categoryId'] = $data->category_id;
		$response['amount'] = $data->amount;
		$response['type'] = $data->type;
		$response['description'] = $data->description;
		$response['location']['name'] = $data->location;
		$response['location']['coordinate'] = $data->coordinate;
		$response['picture'] = $data->picture;
		$response['isDeleted'] = $this->getBoolean($data->is_deleted);
		$response['child'] = array();

		// get list item transaction
		$childDatas = $this->M_TransactionV1->getListItemTransactionById($data->transaction_id, $accountKey)->result();
		foreach ($childDatas as $childData) {
			$child['id'] = $childData->transaction_list_id;
			$child['item'] = $childData->name;
			$child['price'] = $childData->price;
			$child['qty'] = $childData->quantity;
			array_push($response['child'], $child);
		}
		
		$result = array('data' => $response);
		echo json_encode($result);
	}
}
?>