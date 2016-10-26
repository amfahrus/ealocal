<div class="content form_saldo_awal_list">
    <?= form_open(); ?>
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="basic box_title"><h4><span>#</span> Saldo Awal Perkiraan</h4></div>
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
    function edit_jurnal(id) {
        $.ajax({
            url: root + 'mod_saldo/edit_jurnal',
            type: 'post',
            dataType: 'json',
            data: {
                saldo_awal_id: id,
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
            url: root + 'mod_saldo/JurnalToJson?_search=true&' + data,
            page: 1
        }).trigger("reloadGrid");
    }

    $(document).ready(function() {
        $('button[id="button_search"]').click(function() {
            var data = $("form").serialize();
            jQuery("#list2").jqGrid('setGridParam', {
                url: root + 'mod_saldo/JurnalToJson?_search=true&' + data,
                page: 1
            }).trigger("reloadGrid");
        });

        var panjang = $('.inbody').height() - 220;
        var lebar = $('.content').width() - 40;
        jQuery("#list2").jqGrid({
            url: root + 'mod_saldo/JurnalToJson',
            mtype: "post",
            datatype: "json",
            postData: {csrf_eadev_client: csrf_hash},
            colNames: ['No', "<div id='jq_checkbox_head_added'><div>", 'Kode Perkiraan', 'Uraian', 'Rekanan / Sbdaya', 'Debet', 'Kredit'],
            colModel: [
                {name: 'no', index: 'id_jurnal', width: 25, sortable: false, align: "center"},
                {name: 'check', index: 'check', width: 25, sortable: false, align: "center"},
                {name: 'coa', index: 'coa', width: 140, sortable: false},
                {name: 'keterangan', index: 'keterangan', width: 140, sortable: false},
                {name: 'bukubantu', index: 'bukubantu', width: 140, sortable: false},
                {name: 'debet', index: 'debet', width: 100, sortable: false, align: "right"},
                {name: 'kredit', index: 'kredit', width: 100, sortable: false, align: "right"}
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
            } else {
                $('div#jq_checkbox_head_added').addClass('selected')
                $('div#jq_checkbox_head_added').prepend('<div class="checkicon_add"><image src="' + root + 'checkbox.gif" /></div>');
                $('.jq_checkbox_added').each(function() {
                    this.checked = true;
                });
            }
        });

        $('#form_saldoawal_delete').click(function() {
			if (confirm('Apakah anda yakin untuk menghapus data tersebut?')) {

				var id = $('input[class="jq_checkbox_added"]:checked').map(function() {
					return $(this).val();
				}).get();


				$.ajax({
					url: root + "mod_saldo/deletejurnal",
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
