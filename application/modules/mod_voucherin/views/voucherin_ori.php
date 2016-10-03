<div class="content form_voucherin">
    <?= form_open(); ?>
    <input type="hidden" name="form_voucherin_id" value="" />
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="blue box_title"><h4><span>#</span> Voucher In</h4></div>
                <div class="blue box_content form-horizontal">
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="form_voucherin_tanggal">Tanggal</label>
                            <div class="controls">
                                <input type="text" id="form_voucherin_tanggal" name="form_voucherin_tanggal" class="datepicker" autocomplete="off"/>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="form_voucherin_jenis">Jenis Transaksi</label>
                            <div class="controls">
                                <select name="form_voucherin_jenis" id="form_voucherin_jenis">
                                    <option value="K">Kas</option>
                                    <option value="B">Bank</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- Begin Debet -->
                    <input type="hidden" name="form_voucherin_debet_kode" />
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="form_voucherin_debet">Debet</label>
                            <div class="controls">
                                <div class="input-append">
                                    <input class="input-medium" id="form_voucherin_debet" type="text" name="form_voucherin_debet">
<!--                                    <button class="btn btn-info" type="button"><i class="icon-search icon-white"></i></button>-->
                                </div>                                
                                <label class="help-inline" id="form_voucherin_debet_label"></label>
                            </div>
                        </div>
                    </div>
                    <!-- End Debet -->
                    <!-- Begin Kredit -->
                    <input type="hidden" name="form_voucherin_kredit_kode" />
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="form_voucherin_kredit">Kredit</label>
                            <div class="controls">
                                <div class="input-append">
                                    <input class="input-medium" id="form_voucherin_kredit" type="text" name="form_voucherin_kredit">
<!--                                    <button class="btn btn-info" type="button"><i class="icon-search icon-white"></i></button>-->
                                </div>
                                <label class="help-inline" id="form_voucherin_kredit_label"></label>
                            </div>
                        </div>
                    </div>
                    <!-- End Kredit -->
                    <!-- Begin Kredit Rekanan-->
                    <input type="hidden" name="form_voucherin_kredit_rekanan_kode" />
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="form_voucherin_kredit_rekanan">Rekanan</label>
                            <div class="controls">
                                <div class="input-append">
                                    <input class="input-medium" id="form_voucherin_kredit_rekanan" type="text" name="form_voucherin_kredit_rekanan">
                                    <button class="btn btn-primary" type="button"><i class="icon-search icon-white"></i></button>
                                </div>
                                <label class="help-inline" id="form_voucherin_kredit_label_rekanan"></label>
                            </div>
                        </div>
                    </div>
                    <!-- End Kredit Rekanan-->
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="form_voucherin_keterangan">Keterangan</label>
                            <div class="controls">
                                <input type="text" class="span10" id="form_voucherin_keterangan" name="form_voucherin_keterangan" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="form_voucherin_nilai">Nilai</label>
                            <div class="controls">
                                <input type="text" id="form_voucherin_nilai" name="form_voucherin_nilai" class="input_number text-right" autocomplete="off">
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <div class="controls">
                                <div class="btn-group">
                                    <button type="button" name="form_voucherin_tambah" class="btn btn-primary"><i class="cus-table-add"></i>Tambah</button>
                                    <button type="button" name="form_voucherin_batal" class="btn btn-primary"><i class="cus-cancel"></i>Batal</button>
                                    <button type="button" name="form_voucherin_simpan" class="btn btn-primary"><i class="cus-table-save"></i>Simpan</button>
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
            <table id="list2"></table>
            <div id="pager2"></div>
        </div>
    </div>
    <?= form_close(); ?>
