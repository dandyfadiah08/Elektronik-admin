<?= $this->extend('layouts/template') ?>
<?= $this->section('content') ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">

  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0"><?= $page->title ?> - <?= $page->subtitle ?></h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item status"><?= $page->navbar ?></li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Main content -->
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <?=
                htmlSelect([
                  'id' => 'filter-users',
                  'label' => 'Users',
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-sm-4',
                  'prepend' => '<i class="fas fa-info-circle" title="Users Filter"></i>',
                  'attribute' => ' data-placeholder="Users Filters"',
                  'option' => $optionUsers,
                ]) .
                  htmlInput([
                    'id' => 'filter-date',
                    'label' => 'Bonus Date',
                    'class' => 'datepicker myfilter',
                    'form_group' => 'col-sm-4',
                    'append' => '<i class="fas fa-calendar" title="Bonus Date Filter"></i>',
                  ])
                ?>
              </div>
              <table id="datatable1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>ID Bonus</th>
                    <th>Agent Name</th>
                    <th>Amount</th>
                    <th>Notes</th>
                    <th>Date</th>
                    <th>By</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Modal Agent Bonus -->
  <div class="modal" id="modalAgentBonus">
    <div class="modal-dialog">
      <div class="modal-content modal-lg">
        <div class="modal-header">
          <h5 class="modal-title">
            <span>Agent Bonus</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-12">
              <label>Bonus Details</label>
            </div>
            <?= htmlSelect([
              'id' => 'ab-users',
              'label' => 'Users',
              'class' => 'select2bs4 inputAgentBonus',
              'form_group' => 'col-12 col-md-6',
              'attribute' => 'data-placeholder="Choose User to receive Bonus"',
              'option' => '<option></option>',
            ]) . htmlInput([
              'id' => 'ab-bonus',
              'label' => 'Bonus (IDR)',
              'class' => 'inputAgentBonus inputPrice',
              'form_group' => 'col-12 col-md-6',
              'placeholder' => 'Ex. 1.000.000',
            ]) . htmlInput([
              'id' => 'ab-notes',
              'label' => 'Notes',
              'class' => 'inputAgentBonus',
              'form_group' => 'col-12 col-md-6',
              'placeholder' => 'Ex. Agent Bonus Februari 2022',
            ]) ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnAgentBonus" disabled><i class="fas fa-save"></i> Send Bonus</button>
        </div>
      </div>
    </div>
  </div>


</div>
<!-- /.content-wrapper -->



<?= $this->endSection('content') ?>


<?= $this->section('content_css') ?>
<!-- DataTables -->
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/daterangepicker/daterangepicker.css">
<?= $this->endSection('content_css') ?>


