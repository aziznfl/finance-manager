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
                    <div class="box box-success box-solid">
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
                                foreach($debts['debts_balance'] as $list) {
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
                <div class="box box-primary box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">List of Debts & Receivables</h3>
                        <div class="box-tolls pull-right">
                            <a class="btn btn-xs btn-info" href="" data-toggle="modal" data-target="#modal-default" data-id="">
                                <span class="fa fa-plus"></span>&nbsp;&nbsp;Add Transaction
                            </a>
                        </div>
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
                                <th></th>
                            </thead>
                            <tbody>
                            <?php 
                            $i = 0;
                            foreach($debts['debts_list'] as $list) {
                                $i++;
                                $arrow = "left";
                                $color = "red";
                                $description = $list['description'];
                                if ($list['type'] == "receivables" || $list['type'] == "transfer_to") {
                                    $arrow = "right";
                                    $color = "green";
                                }
                                if($description == "" || $description == NULL) $description = '<i class="text-secondary">'.$list['type'].'</i>';
                                echo "<tr>";
                                    echo '<td class="text-center text-middle">'.$i.'</td>';
                                    echo '<td class="text-center text-middle">'.$list['transaction_date'].'</td>';
                                    echo '<td class="text-center text-middle text-capitalize">'.$list['to_who'].'</td>';
                                    echo '<td class="text-center"><span class="fa fa-long-arrow-'.$arrow.' text-'.$color.'"></span><br/>'.$description.'</td>';
                                    echo '<td class="text-right text-middle text-'.$color.'">Rp. '.number_format($list['amount']).'</td>';
                                    echo '<td class="text-center text-middle">'.$list['deadline'].'</td>';
                                    echo '<td class="text-middle">'.
                                            "<a href='' data-toggle='modal' data-target='#modal-default' data-id='".json_encode($list)."'><span class='fa fa-edit'></span></a>".
                                        '</td>';
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