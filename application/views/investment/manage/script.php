
    <script>
        function unbindScript() {
            $(document).unbind('change').ready(function() {
                setTitleAndButton();
                setFirstAddInvesment(true);
                fetchCategories();
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
        }

        // --------- SET -------- //

        function setTitleAndButton() {
            if (!investmentIdentify) {
                $('.content-header').html('<h1>Add <small>Investment</small></h1>');
                $('#btn-submit').html('<span class="fa fa-plus"></span>&nbsp;&nbsp;Save Investment');
                $('#form-delete').addClass('hide');
            } else {
                $('.content-header').html('<h1>Edit <small>Investment</small></h1>');
                $('#btn-submit').html('<span class="fa fa-refresh"></span>&nbsp;&nbsp;Update Investment');
                $('#form-delete').removeClass('hide');
            }
        }

        function setFirstAddInvesment(status) {
            // show only when update and add income investment
            if (status) {
                $("#status-form").addClass("hide");
                $("#remove-form").addClass("hide");
            } else {
                $("#status-form").removeClass("hide");
                $("#remove-form").removeClass("hide");
            }
        }

        // --------- FETCH -------- //

        function fetchCategories(categorySelected = "funding") {
            $(".select2").html("<option></option>");
            
            $.ajax({
                type: "GET",
                url: apiUrl() + 'category/investment',
                contentType: "application/json; charset=utf-8",
                dataType: "JSON",
                headers: {
                    'currentUser': '<?php echo $this->session->userdata('user')->account_key; ?>'
                },
                success: function(response) {
                    $.each(response.data, function(i, category) {
                        var selected = "";
                        if (category.name == categorySelected) {
                            selected = " selected"
                        }
                        $(".select2").append("<option value='" + category.id + "' " + selected + ">" + capitalize(category.name) + "</option>");
                    });
                    unbindSelect2();

                    if (investmentIdentify) {
                        fetchInvestmentFromId();
                    }
                }
            });
        }

        function fetchInvestmentFromId() {
            $.ajax({
                type: "GET",
                url: apiUrl() + 'investment/getOneInvestment',
                contentType: "application/json; charset=utf-8",
                dataType: "JSON",
                data: {
                    'investmentIdentify': investmentIdentify
                },
                headers: {
                    'currentUser': '<?php echo $this->session->userdata('user')->account_key; ?>'
                },
                success: function(response) {
                    var investment = response.data;

                    setValueFromSelect2(investment.instrument.id, !updateValue);
                    setValueFromName("manager", investment.manager, !updateValue);
                    setValueFromName("description", investment.description, !updateValue);

                    setFirstAddInvesment(false);
                    
                    if (!updateValue) {
                        $("#remove-form").addClass("hide");
                        $("#status-form").removeClass("hide");
                    } else {
                        setValueFromName("date", investment.date);
                        setValueFromName("amount", investment.amount.value);
                        $("#remove-form").removeClass("hide");
                        $("#status-form").addClass("hide");
                    }
                }
            });
        }
        
        // ------------- SUBMIT -------------- //

        $("#submit").click(function() {
            // $(this).attr("disabled", "disabled");
            var investment = getInvestment();
            console.log(investmentIdentify, updateValue);

            if (updateValue) {
                updateInvestment(investment);
            } else {
                addChildItemInvestment(investment);
            }

            function getInvestment() {
                var date = getValueFromName("date");
                var instrument = $(".select2").find(':selected').val();
                var manager = getValueFromName("manager");
                var description = getValueFromName("description");
                var status = getValueFromName("status");
                var amount = getValueFromName("amount");
                var isDone = getValueFromName("isDone");

                var investment = {
                    'date': date,
                    'instrumentId': parseInt(instrument),
                    'manager': manager,
                    'description': description,
                    'status': status,
                    'amount': parseInt(amount),
                    'isDone': parseInt(isDone)
                }
                return investment;
            }
        });

        function updateInvestment(investment) {
            $.ajax({
                type: "POST",
                url: apiUrl() + 'investment/manageInvestment',
                contentType: "application/json; charset=utf-8",
                dataType: "JSON",
                data: JSON.stringify(investment),
                headers: {
                    'currentUser': '<?php echo $this->session->userdata('user')->account_key; ?>'
                },
                success: function(response) {
                    console.log(response);
                    // window.location.href = baseUrl() + "/transaction/history";
                },
                error: function(xhr, status, error) {
                    if (error == "") {
                        alert("Please check internet connection!");
                    }
                    $(".form button").removeAttr("disabled");
                }
            });
        }

        function addChildItemInvestment(investment) {
            $.ajax({
                type: "POST",
                url: apiUrl() + 'investment/createChildItemInvestment',
                contentType: "application/json; charset=utf-8",
                dataType: "JSON",
                data: JSON.stringify(investment),
                headers: {
                    'currentUser': '<?php echo $this->session->userdata('user')->account_key; ?>'
                },
                success: function(response) {
                    window.location.href = baseUrl() + "/investment/portfolio";
                },
                error: function(xhr, status, error) {
                    if (error == "") {
                        alert("Please check internet connection!");
                    }
                    $(".form button").removeAttr("disabled");
                }
            });
        }
    </script>
