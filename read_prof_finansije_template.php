<?php
// display the table if the number of users retrieved was greater than zero
if($num>0){

    echo "<table class='table table-hover table-responsive table-bordered'>";

    // table headers
    echo "<tr>";
    echo "<th>Ime</th>";
    echo "<th>Prezime</th>";
    echo "<th>Email</th>";
    echo "<th>Broj telefona</th>";
    echo "<th>Vrsta pristupa</th>";
    echo "<th>Akcija</th>";
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
        echo "<td>{$access_level}</td>";
        echo "<td>";
        if($_SESSION['user_id'] == $id ) {
            echo " <button class=\"btn\"><a href=\"kartica_profesora.php?id= $id \"  >Finansijska kartica</a></button>";
        }
        echo "</td>";
        echo "</tr>";
    }

    echo "</table>";

    $page_url="read_users.php?";
    $total_rows = $user->countAll();

    // actual paging buttons
    include_once 'paging.php';
}

// tell the user there are no selfies
else{
    echo "<div class='alert alert-danger'>
        <strong>No users found.</strong>
    </div>";
}
?>