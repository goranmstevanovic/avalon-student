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
$lokacija = new lokacija($db);
 $broj_lokacija = $lokacija->count_all();
// display the table if the number of users retrieved was greater than zero
if($num>0){ ?>
<form>
    <table id="example" class="display" style="font-size: 12px;" >

    <!-- table headers -->
   <thead><tr><th >Jezik</th><th  >Nivo</th><th >Profesor</th><th >Alias</th><th>Broj djaka</th><?php if($broj_lokacija > 1){?><th>Lokacija</th><?php } ?><th>Online<br/>skola <th>Uzrast</th><th>Udzbenici</th> </th><th>Akcija</th></tr></thead>
   <tfoot><tr><th >Jezik</th><th  >Nivo</th><th >Profesor</th><th >Alias</th><th>Broj djaka</th><?php if($broj_lokacija > 1){?><th>Lokacija</th><?php } ?><th>Online<br/>skola <th>Uzrast</th><th>Udzbenici</th> </th><th>Akcija</th></tr></tfoot>

	<tbody>
<?php
    // loop through the user records
    $i=0;
    include_once 'objects/nivo_znanja.php';
    include_once 'objects/jezik.php';
    include_once 'objects/velicina.php';
    include_once 'objects/udzbenik.php';
    include_once 'objects/udzbenik_grupa.php';
    include_once 'objects/uzrast.php';
    $nivo_znanja = new nivo_znanja($db);
    $jezik = new jezik($db);
    $velicina1 = new velicina($db);
    $udzbenik = new udzbenik($db);
    $uzrast1 = new uzrast($db);
    $udzbenik_grupa = new udzbenik_grupa($db);
   
    while ($row = $stmt33->fetch(PDO::FETCH_ASSOC))
    { 
	    $bass_taj= $row['id'];
        ?>
        <tr id="<?php echo $row['id']; ?> ">
        <?php
            extract($row);
            $stm_jezik = $jezik->read_one_jezik($fk_jezik,'jezik');
            $row_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC);
           
            $stm_nivo = $nivo_znanja->read_one_nivo($nivo,'nivo_znanja');
            $row_nivo_znanja = $stm_nivo->fetch(PDO::FETCH_ASSOC);
        
        $num = $povezivanje->count_student($id);
        $profesor = new user($db);
        $stmt = $profesor->read_one_profesor($fk_profesor,"users");
        $row_category_profesor = $stmt->fetch(PDO::FETCH_ASSOC);

        echo "<td>{$row_jezik['ime']}</td>";
        echo "<td>{$row_nivo_znanja['ime']}</td>";
        echo "<td>{$row_category_profesor['firstname']}&nbsp;{$row_category_profesor['lastname']}</td>";
        echo "<td>{$alias}</td>";
        echo "<td align='center'>{$num}</td>"; ?>
        <?PHP if($broj_lokacija > 1){ ?>
            <td>
            <?php 
                $stmt_lokacija = $lokacija->read_one($row['fk_lokacija']);
                $row_category_lokacija = $stmt_lokacija->fetch(PDO::FETCH_ASSOC);
                if($nacin == 1){
                    echo $row_category_lokacija['ime'];
                }
            ?>
            </td>
            <?PHP 
        } ?>
        <td>
        <?php
            if($nacin == 2){ ?>
                <img src="images/online.png"  style="width: auto;  height: 25px; padding-left: 5px; padding-right: 15px;padding-top: 4px; ">
                <p style="display:none">online</p>
            <?php
            }else{ ?>
                    <img src="images/room2.jpg"  style="width: auto;  height: 25px; padding-left: 5px; padding-right: 15px;padding-top: 4px; ">
                    <p style="display:none">skola</p>
            <?php } ?>
        </td>                
        <td>
            <?php 
            $stmt_uzrast = $uzrast1->read_one($uzrast);
            $row_category_uzrast = $stmt_uzrast->fetch(PDO::FETCH_ASSOC);
            echo $row_category_uzrast['ime'];
            
           
            
            ?>  
           
               
                
            
                
        </td>
        <td>
        <?php 
            $stmt_udzbenici = $udzbenik_grupa->read_all_udzbenik_grupa($row['id']);
            while ($row_udbenici = $stmt_udzbenici->fetch(PDO::FETCH_ASSOC)){
              //  var_dump($row_udbenici['fk_udzbenik']);
                $stmt_ime_udzbenik = $udzbenik->read_one1($row_udbenici['fk_udzbenik'],'udzbenici');
                $row_ime_udbenici = $stmt_ime_udzbenik->fetch(PDO::FETCH_ASSOC);
              //  var_dump($stmt_ime_udzbenik);
              //  echo $row_ime_udbenici['ime'],"<br/>";
                echo"<div class='col'><span class='label label-success' style='line-height: 200%; margin-right: 5px; font-size :10px' >";
                echo  /*$row_category_polozen_ispit['datum'],"&nbsp;", */$row_ime_udbenici['ime'];
                echo" </span></div>";  

            }
        
        ?>
              
        </td>
       
        <td align="center" >
        <a class="btn btn-outline-success btn-sm"  href = "fullcalendar4/kalendar_grupa?grupa=<?php echo $row['id']; ?> " >
            <span class="glyphicon glyphicon-calendar"></span> &nbsp;<b>Raspored</b> </a>
            <a class="btn btn-outline-primary btn-sm"  href = "update_grupa.php?id=<?php echo $row['id']; ?> " >
            <span class="glyphicon glyphicon-edit"></span> &nbsp;<b>Izmeni</b> </a>
            <a class="btn btn-outline-success btn-sm"  href = "dnevnik_grupa?grupa=<?php echo $row['id']; ?> " >
            <span class="glyphicon glyphicon-calendar"></span> &nbsp;<b>Dnevnik rada</b> </a>
            <a class='btn btn-outline-danger btn-sm' style="margin-left:5px" href="kartica_grupa.php?id=<?php echo $row['id']; ?> " >
            <span class="glyphicon glyphicon-euro"></span> &nbsp; kartica</a>    
               
        </td>
	
	    <!--	<td align="center" ><button class="btn btn-danger btn-sm remove" style="border:none;" ><img style="border:0; outline: none;" src="kanta32.png" width=24 height=24>  </button></td> -->
        </tr>


        <?php
    }
        ?>
	</tbody>
    </table>
    </form>
	
	<script>
         $(document).ready(function()
         {
             var table = $('#example').DataTable(
                 {
					
                     "order": [],
                     "language": {"url": "tabele/serbian.lang"},
                     "lengthMenu": [[10, 25, 50, 100,-1], [ 10, 25, 50, 100,"sve"]],
                     dom: 'lBfrtip',
                        buttons:    [{
                                            extend: 'excel',
                                    text:       'Izvezi u Excel',
                                    customData: function (exceldata) {
                                                exportExtension = 'Excel';
                                                return exceldata;
                                        },
                                        exportOptions: {
                                        columns: [ 0, 1, 2, 3, 4, 5 ]
                                        }
                                }
                                
                            ]
                 }
             );
                         
         } );
      </script>
 
	
<script type="text/javascript">
    $(".remove").click(function(){
        var idd = $(this).data("rowid");
        var row = $(this).closest("tr");
        /*   alert("Zdravo id" + id + "! Kako si danas?"); */
        //   alert("Zdravo idd: " + idd + "! Kako si danas?"); 
        if (typeof idd !== 'undefined')
        {
            if (confirm('Jeste li sigurni da želite da deaktivirate grupu ?')) {
                $.ajax({
                    type: "POST",
                    url: "delete_grupa.php",
                    data: {
                        operation: "remove",
                        idd: idd
                    },
                    error: function () {
                        alert('Nešto nije u redu sa brisanjem, kontaktiraj Goran-a');
                    },
                    success: function (data) {
                        row.remove();
                        /* $("#" + ddid).remove(); */
                        alert("Uspešno deaktivirana grupa");
						//location.reload(); 
                    }
                });
            }
        }
    });


</script>
<?php
    $page_url="read_grupa.php?";
  //  $total_rows = $grupa->countAll_grupa();

    // actual paging buttons
  //  include_once 'paging.php';
}

// tell the user there are no selfies
else{
    echo "<div class='alert alert-danger'>
        <strong>No users found.</strong>
    </div>";
}
?>