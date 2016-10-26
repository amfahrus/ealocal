
<div class="content form_saldoawal">
    <?= form_open_multipart('mod_saldo/uploading'); ?>
    <input type="hidden" name="form_saldoawal_id" value="" />

    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="basic box_title"><h4><span>#</span> Input Saldo Awal</h4></div>
                <div class="basic box_content form-horizontal">


                <div class="row-fluid">
                  <div class="span12">
                    <div class="row-fluid">
                      <label class="form-label span4">Template Form Upload</label>
                      <div class="span8 text">
                          <a href="<?= base_url().'files/form_upload_saldo_awal.xlsx' ?>" class="btn btn-info"><i class="cus-page-excel"></i>Download</a>
                      </div>
                    </div>
                  </div>
                </div>

								<div class="row-fluid">
									<div class="span12">
										<div class="row-fluid">
											<label class="form-label span4">Pilih Periode</label>
											<div class="span8 text">
													<?= form_dropdown('periode_year', $op_yearperiode, set_value('periode_year'), 'class="span8"'); ?>
											</div>
										</div>
									</div>
								</div>

								<div class="row-fluid">
									<div class="span12">
										<div class="row-fluid">
											<label class="form-label span4">Pilih Periode Akunting</label>
											<div class="span8 text">
													<div id="periode"></div>
											</div>
										</div>
									</div>
								</div>

                <div class="row-fluid">
                  <div class="span12">
                    <div class="row-fluid">
                        <label class="form-label span4" for="form_importdbf_fileupload">Upload</label>
                        <div class="span8 text">
                            <input type="file" name="userfile" id="form_importdata_fileupload" />
                        </div>
                    </div>
                  </div>
                </div>

                    <div class="row-fluid">
                        <div class="control-group">
                            <div class="controls">
                                <div class="btn-group">
                                    <button type="submit" name="form_saldoawal_tambah" class="btn btn-success"><i class="cus-table-save"></i>Upload</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12">
            <table id="form_saldoawal_list"></table>
            <div id="form_saldoawal_page"></div>
        </div>
    </div>
    <?= form_close(); ?>
</div>
<script>
function getDataPeriode(id) {
      $.ajax({
          url: root + 'mod_saldo/getDataPeriode',
          type: 'post',
          data: { id: id, csrf_eadev_client: csrf_hash},
          success: function(data) {
              $('#periode').html(data);
          }
      });
  }

  $('select[name="periode_year"]').change(function() {
      var id =  $('select[name="periode_year"]').val();
      getDataPeriode(id);
  });

  $(function() {
      var id_periodeyear = $('select[name="periode_year"]').val();
      getDataPeriode(id_periodeyear);

  });
</script>
