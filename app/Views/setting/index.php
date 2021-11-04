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
                  <label for="version_app1">Version WowJual (Android)<small class="invalid-errors"></small></label>
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
                        'data'  => htmlSetData(['id' => 'version_app1']) . htmlSetData(['title' => 'Version WowJual (Android)'])
                      ], false)
                    ?>
                  </div>
                  <label for="version_app1_ios">Version WowJual (Ios)<small class="invalid-errors"></small></label>
                  <div class="row">
                    <?=
                    htmlInput([
                      'id' => 'version_app1_ios',
                      'class' => 'saveInput',
                      'form_group' => 'col',
                      'placeholder' => 'Ex. 1',
                      'value' => $dataSetting->version_app1_ios->val,
                    ]) .
                      htmlButton([
                        'color'  => 'success',
                        'class'  => 'py-2 btnAction btnSaveVersion  col-auto btnSaveSetting',
                        'title'  => 'For Save Setting version',
                        'icon'  => 'fas fa-save',
                        'text'  => 'Save',
                        'data'  => htmlSetData(['id' => 'version_app1_ios']) . htmlSetData(['title' => 'Version WowJual (Ios)'])
                      ], false)
                    ?>
                  </div>

                  <label for="version_app2">Version Wowfonenet (Android)<small class="invalid-errors"></small></label>
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
                        'data'  => htmlSetData(['id' => 'version_app2']) . htmlSetData(['title' => 'Version Wowfonenet (Android)'])
                      ], false)
                    ?>
                  </div>
                  <label for="version_app2_ios">Version Wowfonenet (Ios)<small class="invalid-errors"></small></label>
                  <div class="row">
                    <?=
                    htmlInput([
                      'id' => 'version_app2_ios',
                      'class' => 'saveInput',
                      'form_group' => 'col',
                      'placeholder' => 'Ex. 1',
                      'value' => $dataSetting->version_app2_ios->val,
                    ]) .
                      htmlButton([
                        'color'  => 'success',
                        'class'  => 'py-2 btnAction btnSaveVersion  col-auto btnSaveSetting',
                        'title'  => 'For Save Setting version',
                        'icon'  => 'fas fa-save',
                        'text'  => 'Save',
                        'data'  => htmlSetData(['id' => 'version_app2_ios']) . htmlSetData(['title' => 'Version Wowfonenet (Ios)'])
                      ], false)
                    ?>
                  </div>

                  <label for="version_app3">Version WowMitra (Android)<small class="invalid-errors"></small></label>
                  <div class="row">
                    <?=
                    htmlInput([
                      'id' => 'version_app3',
                      'class' => 'saveInput',
                      'form_group' => 'col',
                      'placeholder' => 'Ex. 1',
                      'value' => $dataSetting->version_app3->val,
                    ]) .
                      htmlButton([
                        'color'  => 'success',
                        'class'  => 'py-2 btnAction btnSaveVersion  col-auto btnSaveSetting',
                        'title'  => 'For Save Setting version',
                        'icon'  => 'fas fa-save',
                        'text'  => 'Save',
                        'data'  => htmlSetData(['id' => 'version_app3']) . htmlSetData(['title' => 'Version WowMitra (Android)'])
                      ], false)
                    ?>
                  </div>
                  <label for="version_app3_ios">Version WowMitra (Ios)<small class="invalid-errors"></small></label>
                  <div class="row">
                    <?=
                    htmlInput([
                      'id' => 'version_app3_ios',
                      'class' => 'saveInput',
                      'form_group' => 'col',
                      'placeholder' => 'Ex. 1',
                      'value' => $dataSetting->version_app3_ios->val,
                    ]) .
                      htmlButton([
                        'color'  => 'success',
                        'class'  => 'py-2 btnAction btnSaveVersion  col-auto btnSaveSetting',
                        'title'  => 'For Save Setting version',
                        'icon'  => 'fas fa-save',
                        'text'  => 'Save',
                        'data'  => htmlSetData(['id' => 'version_app3_ios']) . htmlSetData(['title' => 'Version WowMitra (Ios)'])
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
                        'title'  => 'Save Setting',
                        'icon'  => 'fas fa-save',
                        'text'  => 'Save',
                        'data'  => htmlSetData(['id' => 'url_imei']) . htmlSetData(['title' => 'Url Kemenperin'])
                      ])
                    ?>
                  </div>
                  <label for="chat_app1">Url Chat WowJual<small class="invalid-errors"></small></label>
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
                        'title'  => 'Save Setting',
                        'icon'  => 'fas fa-save',
                        'text'  => 'Save',
                        'data'  => htmlSetData(['id' => 'chat_app1']) . htmlSetData(['title' => 'Url Chat WowJual'])
                      ])
                    ?>
                  </div>

                  <label for="chat_app2">Url Chat Wowfonet<small class="invalid-errors"></small></label>
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
                        'title'  => 'Save Setting',
                        'icon'  => 'fas fa-save',
                        'text'  => 'Save',
                        'data'  => htmlSetData(['id' => 'chat_app2']) . htmlSetData(['title' => 'Url Chat Wowfonet'])
                      ])
                    ?>
                  </div>

                  <label for="chat_app3">Url Chat WowMitra<small class="invalid-errors"></small></label>
                  <div class="row">
                    <?=
                    htmlInput([
                      'id' => 'chat_app3',
                      'class' => 'saveInput',
                      'form_group' => 'col',
                      'placeholder' => 'Ex. https://wowfonenet.id/',
                      'value' => $dataSetting->chat_app3->val,
                    ]) .
                      htmlButton([
                        'color'  => 'success',
                        'class'  => 'py-2 btnAction btnSaveUrl col-auto btnSaveSetting',
                        'title'  => 'Save Setting',
                        'icon'  => 'fas fa-save',
                        'text'  => 'Save',
                        'data'  => htmlSetData(['id' => 'chat_app3']) . htmlSetData(['title' => 'Url Chat WowMitra'])
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
                      'placeholder' => 'Contoh 50.000',
                      'value' => $dataSetting->min_withdraw->val,
                    ]) .
                      htmlButton([
                        'color'  => 'success',
                        'class'  => 'py-2 btnAction btnSaveUrl col-auto btnSaveSetting',
                        'title'  => 'Save Setting',
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
                    'placeholder' => 'Terms & Conditions',
                    'value' => $dataSetting->tnc_app1->val,
                  ]) . 
                    htmlButton([
                      'color'  => 'success',
                      'class'  => 'py-2 btnAction btnSaveUrl col-auto btnSaveSettingTnc',
                      'title'  => 'Save Setting',
                      'icon'  => 'fas fa-save',
                      'text'  => 'Save',
                      'data'  => htmlSetData(['id' => 'tnc_app1']) . htmlSetData(['title' => 'Tnc Wow Jual'])
                    ], false) . 
                    htmlSummernote([
                      'id' => 'tnc_app2',
                      'label' => 'Wowfonet',
                      'class' => 'saveInput',
                      'form_group' => 'col',
                      'placeholder' => 'Terms & Conditions',
                      'value' => $dataSetting->tnc_app2->val,
                    ]) .
                    htmlButton([
                      'color'  => 'success',
                      'class'  => 'py-2 btnAction btnSaveUrl col-auto btnSaveSettingTnc',
                      'title'  => 'Save Setting',
                      'icon'  => 'fas fa-save',
                      'text'  => 'Save',
                      'data'  => htmlSetData(['id' => 'tnc_app2']) . htmlSetData(['title' => 'Tnc Wowfonet'])
                    ], false) . 
                    htmlSummernote([
                      'id' => 'tnc_app3',
                      'label' => 'WowMitra',
                      'class' => 'saveInput',
                      'form_group' => 'col',
                      'placeholder' => 'Terms & Conditions',
                      'value' => $dataSetting->tnc_app3->val,
                    ]) .
                    htmlButton([
                      'color'  => 'success',
                      'class'  => 'py-2 btnAction btnSaveUrl col-auto btnSaveSettingTnc',
                      'title'  => 'Save Setting',
                      'icon'  => 'fas fa-save',
                      'text'  => 'Save',
                      'data'  => htmlSetData(['id' => 'tnc_app3']) . htmlSetData(['title' => 'Tnc Wowfonet'])
                    ], false) . 
                    htmlSummernote([
                      'id' => 'bonus_tnc_app2',
                      'label' => 'Wowfonet Bonus',
                      'class' => 'saveInput',
                      'form_group' => 'col',
                      'placeholder' => 'Terms & Conditions',
                      'value' => $dataSetting->bonus_tnc_app2->val,
                    ]) .
                    htmlButton([
                      'color'  => 'success',
                      'class'  => 'py-2 btnAction btnSaveUrl col-auto btnSaveSettingTnc',
                      'title'  => 'Save Setting',
                      'icon'  => 'fas fa-save',
                      'text'  => 'Save',
                      'data'  => htmlSetData(['id' => 'bonus_tnc_app2']) . htmlSetData(['title' => 'Tnc Bonus Wowfonet'])
                    ], false) .
                    htmlSummernote([
                      'id' => 'short_bonus_tnc_app2',
                      'label' => 'Wowfonet Onboarding Bonus',
                      'class' => 'saveInput',
                      'form_group' => 'col',
                      'placeholder' => 'Terms & Conditions',
                      'value' => $dataSetting->short_bonus_tnc_app2->val,
                    ]) .
                    htmlButton([
                      'color'  => 'success',
                      'class'  => 'py-2 btnAction btnSaveUrl col-auto btnSaveSettingTnc',
                      'title'  => 'Save Setting',
                      'icon'  => 'fas fa-save',
                      'text'  => 'Save',
                      'data'  => htmlSetData(['id' => 'short_bonus_tnc_app2']) . htmlSetData(['title' => 'Tnc Short Bonus Wowfonet'])
                    ], false) .
                    htmlSummernote([
                      'id' => 'app3_merchant_code_help',
                      'label' => 'WowMitra Bantuan Kode Mitra',
                      'class' => 'saveInput',
                      'form_group' => 'col',
                      'placeholder' => 'Merchant Code Help',
                      'value' => $dataSetting->app3_merchant_code_help->val,
                    ]) .
                    htmlButton([
                      'color'  => 'success',
                      'class'  => 'py-2 btnAction btnSaveUrl col-auto btnSaveSettingTnc',
                      'title'  => 'Save Setting',
                      'icon'  => 'fas fa-save',
                      'text'  => 'Save',
                      'data'  => htmlSetData(['id' => 'app3_merchant_code_help']) . htmlSetData(['title' => 'WowMitra Bantuan Kode Mitra'])
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
      height: 300,
      minHeight: null,
      maxHeight: null,
      focus: true,
    });
    $('#tnc_app3').summernote({
      height: 300,
      minHeight: null,
      maxHeight: null,
      focus: true,
    });

    $('#bonus_tnc_app2').summernote({
      height: 300,
      minHeight: null,
      maxHeight: null,
      focus: true,
    });

    $('#short_bonus_tnc_app2').summernote({
      height: 300,
      minHeight: null,
      maxHeight: null,
      focus: true,
    });

    $('#app3_merchant_code_help').summernote({
      height: 300,
      minHeight: null,
      maxHeight: null,
      focus: true,
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