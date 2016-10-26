<div class="content form_voucherin_list_jurnal">
    <?= form_open(); ?>
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="blue box_title"><h4><span>#</span> Voucher In</h4></div>
                <div class="blue box_content form_search" ></div>
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
<?= $searchform; ?>

<link rel="stylesheet" type="text/css" href="<?= base_url(); ?>assets/msdropdown/css/msdropdown/dd.css" />
<script src="<?= base_url(); ?>assets/msdropdown/js/msdropdown/jquery.dd.js"></script>
<script type="text/javascript">
    function edit_jurnal(gid) {
        $.ajax({
            url: root + 'mod_voucherin/edit_jurnal',
            type: 'post',
            dataType: 'json',
            data: {
                nobukti: gid,
                csrf_eadev_client: csrf_hash
            },
            beforeSend: function() {
                $(this).attr('disabled', true);
            },
            complete: function() {
                $(this).attr('disabled', true);
            },
            success: function(json) {
                $('div.alert').remove();
                if (json['error']) {
                    $('.content').prepend('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>' + json['error'] + '</div>');
                    $('div.alert').fadeIn('slow');

                    scrollup();
                    createAutoClosingAlert('div.alert', 3000);
                }
                if (json['success']) {
                    $('.content').prepend('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>' + json['success'] + '</div>');
                    $('div.alert').fadeIn('slow');
                }

                if (json['redirect']) {
                    location = json['redirect'];
                }
            }
        });
    }

    function form_refresh_grid() {
        var data = $("form").serialize();
        jQuery("#list2").jqGrid('setGridParam', {
            url: root + 'mod_voucherin/JurnalToJson?_search=true&' + data,
            page: 1
        }).trigger("reloadGrid");
    }

    function getCOA(id) {
        $('#ivansearch_val_' + id).remove();
        var _rowval = $('#row-val-' + id);
        var _textautocomplete = $('<input type="text" class="span12 text ivansearch_val" id="ivansearch_val_' + id + '" />');
        _textautocomplete.autocomplete({
            minLength: 2,
            source: root + "mod_kdperkiraan/autocomplete_kodeperkiraan",
            select: function(event, ui) {
                if (ui.item.id != 0) {
                    _textautocomplete.val(ui.item.id);
                }
                return false;
            }
        });
        _rowval.append(_textautocomplete);
    }

    function getperiod(id, selectedvalue) {
        selectedvalue = typeof selectedvalue !== 'undefined' ? selectedvalue : '';

        $.ajax({
            url: root + "main/getjsonperiod",
            dataType: 'json',
            type: 'post',
            data: {csrf_eadev_client: csrf_hash},
            success: function(json) {
                $('#ivansearch_val_' + id).msDropDown({byJson: {data: json, name: 'vals[]'}}).data("dd").setIndexByValue(selectedvalue);
            }
        });
    }

    function getBoolean(id, selectedvalue) {
        selectedvalue = typeof selectedvalue !== 'undefined' ? selectedvalue : '';
        $.ajax({
            url: root + "main/getBoolean",
            dataType: 'json',
            type: 'post',
            data: {csrf_eadev_client: csrf_hash},
            success: function(json) {
                $('#ivansearch_val_' + id).msDropDown({byJson: {data: json, name: 'vals[]'}}).data("dd").setIndexByValue(selectedvalue);
            }
        });
    }

    $(document).ready(function() {
        $('button[id="button_search"]').click(function() {
            var data = $("form").serialize();
            jQuery("#list2").jqGrid('setGridParam', {
                url: root + 'mod_voucherin/JurnalToJson?_search=true&' + data,
                page: 1
            }).trigger("reloadGrid");
        });

        var panjang = $('.inbody').height() - 220;
        var lebar = $('.content').width() - 40;
        jQuery("#list2").jqGrid({
            url: root + 'mod_voucherin/JurnalToJson',
            mtype: "post",
            datatype: "json",
            postData: {csrf_eadev_client: csrf_hash},
            colNames: ['No', "<div id='jq_checkbox_head_added'><div>", '#', 'Tanggal', 'Nomor Bukti', 'Nomor Dokumen', 'Kode Proyek', 'Keterangan', 'COA', 'Rekanan', 'Debet', 'Kredit', 'IsApprove'],
            colModel: [
                {name: 'no', index: 'id_jurnal', width: 25, sortable: false, align: "center"},
                {name: 'check', index: 'check', width: 25, sortable: false, align: "center"},
                {name: 'flag', index: 'flag', width: 25, sortable: false, align: "center"},
                {name: 'tanggal', index: 'tanggal', width: 80, sortable: false},
                {name: 'no_bukti', index: 'no_bukti', hidden: false, width: 100, sortable: false},
                {name: 'no_dokumen', index: 'no_dokumen', width: 100, sortable: false},
                {name: 'kode_proyek', index: 'kode_proyek', width: 80, sortable: false},
                {name: 'keterangan', index: 'keterangan', width: 140, sortable: false},
                {name: 'coa', index: 'coa', width: 100, sortable: false},
                {name: 'rekanan', index: 'rekanan', hidden: true, width: 100, sortable: false},
                //{name:'volume', index:'volume',width:100, sortable: false, align:"right"},
                {name: 'debet', index: 'debet', width: 100, sortable: false, align: "right"},
                {name: 'kredit', index: 'kredit', width: 100, sortable: false, align: "right"},
                {name: 'author', index: 'author', width: 80, sortable: false, align: 'center'},
            ],
            width: lebar,
            height: 500,
            rowNum: 20,
            rowList: [20, 40, 60, 80, 100],
            pager: '#pager2',
            viewrecords: true,
            shrinkToFit: false
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

        $('#form_voucherin_delete').click(function() {
			if (confirm('Apakah anda yakin untuk menghapus data tersebut?')) {

				var id = $('input[class="jq_checkbox_added"]:checked').map(function() {
					return $(this).val();
				}).get();


				$.ajax({
					url: root + "mod_voucherin/deletejurnal",
					dataType: 'json',
					type: 'post',
					data: {id: id, csrf_eadev_client: csrf_hash},
					success: function(json) {
						$('div.alert').remove();
						if (json['error']) {
							$('.content').prepend('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>' + json['error'] + '</div>');
							$('div.alert').fadeIn('slow');
						}

						if (json['success']) {
							$('.content').prepend('<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">&times;</button>' + json['success'] + '</div>');
							$('div.alert').fadeIn('slow');
							form_refresh_grid()
						}
					}
				});
			}
        });
    });
</script>
