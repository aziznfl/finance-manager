
    <script>
        setTotal(0);

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
            return "#table-list-items";
        }

        function isLastChild(html) {
            var lastHtml = $(getTableId() + " tbody tr").last().find('input').first()[0];
            return (html == lastHtml);
        }

        //------- Calculate ---------

        function getCountItem() {
            return parseInt($(getTableId() + ' tbody tr').length);
        }

        function setCounter() {
            var countItem = (getCountItem() - 1);
            var counterText = "Total Item: " + countItem;
            $("#table-list-items tfoot").find('[data-tag=\"counter-text\"]').text(counterText);
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

            var price = parseInt($("input[name='price[" + id + "]']").val());
            var qty = parseInt($("input[name='qty[" + id + "]']").val());
            var subtotal = price * qty;
            var newTotal = oldTotal + (subtotal - oldSubtotal);

            $("#subtotal" + id).text(currencyFormat(subtotal));
            setTotal(newTotal);
        }
        
        // ------ Rendering -------

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
            if (id != getCountItem() - 1) {
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

        function setTitle(transactionId) {
            if (transactionId == null) {
                $('.content-header').html('<h1>Add <small>Transaction</small></h1>');
            } else {
                $('.content-header').html('<h1>Edit <small>Transaction</small></h1>');
            }
        }
    </script>
