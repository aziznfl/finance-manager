

    <script>
        var table = $('#datatable-last-transaction').DataTable({
            'orderable': false,
            'searching': false,
            'paging': false,
            'bInfo': false,
            'ajax': {
                'url': apiUrl() + 'transaction/lastTransaction', 
                'type': 'GET',
                'data': {},
                'headers': {
                    'currentUser': '<?php echo $this->session->userdata('user')->account_key; ?>'
                }
            },
            'destroy': true,
            'columns': [
                {'searchable': false, 'orderable': false, 'defaultContent': '', 'className': 'text-center'},
                {'data': 'transaction_date', 'className': 'text-center'},
                {
                    'render': function (param, type, data, meta) {
                        var categoryView = '<b>'+data.category.category_name+'</b>';
                        var descView = '';
                        var tagView = '';
                        
                        if (data.description != null && data.description != '') { descView = '<br/><span class=\"text-secondary\">'+data.description+'</span>'; }
                        if (data.location != null && data.location != '') { 
                            categoryView += '&nbsp;&nbsp;&nbsp;<span class=\"text-primary\"><span class=\"fa fa-map-marker\"></span>&nbsp;'+data.location+'</span>';
                        }
                        if (data.tag != null) { tagView = '<br/><span class=\"label bg-blue\">'+data.tag+'</span>'; }

                        return categoryView+descView+tagView;
                    }
                },
                {'data': 'amount_text', 'className': 'text-right'},
                {'orderable': false, 
                    'className': 'text-center',
                    'render': function (param, type, data, meta) {
                        return "<a href='" + baseUrl() + "transaction/manage?type=tr&transactionId=" + data.transaction_identify + "'><i class='fa fa-edit'></i></a>";
                    }
                }
            ],
            'order': [1, 'desc']
        });

        function renderCharts(month, value) {
			Highcharts.chart('chart-transactions', {
			    chart: {
			        type: 'column'
			    },
			    title: {
			        text: 'Transaction (Outcome)'
			    },
			    xAxis: {
			        categories: month
			    },
			    yAxis: {
			        min: 0,
			        title: {
			            text: 'Amount'
			        },
			        stackLabels: {
			            enabled: true,
			            style: {
			                fontWeight: 'bold',
			                color: ( // theme
			                    Highcharts.defaultOptions.title.style &&
			                    Highcharts.defaultOptions.title.style.color
			                ) || 'gray'
			            }
			        }
			    },
			    legend: {
			        align: 'right',
			        x: 0,
			        verticalAlign: 'top',
			        y: 0,
			        floating: true,
			        backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || 'white',
			        borderColor: '#CCC',
			        borderWidth: 1,
			        shadow: false
			    },
			    tooltip: {
			        headerFormat: '<b>{point.x}</b><br/>',
			        pointFormat: '{series.name}: {point.y}' //<br/>Total: {point.stackTotal}
			    },
			    plotOptions: {
			        column: {
			            stacking: 'normal'
			        },
					series: {
						cursor: 'pointer',
						point: {
							events: {
								click: function () {
									console.log(this.options.key);
								}
							}
						}
					}
				},
			    series: value
			});
        }

        // -------- FETCH -------- //

        function fetchSummaryYoYTransaction() {
            $.ajax({
                type: "GET",
                url: apiUrl() + "summary/yoy",
                contentType: "application/json; charset=utf-8",
                dataType: "JSON",
                headers: {
                    'currentUser': '<?php echo $this->session->userdata('user')->account_key; ?>'
                },
                success: function(response) {
                    var month = getMonthYoYSummary(response.data);
                    var value = getValueYoYSummary(response.data);
                    renderCharts(month, value);
                }
            });
        }

        function fetchCardInfo() {
            $.ajax({
                type: "GET",
                url: apiUrl() + "summary/cardInfo",
                contentType: "application/json; charset=utf-8",
                dataType: "JSON",
                headers: {
                    'currentUser': '<?php echo $this->session->userdata('user')->account_key; ?>'
                },
                success: function(response) {
                    $("#card-info").html();

                    $.each(response, function(i, item) {
                        $("#card-info").append(
                            '<div class="col-lg-3 col-sm-6 col-xs-12" id="card-' + i + '">' +
                                '<div class="small-box ' + item.backgroundColor + '">' +
                                    '<div class="inner">' +
                                        '<h3>' + item.text + '</h3>' +
                                        '<p>' + item.title + '</p>' +
                                    '</div>' +
                                    '<a href="' + baseUrl() + item.link + '" class="small-box-footer">' +
                                        'More Info <i class="fa fa-arrow-circle-right"></i>' +
                                    '</a>' +
                                '</div>' +
                            '</div>'
                        );
                    })
                }
            });
        }

        // -------- GET -------- //

        function getMonthYoYSummary(data) {
            var month = [];
            $.each(data, function (i, item) {
                month.push(item.month + "-" + item.year);
            });
            return month;
        }

        function getValueYoYSummary(data) {
            var valueTransaction = [];
            var valueInvestment = [];

            $.each(data, function(i, item) {
                valueTransaction.push(item.totalTransaction);
                valueInvestment.push(item.totalInvestment);
            });

            var value = [
                {"name": "Transaction", "data": valueTransaction, "stack": "Transaction"}, 
                {"name": "Investment", "data": valueInvestment, "stack": "Investment"}
            ];
            return value;
        }
    </script>
