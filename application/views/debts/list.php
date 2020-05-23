    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Debts & Receivables
        </h1>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Small box -->
        <div class="row">
            <div class="col-md-4 row">
                <div class="col-md-12">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h3>Rp. -1.000.0000</h3>
                            <p>Debts</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h3>Rp. 250.000</h3>
                            <p>Receivables</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">Result</h3>
                        </div>
                        <div class="box-body no-padding">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <th class="text-center">No</th>
                                    <th class="text-center">Who</th>
                                    <th class="text-center"></th>
                                    <th class="text-right">Amount (Rp.)</th>
                                </thead>
                                <tbody>
                                <?php 
                                $i = 0;
                                $balance = 0;
                                foreach($debts_balance as $list) {
                                    $i++;
                                    $balance += $list->balance;
                                    $arrow = "left";
                                    $color = "red";
                                    if ($list->balance > 0) {
                                        $arrow = "right";
                                        $color = "green";
                                    }
                                    echo '<tr>';
                                        echo '<td class="text-center">'.$i.'</td>';
                                        echo '<td class="text-center text-capitalize">'.$list->to_who.'</td>';
                                        echo '<td class="text-center text-'.$color.'"><span class="fa fa-long-arrow-'.$arrow.'"></span></td>';
                                        echo '<td class="text-right text-'.$color.'"><strong>Rp. '.number_format($list->balance).'</strong></td>';
                                    echo '</tr>';
                                } ?>
                                </tbody>
                                <tfoot>
                                    <th colspan="3" class="text-center">Balance</th>
                                    <th class="text-right text-red"><strong>Rp. <?php echo number_format($balance); ?></strong></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">Add New Transaction</h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <form>
                            <div class="form-group row">
                                <div class="form-group col-md-6">
                                    <label>Date</label>
                                    <input class="form-control" type="text" name="" placeholder="Date" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Who</label>
                                    <input class="form-control" type="text" name="" placeholder="Who" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Type</label>
                                    <select class="form-control">
                                        <option>Debts</option>
                                        <option>Receivables</option>
                                        <option>Transfer to</option>
                                        <option>Transfer from</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Amounts</label>
                                    <input class="form-control" type="number" name="" placeholder="Amounts" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Description</label>
                                    <input class="form-control" type="text" name="description" placeholder="Description" />
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Deadline</label>
                                    <input class="form-control" type="number" name="" placeholder="Amounts" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">List of Debts & Receivables</h3>
                    </div>
                    <div class="box-body no-padding">
                        <table class="table table-striped table-hover">
                            <thead>
                                <th class="text-center">No</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Who</th>
                                <th class="text-center"></th>
                                <th class="text-right">Amount (Rp.)</th>
                                <th class="text-center">Deadline</th>
                            </thead>
                            <tbody>
                            <?php 
                            $i = 0;
                            foreach($debts_list as $list) {
                                $i++;
                                $arrow = "left";
                                $color = "red";
                                $description = $list->description;
                                if ($list->type == "receivables" || $list->type == "transfer_to") {
                                    $arrow = "right";
                                    $color = "green";
                                }
                                if($description == "" || $description == NULL) $description = '<i class="text-secondary">'.$list->type.'</i>';
                                echo "<tr>";
                                    echo '<td class="text-center text-middle">'.$i.'</td>';
                                    echo '<td class="text-center text-middle">'.$list->transaction_date.'</td>';
                                    echo '<td class="text-center text-middle text-capitalize">'.$list->to_who.'</td>';
                                    echo '<td class="text-center"><span class="fa fa-long-arrow-'.$arrow.' text-'.$color.'"></span><br/>'.$description.'</td>';
                                    echo '<td class="text-right text-middle text-'.$color.'">Rp. '.number_format($list->amount).'</td>';
                                    echo '<td class="text-center text-middle">'.$list->deadline.'</td>';
                                echo "</tr>";
                            } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->