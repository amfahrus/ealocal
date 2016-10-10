<link rel="stylesheet" type="text/css" media="screen" href="<?= base_url(); ?>assets/choosen/chosen.css" />
<script type="text/javascript" src="<?= base_url(); ?>assets/choosen/chosen.jquery.js"></script>
<script>
    $(document).ready(function() {
        var lebar = $('.inbody').height() - 200;
        var panjang = $('.content').width() - 20;
        var lastsel;
        jQuery("#list2").jqGrid({
            datatype: "local",
            colNames:['','Kode Perusahaan','Nama Perusahaan', 'Telp. Perusahaan','Nama Kontak','Alamat','Telp. Kontak','Kota', 'Tipe Rekanan', 'Status', 'Keterangan', 'Kode Perkiraan',''], 
            colModel:[
                {name:'id_proyek', index:'id_proyek', hidden:true,width:50},
                {name:'kode_rekanan', index:'kode_rekanan', width:100},
                {name:'nama_rekanan', index:'nama_rekanan', width:100},
                {name:'telp_rekanan',index:'telp_rekanan', width:100},
                {name:'nama_kontak',index:'nama_kontak', width:100},
                {name:'alamat',index:'alamat', width:100},
                {name:'telp_kontak',index:'telp_kontak', width:100},
                {name:'kota',index:'kota', width:100},
                {name:'type_rekanan',index:'type_rekanan', width:100},
                {name:'status',index:'status', width:50},
                {name:'keterangan',index:'keterangan', width:100},
                {name:'kdperkiraan',index:'kdperkiraan', width:200, 
                editable: true,
                formatter: "select",
				edittype: "custom",
				editoptions:{
					multiple:true,
					size:6,
					value:<?php echo $kode_perkiraan; ?>,
					custom_element: function (value, options) {
										return $.jgrid.createEl.call(this, "select",
											$.extend(true, {}, options, {custom_element: null, custom_value: null}),
											value);
									},
					custom_value: function ($elem, operation, value) {
										if (operation === "get") {
											return $elem.val();
										}
									}
								}
				},
                {name:'act', index:'act', width: 50, align:'center', sortable: false, formatter:'actions',
					formatoptions: {
							keys: true, // we want use [Enter] key to save the row and [Esc] to cancel editing.
							editbutton : true,
							delbutton : false
					}
                }
            ],
            scroll: true, 
            width: panjang, 
            height: lebar, 
            rownumbers: true,
            rowNum:1000,
            rownumWidth: 40,
            pager: '#pager2',
            viewrecords: true,
            altRows : true,
            editurl:'clientArray',
            onSelectRow: function(id){ 
				if(id && id!==lastsel){ 
					jQuery('#list2').jqGrid('restoreRow',lastsel); 
					jQuery('#list2').jqGrid('editRow',id,true);
					lastsel=id;
				} 
			},
            gridComplete: function(){ 
                /*var ids = jQuery("#list2").jqGrid('getDataIDs'); 
                for(var i=0;i < ids.length;i++){ 
                    var cl = ids[i]; 
                    ce = "<a href=\"#\" onclick=\"alert("+ids[i]+");\" class=\"link_edit\"><img  src=\"<?= base_url(); ?>media/edit.png\" /></a>"; 
                    jQuery("#list2").jqGrid('setRowData',ids[i],{act:ce}); 
                }*/
                var rows = $("#list2").getDataIDs(); 
				for (var i = 0; i < rows.length; i++)
				{
					var status = $("#list2").getCell(rows[i],"status");
					if(status == "Error")
					{
						$("#list2").jqGrid('setRowData',rows[i],false, {  color:'black',weightfont:'bold',background:'orange'});            
					}
				} 
            }
            //caption:"List Upload Master Rekanan"
        });
        var mydata = <?php echo $mydata; ?>; 
        for(var i=0;i<=mydata.length;i++) jQuery("#list2").jqGrid('addRowData',i+1,mydata[i]);
        jQuery("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false,search:false});
        
        $("#simpan").click(function(){
			var gridData = jQuery("#list2").getRowData();
			var postData = JSON.stringify(gridData);
			//alert("JSON serialized jqGrid data:\n" + postData);
			if(gridData.length > 0){
				$.ajax({
					type: "POST",
					url: root+"mod_rekanan/send_import",
					data : {
						jqGridData: postData,
						csrf_eadev_client: csrf_hash
					}
				}).done(function() {
					alert('Data berhasil disimpan!');
					jQuery("#list2").trigger("reloadGrid");
				});
		   } else {
			   alert('Tidak ada data!');
		   }
		});

    });
</script>
<div class="content">
    <?= form_open_multipart("mod_rekanan/from_excel"); ?>
    <?php
    $messages = $this->session->flashdata('messages');
    if (!empty($messages)) {
        echo "<h2>" . $messages . "</h2>";
    }
    ?>
	<div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="basic box_title"><h4><span>#</span> Upload Rekanan</h4></div>
                <div class="basic box_content form-horizontal">
                    <div class="row-fluid">
                        <div class="control-group info">
                            <label class="control-label" for="file">Browse File</label>
                            <div class="controls">
                                <input type="file" name="master_rekanan" value="<?= set_value('master_rekanan'); ?>"/>
                            </div>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="controls">
                            <a class="btn btn-success" href="<?php echo base_url();?>format_export_rekanan.xlsx">Download Format Export Excel</a>
                        </div>
                    </div>
                    <div class="row-fluid">
						<hr>
                    </div>
                    <div class="row-fluid">
                        <div class="control-group info">
                            <div class="controls">
                                <div class="btn-group">
                                    <button type="submit" class="btn btn-info" name="submit"><i class="icon-ok-sign icon-white"></i> Import</button>
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
    <button class="btn btn-primary" name="simpan" id="simpan">Simpan</button>
    
</div>
