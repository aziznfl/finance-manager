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

<script>
  $(function () {
    //Initialize Select2 Elements
    $('.select2').select2({
      placeholder: 'Please select'
    })

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

    $('input[type="number"]')
      .focus(function() {
        console.log("focus number");
        if ($(this).val() == "0") {
          $(this).val("");
        }
      })
      .focusout(function() {
        if($(this).val() == "") {
          $(this).val("0");
        }
      });

    $(".items").sortable({
      connectWith: ".items",
      placeholder: "placeholder",
      update: function(event, ui) {
        // update
      },
      start: function(event, ui) {
        console.log('sort');
        if(ui.helper.hasClass('second-level')) {
          ui.placeholder.removeClass('placeholder');
          ui.placeholder.addClass('placeholder-sub');
        } else { 
          ui.placeholder.removeClass('placeholder-sub');
          ui.placeholder.addClass('placeholder');
        }
      },
      sort: function(event, ui) {
        var pos;
        if(ui.helper.hasClass('second-level')) {
          pos = ui.position.left+20;
        } else {
          pos = ui.position.left;
        }
        if(pos >= 32 && !ui.helper.hasClass('second-level')) {
          // sub class
          ui.placeholder.removeClass('placeholder');
          ui.placeholder.addClass('placeholder-sub');
          ui.helper.addClass('second-level');
        } else if(pos < 25 && ui.helper.hasClass('second-level')) {
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
      drop: function( event, ui ) {
          if (ui.position.left >= 20) {
              $(this).removeClass("first-level");
              $(this).addClass("second-level");
          } else {
              $(this).removeClass("second-level");
              $(this).addClass("first-level");
          }
      },
      over: function( event, ui ) {
          // over
      },
      activate: function( event, ui ) {
          // activate
      }
    });
  });
</script>
<!-- add script of each module -->
<?php if (isset($add_footer)) { echo $add_footer; } ?>
</body>
</html>