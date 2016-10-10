<div class="content popup_rekanan">
    <?= form_open(); ?>
    <div class="row-fluid form-horizontal">
        <div class="span12">
            <div class="row-fluid">
                <div class="control-group info">
                    <label class="control-label" for="popup_koderekanan">Kode Rekanan</label>
                    <div class="controls">
                        <input type="text" name="popup_koderekanan" id="popup_koderekanan"/> 
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="control-group info">
                    <label class="control-label" for="popup_namarekanan">Nama Rekanan</label>
                    <div class="controls">
                        <input type="text" name="popup_namarekanan" id="popup_namarekanan"/> 
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="control-group info">
                    <div class="controls">
                        <button type="button" name="popup_carirekanan" class="btn"><i class="cus-zoom"></i> Cari</button>
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
    function popuprekanan_accept(id) {
        $.ajax({
            url: root + 'mod_rekanan/get_rekanan',
            type:'POST',
            dataType:'json',
            data:{
                item: id,
                csrf_eadev_client: csrf_hash
            },
            beforeSend: function() {
                $(this).attr('disabled',true);
            },
            complete: function() {
                $(this).attr('disabled',true);
            },
            success: function(json) {
                parent.pkcaller(json);
            }
        });
    }
    
    function popup_rekanan_refresh_grid() {
        jQuery("#list2").jqGrid('setGridParam',{
            url: root + "mod_rekanan/popup_json", 
            page:1
        }).trigger("reloadGrid");
    }
    
    function popuprekanan_edit(id) {
        showUrlInDialog(root + "mod_rekanan/popup_view/"+id, "popup_rekanan_refresh_grid", "Edit Buku Bantu Rekanan", "popup_rekanan_edit", 600, 500);

    }
    
    
    $(document).ready(function() {
        $('button[name="popup_carirekanan"]').bind('click', function() {
            var kode_rekanan = $('input[name="popup_koderekanan"]').val();
            var nama_rekanan = $('input[name="popup_namarekanan"]').val();
            var search = "_search=true&kode_rekanan="+kode_rekanan+"&nama_rekanan="+nama_rekanan;
            jQuery("#list2").jqGrid('setGridParam',{
                url: root + 'mod_rekanan/popup_json/<?php echo $coa? $coa : 0; ?>'+ search,
                page:1
            }).trigger("reloadGrid");
        });
        
        jQuery("#list2").jqGrid({
            url: root + 'mod_rekanan/popup_json/'+<?php echo $coa? $coa : 0; ?>, 
            mtype : "post",
            datatype: "json", 
            postData: {csrf_eadev_client: csrf_hash},
            colNames:['#', '#', 'Kode Rekanan', 'Nama Rekanan'], 
            colModel:[ 
                {name:'act',index:'act', width:80, align:"center", sortable:false}, 
                {name:'id_rekanan',index:'id_rekanan',hidden:true}, 
                {name:'kode_rekanan',index:'kode_rekanan',width:150}, 
                {name:'nama_rekanan',index:'nama_rekanan', width:300}, 
            ], 
            rowNum:10, 
            width: 530, 
            height: 250, 
            rownumbers: true, 
            rownumWidth: 40,
            rowList:[10,20,30], 
            pager: '#pager2', 
            viewrecords: true, 
            shrinkToFit: false,
            gridComplete: function(){ 
                var ids = jQuery("#list2").jqGrid('getDataIDs'); 
                for(var i=0;i < ids.length;i++){ 
                    var cl = ids[i]; 
                    ce = "<a href=\"#\" onclick=\"popuprekanan_accept("+ids[i]+");\" class=\"link_edit tooltips\" data-placement=\"right\" data-toggle=\"tooltip\" data-original-title=\"Pilih Data Buku Bantu Rekanan\"><img  src=\"<?= base_url(); ?>media/application_add.png\" /></a>"; 
                    co = "<a href=\"#\" onclick=\"popuprekanan_edit("+ids[i]+");\" class=\"link_edit tooltips\" data-placement=\"right\" data-toggle=\"tooltip\" data-original-title=\"Edit Data Buku Bantu Rekanan\"><img  src=\"<?= base_url(); ?>media/edit2_32x32.png\" /></a>"; 
                    jQuery("#list2").jqGrid('setRowData',ids[i],{act:co+ce}); 
                } 
                $(".tooltips").tooltip();
            }
        }); 
        jQuery("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false,search:false});
    });
</script>
