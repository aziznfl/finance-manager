  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> 2.4.18
    </div>
    <strong>Copyright &copy; 2014-2019 <a href="https://adminlte.io">AdminLTE</a>.</strong> All rights
    reserved.
  </footer>

  <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
  <div class="control-sidebar-bg"></div>
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
        if ($(this).val() == "0") {
          $(this).val("");
        }
      })
      .focusout(function() {
        if($(this).val() == "") {
          $(this).val("0");
        }
      });
  });
</script>
<!-- add script of each module -->
<?php if (isset($add_footer)) { echo $add_footer; } ?>
</body>
</html>