<head>
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
</head>
<?php
// display the table if the number of users retrieved was greater than zero
if($num>0){ ?>

    <table id="example" class="display table-bordered text-center"  style="font-size: 12px;">
      <thead><tr><th>online/<br/>skola</th><th>Ime i prezime</th><th style="width:12%;">Broj telefona</th><th style="width:12em;">Porodica</th>
      <th style="width: 10em;">Grupa</th><?php if($broj_lokacija > 1){ ?><th>Lokacija</th><?php } ?><th style="width:370px;">Akcija</th></tr></thead>
      <tfoot><tr><th>online/<br/>skola</th><th>Ime i prezime</th><th>Broj telefona</th><th>Porodica</th><th>Grupa</th>
      <?php if($broj_lokacija > 1){ ?><th>Lokacija</th><?php } ?><th>Akcija</th></tr></tfoot>
        <tbody>
 <?php
 /*
    include_once '../objects/grupa.php';
    include_once '../objects/povezivanje.php';
    include_once '../objects/nivo_znanja.php';
    include_once '../objects/jezik.php';
    include_once '../objects/djak_ispit.php';
    include_once '../objects/polozeni_ispit.php';
    include_once '../objects/lokacija.php';
  */ 
    $grupa = new grupa($db);
    $povezivanje = new povezivanje($db);
    $djak_ispit = new djak_ispit($db);
    $polozeni_ispit = new polozeni_ispit($db);
    $lokacija = new lokacija($db);
    $profesor = new user($db);
    $porodica = new porodica($db);
    $i=0;
	while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        $i++;
        extract($row);

        

        $stt = $povezivanje->read_all_group_students($id);
        $stt1 = $povezivanje->read_all_group_students($id);
       // $row_grupe = $stt->fetch(PDO::FETCH_ASSOC);
       $stmt_online_grupa = $grupa->read_all_online();
       $stmt_skola_grupa = $grupa->read_all_skola();
       $online_grupe = array();
       $skola_grupe = array();
       while ($row_category_online_grupa = $stmt_online_grupa->fetch(PDO::FETCH_ASSOC)){
          $online_grupe[] = $row_category_online_grupa['id'];
       }

       while ($row_category_skola_grupa = $stmt_skola_grupa->fetch(PDO::FETCH_ASSOC)){
        $skola_grupe[] = $row_category_skola_grupa['id'];
       }

       $brojac_online=0;
       $brojac_skola=0;
       while ($row_grupe_online = $stt1->fetch(PDO::FETCH_ASSOC)){
        //   echo '<pre>'. var_dump($row_grupe_online).'</pre>';
           if(in_array($row_grupe_online["fk_grupa"], $online_grupe) ){
            $brojac_online++;
           }
           if(in_array($row_grupe_online["fk_grupa"], $skola_grupe) ){
            $brojac_skola++;
           }

       }




        ?>
        <tr style="font-size: 12px; " >
           
            <td style=" width: 7%; " >
                <a title='detalji' href="izmena_djak.php?id=<?php echo $row['id']; ?>" >
                    <?php
                    if($brojac_online > 0){ ?>
                        <span class="glyphicon glyphicon-stats"></span>
                        <p style="display:none">online</p>
                        <?php
                    } 
                    if($brojac_skola > 0){ ?>
                        <span class="glyphicon glyphicon-home"></span>  <p style="display:none">skola</p>
                    <?php    
                    } ?>
                </span></a></td>
            <td style='font-size: 12px; width:15%''>
            <a title='detalji' href="prikaz_djak.php?id=<?php echo $row['id']; ?>" >
            <?php     
            echo $firstname,"&nbsp;",$lastname; ?> </a> </td>
            <td style="font-size: 12px; width: 10%;"><?php echo $contact_number; ?></td>
            <td style="font-size: 12px; max-width: 28%;">
            <?php 
             if($fk_porodica != 0){
                $stmt_porodica = $porodica->read_one($fk_porodica);
                $row_porodica = $stmt_porodica->fetch(PDO::FETCH_ASSOC); ?>
                <span data-toggle="modal" data-target="#exampleModal" data-id="<?php echo $fk_porodica; ?>" style='margin-top : 5px; margin-left : 2px; font-size: 0.9em; border-radius:1px;color:black !important; background-color: #00ff88; ' class='badge badge-sm-secondary'><?=$row_porodica['ime'] ?> </span>
          
                <?php
    }
                


             
               
            ?>
            <script>
            $(document).ready(function(){
                $('#exampleModal').on('show.bs.modal', function (e) {
                    var drowid = $(e.relatedTarget).data('id');
                   // alert (drowid);
                   console.log(drowid);
                    $.ajax({
                        type : 'post',
                        url : 'admin/prikaz_porodica.php', //Here you will fetch records 
                        data :  'drowid='+ drowid, //Pass $id
                        success : function(data){
                        $('.fetched-data').html(data);//Show fetched data from database
                        }
                    });
                });
            });
        </script> 
            </td>
            <td style="padding-left: 10px;  font-size: 12px; max-width: 12% ">
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

                    $stmt_prof = $profesor->read_one($row_citanje_grupe['fk_profesor']);
                    $row_profesor =  $stmt_prof->fetch(PDO::FETCH_ASSOC);

                   
                    $stm_nivo = $nivo_znanja->read_one_nivo($nivo,'nivo_znanja');
                    $row_nivo_znanja = $stm_nivo->fetch(PDO::FETCH_ASSOC);
                
                    $i++;
                
                    $color=$row_profesor['color_prof'];
                    $colorcheck=strtolower($color[3]); ?>
                    <a  href = "update_grupa_pregled?id=<?php echo $row_grupe['fk_grupa']; ?> " >
                    <?php
                    if($colorcheck>6 || is_numeric($colorcheck)===false ){
                        echo"<span style='margin-top : 5px; margin-left : 2px; font-size: 0.9em; border-radius:1px;color:black !important; background-color: {$row_profesor['color_prof']}; ' class='badge badge-sm-secondary'>{$row_nivo_znanja['ime']}&nbsp;{$alias} </span>";
                    }else{
                        echo"<span style='margin-top : 5px; margin-left : 2px; font-size: 0.9em;; border-radius:1px;color:white !important; background-color: {$row_profesor['color_prof']}; ' class='badge badge-sm-secondary'>{$row_nivo_znanja['ime']}&nbsp;{$alias}</span>";
                    }
                    echo"</a>";   

                }
            ?>
        
            </td>
            <?php if($broj_lokacija > 1){ ?>
            <td style='text-align:center;'>
            <?php 
                $stmt_lokacija = $lokacija->read_one($row['fk_lokacija']);
                $row_lokacija =  $stmt_lokacija->fetch(PDO::FETCH_ASSOC);
                echo $row_lokacija['ime'];
                ?>
            
            </td>
            <?php } ?>
            <td class="text-center" style="width: 30%">
                &nbsp;<a  class="btn btn-outline-primary btn-sm" href="dodavanje_djak?id=<?php echo $row['id']; ?>  " ><span class="glyphicon glyphicon-plus-sign"></span> u grupu</a>
                <?php
                $broj_grupa_u_kojima_je = $povezivanje->count_grupa($row['id']);
                if($broj_grupa_u_kojima_je>0)
                    { ?>
                        &nbsp;<a class="btn btn-outline-primary btn-sm" href="izbac_djak.php?id=<?php echo $row['id']; ?>  " ><span class="glyphicon glyphicon-minus-sign"></span> iz grupe</a>
                        <?php
                    }
                    ?>
                    &nbsp;&nbsp;<a class="btn btn-outline-success btn-sm"  href="evidencija_djak.php?id=<?php echo $row['id']; ?>  "  target="_blank"><span class="glyphicon glyphicon-question-sign"></span> dolaska</a>
                    &nbsp;&nbsp;<a class="btn btn-outline-danger btn-sm"   href="kartica_djak.php?id=<?php echo $row['id']; ?>  "  ><span class="glyphicon glyphicon-euro"></span> Card</a>
            </td>
        </tr>
   <?php
    }
    ?>
        </tbody>
    </table>
   
   

  <!-- Edit Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" 
            aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <button type="button" class="close" 
                        data-dismiss="modal">
                            <span aria-hidden="true">×</span>
                            <span class="sr-only">Close</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">
                            Detalji:
                        </h4>
                    </div>
                    
                    <!-- Modal Body -->
                    <div class="modal-body">
                        <div class="fetched-data"></div> 
                        
                    </div>
                
                </div>
            </div>
        </div>
<!-- End Modal Footer -->


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
					 "order": [],
                     "language": {"url": "../tabele/serbian.lang"},
                     "lengthMenu": [[ 25, 50, 100, -1], [ 25, 50, 100, "sve"]],
                     dom: 'lBfrtip',
                        buttons:    [{
                                            extend: 'excel',
                                    text:       'Izvezi u Excel',
                                    customData: function (exceldata) {
                                                exportExtension = 'Excel';
                                                return exceldata;
                                        },
                                        exportOptions: {
                                        columns: [  1, 2, 3, 4 ]
                                        }
                                }
                                
                            ]
                 }
             );

            
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