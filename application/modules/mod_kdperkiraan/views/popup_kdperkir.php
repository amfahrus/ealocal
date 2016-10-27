<div class="content popup_kodeperkiraan">
    <?= form_open(); ?>
    <div class="row-fluid form-horizontal">
        <div class="span12">
            <div class="row-fluid">
                <div class="control-group info">
                    <label class="control-label" for="popup_kodeperkiraan">Kode Perkiraan</label>
                    <div class="controls">
                        <input type="text" name="popup_kodeperkiraan" id="popup_kodeperkiraan"/>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="control-group info">
                    <label class="control-label" for="popup_nmperkiraan">Nama Perkiraan</label>
                    <div class="controls">
                        <input type="text" name="popup_nmperkiraan" id="popup_nmperkiraan"/>
                    </div>
                </div>
            </div>
            <div class="row-fluid">
                <div class="control-group info">
                    <div class="controls">
                        <button type="button" name="popup_cariperkiraan" class="btn"><i class="cus-zoom"></i> Cari</button>
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
    $(document).ready(function() {
        $('button[name="popup_cariperkiraan"]').bind('click', function() {
            var kdperkiraan = $('input[name="popup_kodeperkiraan"]').val();
            var nmperkiraan = $('input[name="popup_nmperkiraan"]').val();
            var search = "_search=true&kdperkiraan="+kdperkiraan+"&nmperkiraan="+nmperkiraan;
            jQuery("#list2").jqGrid('setGridParam',{
                url: root + 'mod_kdperkiraan/popup_json/<?php echo $cat; ?>?'+ search,
                page:1
            }).trigger("reloadGrid");
        });

        jQuery("#list2").jqGrid({
            url: root + 'mod_kdperkiraan/popup_json/<?php echo $cat; ?>',
            mtype : "post",
            datatype: "json",
            postData: {csrf_eadev_client: csrf_hash},
            colNames:['Id','Kode Perkiraan', 'Nama Perkiraan', 'Sumberdaya', 'Nasabah'],
            colModel:[
                {name:'dperkir_id',index:'dperkir_id',width:150, hidden:true},
                {name:'kdperkiraan',index:'kdperkiraan',width:150},
                {name:'nmperkiraan',index:'nmperkiraan', width:300},
                {name:'flag_sumberdaya',index:'flag_sumberdaya', width:100, hidden:true},
                {name:'flag_nasabah',index:'flag_nasabah', width:100, hidden:true},
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
            onSelectRow: function(id){
                $.ajax({
                    url: root + 'mod_kdperkiraan/get_kodeperkiraan',
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
        });
        jQuery("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false,search:false});
    });
</script>
