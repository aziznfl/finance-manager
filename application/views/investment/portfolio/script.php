<script>
    var table = $('#transaction_table').DataTable({
        'ajax': {
            'url': link,
            'headers': {
                'currentUser': '<?php echo $this->session->userdata('user')->account_key; ?>'
            }
        },
        'columns': [
            {'data': null, 'className': 'text-center', 'orderable': false, 'searching': false},
            {'data': 'date', 'className': 'text-center'},
            {
                'data': 'amount_text',
                'className': 'text-right',
                'createdCell': function(td, cellData, rowData, row, col) {
                    if (rowData.state_text == 'Done') {
                        if (rowData.amount > 0) $(td).addClass('text-success');
                        else if (rowData.amount < 0) $(td).addClass('text-danger');
                        else $(td).addClass('text-primary');
                    }
                }
            },
            {
                'data': 'state_text',
                'className': 'text-center text-bold',
                'createdCell': function(td, cellData, rowData, row, col) {
                    if (cellData == 'Done') {
                        $(td).addClass('text-success');
                    } else {
                        $(td).addClass('text-primary');
                    }
                }
            },
            {
                'className': 'text-center',
                'render': function(param, type, data, meta) {
                    var valueText = '';
                    if (data.value_text != null) valueText = ' ('+data.value_text+')'
                    return data.description + valueText;
                }
            },
            {
                'data': 'instrument',
                'className': 'text-center'
            },
            {'data': 'manager', 'className': 'text-center'},
            {
                'className': 'text-center',
                'render': function(param, type, data, meta) {
                    if (data.state_text != 'Done') {
                        return '<a class="btn btn-xs btn-primary" href="' + baseUrl() + 'investment/manage?id=' + data.id + '").""><span class="fa fa-plus"></span> Add New</a>';
                    } else {
                        return '';
                    }
                }
            }
        ],
        'order': [1, 'desc']
    });

    function childTranscation(data) {
        var html = '<table class="table table-condensed no-margin no-border" style="margin: -8px !important;">';
        for (i = 0; i < data.length; i++) {
            var amount = data[i].amount_text;
            var value = '';
            amount = data[i].type != "outcome" ? "+" + amount : "-" + amount;
            if (data[i].unit != null) value = data[i].value + ' ' + data[i].unit;

            html += '<tr><td width="1%" class="text-center">-</td>';
            html += '<td class="text-center">' + data[i].transaction_date + '</td>';
            html += '<td class="text-right">' +
                amount + '&nbsp;&nbsp;&nbsp;&nbsp;' +
                '<a href="' + baseUrl() + 'investment/manage?id=' + data[i].transaction_investment_id + '&updateValue=true" title="Edit Transaction">' +
                    '<span class="fa fa-edit"></span>' +
                '</a>&nbsp;&nbsp;' +
                '<a href="#"><span class="fa fa-trash text-danger" title="Delete Transaction"></span></a>' +
                '</td>';
            html += '<td class="text-right" width="10%">'+value+'</td>';
            html += '<td width="50%"></td></tr>';
        }
        html += '</table>';

        return html;
    }
</script>