<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>Transaction History</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
            <li id="month" class="active" data-tab="month"><a href="javascript:void(0)">Filter by Month</a></li>
            <li id="Category" data-tab="category"><a href="javascript:void(0)">Filter by Category</a></li>
        </ul>
        <div class="tab-content">
            <div id="month-tab">
		        <div class="box-body">
					<div class="form-horizontal">
						<select id="date" class="form-control" onchange="changeDate()">
						<?php
							$nowDate = $first_transaction[0]->transaction_date;
							$nowDates = explode("-", $nowDate);
							$yearFirst = $nowDates[0];
							$monthFirst = $nowDates[1];
							$yearNow = date('Y');
							$monthNow = date('m');

							for ($i = (int)$yearFirst; $i <= (int)$yearNow; $i++) {
								$month = $i == $yearFirst ? $monthFirst : '01';
								$year = $i < $yearNow ? '12' : $monthNow;
								for ($j = (int)$month; $j <= (int)$year; $j++) {
									$time = strtotime($j.'/01/'.$i);
									$date = date("M Y", $time);
									echo '<option value="'.$i.'-'.$j.'">'.$date.'</option>';
								}
							}

							$timeTransaction = strtotime($monthFirst."/01/".$yearFirst);
						?>
						</select>
					</div>
					<div class="borderless"></div>
					<div class="row">
						<div class="col-md-4">
							<div class="floating-rounded-default bg-primary">Total: Rp. <span id="top-floating-amount-table">-</span></div>
							<div class="table-responsive" style="margin-bottom: 32px;">
								<table id="datatable-top-transaction" class="table table-bordered table-striped table-hover" style="cursor: pointer;">
									<thead>
										<th class="text-center">No</th>
										<th class="text-center">Category</th>
										<th class="text-center">Rp. (%)</th>
									</thead>
								</table>
							</div>
						</div>
						<div class="col-md-8">
							<h3 style="margin: 0 0 20px;">
								List Transaction
								<small class="pull-right">
									<a class="btn btn-primary btn-sm" id="buttonAddTransaction">
										<i class="fa fa-plus"></i> Add Transaction
									</a>
								</small>
							</h3>
							<div class="table-responsive">
								<table id="datatable-month-transaction" class="table table-bordered table-striped table-hover">
									<thead>
										<th class="text-center" width="10">No</th>
										<th class="text-center" width="200">Date</th>
										<th class="text-center">Rp.</th>
										<th class="text-center">Category</th>
										<th class="text-center"></th>
									</thead>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="category-tab" class="hide">
				<div class="row">
					<div class="col-md-4">
						<div class="table-responsive" style="margin-bottom: 32px;">
							<table id="datatable-top-transaction" class="table table-bordered table-striped table-hover" style="cursor: pointer;">
								<thead>
									<th class="text-center">No</th>
									<th class="text-center">Category</th>
									<th class="text-center">Rp. (%)</th>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<!-- /.content -->
