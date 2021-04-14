  </div>
  <!-- /.content-wrapper -->

  <div class="modal fade" id="modal-default" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <form>
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">×</span>
            </button>
            <h4 class="modal-title">Default Modal</h4>
          </div>
          <div class="modal-body">
            <!-- fill with jquery  -->
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Save</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 2.4.18
    </div>
    <strong>Copyright &copy; 2014-2019 <a href="https://adminlte.io">AdminLTE</a>.</strong> All rights
    reserved.
  </footer>
  </div>
  <!-- ./wrapper -->

  <!-- jQuery 3 -->
  <script src="<?php echo base_url(); ?>assets/jquery/jquery.min.js"></script>
  <!-- jQuery UI 1.11.4 -->
  <script src="<?php echo base_url(); ?>assets/jquery-ui/jquery-ui.min.js"></script>
  <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
  <script>
    $.widget.bridge('uibutton', $.ui.button);
  </script>
  <!-- Bootstrap 3.3.7 -->
  <script src="<?php echo base_url(); ?>assets/bootstrap/js/bootstrap.min.js"></script>
  <!-- highcharts -->
  <script src="<?php echo base_url(); ?>assets/highcharts/highcharts.js"></script>
  <!-- Slimscroll -->
  <script src="<?php echo base_url(); ?>assets/jquery-slimscroll/jquery.slimscroll.min.js"></script>
  <!-- datatables -->
  <script src="<?php echo base_url(); ?>assets/datatables.net/js/jquery.dataTables.min.js"></script>
  <script src="<?php echo base_url(); ?>assets/datatables.net-bs/js/dataTables.bootstrap.min.js"></script>
  <!-- AdminLTE App -->
  <script src="<?php echo base_url(); ?>assets/admin-lte/js/adminlte.min.js"></script>
  <!-- select 2 -->
  <script src="<?php echo base_url(); ?>assets/select2/js/select2.js"></script>
  <!-- bootstrap datepicker -->
  <script src="<?php echo base_url(); ?>assets/moment/min/moment.min.js"></script>
  <script src="<?php echo base_url(); ?>assets/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
  <!-- bootstrap datetimepicker -->
  <script src="<?php echo base_url(); ?>assets/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
  <!-- date -->
  <script src="<?php echo base_url(); ?>assets/date.js"></script>

  <script>
    $(function() {
      unbindSelect2();

      //Datatable
      $('.datatable').DataTable();

      //Date picker
      $('.datepicker').datepicker({
        autoclose: true
      })

      //Date picker
      $('.datetimepicker').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss'
      })

      $(".items").sortable({
        connectWith: ".items",
        placeholder: "placeholder",
        update: function(event, ui) {
          // update
        },
        start: function(event, ui) {
          if (ui.helper.hasClass('second-level')) {
            ui.placeholder.removeClass('placeholder');
            ui.placeholder.addClass('placeholder-sub');
          } else {
            ui.placeholder.removeClass('placeholder-sub');
            ui.placeholder.addClass('placeholder');
          }
        },
        sort: function(event, ui) {
          var pos;
          if (ui.helper.hasClass('second-level')) {
            pos = ui.position.left + 20;
          } else {
            pos = ui.position.left;
          }
          if (pos >= 32 && !ui.helper.hasClass('second-level')) {
            // sub class
            ui.placeholder.removeClass('placeholder');
            ui.placeholder.addClass('placeholder-sub');
            ui.helper.addClass('second-level');
          } else if (pos < 25 && ui.helper.hasClass('second-level')) {
            // set as parent
            ui.placeholder.removeClass('placeholder-sub');
            ui.placeholder.addClass('placeholder');
            ui.helper.removeClass('second-level');
          }
        }
      });

      $(".item").droppable({
        accept: ".item",
        hoverClass: "dragHover",
        drop: function(event, ui) {
          if (ui.position.left >= 20) {
            $(this).removeClass("first-level");
            $(this).addClass("second-level");
          } else {
            $(this).removeClass("second-level");
            $(this).addClass("first-level");
          }
        },
        over: function(event, ui) {
          // over
        },
        activate: function(event, ui) {
          // activate
        }
      });
    });

    function baseUrl() {
      return '<?php echo base_url(); ?>';
    }

    function apiUrl() {
      var location = window.location;

      if (location.hostname.includes("localhost") || location.hostname.includes("127.0.0.1")) {
        var url = location.protocol + "//" + location.host + '/finance-manager-api/';
        return url;
      } else {
        var hostname = location.hostname;
        var domainName = hostname.substring(hostName.lastIndexOf(".", hostName.lastIndexOf(".") - 1) + 1);
        return location.protocol + '//api.' + domainName + '/';
      }
    }

    function findId(data) {
      return $(data).closest('tr').attr('id');
    }

    function nulledIsEmpty(value) {
      if (value == "") {
        return null;
      } else {
        return value;
      }
    }

    function currencyFormat(number) {
      return number.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,').split('.')[0];
    }

    function capitalize(text) {
      var lower = text.toLowerCase();
      return text.charAt(0).toUpperCase() + lower.slice(1);
    }

    function getNumberFromCurrency(string) {
      return parseInt(string.split(",").join(""));
    }

    function getValueFromName(name) {
      return $("input[name='" + name + "']").val();
    }

    function setValueFromName(name, value) {
      $("input[name='" + name + "']").val(value);
    }

    function unbindSelect2() {
      $(document).unbind('change').ready(function() {
        $('.select2').select2({
          placeholder: 'Please select'
        });
      });
    }

    function setValueFromSelect2(value) {
      $(".select2").val(value);
      $(".select2").trigger("change");
    }
  </script>
  <!-- add script of each module -->
  <?php if (isset($add_footer)) {
    echo $add_footer;
  } ?>