<div class="content">
    <?= form_open("mod_rekanan/rekanan_add"); ?>
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="basic box_title"><h4><span>#</span> Rekanan Add</h4></div>
                <div class="basic box_content form-horizontal">
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="kode_rekanan">Kode Rekanan</label>
                            <div class="controls">
                                <input class="span8 text" type="text" id="kode_rekanan" name="kode_rekanan" value="<?= set_value('kode_rekanan'); ?>" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="nama_rekanan">Nama Rekanan</label>
                            <div class="controls">
                                <input class="span8 text" type="text" id="nama_rekanan" name="nama_rekanan" value="<?= set_value('nama_rekanan'); ?>" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="nama_kontak">Nama Kontak</label>
                            <div class="controls">
                                <input class="span8 text" type="text" id="nama_kontak" name="nama_kontak" value="<?= set_value('nama_kontak'); ?>" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="telp_rekanan">Telp. Perusahaan</label>
                            <div class="controls">
                                <input class="span8 text" type="text" id="telp_rekanan" name="telp_rekanan" value="<?= set_value('telp_rekanan'); ?>" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="alamat">Alamat</label>
                            <div class="controls">
                                <input class="span8 text" type="text" id="alamat" name="alamat" value="<?= set_value('alamat'); ?>" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="telp_kontak">Telp. Kontak</label>
                            <div class="controls">
                                <input class="span8 text" type="text" id="telp_kontak" name="telp_kontak" value="<?= set_value('telp_kontak'); ?>" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="kota">Kota</label>
                            <div class="controls">
                                <input class="span8 text" type="text" id="kota" name="kota" value="<?= set_value('kota'); ?>" autocomplete="off" />
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="type_rekanan">Tipe</label>
                            <div class="controls">
                                <?= form_dropdown('type_rekanan', $type_rekanan, set_value('type_rekanan'), 'id="type_rekanan"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="type_rekanan">Kode Perkiraan</label>
                            <div class="controls">
                                <?= form_multiselect('kode_perkiraan[]', $kode_perkiraan, "", 'class="span8 chzn-select"'); ?>
                            </div>
                        </div>
                    </div>
					
                    <div class="row-fluid">
                        <div class="control-group info">
                            <div class="controls">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-info" name="form_rekanan_save"><i class="icon-ok-sign icon-white"></i> Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= form_close(); ?>
</div>
<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url(); ?>assets/choosen/chosen.css" />
<script type="text/javascript" src="<?= base_url(); ?>assets/choosen/chosen.jquery.js"></script>

<script type="text/javascript">
    function formRekananAddClear() {
        $('input[name="kode_rekanan"]').val("");
        $('input[name="nama_rekanan"]').val("");
        $('input[name="nama_kontak"]').val("");
        $('input[name="telp_rekanan"]').val("");
        $('input[name="alamat"]').val("");
        $('input[name="telp_kontak"]').val("");
        $('input[name="kota"]').val("");
        $('select[name="type_rekanan"]').val("0");
        $('.chzn-select').chosen().val("").trigger("liszt:updated");
    }

    $(document).ready(function() {
        $('.chzn-select').chosen({});

        $('button[name="form_rekanan_save"]').bind('click', function() {
            $.ajax({
                url: root + 'mod_rekanan/rekanan_add',
                type: 'post',
                dataType: 'json',
                data: $("form").serialize(),
                beforeSend: function() {
                    $(this).attr('disabled', false);
                },
                complete: function() {
                    $(this).attr('disabled', false);
                },
                success: function(json) {
                    $('div.alert').remove();
                    if (json['error']) {
                        $('.content').prepend('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>' + json['error'] + '</div>');
                        $('div.alert').fadeIn('slow');
                    }
                    if (json['success']) {
                        $('.content').prepend('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>' + json['success'] + '</div>');
                        $('div.alert').fadeIn('slow');
                        formRekananAddClear();
                    }
                    scrollup();
                    createAutoClosingAlert('div.alert', 3000);
                }
            });
        });
    });
</script>

