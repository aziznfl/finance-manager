
    <script>
        setTotal(0);
        setForm();
        var counterItem = 0;

        // Function

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
                            setSubtotal(id);
                        }
                    }
                );
            });
        }

        function getTableId() {
            return "#table-list-items ";
        }

        function setForm() {
            $(".form").attr('onsubmit', 'return post();');
        }

        function getLastIdItem() {
            var id = $(getTableId() + "tbody tr").last().attr('id');
            if (typeof id == 'undefined') {
                return -1;
            }
            return parseInt(id);
        }

        function isLastChild(html) {
            var lastHtml = $(getTableId() + "tbody tr").last().find('input').first()[0];
            return (html == lastHtml);
        }

        function isLastId(id) {
            var lastId = getLastIdItem();
            return (lastId == id);
        }

        function setFormTransaction(data) {
            setValueFromName("transactionId", data.transactionId);
            setValueFromName("date", data.transactionDate);
            setValueFromSelect2(data.categoryId);
            setValueFromName("amount", data.amount);
            setValueFromName("location", data.location.name);
            setValueFromName("description", data.description);
            setValueFromName("tag", data.tag);

            // set child
            data.child.forEach(setFormTransactionList);
        }

        function setFormTransactionList(child) {
            setValueFromName("itemId[" + counterItem + "]", child.itemId)
            setValueFromName("items["+ counterItem +"]", child.item);
            setValueFromName("price["+ counterItem +"]", child.price);
            setValueFromName("qty["+ counterItem +"]", child.qty);
            setSubtotal(counterItem);
            insertNewLineItemList();
            counterItem++;
        }

        function setAsNewTransaction() {
            transactionId = null;
            setBrowserUrl('transaction/manage');
            setTitleAndButton();
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

        function getSubtotal(id) {
            return getNumberFromCurrency($("#subtotal" + id).text());
        }

        function setSubtotal(id) {
            var oldSubtotal = getSubtotal(id);
            var oldTotal = getTotal();

            var price = parseInt(getValueFromName("price[" + id + "]"));
            var qty = parseInt(getValueFromName("qty[" + id + "]"));
            var subtotal = price * qty;
            var newTotal = oldTotal + (subtotal - oldSubtotal);

            $("#subtotal" + id).text(currencyFormat(subtotal));
            setTotal(newTotal);
        }
        
        function getTotal() {
            return getNumberFromCurrency($("#table-list-items tfoot").find('[data-tag=\"total-text\"]').text());
        }

        function setTotal(total) {
            $("#table-list-items tfoot").find('[data-tag=\"total-text\"]').text(currencyFormat(total));
        }

        // ------ Rendering ------ //

        function insertNewLineItemList() {
            var table = $('#table-list-items tbody');
            table.append(renderNewLineItemList());
            unbindScript();
        }

        function renderNewLineItemList() {
            var count = getLastIdItem() + 1;
            return "<tr id='" + count + "'>" +
                "<td><input name='items[" + count + "]' class='form-control addItems' placeholder='Item'></td>" +
                "<td><input name='price[" + count + "]' class='form-control text-right counter' type='number' value='0'></td>" +
                "<td><input name='qty[" + count + "]' class='form-control text-right counter' type='number' value='1'></td>" +
                "<td id='subtotal" + count + "' class='text-right text-center-vertical'>0</td>" +
                "<td class='text-center text-center-vertical'>" +
                    "<a href='javascript:void(0)' onclick='removeItem(" + count + ")'>" +
                        "<span class='fa fa-remove text-red'></span>" +
                    "</a>" +
                "</td>" +
                "<td class='hide'><input type='number' name='itemId[" + count + "]'></td>" +
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

        function setTitleAndButton() {
            if (!transactionId) {
                $('.content-header').html('<h1>Add <small>Transaction</small></h1>');
                $('#btn-submit').html('<span class="fa fa-plus"></span>&nbsp;&nbsp;Save Transaction');
                $('#form-delete').addClass('hide');
            } else {
                $('.content-header').html('<h1>Edit <small>Transaction</small></h1>');
                $('#btn-submit').html('<span class="fa fa-refresh"></span>&nbsp;&nbsp;Update Transaction');
                $('#form-delete').removeClass('hide');
            }
        }

        //------- Fetch -------//

        function fetchCategory() {
            $(".select2").html("<option></option>");
            unbindSelect2();
            
            $.ajax({
                type: "GET",
                url: apiUrl() + 'category/listItem',
                contentType: "application/json; charset=utf-8",
                dataType: "JSON",
                headers: {
                    'currentUser': '<?php echo $this->session->userdata('user')->account_key; ?>'
                },
                success: function(response) {
                    $.each(response.data, function(i, parent) {
                        $(".select2").append("<option value='" + parent.id + "'>" + capitalize(parent.name) + "</option>");
                        $.each(parent.child, function(i, child) {
                            $(".select2").append("<option value='" + child.id + "'>- " + capitalize(child.name) + "</option>");
                        });
                    });
                    unbindSelect2();

                    if (transactionId) {
                        fetchTransactionFromId();
                    }
                }
            });
        }

        function fetchTransactionFromId() {
            if (transactionId) {
                $.ajax({
                    type: "GET",
                    url: apiUrl() + 'transaction/getOneTransaction',
                    contentType: "application/json; charset=utf-8",
                    dataType: "JSON",
                    data: {'transactionIdentify': transactionId},
                    headers: {
                        'currentUser': '<?php echo $this->session->userdata('user')->account_key; ?>'
                    },
                    success: function(response) {
                        setFormTransaction(response.data);
                    },
                    error: function() {
                        alert("Transaction not found!");
                        setAsNewTransaction();
                    }
                });
            }
        }

        //------- Submit -------//

        function post() {
            $(".form button").attr("disabled", "disabled");
            var successChecking = false;
            var transaction = getTransaction();

            // check category
            if (!isNaN(transaction.categoryId)) {
                successChecking = true;
            }

            if (successChecking) {
                $.ajax({
                    type: "POST",
                    url: apiUrl() + 'transaction/manageTransactionNewFlow',
                    contentType: "application/json; charset=utf-8",
                    dataType: "JSON",
                    data: JSON.stringify(transaction),
                    headers: {
                        'currentUser': '<?php echo $this->session->userdata('user')->account_key; ?>'
                    },
                    success: function(response) {
                        window.location.href = baseUrl() + "/transaction/history";
                    },
                    error: function(xhr, status, error) {
                        if (error == "") {
                            alert("Please check internet connection!");
                        }
                        $(".form button").removeAttr("disabled");
                    }
                });
            } else {
                $(".form button").removeAttr("disabled");
            }
            return false;
        }

        function getTransaction() {
            var transactionId = nulledIsEmpty(getValueFromName("transactionId"));
            var date = getValueFromName("date");
            var category = parseInt($(".select2").find(':selected').val());
            var amount = parseInt(getValueFromName("amount"));
            var location = nulledIsEmpty(getValueFromName("location"));
            var description = nulledIsEmpty(getValueFromName("description"));
            var tag = nulledIsEmpty(getValueFromName("tag"));

            var transaction = {
                'transactionId': transactionId,
                'date': date, 
                'categoryId': category, 
                'amount': amount,
                'location': {
                    'name': location,
                    'coordinate': null
                },
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
                var itemId = parseInt(getValueFromName("itemId[" + id + "]"));
                var name = getValueFromName("items[" + id + "]");
                var price = parseInt(getValueFromName("price[" + id + "]"));
                var qty = parseInt(getValueFromName("qty[" + id + "]"));
                if (name != "") {
                    var item = {
                        'itemId': itemId,
                        'name': name,
                        'price': price,
                        'qty': qty
                    };
                    items.push(item);
                }
            });
            return items;
        }

        function removeTransaction() {
            var successChecking = true;

            var request = {
                'transactionId': transactionId
            };
            if (successChecking) {
                $.ajax({
                    type: "GET",
                    url: apiUrl() + "transaction/removeTransaction",
                    contentType: "application/json; charset=utf-8",
                    dataType: "JSON",
                    data: {
                        "transactionId": transactionId
                    },
                    headers: {
                        'currentUser': '<?php echo $this->session->userdata('user')->account_key; ?>'
                    },
                    success: function(response) {
                        alert(response.data + " transaction has been successfully removed!");
                        window.location.href = baseUrl() + "/transaction/history";
                    },
                    error: function() {
                        alert("failed to remove this transaction!");
                    }
                });
            }
        }
    </script>
