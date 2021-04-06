    
    <script>
        var tableMonthTrans;
        var tableTopTrans;
        var tableCategory;
        var params = 'year=<?php echo $year; ?>&month=<?php echo $month; ?>';
        var categoryId = 0;
        var isFirstClick = true;
        var isFirstCategory = true;

        var list = <?php echo json_encode($total_month_transaction->result_array()); ?>;

        function changeDate(year, month) {
            categoryId = 0;
            params = 'year=' + year + '&month=' + month;
            $('#card-' + year + '-' + month).addClass('active').siblings().removeClass('active');
            $('#buttonAddTransaction').attr('href', baseUrl() + 'transaction/manage?' + params);
            window.history.pushState('object or string', 'Title', baseUrl() + 'transaction/history?' + params);

            renderMonthTransaction();
            renderTopTransaction();

            // set position of month balance
            var index = $.map(list, function(item, i) {
                if (item.year == year && item.month == month) { return i; }
            })[0];
            var cardViewWidth = (225 + 14)
            var cardWidth = $('.card-box').width();
            var center = ((cardWidth - cardViewWidth) / 2) - 5;
            var position = (index * cardViewWidth) - center;
            if (isFirstClick) {
                $('.card-box').scrollLeft(position);
                isFirstClick = false;
            } else {					
                $('.card-box').animate({
                  scrollLeft: position
                }, 'slow');
            }
        }

        function selectCategory(category) {
            categoryId = category;
            tableMonthTrans.draw();
        }

        function renderTopTransaction() {
            var link = baseUrl() + 'api/getTopTransaction?' + params;
            tableTopTrans = $('#datatable-top-transaction').DataTable({
                'ordering': false,
                'searching': false,
                'paging': false,
                'destroy': true,
                'ajax': {
                    'url': link,
                    'dataSrc': function(json) {
                        $('#top-floating-amount-table').html(json.total_text);
                        return json.data;
                    }
                },
                'columns': [
                    {
                        'className': 'text-center', 
                        'render': function(param, type, data, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        'data': 'category_name'
                    },
                    {
                        'className': 'text-right',
                        'render': function(param, type, data, meta) {
                            return data.total_text+' (<b>'+data.percentage+'</b>)';
                        }
                    }
                ],
                'order': [2, 'desc']
            });
        }

        function renderMonthTransaction() {
            var link = baseUrl() + 'api/getMonthTransaction?' + params + '&category_id=' + categoryId;
            tableMonthTrans = $('#datatable-month-transaction').DataTable({
                'ajax': {
                    'url': link,
                    'type': 'POST',
                    'headers': {
                        'currentUser': '<?php echo $this->session->userdata('user')->account_key; ?>'
                    }
                },
                'destroy': true,
                'columns': [
                    {'searchable': false, 'orderable': false, 'defaultContent': '', 'className': 'text-center'},
                    {
                        'render': function (param, type, data, meta) {
                            return getTextDescription(data);
                        }
                    },
                    {
                        'className': 'text-right',
                        'render': function (param, type, data, meta) {
                            return getTextPrice(data);
                        }
                    },
                    {'orderable': false, 
                        'className': 'text-center',
                        'render': function (param, type, data, meta) {
                            return '<a href=' + baseUrl() + 'transaction/manage?type=tr&transactionId=' + data.transactionIdentify + '><i class=\"fa fa-edit\"></i></a>';
                        }
                    }
                ],
                'order': [1, 'desc']
            });
        }

        function renderCategory() {
            var link = baseUrl() + 'api/getCategories';
            tableCategory = $('#datatable-category').DataTable({
                'ajax': link,
                'ordering': false,
                'searching': false,
                'paging': false,
                'destroy': true,
                'columns': [
                    {
                        'className': 'text-center', 
                        'render': function(param, type, data, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {'data': 'category.name', 'className': 'text-capitalize'}
                ]
            });

            $('#datatable-category tbody').on('click', 'tr', function() {
                var row = tableCategory.row(this);

                $(this).attr('style', 'background-color: #dff0d8').siblings().attr('style', '');
                renderSubCategory(row.data().category.id);
            });
        }

        function renderSubCategory(categoryId) {
            var link = baseUrl() + 'api/getCategoryTransaction?categoryId=' + categoryId;
            $.ajax({
                method: 'GET',
                url: link,
                data: {categoryId: categoryId},
                dataType: 'JSON',
                success: function(response){
                    var html = '';
                    let data = response.data;
                    for(var i=0; i<data.length; i++) {
                        let result = data[i];

                        html += '<tr>' +
                            '<td class=\"text-center\">'+ (i+1) +'</td>' +
                            '<td class=\"text-center\">'+ result.transaction_date +'</td>' +
                            '<td>'+ textDescription(result) +'</td>' +
                            '<td class=\"text-right\">'+ result.amount.text +'</td>' +
                        '</tr>';
                    }
                    $('#datatable-sub-category tbody').html(html);
                }
            });
        }

        function getTextDescription(data) {
            var categoryView = '<br/><b>'+data.category.name+'</b>';
            var descView = '';
            var countView = '';

            var date = Date.parse(data.transactionDate.replace(' ', 'T')).toString('dd MMM yyyy - HH:mm:ss');
            var dateView = '<span class=\"hidden\">' + data.transactionDate + '</span><span class=\"text-secondary\">' + date + '</span>';
            
            if (data.place.name != null && data.place.name != '') { 
                categoryView += '&nbsp;&nbsp;&nbsp;<span class=\"text-primary\"><span class=\"fa fa-map-marker\"></span>&nbsp;'+data.place.name+'</span>';
            }
            if (data.isDeleted != 0) { categoryView += '&nbsp;&nbsp;<span class=\"label bg-red\">Deleted</span>'; }
            if (data.description != null && data.description != '') { descView += '<br/>' + data.description; }
            if (data.item.count > 0) { countView += '<br/><i class=\"text-secondary\">' + data.item.count + ' item(s)</i>'; }

            return dateView + categoryView + descView + countView;
        }

        function getTextPrice(data) {
            var text = data.total.text;

            if (data.tag != null) { text += '<br/><span class=\"label bg-blue\">'+data.tag+'</span>'; }
            
            return text;
        }

        <!------ MODAL ----->
        function showDetailItemsTransaction(row) {
            $('#modal-transaction-detail').modal('show');

            $('#modal-detail-transaction-date').text(row.transactionDate);
            $('#modal-detail-transaction-category').text(row.category.name);
            $('#modal-detail-transaction-description').text(row.description);

            // generate list item transaction
            $('#modal-detail-transaction-item-table').html("");
            if (row.item.count > 0) {
                $('#modal-detail-transaction-item-table').removeClass('hide');

                var headerList = ["No", "Item", "Price (Rp.)", "Qty", "Total (Rp.)"];
                var styleList = ["text-center", "", "text-right", "text-right", "text-right"];
                $('#modal-detail-transaction-item-table').append(createHeaderTable(headerList, styleList));
                $('#modal-detail-transaction-item-table').append(createBodyTable(row));
            } else {
                $('#modal-detail-transaction-item-table').addClass('hide');
            }
        }

        function createHeaderTable(headerList, style) {
            var header = "<thead>";
            for (var i = 0; i < headerList.length; i++) {
                header += "<th class=" + style[i] + ">" + headerList[i] + "</th>";
            }
            return header += "</thead>";
        }

        function createBodyTable(row) {
            var body = "<tbody>";
            var total = 0;
            for (var i = 0; i < row.item.count; i++) {
                var item = row.item.list[i];
                total += item.total.value;
                body += addTableRow(i+1, item.name, item.price.text, item.qty, item.total.text);
            }
            $('#modal-detail-transaction-item-table').append(createFooterTable(total));
            return body += "</tbody>";
        }

        function addTableRow(no, item, price, qty, total) {
            return "<tr>" +
                "<td class='text-center'>" + no + "</td>" +
                "<td>" + item + "</td>" +
                "<td class='text-right'>" + price + "</td>" +
                "<td class='text-right'>" + qty + "</td>" +
                "<td class='text-right'>" + total + "</td>" +
            "</tr>";
        }

        function createFooterTable(total) {
            return "<tfoot>" +
                "<th colspan='4' class='text-right'>Total</th>" +
                "<th class='text-right'>" + currencyFormat(total) + "</th>" +
            "</tfoot>";
        }
        <!------ END OF MODAL ----->
    </script>
