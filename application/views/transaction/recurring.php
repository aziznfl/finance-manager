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
								$date = "";

								if ($arr[4] == "*") {
									// not every weekend
									$date .= date("Y");
									if ($arr[3] != "*") {
										// every year
										$repetition_text .= " Year<br/>(on ".dateText($arr, "F jS - H:i").")";
										$date .= "-".dateText($arr, "m-d%20H:i:00");
									} else if ($arr[3] == "*") {
										// not every year
										$date .= "-".date("m");
										if ($arr[2] != "*") {
											$repetition_text .= " Month<br/>(on ".dateText($arr, "jS - H:i").")";
											$date .= "-".dateText($arr, "d%20H:i:00");
										} else {
											$date .= "-".dateText($arr, "d%20H:i:00");
										}
									}
								}

								$location = "";
								if ($result["location"] != "") {
									$location = "&nbsp;&nbsp;&nbsp;".
										"<span class='text-primary'>".
											"<span class='fa fa-map-marker'></span>&nbsp;".$result['location'].
										"</span>";
								}

								echo "<tr>";
								echo "<td class='text-center'>".$i."</td>";
								echo "<td class='text-center'>".$repetition_text."</td>";
								echo "<td>".
										"<div style='font-size: 16px; font-weight: 400;''>Rp. ".number_format($result['amount'])."</div>".
										"<div>".
											"<span style='font-weight: 600;'>".ucwords($result['category_name'])."</span>".$desc_text.$location.
										"</div>".
										"<span class='label bg-blue'>".$result['tag']."</span>".
									"</td>";
								echo "<td class='text-center'>".
										"<a href=".base_url("transaction/manage?date=".$date."&amount=".$result['amount']."&category=".$result['category_id']."&desc=".$result["description"]."&location=".$result["location"]."&tag=".$result["tag"])."><span class='fa fa-plus'></span></a>".
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
	function dateText($arr, $return) {
		$makeTime = mktime(intval($arr[1]), intval($arr[0]), 0, intval($arr[3]), intval($arr[2]));
		return date($return, $makeTime);
	}
?>