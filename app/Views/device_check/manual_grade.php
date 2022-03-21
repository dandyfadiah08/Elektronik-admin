<?= $this->extend('layouts/template') ?>
<?= $this->section('content') ?>

  <!-- Modal Manual Grade -->
  <div class="modal" tabindex="-1" id="modalManualGrade">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <span>Manual Grade</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form>
            <div class="form-group">
              <label for="grade">Grade <small class="invalid-errors"></small></label>
              <select id="grade" data-placeholder="Choose Grade" class="form-control select2bs4 myfilter">
                <option></option>
                <option value="S">S</option>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
                <option value="E">E</option>
                <option value="Reject">Reject</option>
              </select>
            </div>
            <div class="form-group mb-0">
              <label for="fullset">Kelengkapan <small class="invalid-errors"></small></label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="fullset" id="fullset-1" value="1">
              <label class="form-check-label" for="fullset-1">Fullset</label>
            </div>
            <div class="form-check form-check-inline">
              <input class="form-check-input" type="radio" name="fullset" id="fullset-0" value="0">
              <label class="form-check-label" for="fullset-0">Unit Only</label>
            </div>
            <div class="form-group">
              <label for="damage">Kerusakan <small class="invalid-errors"></small></label>
              <select id="damage" data-placeholder="Choose/Write Damages" class="form-control select2bs4 myfilter">
                <option></option>
                <?php
                  $damages = getGradeDamages();
                  for ($i=0; $i < count($damages); $i++) {
                    echo "<option value=\"$damages[$i]\" data-index=\"$i\">$damages[$i]</option>";
                  }
                ?>
              </select>
            </div>
            <div class="form-group" id="damage-other-wrapper" style="display: none;">
              <lable for="damage-other">Damage (write) <small class="invalid-errors"></small></lable>
              <textarea id="damage-other" rows="5" class="form-control" placeholder="Write damage here..."></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnManualGrade" disabled>Give Grade</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Retry Photo -->
  <div class="modal" tabindex="-1" id="modalRetryPhoto">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <span>Retry Photo</span>
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <form>
            <div class="form-group">
              <label for="photos">Photo to Retry <small class="invalid-errors"></small></label>
              <select id="photos" data-placeholder="Choose Photo to retry" class="form-control select2bs4 myfilter" multiple="multiple">
                <?php
                  $names = getRetryPhotoNames();
                  for ($i=1; $i < count($names); $i++) {
                    echo "<option value=\"$i\">$names[$i]</option>";
                  }
                ?>
              </select>
            </div>
            <div class="form-group">
              <label for="reason">Reason <small class="invalid-errors"></small></label>
              <select id="reason" data-placeholder="Choose/Write Damages" class="form-control select2bs4 myfilter">
                <option></option>
                <?php
                  $reason = getRetryPhotoReasons();
                  for ($i=0; $i < count($reason); $i++) {
                    echo "<option value=\"$reason[$i]\" data-index=\"$i\">$reason[$i]</option>";
                  }
                ?>
              </select>
            </div>
            <div class="form-group" id="reason-other-wrapper" style="display: none;">
              <lable for="reason-other">Reason (write) <small class="invalid-errors"></small></lable>
              <textarea id="reason-other" rows="5" class="form-control" placeholder="Write reason here..."></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnRetryPhoto" disabled>Request Retry Photo</button>
        </div>
      </div>
    </div>
  </div>

<?= $this->endSection('content') ?>


<?= $this->section('content_css') ?>
<!-- DataTables -->
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/adminlte3/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">
<?= $this->endSection('content_css') ?>


<?= $this->section('content_js') ?>
<script src="<?= base_url() ?>/assets/adminlte3/plugins/select2/js/select2.full.min.js"></script>

