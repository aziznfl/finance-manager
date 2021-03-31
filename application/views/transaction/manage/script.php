
    <script>
        setTotal(0);
        setForm();

        function unbindScript() {
            $(document).unbind('change').ready(function() {
                setCounter();

                $(".addItems").focusout(function() {
                    var lastChild = isLastChild(this);
                    if (this.value != "" && lastChild) {
                        insertNewLineItemList();
                    }
                });

                $('input[type="number"]').focus(
                    function() {
                        if ($(this).val() == "0") {
                            $(this).val("");
                        }
                    }
                ).focusout(
                    function() {
                        if ($(this).val() == "") {
                            $(this).val("0");
                        }

                        // counter total items
                        if ($(this).hasClass('counter')) {
                            var id = findId(this);
                            setSubTotal(id);
                        }
                    }
                );
            });
        }

        function getTableId() {
            return "#table-list-items ";
        }

        function isLastChild(html) {
            var lastHtml = $(getTableId() + "tbody tr").last().find('input').first()[0];
            return (html == lastHtml);
        }

        function isLastId(id) {
            var lastId = $(getTableId() + "tbody tr").last().attr('id');
            return (lastId == id);
        }

        //------- Calculate --------//

        function getCountItem() {
            return parseInt($(getTableId() + "tbody tr").length);
        }

        function setCounter() {
            var countItem = (getCountItem() - 1);
            var counterText = "Total Item: " + countItem;
            $("#table-list-items tfoot").find('[data-tag=\"counter-text\"]').text(counterText);
        }

        function setForm() {
            // $('.form').attr('action', baseUrl() + "transaction/manageTransaction");
            $(".form").attr('onsubmit', 'return post();');
        }

        function getTotal() {
            return getNumberFromCurrency($("#table-list-items tfoot").find('[data-tag=\"total-text\"]').text());
        }

        function setTotal(total) {
            $("#table-list-items tfoot").find('[data-tag=\"total-text\"]').text(currencyFormat(total));
        }

        function getSubtotal(id) {
            return getNumberFromCurrency($("#subtotal" + id).text());
        }

        function setSubTotal(id) {
            var oldSubtotal = getSubtotal(id);
            var oldTotal = getTotal();

            var price = parseInt(getValueFromName("price[" + id + "]"));
            var qty = parseInt(getValueFromName("qty[" + id + "]"));
            var subtotal = price * qty;
            var newTotal = oldTotal + (subtotal - oldSubtotal);

            $("#subtotal" + id).text(currencyFormat(subtotal));
            setTotal(newTotal);
        }

        function getValueFromName(name) {
            return $("input[name='" + name + "']").val();
        }
        
        // ------ Rendering ------ //

        function insertNewLineItemList() {
            var table = $('#table-list-items tbody');
            table.append(renderNewLineItemList());
            unbindScript();
        }

        function renderNewLineItemList() {
            var tr = $('#table-list-items tbody tr');
            var count = tr.length;
            return "<tr id='" + count + "'>" +
                "<td><input name='items[" + count + "]' class='form-control addItems' placeholder='Item'></td>" +
                "<td><input name='price[" + count + "]' class='form-control text-right counter' type='number' value='0'></td>" +
                "<td><input name='qty[" + count + "]' class='form-control text-right counter' type='number' value='0'></td>" +
                "<td id='subtotal" + count + "' class='text-right text-center-vertical'>0</td>" +
                "<td class='text-center text-center-vertical'>" +
                    "<a href='javascript:void(0)' onclick='removeItem(" + count + ")'>" +
                        "<span class='fa fa-remove text-red'></span>" +
                    "</a>" +
                "</td>" +
            "</td>";
        }

        function removeItem(id) {
            if (!isLastId(id)) {
                if (confirm('Are you sure want to delete this item?')) {
                    // get old data
                    var subtotal = getSubtotal(id);
                    var total = getTotal();
                    
                    // remove row html
                    $("#" + id).remove();

                    // set new total
                    var newTotal = total - subtotal;
                    setTotal(newTotal);
                    setCounter();
                }
            }
        }

        function setTitle(transactionId) {
            if (transactionId == null) {
                $('.content-header').html('<h1>Add <small>Transaction</small></h1>');
            } else {
                $('.content-header').html('<h1>Edit <small>Transaction</small></h1>');
            }
        }

        //------- Submit -------//

        function post() {
            var transaction = getTransaction();
            $.ajax({
                type: "POST",
                url: baseUrl() + 'api/insertTransactionNewFlow',
                data: {'hello': 'world'},
                headers: {
                    'currentUser': '<?php echo $this->session->userdata('user')->account_key; ?>'
                },
                contentType: "application/json; charset=utf-8",
                dataType: "JSON",
                success: function(response) {
                    console.log(response);
                }
            });
            return false;
        }

        function getTransaction() {
            var date = getValueFromName("date_tr");
            var category = $(".select2").find(':selected').val();
            var amount = parseInt(getValueFromName("amount"));
            var location = getValueFromName("location");
            var description = getValueFromName("description");
            var tag = getValueFromName("tag");

            var transaction = {
                'date': date, 
                'categoryId': category, 
                'amount': amount,
                'location': location, 
                'description': description, 
                'tag': tag,
                'items': getItemsTransaction()
            }
            return transaction;
        }

        function getItemsTransaction() {
            var items = [];
            $(getTableId() + "tbody tr").each(function(index) {
                var id = $(this).attr('id');
                var name = getValueFromName("items[" + id + "]");
                var price = parseInt(getValueFromName("price[" + id + "]"));
                var qty = parseInt(getValueFromName("qty[" + id + "]"));
                if (name != "") {
                    var item = {
                        'name': name,
                        'price': price,
                        'qty': qty
                    };
                    items.push(item);
                }
            });
            return items;
        }
    </script>
