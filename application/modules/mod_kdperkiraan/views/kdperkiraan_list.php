<div class="content">
    <?= form_open('mod_kdperkiraan/to_excel',array('id' => 'filter')); ?>
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="basic box_title"><h4><span>#</span> Master Kode Perkiraan</h4></div>
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
            url: root + 'mod_kdperkiraan/kdperkiraan_json', 
            mtype : "post",
            datatype: "json", 
            postData: {csrf_eadev: csrf_hash},
            colNames:['Kode Akun', 'Nama Akun','Rekanan','SB Daya','Proyek'], 
            colModel:[ 
                {name:'kdperkiraan',index:'kdperkiraan', width:150}, 
                {name:'nmperkiraan',index:'nmperkiraan', width:300},
                {name:'bukubantu1',index:'bukubantu1', width:100, align:"center"},
                {name:'bukubantu2',index:'bukubantu2', width:100, align:"center"},
                {name:'bukubantu3',index:'bukubantu3', width:100, align:"center"}
            ], 
            rowNum:20, 
            width: panjang, 
            height: lebar, 
            rownumbers: true, 
            rownumWidth: 40,
            rowList:[20,30,40,50], 
            pager: '#pager2', 
            multiselect: true,
            viewrecords: true, 
            shrinkToFit: false
        }); 
        
        jQuery("#list2").jqGrid('setGroupHeaders', { 
            useColSpanStyle: true, 
            groupHeaders:[
                {startColumnName: 'bukubantu1', numberOfColumns: 3, titleText: 'Buku Bantu'} 
            ] 
        });
        jQuery("#list2").jqGrid('navGrid','#pager2',{edit:false,add:false,del:false,search:false});
        
        $('#button_search').click(function () {
            var str = $("form").serialize();
            var search = "_search=true&"+ str;
            jQuery("#list2").jqGrid('setGridParam',{
                url: root + 'mod_kdperkiraan/kdperkiraan_json?'+ search,
                page:1
            }).trigger("reloadGrid");
        });
        
        $("#reset_search").click(function() {
            $('.cols_cari').val("");
            $('.ops_cari').val("");
            $('.text').val("");
            jQuery("#list2").jqGrid('setGridParam',{
                url: root + 'mod_kdperkiraan/kdperkiraan_json',
                page:1
            }).trigger("reloadGrid");
        });
        $("#form_kode_perkiraan_excel").click(function(){
			filter.submit();
		});
    });
</script>
