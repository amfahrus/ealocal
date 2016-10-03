<div class="content form_approval">
    <?= form_open(); ?>
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="basic box_title"><h4><span>#</span> Jurnal Approve</h4></div>
                <div class="basic box_content form_search" ></div>
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
    function edit_jurnal(id,gid,count) {
		
		if(count==1){
			showUrlInDialog(root + 'mod_approval/setVoucher/' + id, "redirect_jurnal_json", "Pilih Voucher", "form_ganti_voucher");
		} else {
			redirect_jurnal(gid);
		}
    }
    
    function redirect_jurnal_json(resp){		
        $.ajax({
            url: root + 'mod_approval/edit_jurnal',
            type: 'post',
            dataType: 'json',
            data: {
                nobukti: resp.nobukti,
                csrf_eadev: csrf_hash
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
    
    function redirect_jurnal(gid){		
        $.ajax({
            url: root + 'mod_approval/edit_jurnal',
            type: 'post',
            dataType: 'json',
            data: {
                nobukti: gid,
                csrf_eadev: csrf_hash
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

    function form_approval_refresh_grid() {
        var data = $("form").serialize();
        jQuery("#list2").jqGrid('setGridParam', {
            url: root + 'mod_approval/JurnalToJson?_search=true&' + data,
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
            data: {csrf_eadev: csrf_hash},
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
            data: {csrf_eadev: csrf_hash},
            success: function(json) {
                $('#ivansearch_val_' + id).msDropDown({byJson: {data: json, name: 'vals[]'}}).data("dd").setIndexByValue(selectedvalue);
            }
        });
    }

    $(document).ready(function() {
        $('button[id="button_search"]').click(function() {
            var data = $("form").serialize();
            jQuery("#list2").jqGrid('setGridParam', {
                url: root + 'mod_approval/JurnalToJson?_search=true&' + data,
                page: 1
            }).trigger("reloadGrid");
        });
		
        $('button[id="form_approve_jurnal"]').click(function() {
			
			var konfirmasi = $('<div></div>');
			konfirmasi.addClass('form_approval_dialog_confirm');
			konfirmasi.dialog({
				closeOnEscape: true,
				autoOpen: false,
				width: 400,
				height: 150,
				modal: true,
				title: 'Konfirmasi Approve Jurnal',
				buttons: [
					{
						text: "Ya",
						click: function() {
							$.ajax({
								url: root + 'mod_approval/addJurnal',
								dataType: 'json',
								type: 'post',
								data: $("form").serialize(),
								beforeSend: function() {
									$(this).attr('disabled', true);
								},
								complete: function() {
									$(this).attr('disabled', false);
								},
								success: function(json) {
									$('div.alert').remove();
									if (json['response']['error']) {
										$('.form_approval').prepend('<div class="alert alert-error"><button type="button" class="close" data-dismiss="alert">&times;</button>' + json['response']['error'] + '</div>');
										$('div.alert').fadeIn('slow');
									}

									if (json['response']['success']) {
										var lang = '';
										var counter_error = '';
										$.each(json, function() {
											lang += '<table class="table table-hover">';
											$.each(this['informasi'], function() {
												lang += '<tr>';
												lang += '<td>' + this['no'] + '</td>';
												lang += '<td>' + this['no_dokumen'] + '</td>';
												lang += '<td>' + this['nobukti'] + '</td>';
												lang += '<td>' + this['status'] + '</td>';
												lang += '<td>' + this['keterangan'] + '</td>';
												lang += '</tr>';
											});
											lang += '</table>';
											counter_error = this['counter_error'];
										});

										var tag = $('<div></div>');
										tag.addClass('form_approval_dialog');
										tag.dialog({
											closeOnEscape: true,
											autoOpen: false,
											width: 600,
											height: 200,
											modal: true,
											title: 'Information',
											buttons: [
												{
													text: "OK",
													click: function() {
														$('div.form_approval_dialog').dialog("close");
													}
												}
											],
											close: function() {
												$('div.form_approval_dialog').dialog("destroy").remove();
											},
											open: function() {
												$('div.form_approval_dialog').prepend(lang);
												$('.ui-dialog-buttonpane').prepend('<div class="ui-dialog-label"><p>Terdapat Sebanyak <b>' + counter_error + '</b> Jurnal Gagal Approve</p></div>');
											}
										}).dialog('open');
										form_approval_refresh_grid();
									}
									createAutoClosingAlert('div.alert', 2000);

								}
							});
							$('div.form_approval_dialog_confirm').dialog( "close" );
						}
					},
					{
						text: "Tidak",
						click: function() {
							$('div.form_approval_dialog_confirm').dialog( "close" );
						}
					}
				],
				close: function() {
					$('div.form_approval_dialog_confirm').dialog("destroy").remove();
				},
				open: function() {
					$('div.form_approval_dialog_confirm').prepend('Apakah anda yakin untuk approve jurnal ini?');
				}
			}).dialog('open');
			
            
        });

        var panjang = $('.inbody').height() - 220;
        var lebar = $('.content').width() - 40;
        jQuery("#list2").jqGrid({
            url: root + 'mod_approval/JurnalToJson',
            mtype: "post",
            datatype: "json",
            postData: {csrf_eadev: csrf_hash},
            colNames: ['No', "<div id='jq_checkbox_head_added'><div>", '#', 'Tanggal', 'Nomor Bukti', 'Nomor', 'Kode Proyek', 'Keterangan', 'COA', 'Rekanan', 'Debet', 'Kredit', 'IsApprove'],
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
    });
</script>
