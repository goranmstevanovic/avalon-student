<?php
// display the table if the number of users retrieved was greater than zero
if($num>0){

    echo "<table class='table table-hover table-responsive table-bordered'>";

    // table headers
    echo "<tr style='text-align: center'>";
    echo "<th>R.br:</th>";
    echo "<th>Jezik:</th>";
    echo "<th>Nivo:</th>";
    echo "<th>Profesor:</th>";
    echo "<th>Alias:</th>";
    echo "<th>Broj djaka:</th>";
    echo "<th style='text-align: center' >Akcija:</th>";
    echo "</tr>";

    // loop through the user records
    $i=0;
    while ($row = $stmt33->fetch(PDO::FETCH_ASSOC)){
        extract($row);
        if($fk_jezik==1){$fk_jezik="Engleski";}else{$fk_jezik="Nemacki";}
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

        $num = $povezivanje->count_student($id);




        $profesor = new user($db);
        $stmt = $profesor->read_one_profesor($fk_profesor,"users");
        $row_category_profesor = $stmt->fetch(PDO::FETCH_ASSOC);
        // display user details
        echo "<tr>";
        echo "<td align='center'>{$i}.</td>";
        echo "<td>{$fk_jezik}</td>";
        echo "<td>{$nivo}</td>";
        echo "<td>{$row_category_profesor['firstname']}&nbsp;{$row_category_profesor['lastname']}</td>";
        echo "<td>{$alias}</td>";
        echo "<td align='center' >{$num}</td>";
        echo "<td style='text-align: center'>";
        if($_SESSION['user_id'] == $row_category_profesor['id'] ) {
            echo "<a class='btn btn-danger btn-sm' href=\"kartica_grupa.php?id= $id \"  >Finansijska kartica</a>";
        }
        echo "</td>";
        echo "</tr>";
    }

    echo "</table>";

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