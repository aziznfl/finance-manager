<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Recurring <small>Transaction</small>
    </h1>
</section>

<!-- Main content -->
<section class="content">
	<div class="row">
		<div class="col-md-8 col-sm-12">
			<div class="box box-primary">
				<div class="box-body">
					<table class="table datatable">
						<thead>
							<tr>
								<th class="text-center" width="10%">No</th>
								<th class="text-center">Interval</th>
								<th class="text-center">Desc</th>
								<th class="text-center"></th>
							</tr>
						</thead>
						<tbody>
						<?php
							$i = 0;
							foreach($transaction as $result) {
								$i++;

								$desc_text = "";
								if ($result["description"] != "" || $result["description"] != null) $desc_text = " - ".ucwords($result["description"]);

								$repetition_text = "Every ";
								$arr = explode(" ", $result["repetition"]);
								if ($arr[4] == "*") {
									// not every weekend
									if ($arr[3] != "*") {
										// every year
										$repetition_text .= " Year<br/>(on ".dateOnYear($arr).")";
									} else if ($arr[3] == "*") {
										// not every year
										if ($arr[2] != "*") {
											$repetition_text .= " Month<br/>(on ".ordinal($arr[2])." - ".numberDate($arr[1]).":".numberDate($arr[0]).")";
										}
									}
								}

								echo "<tr>";
								echo "<td class='text-center'>".$i."</td>";
								echo "<td class='text-center'>".$repetition_text."</td>";
								echo "<td>".
										"<div style='font-size: 16px; font-weight: 400;''>Rp. ".number_format($result['amount'])."</div>".
										"<div><span style='font-weight: 600;'>".ucwords($result['category_name'])."</span>".$desc_text."</div>".
									"</td>";
								echo "<td class='text-center'>".
										"<a href=".base_url("transaction/manage?amount=".$result['amount']."&category=".$result['category_id']."&desc=".$result["description"])."><span class='fa fa-plus'></span></a>".
									"</td>";
								echo "</tr>";
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>

<?php
	function numberDate($number) {
		if (intval($number) < 9) return "0".$number;
		else return $number;
	}
	function dateOnYear($arr) {
		echo intval($arr[3]).", ".intval($arr[4]).", 0, ".intval($arr[0]).", ".intval($arr[1]);
		$makeTime = mktime(intval($arr[1]), intval($arr[0]), 0, intval($arr[3]), intval($arr[2]));
		return date("F jS - H:i", $makeTime);
	}
	function ordinal($number) {
		$ends = array('th','st','nd','rd','th','th','th','th','th','th');
		if ((($number % 100) >= 11) && (($number%100) <= 13)) return $number. 'th';
		else return $number. $ends[$number % 10];
	}
?>