<script>
  const INPUT_MANUAL_GRADE = ['grade', 'fullset', 'damage', 'damage-other']
  const INPUT_RETRY_PHOTO = ['photos', 'reason', 'reason-other']
  const REQUIRED_MSG = 'harus diisi'
  const REQUIRED_MSG_2 = 'harus dipilih'
  $(document).ready(function() {
    $('.select2bs4').select2({
      theme: 'bootstrap4',
      placeholder: $(this).data('placeholder')
    })

    $('#btnManualGrade').click(function() {
      clearErrors(INPUT_MANUAL_GRADE)
      manualGrade();
    })

    async function manualGrade() {
      const thisHTML = btnOnLoading('#btnManualGrade');
      var grade = $('#grade option:selected').val();
      var fullset = $('input[name="fullset"]:checked').val();
      let damage = getDamageValue()

      $('#btnManualGrade').html(`<i class="fas fa-spinner fa-spin"></i> Doing magic..`)

      Swal.fire({
        title: `You are going to add grade: ${grade} - ${fullset == 1 ? 'Fullset' : 'Unit Only'}`,
        html: `Damage:<br>${damage}<br><br>Click <b>Give Grade</b> to proceed, <br><b>Change</b> to change grade, or<br><b>Close</b> to cancel reviewing`,
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: `Give Grade`,
        denyButtonText: `Change`,
        cancelButtonText: `Close`,
      }).then((result) => {
        if (result.isConfirmed) {
          var url = '<?= base_url('device_check/manual_grade'); ?>';
          $.ajax({
            data: {
              check_id: <?= $dc->check_id ?>,
              grade: grade,
              fullset: fullset,
              damage: damage,
            },
            type: 'POST',
            dataType: 'JSON',
            url: url
          }).done(function(response) {
            var class_swal = response.success ? 'success' : 'error';
            playSound()
            if (response.success) changeCountBadge('unreviewed_count', false);
            Swal.fire(response.message, '', class_swal).then(function() {
              if (response.success) {
                window.location.reload();
              }
            })
          }).fail(function(e) {
            Swal.fire('An error occured!', '', 'error')
            console.log(e);
          }).always(function() {
            btnOnLoading('#btnManualGrade', false, thisHTML)
            checkInputManualGrade()
          })
        } else if (result.isDismissed) {
          btnOnLoading('#btnManualGrade', false, thisHTML)
          $('#modalManualGrade').modal('hide');
          return false;
        } else {
          // change
          btnOnLoading('#btnManualGrade', false, thisHTML)
          checkInputManualGrade()
        }
      });

    }

    $('#modalManualGrade').on('show.bs.modal', function() {
      checkInputManualGrade(true)
    });
    
    $('#grade, input[name="fullset"], #damage').on('change', () => checkInputManualGrade());
    $('#damage-other').on('keyup', () => checkInputManualGrade());

    function checkInputManualGrade(first = false) {
      var grade = $('#grade option:selected').val();
      var fullset = $('input[name="fullset"]:checked').val();
      let damage = getDamageValue()
      const damagesIndex = $('#damage option:selected').data('index')
      let disabled = true
      clearErrors(INPUT_MANUAL_GRADE)

      if(first) return $('#btnManualGrade').prop('disabled', disabled);

      if(grade === '') {
        inputError('grade', REQUIRED_MSG_2);
      } else if(fullset === undefined) {
        inputError('fullset', REQUIRED_MSG_2);
      } else if(damage == '' && damagesIndex == 1) {
        inputError('damage-other', REQUIRED_MSG);
      }
      else if(damage == '' && damagesIndex > 1) {
        inputError('damage', REQUIRED_MSG_2);
      } else disabled = false

      $('#btnManualGrade').prop('disabled', disabled);
    }

    $('#damage').on('change', function() {
      // const index = $('#damage option:selected').data('index')
      // let value = $('#damage').val()
      // if(index == 1) {
      //   $('#damage-other-wrapper').show()
      // } else {
      //   $('#damage-other-wrapper').hide()
      // }
      valueChange('damage')
    })

    function getDamageValue() {
      return getValue('damage')
    }

    /**
     * 
     * Retry Photo 
     * 
     */

    $('#btnRetryPhoto').click(function() {
      clearErrors(INPUT_RETRY_PHOTO)
      retryPhoto();
    })

    async function retryPhoto() {
      const btn = '#btnRetryPhoto'
      const thisHTML = btnOnLoading(btn);
      var photos = $('#photos').val();
      console.log(typeof photos, photos);
      let reason = getReasonValue()
      
      Swal.fire({
        title: `You are going to request retry photo:<br>`,
        html: `<b>Photo</b><br>: ${getPhotoNames(photos)}<br><b>Reason</b>:<br>${reason}<br><br>Click <b>Give Grade</b> to proceed, <br><b>Change</b> to change grade, or<br><b>Close</b> to cancel reviewing`,
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: `Request Retry Photo`,
        denyButtonText: `Change`,
        cancelButtonText: `Close`,
      }).then((result) => {
        if (result.isConfirmed) {
          var url = '<?= base_url('device_check/retry_photo'); ?>';
          $.ajax({
            data: {
              check_id: <?= $dc->check_id ?>,
              photos: photos,
              reason: reason,
            },
            type: 'POST',
            dataType: 'JSON',
            url: url
          }).done(function(response) {
            var class_swal = response.success ? 'success' : 'error';
            playSound()
            if (response.success) changeCountBadge('unreviewed_count', false);
            Swal.fire(response.message, '', class_swal).then(function() {
              if (response.success) {
                window.location.reload();
              }
            })
          }).fail(function(e) {
            Swal.fire('An error occured!', '', 'error')
            console.log(e);
          }).always(function() {
            btnOnLoading(btn, false, thisHTML)
            checkInputRetryPhoto()
          })
        } else if (result.isDismissed) {
          btnOnLoading(btn, false, thisHTML)
          $('#modalRetryPhoto').modal('hide');
          return false;
        } else {
          // change
          btnOnLoading(btn, false, thisHTML)
          checkInputRetryPhoto()
        }
      });
    }

    $('#modalRetryPhoto').on('show.bs.modal', function() {
      setTimeout(() => {
        $("#photos").val(null).change() // to show placeholder
        clearErrors(['photos']) // to clear errors because change is triggered
      }, 50);
      checkInputRetryPhoto(true)
    });
    $('#photos, #reason').on('change', () => checkInputRetryPhoto());
    $('#reason-other').on('keyup', () => checkInputRetryPhoto());
    function checkInputRetryPhoto(first = false) {
      const photos = $('#photos option:selected').val()
      let reason = getReasonValue()
      const reasonIndex = $('#reason option:selected').data('index')
      let disabled = true
      clearErrors(INPUT_RETRY_PHOTO)

      if(first) return $('#btnRetryPhoto').prop('disabled', disabled);

      if(photos === undefined) {
        inputError('photos', REQUIRED_MSG_2);
      } else if(reason == '' && reasonIndex == 0) {
        inputError('reason-other', REQUIRED_MSG);
      }
      else if(reason == '' && (reasonIndex > 0 || reasonIndex === undefined)) {
        inputError('reason', REQUIRED_MSG_2);
      } else disabled = false

      console.log({reason: reason, reasonIndex: reasonIndex});

      $('#btnRetryPhoto').prop('disabled', disabled);
    }

    $('#reason').on('change', function() {
      valueChange('reason', 0)
    })

    function getReasonValue() {
      return getValue('reason', false, 0)
    }


    function valueChange(id, indexOther = 1) {
      const index = $('#'+id+' option:selected').data('index')
      let value = $('#'+id).val()
      if(index == indexOther) {
        $('#'+id+'-other-wrapper').show()
      } else {
        $('#'+id+'-other-wrapper').hide()
      }
    }
    
    function getValue(id, indexEmpty = 0, indexOther = 1) {
      const index = $('#'+id+' option:selected').data('index')
      if(index === indexOther) return $('#'+id+'-other').val()
      else if(index === indexEmpty) return ""
      else return $('#'+id).val()
    }

    function getPhotoNames(ids) {
      let names = []
      ids.forEach(id => {
        names.push("Photo "+getPhotoName(id))
      });
      return names.join(", ")
    }
    function getPhotoName(id) {
      const names = [
        "",
        <?php
          for ($i=1; $i < count($names); $i++) {
            echo "\"$names[$i]\", ";
          }
        ?>
      ]
      return names[id] ?? id
    }
  });
</script>
<?= $this->endSection('content_js') ?>