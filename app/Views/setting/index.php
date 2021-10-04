<?= $this->extend('layouts/template') ?>
<?= $this->section('content') ?>

<style>
  .btnSaveSetting {
    height: fit-content;
  }
</style>

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
            <li class="breadcrumb-item"><a href="#">Setting</a></li>
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
              <div class="card card-primary">
                <div class="card-header" data-card-widget="collapse">
                  <h3 class="card-title">Version Application</h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool">
                      <i class="fas fa-minus"></i>
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  <label for="version_app1">Version WowJual (App 1)<small class="invalid-errors"></small></label>
                  <div class="row">
                    <?=
                    htmlInput([
                      'id' => 'version_app1',
                      'class' => 'saveInput',
                      'form_group' => 'col',
                      'placeholder' => 'Ex. 1',
                      'value' => $dataSetting->version_app1->val,
                    ]) .
                      htmlButton([
                        'color'  => 'success',
                        'class'  => 'py-2 btnAction btnSaveVersion col-auto btnSaveSetting',
                        'title'  => 'For Save Setting version',
                        'icon'  => 'fas fa-save',
                        'text'  => 'Save',
                        'data'  => htmlSetData(['id' => 'version_app1']) . htmlSetData(['title' => 'Version WowJual (App 1)'])
                      ], false)
                    ?>
                  </div>
                  <label for="version_app2">Version Wowfonenet (App 2)<small class="invalid-errors"></small></label>
                  <div class="row">
                    <?=
                    htmlInput([
                      'id' => 'version_app2',
                      'class' => 'saveInput',
                      'form_group' => 'col',
                      'placeholder' => 'Ex. 1',
                      'value' => $dataSetting->version_app2->val,
                    ]) .
                      htmlButton([
                        'color'  => 'success',
                        'class'  => 'py-2 btnAction btnSaveVersion  col-auto btnSaveSetting',
                        'title'  => 'For Save Setting version',
                        'icon'  => 'fas fa-save',
                        'text'  => 'Save',
                        'data'  => htmlSetData(['id' => 'version_app2']) . htmlSetData(['title' => 'Version Wowfonenet (App 2)'])
                      ], false)
                    ?>
                  </div>

                </div>
              </div>

              <div class="card card-primary">
                <div class="card-header" data-card-widget="collapse">
                  <h3 class="card-title">Url Application</h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool">
                      <i class="fas fa-minus"></i>
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  <label for="url_imei">Url Kemenperin<small class="invalid-errors"></small></label>
                  <div class="row">
                    <?=
                    htmlInput([
                      'id' => 'url_imei',
                      'class' => 'saveInput',
                      'form_group' => 'col',
                      'placeholder' => 'Ex. https://wowfonenet.id/',
                      'value' => $dataSetting->url_imei->val,
                    ]) .
                      htmlButton([
                        'color'  => 'success',
                        'class'  => 'py-2 btnAction btnSaveUrl col-auto btnSaveSetting',
                        'title'  => 'For Save Setting Url',
                        'icon'  => 'fas fa-save',
                        'text'  => 'Save',
                        'data'  => htmlSetData(['id' => 'url_imei']) . htmlSetData(['title' => 'Url Kemenperin'])
                      ])
                    ?>
                  </div>
                  <label for="chat_app1">Url Chat App 1<small class="invalid-errors"></small></label>
                  <div class="row">
                    <?=
                    htmlInput([
                      'id' => 'chat_app1',
                      'class' => 'saveInput',
                      'form_group' => 'col',
                      'placeholder' => 'Ex. https://wowfonenet.id/',
                      'value' => $dataSetting->chat_app1->val,
                    ]) .
                      htmlButton([
                        'color'  => 'success',
                        'class'  => 'py-2 btnAction btnSaveUrl col-auto btnSaveSetting',
                        'title'  => 'For Save Setting Url',
                        'icon'  => 'fas fa-save',
                        'text'  => 'Save',
                        'data'  => htmlSetData(['id' => 'chat_app1']) . htmlSetData(['title' => 'Url Chat App 1'])
                      ])
                    ?>
                  </div>

                  <label for="chat_app2">Url Chat App 2<small class="invalid-errors"></small></label>
                  <div class="row">
                    <?=
                    htmlInput([
                      'id' => 'chat_app2',
                      'class' => 'saveInput',
                      'form_group' => 'col',
                      'placeholder' => 'Ex. https://wowfonenet.id/',
                      'value' => $dataSetting->chat_app2->val,
                    ]) .
                      htmlButton([
                        'color'  => 'success',
                        'class'  => 'py-2 btnAction btnSaveUrl col-auto btnSaveSetting',
                        'title'  => 'For Save Setting Url',
                        'icon'  => 'fas fa-save',
                        'text'  => 'Save',
                        'data'  => htmlSetData(['id' => 'chat_app2']) . htmlSetData(['title' => 'Url Chat App 2'])
                      ])
                    ?>
                  </div>

                </div>
              </div>
              <div class="card card-primary">
                <div class="card-header" data-card-widget="collapse">
                  <h3 class="card-title">Terms & Condition</h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool">
                      <i class="fas fa-minus"></i>
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  <label for="min_withdraw">Minimal Withdraw<small class="invalid-errors"></small></label>
                  <div class="row">
                    <?=
                    htmlInput([
                      'id' => 'min_withdraw',
                      'class' => 'saveInput inputPrice',
                      'form_group' => 'col',
                      'placeholder' => 'Ex. 50.000',
                      'value' => $dataSetting->min_withdraw->val,
                    ]) .
                      htmlButton([
                        'color'  => 'success',
                        'class'  => 'py-2 btnAction btnSaveUrl col-auto btnSaveSetting',
                        'title'  => 'For Save Setting Url',
                        'icon'  => 'fas fa-save',
                        'text'  => 'Save',
                        'data'  => htmlSetData(['id' => 'min_withdraw']) . htmlSetData(['title' => 'Minimal Withdraw'])
                      ])
                    ?>
                  </div>
                  <?=
                  htmlSummernote([
                    'id' => 'tnc_app1',
                    'label' => 'Wow Jual',
                    'class' => 'saveInput',
                    'form_group' => 'col',
                    'placeholder' => 'Ex. 50.000',
                    'value' => $dataSetting->tnc_app1->val,
                  ]) . 
                    htmlButton([
                      'color'  => 'success',
                      'class'  => 'py-2 btnAction btnSaveUrl col-auto btnSaveSettingTnc',
                      'title'  => 'For Save Setting Url',
                      'icon'  => 'fas fa-save',
                      'text'  => 'Save',
                      'data'  => htmlSetData(['id' => 'tnc_app1']) . htmlSetData(['title' => 'Tnc Wow Jual'])
                    ], false) . 
                    htmlSummernote([
                      'id' => 'tnc_app2',
                      'label' => 'Wowfonet',
                      'class' => 'saveInput',
                      'form_group' => 'col',
                      'placeholder' => 'Ex. 50.000',
                      'value' => $dataSetting->tnc_app2->val,
                    ]) .
                    htmlButton([
                      'color'  => 'success',
                      'class'  => 'py-2 btnAction btnSaveUrl col-auto btnSaveSettingTnc',
                      'title'  => 'For Save Setting Url',
                      'icon'  => 'fas fa-save',
                      'text'  => 'Save',
                      'data'  => htmlSetData(['id' => 'tnc_app2']) . htmlSetData(['title' => 'Tnc Wowfonet'])
                    ], false) . 
                    htmlSummernote([
                      'id' => 'short_tnc_app2',
                      'label' => 'Short Wowfonet',
                      'class' => 'saveInput',
                      'form_group' => 'col',
                      'placeholder' => 'Ex. 50.000',
                      'value' => $dataSetting->short_tnc_app2->val,
                    ]) .
                    htmlButton([
                      'color'  => 'success',
                      'class'  => 'py-2 btnAction btnSaveUrl col-auto btnSaveSettingTnc',
                      'title'  => 'For Save Setting Url',
                      'icon'  => 'fas fa-save',
                      'text'  => 'Save',
                      'data'  => htmlSetData(['id' => 'short_tnc_app2']) . htmlSetData(['title' => 'Tnc Short Wowfonet'])
                    ], false)
                  ?>
                </div>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>
