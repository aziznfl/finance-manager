    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Investment</h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">List Transaction</h3>
            </div>
            <div class="box-body no-padding">
                <div class="table-responsive">
                	<table id="transaction_table" class="table table-bordered table-striped table-hover">
                		<thead>
                			<tr>
            	    			<th class='text-center'>No</th>
            	    			<th class='text-center'>Date</th>
            	    			<th class='text-center'>Amount</th>
            	    			<th class='text-center'>Description</th>
            	    		</tr>
            	    	</thead>
            	    	<tbody>
                		<?php
                		$i = 0;
                		$total = 0;
                		foreach ($list->result() as $transaction) {
                            $i++;
                            $total += $transaction->amount;
                            echo "<tr>";
                            echo "<td class='text-center'>".$i."</td>";
                            echo "<td class='text-center'>".$transaction->date."</td>";
                            echo "<td class='text-right'>".number_format($transaction->amount)."</td>";
                            echo "<td class='text-center'>".$transaction->description."</td>";
                            echo "</tr>";
                		}
                		?>
                		</tbody>
                		<tfoot>
                			<tr>
                				<th class="text-center" colspan="2">Total</th>
                				<th class="text-right"><?php echo number_format($total); ?></th>
                				<th></th>
                			</tr>
                		</tfoot>
                	</table>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->