</div>
<script type="text/javascript">

    function voucherin_getSession(id) {
        $.ajax({
            url: root + 'mod_voucherin/getSessionId',
            dataType: 'json',
            type: 'post',
            data: {id: id},
            success: function(json) {
                $('div.alert').remove();
                $('input[name="form_voucherin_id"]').val(json['record']["id"]);
                $('input[name="form_voucherin_debet"]').val(json['record']["debet_kode"]);
                $('input[name="form_voucherin_debet_kode"]').val(json['record']["debet_kode"]);
                $('label#form_voucherin_debet_label').html(json['record']["debet_label"]);
                $('input[name="form_voucherin_kredit"]').val(json['record']["kredit_kode"]);
                $('input[name="form_voucherin_kredit_kode"]').val(json['record']["kredit_kode"]);
                $('label#form_voucherin_kredit_label').html(json['record']["kredit_label"]);
                $('input[name="form_voucherin_kredit_rekanan"]').val(json['record']["kredit_rekanan_kode"]);
                $('input[name="form_voucherin_kredit_rekanan_kode"]').val(json['record']["kredit_rekanan_kode"]);
                $('label#form_voucherin_kredit_label_rekanan').html(json['record']["kredit_rekanan_label"]);
                $('input[name="form_voucherin_keterangan"]').val(json['record']["keterangan"]);
                $('input[name="form_voucherin_nilai"]').val(json['record']["nilai"]);
            }
        });
    }

    function form_voucherin_clear_session() {
        $.ajax({
            url: root + "mod_voucherin/cleanTransaksi",
            dataType: 'json',
            type: 'post',
            success: function(json) {
                form_voucherin_refresh_grid();
            }
        });
    }

    function form_voucherin_clear() {
        $('div.alert').remove();
        $('input[name="form_voucherin_id"]').val("");
        $('input[name="form_voucherin_debet"]').val("");
        $('input[name="form_voucherin_debet_kode"]').val("");
        $('label#form_voucherin_debet_label').html("");
        $('input[name="form_voucherin_kredit"]').val("");
        $('input[name="form_voucherin_kredit_kode"]').val("");
        $('label#form_voucherin_kredit_label').html("");
        $('input[name="form_voucherin_keterangan"]').val("");
        $('input[name="form_voucherin_nilai"]').val("");
        $('input[name="form_voucherin_kredit_rekanan"]').val("");
        $('input[name="form_voucherin_kredit_rekanan_kode"]').val("");
        $('label#form_voucherin_kredit_label_rekanan').html("");
    }

    function form_voucherin_refresh_grid() {
        jQuery("#list2").jqGrid('setGridParam', {
            url: root + mod + '/sess2json',
            page: 1
        }).trigger("reloadGrid");
    }

    $(function() {
        $('#form_voucherin_delete').click(function() {
            var id = $('input[class="jq_checkbox_added"]:checked').map(function() {
                return $(this).val();
            }).get();

            $.ajax({
                url: root + "mod_voucherin/deletejurnal",
                dataType: 'json',
                type: 'post',
                data: {id: id},
                success: function(json) {
                    $('div.alert').remove();
                    if (json['error']) {
                        $('.content').prepend('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>' + json['error'] + '</div>');
                        $('div.alert').fadeIn('slow');
                    }

                    if (json['success']) {
                        $('.content').prepend('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>' + json['success'] + '</div>');
                        $('div.alert').fadeIn('slow');
                        form_voucherin_refresh_grid();
                    }
                }
            });
        });

        $('select[name="form_voucherin_jenis"]').change(function() {
            var jenis = $(this).val();
            var cat = $('select[name="form_voucherin_jenis"]').val();
            $('input[name="form_voucherin_debet"]').val("");
            $('input[name="form_voucherin_debet_kode"]').val("");
            $('label#form_voucherin_debet_label').html("");
            $('input[name="form_voucherin_debet"]').autocomplete("option", {
                source: root + "mod_kdperkiraan/autocomplete_kodeperkiraan?_search=true&cat=" + cat
            });
            form_voucherin_clear_session();
        });

        $('input.input_number').number(true, 2);

        $(".datepicker").datepicker({
            showOn: "button",
            buttonImage: root + "images/calendar.gif",
            dateFormat: 'yy-mm-dd',
            buttonImageOnly: true,
            changeMonth: true,
            changeYear: true
        });

        $('button[name="form_voucherin_tambah"]').click(function() {
            var form_voucherin_id = $('input[name="form_voucherin_id"]').val();
            var form_voucherin_tanggal = $('input[name="form_voucherin_tanggal"]').val();
            var form_voucherin_jenis = $('select[name="form_voucherin_jenis"]').val();
            var form_voucherin_debet_kode = $('input[name="form_voucherin_debet_kode"]').val();
            var form_voucherin_debet_label = $('label#form_voucherin_debet_label').html();
            var form_voucherin_kredit_kode = $('input[name="form_voucherin_kredit_kode"]').val();
            var form_voucherin_kredit_label = $('label#form_voucherin_kredit_label').html();
            var form_voucherin_keterangan = $('input[name="form_voucherin_keterangan"]').val();
            var form_voucherin_nilai = $('input[name="form_voucherin_nilai"]').val();
            var form_voucherin_kredit_rekanan_kode = $('input[name="form_voucherin_kredit_rekanan_kode"]').val();
            var form_voucherin_kredit_label_rekanan = $('label#form_voucherin_kredit_label_rekanan').html();

            $.ajax({
                url: root + "mod_voucherin/add",
                dataType: 'json',
                type: 'post',
                data: {
                    form_voucherin_id: form_voucherin_id,
                    form_voucherin_tanggal: form_voucherin_tanggal,
                    form_voucherin_jenis: form_voucherin_jenis,
                    form_voucherin_debet_kode: form_voucherin_debet_kode,
                    form_voucherin_debet_label: form_voucherin_debet_label,
                    form_voucherin_kredit_kode: form_voucherin_kredit_kode,
                    form_voucherin_kredit_label: form_voucherin_kredit_label,
                    form_voucherin_keterangan: form_voucherin_keterangan,
                    form_voucherin_nilai: form_voucherin_nilai,
                    form_voucherin_kredit_rekanan_kode: form_voucherin_kredit_rekanan_kode,
                    form_voucherin_kredit_label_rekanan: form_voucherin_kredit_label_rekanan
                },
                beforeSend: function() {
                    $(this).attr('disabled', true);
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
                        form_voucherin_clear();
                        form_voucherin_refresh_grid();
                    }
                }
            });
        });

        $('button[name="form_voucherin_batal"]').click(function() {
            form_voucherin_clear();
            form_voucherin_refresh_grid();

        });

        $('button[name="form_voucherin_simpan"]').click(function() {
            var form_voucherin_tanggal = $('input[name="form_voucherin_tanggal"]').val();
            var form_voucherin_jenis = $('select[name="form_voucherin_jenis"]').val();
            $.ajax({
                url: root + "mod_voucherin/addJurnal",
                dataType: 'json',
                type: 'post',
                data: {
                    form_voucherin_tanggal: form_voucherin_tanggal,
                    form_voucherin_jenis: form_voucherin_jenis
                },
                beforeSend: function() {
                    $(this).attr('disabled', true);
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
                        form_voucherin_refresh_grid();
                        form_voucherin_clear();
                    }
                }
            });
        });

        $('input[name="form_voucherin_debet"]').autocomplete({
            minLength: 2,
            source: root + "mod_kdperkiraan/autocomplete_kodeperkiraan?_search=true&cat=" + $('select[name="form_voucherin_jenis"]').val(),
            create: function(event, ui) {
                $('input[name="form_voucherin_debet_kode"]').val("");
                $('input[name="form_voucherin_debet"]').val("");
                $('label#form_voucherin_debet_label').html("");
                return false;
            },
            search: function(event, ui) {
                $('input[name="form_voucherin_debet_kode"]').val("");
                $('label#form_voucherin_debet_label').html("");
            },
            select: function(event, ui) {
                if (ui.item.id != 0) {
                    $('input[name="form_voucherin_debet_kode"]').val(ui.item.id);
                    $('input[name="form_voucherin_debet"]').val(ui.item.id);
                    $('label#form_voucherin_debet_label').html(ui.item.label);
                }
                return false;
            }
        });

        $('input[name="form_voucherin_kredit"]').autocomplete({
            minLength: 2,
            source: root + "mod_kdperkiraan/autocomplete_kodeperkiraan",
            create: function(event, ui) {
                $('input[name="form_voucherin_kredit_kode"]').val("");
                $('input[name="form_voucherin_kredit"]').val("");
                $('label#form_voucherin_kredit_label').html("");
                return false;
            },
            search: function(event, ui) {
                $('input[name="form_voucherin_kredit_kode"]').val("");
                $('label#form_voucherin_kredit_label').html("");
            },
            select: function(event, ui) {
                $('input[name="form_voucherin_kredit_kode"]').val(ui.item.id);
                $('input[name="form_voucherin_kredit"]').val(ui.item.id);
                $('label#form_voucherin_kredit_label').html(ui.item.label);
                return false;
            }
        });

        // Begin Autocomplete Kredit Rekanan
        $('input[name="form_voucherin_kredit_rekanan"]').autocomplete({
            minLength: 2,
            source: root + "mod_rekanan/autocomplete_rekanan",
            open: function(event, ui) {
                $('.ui-autocomplete').append('<li><a class="btn" onClick="showUrlInDialog(\'' + root + 'mod_rekanan/popup_add\');" href="#"><i class="cus-application-form-add"></i> Tambah</a></li>');
            },
            create: function(event, ui) {
                $('input[name="form_voucherin_kredit_rekanan_kode"]').val("");
                $('input[name="form_voucherin_kredit_rekanan"]').val("");
                $('label#form_voucherin_kredit_label_rekanan').html("");
                return false;
            },
            search: function(event, ui) {
                $('input[name="form_voucherin_kredit_rekanan_kode"]').val("");
                $('label#form_voucherin_kredit_label_rekanan').html("");
            },
            select: function(event, ui) {
                $('input[name="form_voucherin_kredit_rekanan_kode"]').val(ui.item.id);
                $('input[name="form_voucherin_kredit_rekanan"]').val(ui.item.id);
                $('label#form_voucherin_kredit_label_rekanan').html(ui.item.label);
                return false;
            }
        });

        var lebar = $('.inbody').height() - 120;
        var panjang = $('.content').width() - 20;

        jQuery("#list2").jqGrid({
            url: root + mod + '/sess2json',
            mtype: "post",
            datatype: "json",
            colNames: ['#', '', "<div id='jq_checkbox_head_added'><div>", 'kode_perkiraan', 'Kode Perkiraan', 'kode_rekanan', 'Rekanan', 'Debet', 'Kredit', 'Keterangan'],
            colModel: [
                {name: 'act', index: 'act', width: 20, sortable: false, align: "center"},
                {name: 'id', key: true, index: 'id', hidden: true, width: 20},
                {name: 'check', index: 'check', width: 20, sortable: false, align: "center"},
                {name: 'kode_perkiraan', index: 'kode_perkiraan', hidden: true, width: 200},
                {name: 'kode_perkiraan_label', index: 'kode_perkiraan_label', hidden: false, width: 200},
                {name: 'kode_rekanan', index: 'kode_rekanan', hidden: true, width: 200},
                {name: 'kode_rekanan_label', index: 'kode_rekanan_label', hidden: false, width: 200},
                {name: 'debet', index: 'debet', width: 200, align: "right", formatter: 'currency', formatoptions: {thousandsSeparator: ","}},
                {name: 'kredit', index: 'kredit', width: 200, align: "right", formatter: 'currency', formatoptions: {thousandsSeparator: ","}},
                {name: 'uraian', index: 'uraian', width: 300}
            ],
            scroll: true,
            width: panjang,
            height: lebar,
            rownumbers: true,
            rowNum: 1000,
            rownumWidth: 40,
            multiselect: false,
            pager: '#pager2',
            viewrecords: true,
            footerrow: true,
            userDataOnFooter: true,
            altRows: true,
            shrinkToFit: false,
            gridComplete: function() {
                var ids = jQuery("#list2").jqGrid('getDataIDs');
                for (var i = 0; i < ids.length; i++) {
                    var cl = ids[i];
                    ce = "<a href=\"#\" onclick=\"voucherin_getSession(" + ids[i] + ");\" class=\"link_edit\"><img  src=\"<?= base_url(); ?>media/edit.png\" /></a>";
                    jQuery("#list2").jqGrid('setRowData', ids[i], {act: ce});
                }
            }
            //caption:"List Proyek"
        });
        jQuery("#list2").jqGrid('navGrid', '#pager2', {edit: false, add: false, del: false, search: false});

        $('div#jq_checkbox_head_added').prepend('<div class="checkicon_add"><image src="' + root + 'uncheckbox.gif" /></div>');
        $('div#jq_checkbox_head_added').removeClass('selected');

        $('div#jq_checkbox_head_added').click(function() {
            $('.checkicon_add').remove();

            if ($('div#jq_checkbox_head_added').hasClass('selected')) {
                $('div#jq_checkbox_head_added').removeClass('selected');
                $('div#jq_checkbox_head_added').prepend('<div class="checkicon_add"><image src="' + root + 'uncheckbox.gif" /></div>');
                $('.jq_checkbox_added').each(function() {
                    this.checked = false;
                });
            }
            else {
                $('div#jq_checkbox_head_added').addClass('selected')
                $('div#jq_checkbox_head_added').prepend('<div class="checkicon_add"><image src="' + root + 'checkbox.gif" /></div>');
                $('.jq_checkbox_added').each(function() {
                    this.checked = true;
                });
            }
        });
    });
</script>