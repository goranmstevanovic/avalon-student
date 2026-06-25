<!doctype html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--favicon-->
	<link rel="icon" href="images/kalen.png" type="image/png" />
	<!--plugins-->
	<link href="assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
	<link href="assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
	<link href="assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
	<link href="assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- loader-->
	<link href="assets/css/pace.min.css" rel="stylesheet" />
	<script src="assets/js/pace.min.js"></script>
	<!-- Bootstrap CSS -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/css/bootstrap-extended.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
	<link href="assets/css/app.css" rel="stylesheet">
	<link href="assets/css/icons.css" rel="stylesheet">
	<!-- Theme Style CSS -->
	<link rel="stylesheet" href="assets/css/dark-theme.css" />
	<link rel="stylesheet" href="assets/css/semi-dark.css" />
	<link rel="stylesheet" href="assets/css/header-colors.css" />
	<title>Trajanje casova</title>
</head>

<body>
	<!--wrapper-->
	<div class="wrapper">
		<!--sidebar wrapper -->
		<!--sidebar wrapper -->
		  <?php 
         // core configuration
        include_once "config/core.php";

        // check if logged in as admin
        include_once "login_checker.php";

          
          include __DIR__ . '/app/sidebar.php'; ?>
		<!--end sidebar wrapper -->
		<!--start header -->
		
		<!--end sidebar wrapper -->
		<!--start header -->
		<header>
			<?php include __DIR__ . '/app/header.php'; ?>
		</header>
		<!--end header -->
		<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!--breadcrumb-->
		
				<!--end breadcrumb-->
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="mb-0 text-uppercase">Moje grupe:</h6>
                    </div>
                    <div class="col-md-6 text-end">
                        <!-- <a class="btn btn-primary btn-sm"
                        href="<?php echo $home_url; ?>nova_grupa">
                            <i class="bi bi-plus fs-6"></i>
                            Nova grupa
                        </a> -->
                    </div>

                </div>
				
                 
				<hr/>
                <?php
                // core configuration
                include_once "config/core.php";

                // check if logged in as admin
                include_once "login_checker.php";

                // include classes
                include_once 'config/database.php';
                include_once 'config/autoload.php';


                // get database connection
                $database = new Database();
                $db = $database->getConnection();
                $grupa = new grupa($db);

                // initialize objects
                $stmt33 = $grupa->read_allgrupa_profesor("grupe", $_SESSION['user_id']); 
                ?>
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table id="example" class="table table-striped table-bordered text-center" style="width:100%">
								 <thead><tr><th >Jezik</th><th  >Nivo</th><th >Profesor</th><th >Alias</th><th>Broj djaka</th><?php if($broj_lokacija > 1){?><th>Lokacija</th><?php } ?><th>Online<br/>skola <th>Uzrast</th> </th><th>Akcija</th></tr></thead>
                                 <tfoot><tr><th >Jezik</th><th  >Nivo</th><th >Profesor</th><th >Alias</th><th>Broj djaka</th><?php if($broj_lokacija > 1){?><th>Lokacija</th><?php } ?><th>Online<br/>skola <th>Uzrast</th> </th><th>Akcija</th></tr></tfoot>
									 <?php
    // loop through the user records
    $i=0;

    $nivo_znanja = new nivo_znanja($db);
    $jezik = new jezik($db);
    $velicina1 = new velicina($db);
    $udzbenik = new udzbenik($db);
    $uzrast1 = new uzrast($db);
    $udzbenik_grupa = new udzbenik_grupa($db);
    $povezivanje = new povezivanje($db);
   
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

       
        <td align="center" >
                                        <a class="btn btn-outline-success btn-sm"
                                        href="fullcalendar4/kalendar_grupa?grupa=<?php echo $row['id']; ?>"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="Raspored">
                                            <i class="bi bi-calendar3"></i>
                                        </a>

                                        <a class="btn btn-outline-primary btn-sm"
                                        href="update_grupa_pregled?id=<?php echo $row['id']; ?>"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="Detalji grupe">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>

                                        <a class="btn btn-outline-info btn-sm"
                                        href="dnevnik_grupa?grupa=<?php echo $row['id']; ?>"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="Dnevnik rada">
                                            <i class="bi bi-journal-text"></i>
                                        </a>

                                        <!-- <a class="btn btn-outline-danger btn-sm ms-1"
                                        href="kartica_grupa.php?id=<?php echo $row['id']; ?>"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top"
                                        title="Finansijska kartica">
                                            <i class="bi bi-cash-coin"></i>
                                        </a>    -->
               
        </td>
	
	    <!--	<td align="center" ><button class="btn btn-danger btn-sm remove" style="border:none;" ><img style="border:0; outline: none;" src="kanta32.png" width=24 height=24>  </button></td> -->
        </tr>


        <?php
    }
        ?>
								
								</tbody>
								

							</table>
						</div>
					</div>
				</div>
				
			</div>
		</div>
		<!--end page wrapper -->
		<!--start overlay-->
		<div class="overlay toggle-icon"></div>
		<!--end overlay-->
		<!--Start Back To Top Button--> <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
		<!--End Back To Top Button-->
		<footer class="page-footer">
			<?php include __DIR__ . '/app/footer.php'; ?>
		</footer>
	</div>
	<!--end wrapper-->

	<!-- search modal -->
    <div class="modal" id="SearchModal" tabindex="-1">
		<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down">
		  <div class="modal-content">
			<div class="modal-header gap-2">
			  <div class="position-relative popup-search w-100">
				<input class="form-control form-control-lg ps-5 border border-3 border-primary" type="search" placeholder="Search">
				<span class="position-absolute top-50 search-show ms-3 translate-middle-y start-0 top-50 fs-4"><i class='bx bx-search'></i></span>
			  </div>
			  <button type="button" class="btn-close d-md-none" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="search-list">
				   <p class="mb-1">Html Templates</p>
				   <div class="list-group">
					  <a href="javascript:;" class="list-group-item list-group-item-action active align-items-center d-flex gap-2 py-1"><i class='bx bxl-angular fs-4'></i>Best Html Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-vuejs fs-4'></i>Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-magento fs-4'></i>Responsive Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-shopify fs-4'></i>eCommerce Html Templates</a>
				   </div>
				   <p class="mb-1 mt-3">Web Designe Company</p>
				   <div class="list-group">
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-windows fs-4'></i>Best Html Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-dropbox fs-4' ></i>Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-opera fs-4'></i>Responsive Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-wordpress fs-4'></i>eCommerce Html Templates</a>
				   </div>
				   <p class="mb-1 mt-3">Software Development</p>
				   <div class="list-group">
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-mailchimp fs-4'></i>Best Html Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-zoom fs-4'></i>Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-sass fs-4'></i>Responsive Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-vk fs-4'></i>eCommerce Html Templates</a>
				   </div>
				   <p class="mb-1 mt-3">Online Shoping Portals</p>
				   <div class="list-group">
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-slack fs-4'></i>Best Html Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-skype fs-4'></i>Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-twitter fs-4'></i>Responsive Html5 Templates</a>
					  <a href="javascript:;" class="list-group-item list-group-item-action align-items-center d-flex gap-2 py-1"><i class='bx bxl-vimeo fs-4'></i>eCommerce Html Templates</a>
				   </div>
				</div>
			</div>
		  </div>
		</div>
	  </div>
    <!-- end search modal -->



	<!--start switcher-->
	
	<!--end switcher-->
	<!-- Bootstrap JS -->
	<script src="assets/js/bootstrap.bundle.min.js"></script>
	<!--plugins-->
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
	<script src="assets/plugins/metismenu/js/metisMenu.min.js"></script>
	<script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
	<script src="assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
	<script src="assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