<?= $this->section('content_js') ?>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/select2/js/select2.full.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/moment/moment.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/daterangepicker/daterangepicker.js"></script>
<script>
  // const base_url = '<?= base_url() ?>';
  const path = '/bonus';
  var errors = null;
  var _search = <?= $search ?>;
  const inputAgentBonus = ['ab-users', 'ab-bonus', 'ab-notes'];
  const exportAccess = <?= hasAccess($role, 'r_export_bonus') ? 'true' : 'false' ?>;

  $(document).ready(function() {
    $('.select2bs4').select2({
      theme: 'bootstrap4',
      placeholder: $(this).data('placeholder')
    })


    initDateRangePicker();

    let datatable = $("#datatable1").DataTable({
      responsive: true,
      lengthChange: false,
      autoWidth: false,
      processing: true,
      serverSide: true,
      scrollX: true,
      ajax: {
        url: base_url + path + '/load_data',
        type: "post",
        data: function(d) {
          d.user_id = $('#filter-users option:selected').val();
          d.date = $('#filter-date').val();

          return d;
        },
      },
      columnDefs: [{
        targets: [0, 1, 5, 6],
        className: "text-center",
      }, {
        targets: [3],
        className: "text-right",
      }, {
        targets: [0],
        orderable: false
      }],
      order: [
        [5, "desc"]
      ],
      dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
      lengthMenu: [10, 50, 100],
      buttons: [
        "reload", {
          text: `<i class="fas fa-plus"></i> Add`,
          action: btnAddClicked,
          className: "btn-success"
        }, "export", "colvis", "pageLength"
      ],
    });
    datatable.buttons().container()
      .appendTo($('.col-sm-6:eq(0)', datatable.table().container()));
    // datatable.button().add(0, btnRefresh(() => datatable.ajax.reload()))

    $('.myfilter').change(function() {
      datatable.ajax.reload();
    })

    function btnAddClicked(e) {
      // console.log(e)
      $.ajax({
        url: `${base_url}/users/getUserAgent`,
        type: "post",
        dataType: "json",
        data: {
          check_id: $(this).data('check_id'),
        }
      }).done(function(response) {
        console.log(response);
        if (response.success) {
          let html = '<option value=""></option>'
          response.data.forEach(user => {
            html += `<option value="${user.user_id}">${user.name} / ${user.nik}</option>`;
          })
          $('#ab-users').html(html);
          $('#ab-users').trigger('change')
          $('#modalAgentBonus').modal('show');
        } else Swal.fire(response.message, '', "error")
      }).fail(function(response) {
        Swal.fire('An error occured!', '', 'error')
        console.log(response);
      })
    }

    // button Agent Bonus
    $('#btnAgentBonus').click(function() {
      const thisHTML = btnOnLoading('#btnAgentBonus')

      const title = `Confirmation`;
      const subtitle = `You are going to <b>Send Bonus</b> for <br>
        <center><table>
        <tr><td class="text-left">User</td><td> : </td><td><b>${$('#ab-users option:selected').html()}</b></td></tr>
        <tr><td class="text-left">Bonus (IDR)</td><td> : </td><td><b>${$('#ab-bonus').val()}</b></td></tr>
        </table></center>
        <br>Are you sure ?`;
      Swal.fire({
        title: title,
        html: subtitle,
        icon: 'info',
        confirmButtonText: `<i class="fas fa-check-circle"></i> Yes, Send Bonus Now`,
        showCancelButton: true,
        cancelButtonText: `<i class="fas fa-undo"></i> No, go back`,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#dc3545',
        input: 'number',
        inputAttributes: {
          autocapitalize: 'off',
          maxlength: 6,
          minlength: 6,
        },
        backdrop: `
          rgba(0,0,100,0.4)
          url("${base_url}/assets/images/warning.gif")
          right center
          no-repeat
          `,
      }).then(function(result) {
        console.log(result);
        if (result.isConfirmed) {
          let data = {
            user_id: $('#ab-users option:selected').val(),
            bonus: $('#ab-bonus').val(),
            notes: $('#ab-notes').val(),
            codeauth: result.value,
          };
          console.log('data');
          console.log(data);
          $.ajax({
            url: base_url + path + '/sendBonus',
            type: 'post',
            dataType: 'json',
            data: data,
          }).done(function(response) {
            btnOnLoading('#btnAgentBonus', false, thisHTML)
            if (response.success) {
              Swal.fire('Success', response.message, 'success');
              $('#modalAgentBonus').modal('hide');
              datatable.ajax.reload();
            } else {
              Swal.fire('Failed', response.message, 'error');
            }
          }).fail(function(response) {
            btnOnLoading('#btnAgentBonus', false, thisHTML)
            Swal.fire('Failed', 'Could not perform the task, please try again later. #wtd01v', 'error');
          })
        } else {
          btnOnLoading('#btnAgentBonus', false, thisHTML)
        }
      });
    })

    $('.inputAgentBonus').keyup(function() {
      btnSaveStateAgentBonus(inputAgentBonus)
    });

    function btnSaveStateAgentBonus(inputs, isFirst = false) {
      $('#btnAgentBonus').prop('disabled', !saveValidation(inputs))
      if (isFirst) clearErrors(inputs)
    }

    function saveValidation(inputs, first = false) {
      clearErrors(inputs)
      return !checkIsInputEmpty(inputs);
    }

    if (_search) {
      $('#isLoading').removeClass('d-none');
      setTimeout(() => {
        $('#isLoading').addClass('d-none');
        datatable.search(_search).draw();
      }, 2000);
    }

    if (exportAccess) {
      $('.btnExport').parent().parent().removeClass('d-none');
    }

  });

  if (exportAccess) {
    function btnExportClicked() {
      exportData({
        status: $('#filter-status').val(),
        status_payment: $('#filter-status_payment').val(),
        date: $('#filter-date').val(),
      })
    }
  }
</script>
<?= $this->endSection('content_js') ?>