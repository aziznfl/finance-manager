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
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td class="text-center">Aldi</td>
                                        <td class="text-center text-red"><span class="fa fa-long-arrow-left"></span></td>
                                        <td class="text-right text-red"><strong>Rp. -1.000.000</strong></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">2</td>
                                        <td class="text-center">Rino</td>
                                        <td class="text-center text-green"><span class="fa fa-long-arrow-right"></span></td>
                                        <td class="text-right text-green"><strong>Rp. 250.000</strong></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <th colspan="3" class="text-center">Balance</th>
                                    <th class="text-right text-red"><strong>Rp. -750.000</strong></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="box box-success collapsed-box">
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
                            <div class="form-group">
                                <div class="form-group">
                                    <label>Date</label>
                                    <input class="form-control" type="text" name="" placeholder="Date" />
                                </div>
                                <div class="form-group">
                                    <label>Who</label>
                                    <input class="form-control" type="text" name="" placeholder="Who" />
                                </div>
                                <div class="form-group">
                                    <label>Type</label>
                                    <select class="form-control">
                                        <option>Debts</option>
                                        <option>Receivables</option>
                                        <option>Transfer</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Amounts</label>
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
                                <tr>
                                    <td class="text-center text-middle">1</td>
                                    <td class="text-center text-middle">1 March 2020</td>
                                    <td class="text-center text-middle">Aldi</td>
                                    <td class="text-center"><span class="fa fa-long-arrow-left text-red"></span><br/>Beli martabak</td>
                                    <td class="text-right text-middle text-red">Rp. -2.000.000</td>
                                    <td class="text-center text-middle">1 April 2020</td>
                                </tr>
                                <tr>
                                    <td class="text-center text-middle">2</td>
                                    <td class="text-center text-middle">3 April 2020</td>
                                    <td class="text-center text-middle text-capitalize">rino</td>
                                    <td class="text-center"><span class="fa fa-long-arrow-right text-green"></span><br/><i class="text-secondary">transfer</i></td>
                                    <td class="text-right text-middle text-green">Rp. 750.000</td>
                                    <td class="text-center text-middle">1 Mei 2020</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /.content -->