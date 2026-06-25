<?php
// display the table if the number of users retrieved was greater than zero
if($num>0){

    echo "<table class='table table-hover table-responsive table-bordered'>";

    // table headers
    echo "<tr style='text-align: center;' >";
    echo "<th>Ime</th>";
    echo "<th>Prezime</th>";
    echo "<th>Email</th>";
    echo "<th>Broj telefona</th>";
    echo "<th>Vrsta pristupa</th>";
    echo "<th>Boja u kalendaru:</th>";
	
  //  echo "<th>Akcija</th>";
    echo "</tr>";

    // loop through the user records
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        extract($row);

        // display user details
        echo "<tr>";
        echo "<td>{$firstname}</td>";
        echo "<td>{$lastname}</td>";
        echo "<td>{$email}</td>";
        echo "<td>{$contact_number}</td>";
        if($access_level == 'Customer') { $access_level1 = "Profesor";}
			elseif($access_level == 'Sekretar'){
                $access_level1 = "Sekretar";
            }else{ $access_level1 = "Admin"; }
        
        
        echo "<td  align = 'center'>{$access_level1}</td>"; ?>

        <td align="center" >
            <?php
            if($access_level=='Customer'){
				//echo $color_prof;
            ?>
            <svg width="40" height="20">
                <rect width="40" height="20" style="fill:<?php echo $color_prof; ?>;stroke-width:0;stroke:rgb(0,0,0)" />
                Sorry, your browser does not support inline SVG.
            </svg>
             <?php } ?>
        </td>
        
		
		
      </tr>
    <?php  
    }

    echo "</table>";

    $page_url="read_users.php?";
   // $total_rows = $user->countAll();

    // actual paging buttons
   //include_once 'paging.php';
}

// tell the user there are no selfies
else{
    echo "<div class='alert alert-danger'>
        <strong>No users found.</strong>
    </div>";
}
?>