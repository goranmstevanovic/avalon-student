<head>
<!-- <link href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.css" rel="stylesheet" /> -->
	<link href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.css" rel="stylesheet" />
<!--	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>  -->
<!--	<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script> -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script> 
	<script src="https://cdn.datatables.net/buttons/1.4.2/js/dataTables.buttons.min.js"></script>
<!--	<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/pdfmake.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.32/vfs_fonts.js"></script> -->
	<script src="https://cdn.datatables.net/buttons/1.4.2/js/buttons.html5.min.js"></script> 
</head>
<?php
// display the table if the number of users retrieved was greater than zero
if($num>0){ ?>

    <table id="example" class="display"  style="font-size: 12px;">
      <thead><tr><th style="width:15em;">Ime i prezime</th><th style="width:12%;">Broj telefona</th><th style="width:12em;">Adresa</th><th style="width: 10em;">Grupa</th><th>Lokacija</th><th style="width:350px;">Akcija</th></tr></thead>
      <tfoot><tr><th>Ime i prezime</th><th>Broj telefona</th><th>Adresa</th><th>Grupa</th><th>Lokacija</th><th>Akcija</th></tr></tfoot>
        <tbody>
 <?php

    $i=0;
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $i++;
        extract($row);

        include_once 'objects/grupa.php';
        include_once 'objects/povezivanje.php';
        include_once 'objects/nivo_znanja.php';
        include_once 'objects/jezik.php';
        include_once 'objects/lokacija.php';
        $grupa = new grupa($db);
        $povezivanje = new povezivanje($db);
        $lokacija = new lokacija($db);

        $stt = $povezivanje->read_all_group_students($id);
        $stt1 = $povezivanje->read_all_group_students($id);
       // $row_grupe = $stt->fetch(PDO::FETCH_ASSOC);
       
       $stmt_skola_grupa = $grupa->read_all_skola();
       $stmt_moje_grupa = $grupa->read_all_moji($_SESSION['user_id']);
       $skola_grupe = array();
       $moje_grupe = array();
       while ($row_category_skola_grupa = $stmt_skola_grupa->fetch(PDO::FETCH_ASSOC)){
          $skola_grupe[] = $row_category_skola_grupa['id'];
       }
       while ($row_category_moje_grupa =$stmt_moje_grupa->fetch(PDO::FETCH_ASSOC)){
        $moje_grupe[] = $row_category_moje_grupa['id'];
        }
       
       $brojac_skola=0;
       $brojac_moji_djaci = 0;
       while ($row_grupe_skola = $stt1->fetch(PDO::FETCH_ASSOC)){
        //   echo '<pre>'. var_dump($row_grupe_online).'</pre>';
           if(in_array($row_grupe_skola["fk_grupa"], $skola_grupe) ){
            $brojac_skola++;
           }
           if(in_array($row_grupe_skola["fk_grupa"], $moje_grupe) ){
            $brojac_moji_djaci++;
           }

       }

       if($brojac_skola > 0 && $brojac_moji_djaci > 0){

        ?>

        <tr style="font-size: 12px;" >
        
            <td style="font-size: 12px; width-max:38%; display:inline-block;"><a title='detalji' href="izmena_djak.php?id=<?php echo $row['id']; ?>" > 
                 <img src="images/room2.jpg"  style="width: auto;  height: 25px; padding-left: 5px; padding-right: 5px;padding-top: 4px; ">
            
            <?php   
            echo "<span>",$firstname,"&nbsp;",$lastname; ?></span> </a> </td>
            <td style="font-size: 12px;"><?php echo $contact_number; ?></td>
            <td style="font-size: 12px;"><?php echo $address; ?></td>
            <td style=" font-size: 12px; max-width: 12% ">
            <?php
                $nivo_znanja = new nivo_znanja($db);
                $jezik = new jezik($db);
                while ($row_grupe = $stt->fetch(PDO::FETCH_ASSOC))
                {
                    $sst = $grupa-> read_one_grupa1($row_grupe['fk_grupa']);
                    $row_citanje_grupe = $sst->fetch(PDO::FETCH_ASSOC);
                    extract($row_citanje_grupe);
                    //echo "nivo=", $nivo;
                    $stm_jezik = $jezik->read_one_jezik($fk_jezik,'jezik');
                    $row_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC);


                   
                    $stm_nivo = $nivo_znanja->read_one_nivo($nivo,'nivo_znanja');
                    $row_nivo_znanja = $stm_nivo->fetch(PDO::FETCH_ASSOC);
                
                    $i++;
                    if($nacin == 1){
                echo"<span class='label label-default' style='line-height: 200%' >";
                    
                        echo $row_jezik['alias'],"&nbsp;" ,$row_nivo_znanja['ime'], "&nbsp;", $alias;   
                   
                    
                echo" </span><br/>"; 
                    } 

                }
            ?>
        
            </td>
            <td style='text-align:center;'>
            <?php 
                $stmt_lokacija = $lokacija->read_one($row['fk_lokacija']);
                $row_lokacija =  $stmt_lokacija->fetch(PDO::FETCH_ASSOC);
                echo $row_lokacija['ime'] ?? "Nema lokaciju";
                ?>
            
            </td>
            <td class="text-center">
                &nbsp;<a style="color:white;" class="btn btn-info btn-sm" href="dodavanje_djak.php?id=<?php echo $row['id']; ?>  "  > <span class="glyphicon glyphicon-plus"></span>u grupu</a>
                <?php
                $broj_grupa_u_kojima_je = $povezivanje->count_grupa($row['id']);
                if($broj_grupa_u_kojima_je>0)
                    { ?>
                        &nbsp;<a class="btn btn-info btn-sm" style="color:white;" href="izbac_djak.php?id=<?php echo $row['id']; ?>  " > <span class="glyphicon glyphicon-minus"></span> iz grupe</a>
                        <?php
                    }
                    ?>
                    &nbsp;&nbsp;<a class="btn btn-success btn-sm" style="color:white;"  href="evidencija_djak.php?id=<?php echo $row['id']; ?>  "  target="_blank">ev. prisustva</a>
                   
            </td>
        </tr>
   <?php
       }
    }
    ?>
        </tbody>
    </table>
  <!-- <script>
       $(document).ready(function()
       {
           $('#example').DataTable(
               {
               "language": { "url": "../tabele/serbian.lang"  },
               "lengthMenu": [[-1, 25, 50,100 ], [ "SVI",25, 50,100]],
                   select: true
                } );
       } );

    </script> -->
     <script>
         $(document).ready(function()
         {
             var table = $('#example').DataTable(
                 {
					'columns': [
                         null,
                         null,
                         null,
                         null,
                         null,
                         { orderable: false }

                     ],
                     "order": [],
                     "language": {"url": "tabele/serbian.lang"},
                     "lengthMenu": [[-1, 25, 50, 100], ["sve", 25, 50, 100]], 
                        dom: 'lBfrtip', 
                        buttons:    [{
                                            extend: 'excel',
                                    text:       'Izvezi u Excel',
                                    customData: function (exceldata) {
                                                exportExtension = 'Excel';
                                                return exceldata;
                                        },
                                        exportOptions: {
                                        columns: [ 0, 1, 2, 3, ]
                                        }
                                }
                                
                            ]
                 }
             );

             $('#example tbody').on( 'click', 'tr', function () {
                 if ( $(this).hasClass('selected') ) {
                     $(this).removeClass('selected');
                 }
                 else {
                     table.$('tr.selected').removeClass('selected');
                     $(this).addClass('selected');
                 }
             } );

             $('#button').click( function () {
                 table.row('.selected').remove().draw( false );
             } );
         } );
      </script>

<?php
    //

    // $page_url="read_djak.php?";
    // $total_rows = $djak->countAll_djak();

    // actual paging buttons
    // include_once 'paging.php';
}

// tell the user there are no selfies
else{
    echo "<div class='alert alert-danger'>
        <strong>No users found.</strong>
    </div>";
}
?>