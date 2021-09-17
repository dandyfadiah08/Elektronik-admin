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
            <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Master</a></li>
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
                  'id' => 'filter-status',
                  'label' => 'Status',
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-4',
                  'prepend' => '<i class="fas fa-info-circle" title="Status Filter"></i>',
                  'attribute' => 'data-placeholder="Status Filter"',
                  'option' => $optionStatus,
                ]) . htmlSelect([
                  'id' => 'filter-notification',
                  'label' => 'Notification',
                  'class' => 'select2bs4 myfilter',
                  'form_group' => 'col-4',
                  'prepend' => '<i class="fas fa-bell" title="Web Noticiation Filter"></i>',
                  'attribute' => 'data-placeholder="Web Notification Filter"',
                  'option' => '<option></option>
                  <option value="all">All</option>
                  <option value="1">Active</option>
                  <option value="2">Inactive</option>',
                ])
                ?>
              </div>
              <table id="datatable1" class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Last Updated</th>
                    <th>Status / Action</th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Modal Add EDit -->
  <div class="modal" id="modalAddEdit">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <span class="modal_add">Add Admin</span>
            <span class="modal_edit">Edit Admin</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form id="formAddEdit">
            <input type="hidden" id="id">
            <div class="row">
              <?= htmlInput([
                'id' => 'username',
                'label' => 'Username',
                'class' => 'saveInput',
                'form_group' => 'col-6',
                'placeholder' => 'Ex. device_checker_1',
                'prepend' => '<i class="fas fa-user"></i>',
              ]) . htmlInput([
                'id' => 'password',
                'label' => 'Password',
                'class' => 'saveInput',
                'type' => 'password',
                'form_group' => 'col-6',
                'prepend' => '<i class="fas fa-lock btnViewPassword" data-state="hidden" data-target="#password" title="Click to toggle view/hidden password"></i>',
                'placeholder' => 'Ex. #t0p53crEt@',
              ]) . htmlInput([
                'id' => 'email',
                'label' => 'Email',
                'class' => 'saveInput',
                'type' => 'email',
                'form_group' => 'col-6',
                'prepend' => '<i class="fas fa-at"></i>',
                'placeholder' => 'Ex. john.doe@mail.com',
              ]) . htmlInput([
                'id' => 'name',
                'label' => 'Name',
                'class' => 'saveInput',
                'type' => 'text',
                'form_group' => 'col-6',
                'placeholder' => 'Ex. John Doe',
                'prepend' => '<i class="fas fa-font"></i>',
              ]) . htmlSelect([
                'id' => 'role_id',
                'label' => 'Role',
                'class' => 'saveInput select2bs4',
                'form_group' => 'col-6',
                'prepend' => '<i class="fas fa-user-tag"></i>',
                'attribute' => 'data-placeholder="Choose role"',
                'option' => $optionRole,
              ]) ?>
            </div>
            <?= htmlSwitch([
              'id' => 'status',
              'label' => 'Status',
              'class' => 'saveInput',
              'on' => 'ACTIVE',
              'off' => 'INACTIVE',
            ]) ?>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnAddEdit" disabled><i class="fas fa-save"></i> Save</button>
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
<script src="<?= base_url() ?>/assets/adminlte3/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<script>
  const path = '/admin';
  var errors = null;
  const inputs = ['username', 'password', 'email', 'name', 'role_id'];
  $(document).ready(function() {
    $('.select2bs4').select2({
      theme: 'bootstrap4',
      placeholder: $(this).data('placeholder')
    })

    $("input[data-bootstrap-switch]").bootstrapSwitch();

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
          d.status = $('#filter-status option:selected').val();
          d.notification = $('#filter-notification option:selected').val();
          return d;
        },
      },
      columnDefs: [{
        targets: [0, 1, 2, 3, 4, 6],
        className: "text-center",
      }, {
        targets: [0, 6],
        orderable: false
      }],
      order: [
        [1, "asc"]
      ],
      dom: "l<'row my-2'<'col'B><'col'f>>t<'row my-2'<'col'i><'col'p>>",
      lengthMenu: [10, 50, 100],
      buttons: [{
        text: `<i class="fas fa-plus"></i> Add`,
        action: btnAddClicked,
        className: "btn-success"
      }, "colvis", "pageLength"],
    });
    datatable.buttons().container()
      .appendTo($('.col-sm-6:eq(0)', datatable.table().container()));
    datatable.button().add(0, btnRefresh(() => datatable.ajax.reload()))

    $('.myfilter').change(function() {
      datatable.ajax.reload();
    })

    $('body').on('click', '.btnEdit', function(e) {
      btnEditClicked(this)
    });
    $('body').on('click', '.btnDelete', function(e) {
      btnDeleteClicked(this)
    });
    $('#btnAddEdit').click(btnSaveClicked);

    function btnAddClicked() {
      $('input[type="text"]').val('');
      $('input[type="email"]').val('');
      $('input[type="password"]').val('');
      $('#id').val('');
      $('#role_id').val('');
      $('#role_id').trigger('change');
      btnSaveState(true);
      $('.modal_add').show();
      $('.modal_edit').hide();
      $('#modalAddEdit').modal('show');
    }

    function btnEditClicked(e) {
      const id = $(e).data('id');

      $('#id').val(id);
      $.ajax({
        url: `${base_url}${path}/details`,
        type: "post",
        dataType: "json",
        data: {
          id: id,
        }
      }).done(function(response) {
        var class_swal = response.success ? 'success' : 'error';
        if (response.success) {
          $('#username').val(response.data.username);
          $('#password').val(atob(response.data.password));
          $('#name').val(response.data.name);
          $('#email').val(response.data.email);
          $('#role_id').val(response.data.role_id);
          $('#role_id').trigger('change');
          if (response.data.status == 'active') $('#status').bootstrapSwitch('state', true)
          else $('#status').bootstrapSwitch('state', false)
        } else
          Swal.fire(response.message, '', class_swal)
      }).fail(function(response) {
        Swal.fire('An error occured!', '', 'error')
        console.log(response);
      })

      btnSaveState(true);
      $('.modal_add').hide();
      $('.modal_edit').show();
      $('#modalAddEdit').modal('show');
    }

    function btnDeleteClicked(e) {
      const id = $(e).data('id');
      const username = $(e).data('username');
      const role_name = $(e).data('role_name');
      const status = $(e).data('status');
      Swal.fire({
        title: `You are going to delete <b><span class="text-${status == 'active' ? 'success' : 'danger'}">${status}</span> Admin: <span class="text-primary">${username} (${role_name})</span>`,
        html: `Click <b>Continue Delete</b> to proceed, or<br><b>Close</b> to cancel this action`,
        showCancelButton: true,
        confirmButtonText: `Continue Delete`,
        cancelButtonText: `Close`,
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: `${base_url}${path}/delete`,
            type: "post",
            dataType: "json",
            data: {
              id: id,
            }
          }).done(function(response) {
            var class_swal = response.success ? 'success' : 'error';
            Swal.fire(response.message, '', class_swal).then(() => {
              if (response.success) datatable.ajax.reload();
            })
          }).fail(function(response) {
            Swal.fire('An error occured!', '', 'error')
            console.log(response);
          })
        }
      });
    }

    function btnSaveClicked() {
      const id = $('#id').val();
      const username = $('#username').val();
      const email = $('#email').val();
      const name = $('#name').val();
      const role_id = $('#role_id option:selected').val();
      const role_name = $('#role_id option:selected').text();
      const status = $('#status').prop('checked') ? 'active' : 'inactive';

      if (saveValidation())
        Swal.fire({
          title: `You are going to save Admin to be:`,
          html: `<table class="mx-auto">
        <tr><td class="text-left">Username</td><td>&nbsp; : &nbsp;</td><td class="text-left"> ${username}</td></tr>
        <tr><td class="text-left">Email</td><td>&nbsp; : &nbsp;</td><td class="text-left"> ${email}</td></tr>
        <tr><td class="text-left">Name</td><td>&nbsp; : &nbsp;</td><td class="text-left"> ${name}</td></tr>
        <tr><td class="text-left">Role</td><td>&nbsp; : &nbsp;</td><td class="text-left"> ${role_name}</td></tr>
        <tr><td class="text-left">Status</td><td>&nbsp; : &nbsp;</td><td class="text-left"> ${status == 'active' ? 'Active' : 'Inactive'}</td></tr>
        </table><br>Click <b>Continue Update</b> to proceed, or<br><b>Close</b> to cancel this action`,
          showCancelButton: true,
          confirmButtonText: `Continue Save`,
          cancelButtonText: `Close`,
        }).then((result) => {
          if (result.isConfirmed) {
            $.ajax({
              url: `${base_url}${path}/save`,
              type: "post",
              dataType: "json",
              data: {
                id: id,
                username: username,
                password: btoa($('#password').val()),
                password_length: masking($('#password').val(), 0, 0),
                email: email,
                name: name,
                role_id: role_id,
                role_name: role_name,
                status: status,
              }
            }).done(function(response) {
              var class_swal = response.success ? 'success' : 'error';
              if (response.success) {
                Swal.fire(response.message, '', class_swal)
                datatable.ajax.reload();
                $('#modalAddEdit').modal('hide');
              } else if (typeof response.data !== undefined) {
                for (const [key, value] of Object.entries(response.data)) {
                  inputError(key == 'password_length' ? 'password' : key, value)
                }
              } else
                Swal.fire(response.message, '', class_swal)
            }).fail(function(response) {
              Swal.fire('An error occured!', '', 'error')
              console.log(e);
            })
          }
        });
    }

    $('.saveInput').keyup(function() {
      btnSaveState()
    });
    $('.saveInput').change(function() {
      btnSaveState()
    });

    function btnSaveState(isFirst = false) {
      $('#btnAddEdit').prop('disabled', !saveValidation())
      if (isFirst) clearErrors(inputs)
    }

    function saveValidation(first = false) {
      clearErrors(inputs)
      return !checkIsInputEmpty(inputs);
    }

    $('div > span > .btnViewPassword, .btnViewPassword').click(function(e) {
      togglePassword({
        event: e,
        with_color: true,
        color_hide: 'secondary'
      });
    });

  });
</script>
<?= $this->endSection('content_js') ?>