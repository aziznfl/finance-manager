
    <script>

        // Function

        function unbindScript() {
            $(document).unbind('change').ready(function() {
                
            });
        }

        function fetchRecurring() {
            $.ajax({
                type: "GET",
                url: apiUrl() + 'transaction/fetchrecurring',
                contentType: "application/json; charset=utf-8",
                dataType: "JSON",
                headers: {
                    'currentUser': '<?php echo $this->session->userdata('user')->account_key; ?>'
                },
                success: function(response) {
                    var i = 0;

                    $.each(response.data, function(i, data) {
                        var date = new Date();
                        var repetitionText = "Every ";
                        var arr = data.repetition.split(" ");
                        var newDates = getDateFromRepetition(arr);
                        var oldDate = new Date(newDates[0], newDates[1], newDates[2], arr[1], arr[0]);
                        var dateText = "";
                        if (arr[4] == "*") {
                            // not every weekend
                            dateText += date.getFullYear();
                            if (arr[3] != "*") {
                                // every year
                                oldDate.toLocaleString()
                                repetitionText += " Year<br/>(on " + oldDate.toLocaleDateString('en-US', { month: 'long'}) + " " + oldDate.toLocaleDateString('en-US', { weekday: 'long'}) + ")";
                                dateText += "-" + oldDate.getMonth() + "-" + oldDate.getDate() + "%20" + oldDate.getHours() + ":" + oldDate.getMinutes() + ":00";
                            } else if (arr[3] == "*") {
                                // not every year
                                dateText += "-" + date.getMonth();
                                if (arr[2] != "*") {
                                    // repetitionText += " Month<br/>(on " + dateText(arr, "jS - H:i") + ")";
                                }
                                dateText += "-" + oldDate.getDate() + "%20" + oldDate.getHours() + ":" + oldDate.getMinutes() + ":00";
                            }
                        }

                        $(".datatable tbody").append("<tr id='row"+i+"'>");
                        $("#row"+i).append("<td>" + (i + 1) + "</td>");
                        $("#row"+i).append("<td>" + repetitionText + "</td>");

                        // description column
                        var description = "";
                        if (data.description) {
                            description = " - " + data.description;
                        }
                        var location = "";
                        if (data.location.name) {
                            location = "&nbsp;&nbsp;" +
                                "<span class='text-primary'>" +
                                    "<span class='fa fa-map-marker'></span>&nbsp;" + data.location.name +
                                "</span>";
                        }
                        $("#row"+i).append(
                            "<td>" +
                                "<div style='font-size: 16px; font-weight: 400;'>Rp. " + data.amount.text + "</div>" +
                                "<div>" +
                                    "<span style='font-weight: 600;'>" + capitalize(data.category.name) + "</span>" + description + location +
                                "</div>" +
                                "<span class='label bg-blue'>" + data.tag + "</span>" +
                            "</td>"
                        );

                        // link column
                        var params = "?date=" + dateText;
                        params += "&amount=" + data.amount.value;
                        params += "&category=" + data.category.id;
                        if (data.description) {
                            params += "&desc=" + data.description;
                        }
                        if (data.location.name) {
                            params += "&location=" + data.location.name;
                        }
                        if (data.tag) {
                            params += "&tag=" + data.tag;
                        }
                        $("#row"+i).append(
                            "<td class='text-center'>" +
								"<a href=" + baseUrl() + "transaction/manage" + params + "><span class='fa fa-plus'></span></a>" +
							"</td>"
                        );

                        i++;
                    });

                    unbindDataTable();
                }
            });
        }

        function getFullMonth(index) {
            var month = new Array();
            month[0] = "January";
            month[1] = "February";
            month[2] = "March";
            month[3] = "April";
            month[4] = "May";
            month[5] = "June";
            month[6] = "July";
            month[7] = "August";
            month[8] = "September";
            month[9] = "October";
            month[10] = "November";
            month[11] = "December";
            return month[index];
        }

        function getDateFromRepetition(arr) {
            var newDate = new Date();
            
            var year = newDate.getFullYear();
            if (arr[4] != "*") {
                year = parseInt(arr[4]);
            }

            var month = newDate.getMonth();
            if (arr[3] != "*") {
                month = parseInt(arr[3]);
            }

            var day = newDate.getDate();
            if (arr[2] != "*") {
                day = parseInt(arr[2]);
            }

            return [year, month, day];
        }

        function dateText(arr, dateInitial) {
            makeTime = mktime(intval(arr[1]), intval(arr[0]), 0, intval(arr[3]), intval(arr[2]));
            return date(dateInitial, makeTime);
        }
    </script>
