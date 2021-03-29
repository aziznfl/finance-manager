
    <script>
        function unbindScript() {
            $(document).unbind('change').ready(function() {
                $(".addItems").focusout(function() {
                    var count = $('#table-list-items tbody tr').length;
                    var id = findId(this);
                    if (this.value != "" && count == (parseInt(id) + 1)) {
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
                            var price = $("input[name='price[" + id + "]']").val();
                            var qty = $("input[name='qty[" + id + "]']").val();
                            $("#subtotal" + id).html(price * qty);
                            countTotal();
                        }
                    }
                );
            });
        }

        // must count based on id `tr`
        function countTotal() {
        }

        function findId(data) {
            return $(data).closest('tr').attr('id');
        }

        function insertNewLineItemList() {
            var table = $('#table-list-items tbody');
            table.append(renderNewLineItemList());
            unbindScript();
        }

        function renderNewLineItemList() {
            var tr = $('#table-list-items tbody tr');
            var count = tr.length;
            return "<tr id='" + count + "'>" +
                "<td class='text-center' style='vertical-align: middle;'>" + (count + 1) + "</td>" +
                "<td><input name='items[" + count + "]' class='form-control addItems'></td>" +
                "<td><input name='price[" + count + "]' class='form-control text-right counter' type='number' value='0'></td>" +
                "<td><input name='qty[" + count + "]' class='form-control text-right counter' type='number' value='0'></td>" +
                "<td id='subtotal" + count + "' class='text-right' style='vertical-align: middle;'>0</td>" +
            "</td>";
        }

        function setTitle(transactionId) {
            if (transactionId == null) {
                $('.content-header').html('<h1>Add <small>Transaction</small></h1>');
            } else {
                $('.content-header').html('<h1>Edit <small>Transaction</small></h1>');
            }
        }
    </script>