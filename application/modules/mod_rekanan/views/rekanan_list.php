<div class="content">
    <?= form_open(); ?>
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="basic box_title"><h4><span>#</span> Master Rekanan</h4></div>
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
<script type="text/javascript">
    $(document).ready(function() {
        var lebar = $('.inbody').height() - 200;
        var panjang = $('.content').width() - 20;
        jQuery("#list2").jqGrid({
            url: root + 'mod_rekanan/rekanan_json', 
            mtype : "post",
            datatype: "json", 
            postData: {csrf_eadev_client: csrf_hash},
            colNames:['id_rekanan','Proyek', 'Tipe','Kode Rekanan', 'Nama Rekanan',''], 
            colModel:[ 
                {name:'id_rekanan',index:'id_rekanan',hidden:true, width:100}, 
                {name:'kode_proyek',index:'kode_proyek', width:150}, 
                {name:'nama_library',index:'nama_library', width:150}, 
                {name:'kode_rekanan',index:'kode_rekanan', width:150}, 
                {name:'nama_rekanan',index:'nama_rekanan', width:150},
                {name:'aksi',width:30, align: "center"}], 
            rowNum:10, 
            width: panjang, 
            height: lebar, 
            rownumbers: true, 
            rownumWidth: 40,
            rowList:[10,20,30], 
            pager: '#pager2', 
            multiselect: true,
            viewrecords: true, 
            sortorder: "desc",
            shrinkToFit: false
            //caption:"List Users" 
        }); 
        jQuery("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false,search:false});
        
        $('#button_search').click(function () {
            var str = $("form").serialize();
            var search = "_search=true&"+ str;
            jQuery("#list2").jqGrid('setGridParam',{
                url: root + 'mod_rekanan/rekanan_json?'+ search,
                page:1
            }).trigger("reloadGrid");
        });
        
        $("#reset_search").click(function() {
            $('.cols_cari').val("");
            $('.ops_cari').val("");
            $('.text').val("");
            jQuery("#list2").jqGrid('setGridParam',{
                url: root + 'mod_rekanan/rekanan_json',
                page:1
            }).trigger("reloadGrid");
        });
        
        $("#form_rekanan_delete").click(function() {
            var id = jQuery("#list2").jqGrid('getGridParam','selarrrow'); 
            if(id){
                $.ajax({
                    url: root + 'mod_rekanan/rekanan_delete',
                    type: 'post',
                    data: { id: id, csrf_eadev_client: csrf_hash},
                    success: function(json) {
                        jQuery("#list2").jqGrid().trigger('reloadGrid');
                    }
                });	
            } else {
                alert("Please Select Row to delete!");
            }
        });
    });
</script>
