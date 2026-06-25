<?php
// display the table if the number of users retrieved was greater than zero
if($num>0){ ?>

    <table id="example" class="display" style="width:100%">
         <thead><tr><th>Ime</th><th>Prezime</th><th>Broj telefona</th><th>Adresa</th><th>Grupa</th><th>Akcija</th></tr></thead>
         <tfoot><tr><th>Ime</th><th>Prezime</th><th>Broj telefona</th><th>Adresa</th><th>Grupa</th><th>Akcija</th></tr></tfoot>
        <tbody>
        <?php

        $i=0;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $i++;
            extract($row);

            include_once 'objects/grupa.php';
            include_once 'objects/povezivanje.php';
            $grupa = new grupa($db);
            $povezivanje = new povezivanje($db);

            $stt = $povezivanje->read_all_group_students($id);
            // $row_grupe = $stt->fetch(PDO::FETCH_ASSOC);




            ?>
            <tr>
                <td><?php echo $firstname; ?> </td>
                <td><?php echo $lastname; ?> </td>
                <td><?php echo $contact_number; ?></td>
                <td><?php echo $address; ?></td>
                <td style="padding-left: 10px; padding-left: 10px; ">
                    <?php
                    while ($row_grupe = $stt->fetch(PDO::FETCH_ASSOC))
                    {
                        $sst = $grupa-> read_one_grupa1($row_grupe['fk_grupa']);
                        $row_citanje_grupe = $sst->fetch(PDO::FETCH_ASSOC);
                        extract($row_citanje_grupe);
                        //echo "nivo=", $nivo;
                        if($fk_jezik==1){$fk_jezik="Eng ";}else{$fk_jezik="Nem ";}
                        switch ($nivo) {
                            case 1:
                                $nivo="A1";
                                break;
                            case 2:
                                $nivo="A1.1";
                                break;
                            case 3:
                                $nivo="A1.2";
                                break;
                            case 4:
                                $nivo="A2";
                                break;
                            case 5:
                                $nivo="A2.1";
                                break;
                            case 6:
                                $nivo="A2.2";
                                break;
                            case 7:
                                $nivo="B1";
                                break;
                            case 8:
                                $nivo="B1.1";
                                break;
                            case 9:
                                $nivo="B1.2";
                                break;
                            case 10:
                                $nivo="B2";
                                break;
                            case 11:
                                $nivo="B2.1";
                                break;
                            case 12:
                                $nivo="B2.2";
                                break;
                            case 13:
                                $nivo="C1";
                                break;
                            case 14:
                                $nivo="C1.1";
                                break;
                            case 15:
                                $nivo="C1.2";
                                break;
                            case 16:
                                $nivo="C2";
                                break;
                            case 17:
                                $nivo="C2.1";
                                break;
                            case 18:
                                $nivo="C2.2";
                                break;
                        }
                        $i++;
                        // echo $row_grupe['fk_grupa'];
                        echo $fk_jezik,"&nbsp;" ,$nivo, "&nbsp;", $alias,  "<br>";




                    }
                    ?>

                </td>
                <td>
                    <?php
                    $broj_grupa_u_kojima_je = $povezivanje->count_grupa($row['id']);
                    if($broj_grupa_u_kojima_je>0)
                    { ?>
                    &nbsp;<a class="btn btn-danger btn-sm" href="kartica_djak.php?id=<?php echo $row['id']; ?>  "  target="_blank">Finans. kartica</a>

                        <?php
                    }
                    ?>
                </td>
            </tr>
            <?php
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
                         { 'searchable': false }

                     ],
                    "language": {"url": "../tabele/serbian.lang"},
                    "lengthMenu": [[-1, 25, 50, 100], ["sve", 25, 50, 100]]
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