<style>
.warna {
 color: blue !important;
}
table {
  border-top: 0.5px solid blue;
  border-bottom: 0.5px solid blue;
}
table > thead > tr > th,
table > thead > tr > td{
   border: 0.5px solid blue;
}
table > tfoot > tr > th,
table > tfoot > tr > td{
   border: 0.5px solid blue;
}
table > tbody > tr > td {
  /*border-right: 0.5px solid blue;
  border-left: 0.5px solid blue;*/
  border: 0.5px solid blue;
}
</style>

<div id="form_popupformulir" class="table-responsive">
  <h3 class="warna"><center>Bukti Pemasukan Kas/Bank</center></h3>
  <b class="warna">Nomor</b> : <?php echo $formulir['jurnal']['no_dokumen'];?>
  <br>
  <b class="warna">Tanggal</b> : <?php echo $formulir['jurnal']['tanggal'];?>
  <br>
  <b class="warna">Kode Perkiraan Debet</b> : <?php echo $formulir['jurnal']['detail'][1]["detail"]["D"]["kdperkiraan"];?>
  <table class="table">
    <thead>
      <tr>
        <th class="warna" width="5%"><center>Nomor</center></th>
        <th class="warna" width="45%"><center>Uraian</center></th>
        <th class="warna" width="20%"><center>Kode Perkiraan Kredit</center></th>
        <th class="warna" width="30%"><center>Jumlah Uang (Rp)</center></th>
      </tr>
    </thead>
    <tbody>
      <?php
        $x = 0;
        $nilai = 0;
        foreach($formulir['jurnal']['detail'] as $row){
          $x++;
          $nilai = $nilai + $row["nilai"];
      ?>
      <tr>
        <td><?php echo $x;?></td>
        <td><?php echo $row['keterangan'];?></td>
        <td><?php echo $row["detail"]["K"]["kdperkiraan"];?></td>
        <td><span class="pull-right"><?php echo number_format($row["nilai"],2);?></span></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <tr>
        <th class="warna" colspan="3"><center>Total</center></th>
        <th><span class="pull-right"><?php echo number_format($nilai,2); ?></span></th>
      </tr>
    </tfoot>
  </table>
</div>
<div class="row-fluid">
    <div class="control-group info">
        <div class="controls pull-right">
            <button type="button" name="popup_addjurnal" class="btn btn-primary"> Submit</button>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
      $('button[name="popup_addjurnal"]').bind('click', function() {
        parent.pkcaller();
      });
    });
</script>
