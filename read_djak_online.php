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
                        <h6 class="mb-0 text-uppercase">Moji online polaznici:</h6>
                    </div>
                    <div class="col-md-6 text-end">
                        <a class="btn btn-primary btn-sm"
                        href="<?php echo $home_url; ?>novi_djak">
                            <i class="bi bi-plus fs-6"></i>
                            Novi polaznik
                        </a>
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
                $djak = new djak($db);

                // initialize objects
                $stmt = $djak->readAll_djak_skola_online_prof($_SESSION['user_id']); 
                ?>
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table id="example" class="table table-striped table-bordered text-center" style="width:100%">
								<thead><tr><th>Ime i prezime</th><th style="width:12%;">Broj telefona</th><th style="width:10em;">Adresa</th><th style="width: 10em;">Grupa</th><th style="width:370px;">Akcija</th></tr></thead>
                                <tfoot><tr><th>Ime i prezime</th><th>Broj telefona</th><th>Adresa</th><th>Grupa</th><th>Akcija</th></tr></tfoot>
								<tbody>
									 <?php

                                    $i=0;
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                                        $i++;
                                        extract($row);

                                        // include_once 'objects/grupa.php';
                                        // include_once 'objects/povezivanje.php';
                                        // include_once 'objects/nivo_znanja.php';
                                        // include_once 'objects/jezik.php';
                                        $grupa = new grupa($db);
                                        $povezivanje = new povezivanje($db);

                                        $stt = $povezivanje->read_all_group_students($id);
                                        $stt1 = $povezivanje->read_all_group_students($id);
                                    // $row_grupe = $stt->fetch(PDO::FETCH_ASSOC);
                                    
                                    $stmt_online_grupa = $grupa->read_all_online();
                                    $stmt_moje_grupa = $grupa->read_all_moji($_SESSION['user_id']);
                                    $online_grupe = array();
                                    $moje_grupe = array();
                                    while ($row_category_online_grupa = $stmt_online_grupa->fetch(PDO::FETCH_ASSOC)){
                                        $online_grupe[] = $row_category_online_grupa['id'];
                                    }
                                    while ($row_category_moje_grupa =$stmt_moje_grupa->fetch(PDO::FETCH_ASSOC)){
                                        $moje_grupe[] = $row_category_moje_grupa['id'];
                                        }
                                    
                                    $brojac_online=0;
                                    $brojac_moji_djaci = 0;
                                    while ($row_grupe_online = $stt1->fetch(PDO::FETCH_ASSOC)){
                                        //   echo '<pre>'. var_dump($row_grupe_online).'</pre>';
                                        if(in_array($row_grupe_online["fk_grupa"], $online_grupe) ){
                                            $brojac_online++;
                                        }
                                        if(in_array($row_grupe_online["fk_grupa"], $moje_grupe) ){
                                            $brojac_moji_djaci++;
                                        }

                                    }

                                    if($brojac_online > 0 && $brojac_moji_djaci>0 ){

                                        ?>

                                        <tr style="font-size: 12px;" >
                                        
                                            <td style="font-size: 12px; width:20%"><a title='detalji' href="izmena_djak.php?id=<?php echo $row['id']; ?>" > 
                                                <img src="images/online.png"  style="width: auto;  height: 25px; padding-left: 5px; padding-right: 15px;padding-top: 4px; ">
                                            
                                            <?php   
                                            echo $firstname,"&nbsp;",$lastname; ?> </a> </td>
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
                                                    if($nacin == 2){
                                                echo"<span class='label label-default' style='line-height: 200%' >";
                                                    
                                                        echo $row_jezik['alias'],"&nbsp;" ,$row_nivo_znanja['ime'], "&nbsp;", $alias;   
                                                
                                                    
                                                echo" </span><br/>"; 
                                                    } 

                                                }
                                            ?>
                                        
                                            </td>
                                            <td class="text-center" style="width: 380px">
                                                
                                                    &nbsp;&nbsp;<a class="btn btn-outline-success btn-sm"   href="evidencija_djak.php?id=<?php echo $row['id']; ?>  "  target="_blank"><i class="bx bxs-check-circle"></i>Ev. dolaska</a>
                                                   &nbsp;&nbsp;<a class="btn btn-outline-danger btn-sm"   href="kartica_djak.php?id=<?php echo $row['id']; ?>  "  ><i class="bx bxs-dollar-circle"></i> Card</a> 
                                            </td>
                                        </tr>
                                <?php
                                    }
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