<?php
 include_once 'header.php';

 if(isset($_GET['tglmasuk']) AND isset($_GET['tglkeluar']) )
 {
     $tglmasuk = $_GET['tglmasuk'];
     $tglkeluar = $_GET['tglkeluar'];
 }

?>

<h1 class="text-center">ICD RAWAT JALAN</h1>

<div class="container">
    <h5>Tanggal:</h5>
    <form class="form-inline" action="" method="get">
        <div class="form-group">
            <input type="date" name="tglmasuk" class="form-control" id="" required>
        </div>
        <h4>S/D</h4>
        <div class="form-group">
            <input type="date" name="tglkeluar" class="form-control" id="" required>
        </div>
        <input type="submit" class="btn btn-primary" value="CARI">
    </form>
    
    <br>
    <br>
    <table id="example" class="table table-striped table-bordered nowrap" style="width:100%"> 
        <thead>
            <tr>
                <th>JAMINAN</th>
                <th>REG NO</th>
                <th>NO MR</th>
                <th>NAMA</th>
                <th>POLI ID</th>
                <th>LAYANAN</th>
                <th>BARU</th>
                <th>ELEMEN</th>
                <th>TGL MASUK</th>
                <th>TGL KELUAR</th>
                <th>ICD</th>
                <th>ICD DESC</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $sql="SELECT 
            ku_kode_eselon.deskripsi as JAMINAN,      
            transaksi_icd.reg_no as REGNO,  
            reg.no_mr as NO_MR,   
            reg.nama as NAMA,  
            transaksi_icd.poli_id as POLI_ID, 
            list(transaksi_icd.baru) as BARU,   
            list(upper(transaksi_icd.elemen)) as ELEMEN,
            master_poli.poli_name as LAYANAN,
            min(transaksi_icd.last_update) as TGL_MASUK,
            max(transaksi_icd.last_update) as TGL_KELUAR,
            list(upper(transaksi_icd.icd_id)) as ICD, 
            list(icd_desc) as ICD_DESC
       FROM transaksi_icd,   
            icd,
            master_poli,
            reg,
            ku_kode_eselon
        WHERE transaksi_icd.icd_id = icd.icd_id 
            and master_poli.poli_id =  transaksi_icd.poli_id
            and transaksi_icd.reg_no = reg.reg_no
            and reg.rawat_jalan = 'Y'
            and ku_kode_eselon.kode_eselon = reg.eselon
            and transaksi_icd.last_update between '$tglmasuk' and '$tglkeluar'                            GROUP BY transaksi_icd.reg_no,   
                    transaksi_icd.poli_id,
                    master_poli.poli_name,
                    ku_kode_eselon.deskripsi,
                    reg.no_mr,   
                    reg.nama
            order by ku_kode_eselon.deskripsi, 
                    transaksi_icd.poli_id, 
                    transaksi_icd.reg_no"; 
            $rs=odbc_exec($connect,$sql);
            while($row = odbc_fetch_array($rs)){
        ?>
            <tr>
                <td><?php echo $row['JAMINAN'];?></td>
                <td><?php echo $row['REGNO'];?></td>
                <td><?php echo $row['NO_MR'];?></td>
                <td><?php echo $row['NAMA'];?></td>
                <td><?php echo $row['POLI_ID'];?></td>
                <td><?php echo $row['LAYANAN'];?></td>
                <td><?php echo $row['BARU'];?></td>
                <td><?php echo $row['ELEMEN'];?></td>
                <th><?php echo $row['TGL_MASUK'];?></th>
                <th><?php echo $row['TGL_KELUAR'];?></th>
                <th><?php echo $row['ICD'];?></th>
                <th><?php echo $row['ICD_DESC'];?></th>
            </tr>
        <?php
            }
        ?>
        </tbody>
        <tfoot>
            <tr>
                <th>JAMINAN</th>
                <th>REG NO</th>
                <th>NO MR</th>
                <th>NAMA</th>
                <th>POLI ID</th>
                <th>LAYANAN</th>
                <th>BARU</th>
                <th>ELEMEN</th>
                <th>TGL MASUK</th>
                <th>TGL KELUAR</th>
                <th>ICD</th>
                <th>ICD DESC</th>
            </tr>
        </tfoot>
    </table>
</div>
<?php
include_once 'footer.php';
?>
<script>
$(document).ready(function() {
    $('#example').DataTable( {
        initComplete: function () {
            this.api().columns().every( function () {
                var column = this;
                var select = $('<select><option value=""></option></select>')
                    .appendTo( $(column.footer()).empty() )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
 
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' )
                } );
            } );
        },
        "scrollX": true,
        dom: 'Bfrtip',
        lengthMenu: [
            [ 10, 25, 50, -1 ],
            [ '10 rows', '25 rows', '50 rows', 'Show all' ]
        ],
        buttons: [
            'pageLength', 'copy', 'csv', 'excel', 'pdf', 'print'
        ]
        
    } );
} );
</script>
