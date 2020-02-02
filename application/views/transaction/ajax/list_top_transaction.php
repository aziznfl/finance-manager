		<table id="transaction_table" class="col-md-12 table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th class='text-center'>No</th>
					<th class='text-center'>Category</th>
					<th class='text-center'>Amount</th>
					<th class='text-center'>Persentage</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$total = 0;
				$trans = [];
				foreach ($transactions->result() as $transaction) {
					
					$total += $transaction->total;
				}
				$i = 0;
				foreach ($transactions->result() as $transaction) {
					$location = $transaction->location != NULL ? " (@".$transaction->location.")" : "";

					$i++;
					echo "<tr>";
					echo "<td class='text-center'>".$i."</td>";
					echo "<td class='text-center'>".ucwords($transaction->category_name)."</td>";
					echo "<td class='text-center'>".number_format($transaction->total)."</td>";
					echo "<td class='text-center'>".number_format($transaction->total/$total*100, 2)." %</td>";
					echo "</tr>";
				}
				?>
			</tbody>
		</table>