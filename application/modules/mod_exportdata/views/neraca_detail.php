<div class="content">
	
    <table id="list4"></table>
    <div id="pager4"></div>
    
</div>
<script type="text/javascript">
	$(document).ready(function() {
		//alert('tes');
        var lebar = $('.inbody').height() - 200;
        var panjang = $('.content').width() - 20;
        jQuery("#list4").jqGrid({
            url: root + 'mod_neraca/detail_json/<?php echo $group; ?>/<?php echo $periode; ?>', 
			mtype : "post",
            datatype: "json", 
            colNames:['No','Tanggal', 'Nomor Bukti', 'Kode Proyek', 'COA', 'Rekanan', 'Keterangan', 'Debit', 'Kredit'], 
            colModel:[ 
                {name:'nomor',index:'id_jurnal', sortable: false, width:25, align:"center"}, 
                {name:'tanggal',index:'tanggal', sortable: false, width:100, formatter:'date', formatoptions:{srcformat:"Y-m-d",newformat:"d M Y"}, align:"center"},  
                {name:'nobukti',index:'nobukti', sortable: false, width:100},
                {name:'kode_proyek',index:'kode_proyek', sortable: false, width:100}, 
                {name:'coa',index:'coa', sortable: false, width:100},
                {name:'rekanan',index:'rekanan', sortable: false, width:100},
                {name:'keterangan',index:'keterangan', sortable: false, width:100},
                {name:'debit',index:'debit', sortable: false, width:100, align:"right"},
                {name:'kredit',index:'kredit', sortable: false, width:100, align:"right"}],
            rowNum:10, 
            width: 560,
            height: 200,
            rownumWidth: 40,
            rowList:[10,20,30], 
            pager: '#pager4',
            shrinkToFit: false
        }); 
        jQuery("#list4").jqGrid('navGrid','#pager4',{edit:false,add:false,del:false,search:false});

    });
</script>