<script>
$(document).ready(function() {

    $('#example').DataTable({
        
        language: {
            url: "tabele/serbian.lang",
            searchPlaceholder: "Pretraga...",
            search: ""
        },

        lengthMenu: [
            [10, 25, 50, 100, -1],
            [10, 25, 50, 100, "Sve"]
        ],

        stateSave: true

    });

});
</script>
	<!-- <script>
		$(document).ready(function() {
			var table = $('#example2').DataTable( {
				lengthChange: false,
				buttons: [ 'copy', 'excel', 'pdf', 'print']
			} );
		 
			table.buttons().container()
				.appendTo( '#example2_wrapper .col-md-6:eq(0)' );
		} );
	</script> -->
	<!--app JS-->
     <script type="text/javascript">
    $(".remove").click(function(){
        var idd = $(this).data("rowid");
        var row = $(this).closest("tr");
        /*   alert("Zdravo id" + id + "! Kako si danas?"); */
        //   alert("Zdravo idd: " + idd + "! Kako si danas?"); 
        if (typeof idd !== 'undefined')
        {
            if (confirm('Jeste li sigurni da želite da obrisete duzinu trajanja casa ?')) {
                $.ajax({
                    type: "POST",
                    url: "delete_duzina_trajanja_casa.php",
                    data: {
                        operation: "remove",
                        idd: idd
                    },
                    error: function (data) {
                        alert('Doslo je do greske...');
                    },                     
                    success: function (data) {
                        if(data.status == "jok"){
                            alert("nece da moze, jer je doslo do greske sa upisom u bazu");
                        }else{
                            row.remove();
                        
                        alert("Uspešno obrisana duzina trajanja casa");
						location.reload(); 

                        }
                       
                        
                    }
                });
            }
        }
    });



</script>
	<script src="assets/js/app.js"></script>
</body>

</html>