<!-- /.content-wrapper -->

<?= $this->endSection('content') ?>


<?= $this->section('content_css') ?>
<!-- DataTables -->

<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/datatables-buttons/css/buttons.bootstrap4.min.css">

<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/summernote/summernote-bs4.min.css">


<?= $this->endSection('content_css') ?>


<?= $this->section('content_js') ?>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/moment/moment.min.js"></script>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/summernote/summernote-bs4.min.js"></script>


<script>
  const path = '/setting';
  var errors = null;

  $(document).ready(function() {


    

    $('#tnc_app1').summernote({
      height: 300, // set editor height
      minHeight: null, // set minimum height of editor
      maxHeight: null, // set maximum height of editor
      focus: true // set focus to editable area after initializing summernote
    });
    $('#tnc_app2').summernote({
      height: 300, // set editor height
      minHeight: null, // set minimum height of editor
      maxHeight: null, // set maximum height of editor
      focus: true // set focus to editable area after initializing summernote
    });

    $('#short_tnc_app2').summernote({
      height: 300, // set editor height
      minHeight: null, // set minimum height of editor
      maxHeight: null, // set maximum height of editor
      focus: true // set focus to editable area after initializing summernote
    });

    $('.btnSaveSetting').click(function() {
      var id_input = $(this).data('id');
      var title = $(this).data('title');
      var val = "";
      if(id_input == "tnc_app1" || id_input == "tnc_app2"){
        val = $('#' + id_input).summernote('code');
      } else val = $('#' + id_input).val();
      var data = {
        _key: id_input,
        val: val,
        title: title,
      };
      saveClicked(data);
    });

    function saveClicked(data) {
      $.ajax({
        url: `${base_url}${path}/save`,
        type: "post",
        dataType: "json",
        data: data,
      }).done(function(response) {
        var class_swal = response.success ? 'success' : 'error';
        Swal.fire(response.message, '', class_swal)
      }).fail(function(response) {
        Swal.fire('An error occured!', '', 'error')
        console.log(response);
      })
    }

    $('.btnSaveSettingTnc').click(function() {
      var id_input = $(this).data('id');
      var title = $(this).data('title');
      val = $('#' + id_input).summernote('code');
      
      var data = {
        _key: id_input,
        val: val,
        title: title,
      };
      saveClickedTnc(data);
    });

    function saveClickedTnc(data) {
      $.ajax({
        url: `${base_url}${path}/saveTnc`,
        type: "post",
        dataType: "json",
        data: data,
      }).done(function(response) {
        var class_swal = response.success ? 'success' : 'error';
        Swal.fire(response.message, '', class_swal)
      }).fail(function(response) {
        Swal.fire('An error occured!', '', 'error')
        console.log(response);
      })
    }

  })
</script>
<?= $this->endSection('content_js') ?>