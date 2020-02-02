		<table id="transaction_table" class="col-md-12 table table-bordered table-striped table-hover">
			<thead>
				<tr>
					<th class='text-center'>No</th>
					<th class='text-center'>Date</th>
					<th class='text-center'>Amount</th>
					<th class='text-center'>Category</th>
					<th class='text-center'>Description</th>
					<th class='text-center'>Action</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$i = 0;
				foreach ($transactions->result() as $transaction) {
					$location = $transaction->location != NULL ? " (@".$transaction->location.")" : "";

					$i++;
					echo "<tr>";
					echo "<td class='text-center'>".$i."</td>";
					echo "<td class='text-center'>".$transaction->transaction_date."</td>";
					echo "<td class='text-right'>".number_format($transaction->amount)."</td>";
					echo "<td class='text-center'>".ucwords($transaction->category_name)."</td>";
					echo "<td class='text-center'>".$transaction->description, $location."</td>";
					echo "<td class='text-center'>
						<a href='#' title='Edit'><i class='glyphicon glyphicon-edit'></i></a>&nbsp;&nbsp;
						<a href='#' title='Copy'><i class='glyphicon glyphicon-copy'></i></a>
					</td>";
					echo "</tr>";
				}
				?>
			</tbody>
		</table>