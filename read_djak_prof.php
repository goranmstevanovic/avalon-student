<!doctype html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--favicon-->
	<link rel="icon" href="../images/kalen.png" type="image/png" />
	<!--plugins-->
	<link href="../assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
	<link href="../assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
	<link href="../assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
	<link href="../assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
	<!-- loader-->
	<link href="../assets/css/pace.min.css" rel="stylesheet" />
	<script src="../assets/js/pace.min.js"></script>
	<!-- Bootstrap CSS -->
	<link href="../assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="../assets/css/bootstrap-extended.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
	<link href="../assets/css/app.css" rel="stylesheet">
	<link href="../assets/css/icons.css" rel="stylesheet">
	<!-- Theme Style CSS -->
	<link rel="stylesheet" href="../assets/css/dark-theme.css" />
	<link rel="stylesheet" href="../assets/css/semi-dark.css" />
	<link rel="stylesheet" href="../assets/css/header-colors.css" />
	<title>Svi polaznici profesora </title>
</head>

<body>
	<!--wrapper-->
	<div class="wrapper">
		<!--sidebar wrapper -->
		  <?php include __DIR__ . '/app/sidebar.php'; ?>
		<!--end sidebar wrapper -->
		<!--start header -->
		<header>
			<?php include __DIR__ . '/app/header.php'; ?>
		</header>
		
		<!--end header -->
		<?php
			// core configuration
			include_once "../config/core.php";

			// check if logged in as admin
			include_once "login_checker.php";

			// include classes
			include_once '../config/database.php';
			include_once '../config/autoload.php';
			//include_once 'objects/djak.php';
			if(isset($_GET['id'])){
				$id_profesora = $_GET['id'];
			}
			// get database connection
			$database = new Database();
			$db = $database->getConnection();

			// initialize objects
			$djak = new djak($db);
			$user = new user($db);
			$stmt_profesor = $user->read_one($id_profesora);
			$row_prof = $stmt_profesor->fetch(PDO::FETCH_ASSOC);
			$stmt = $djak->read_all_djak_prof($id_profesora);
		?>	
			//echo basename(__FILE__, '.php');
		<!--start page wrapper -->
		<div class="page-wrapper">
			<div class="page-content">
				<!--breadcrumb-->
			
				<!--end breadcrumb-->
				
				<h6 class="mb-0 text-uppercase">Svi djaci profesora: <?=$row_prof['firstname']  ?> &nbsp; <?=$row_prof['lastname']  ?>:</h6>
				<hr/>
				<div class="card">
					<div class="card-body">
						<div class="table-responsive">
							<table id="example2" class="table table-striped table-bordered">
 							<thead>
								<tr><th>online/<br/>skola</th>
								<th>Ime i prezime</th>
								<th style="width:12%;">Broj telefona</th>
								<th style="width:12em;">Porodica</th>
      							<th style="width: 10em;">Grupa</th>
								<?php if($broj_lokacija > 1){ ?><th>Lokacija</th><?php } ?>
								<th style="width: 20%;">Akcija</th>
								</tr>
							</thead>
    
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
													<img src="../images/online.png"  style=" height: 25px; padding-left: 0px; padding-right: 3px;padding-top: 4px; ">
													<p style="display:none">online</p>
													<?php
												} 
												if($brojac_skola > 0){ ?>
													<span>    <img src="../images/room2.jpg"  style=" height: 25px; padding-left: 2px; padding-right: 0px;padding-top: 4px; ">
													<p style="display:none">skola</p>
													<?php    
												} ?>
											</span></a></td>
										<td style='font-size: 12px; width:15%''>
										<a title='detalji' href="izmena_djak.php?id=<?php echo $row['id']; ?>" >
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
										// $(document).ready(function(){
										// 	$('#exampleModal').on('show.bs.modal', function (e) {
										// 		var drowid = $(e.relatedTarget).data('id');
										// 	// alert (drowid);
										// 	console.log(drowid);
										// 		$.ajax({
										// 			type : 'post',
										// 			url : 'prikaz_porodica.php', //Here you will fetch records 
										// 			data :  'drowid='+ drowid, //Pass $id
										// 			success : function(data){
										// 			$('.fetched-data').html(data);//Show fetched data from database
										// 			}
										// 		});
										// 	});
										// });
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
										<td class="text-center" style="width: 20%">
										
										
												&nbsp;&nbsp;<a class="btn btn-outline-success btn-sm"  href="evidencija_djak.php?id=<?php echo $row['id']; ?>  "  target="_blank"><span class="glyphicon glyphicon-question-sign"></span> dolaska</a>
												&nbsp;&nbsp;<a class="btn btn-outline-danger btn-sm"   href="kartica_djak.php?id=<?php echo $row['id']; ?>  "  ><span class="glyphicon glyphicon-euro"></span> Card</a>
										</td>
									</tr>
									<?php
								}
    ?>


								</tbody>
								<tfoot>
									<tr><th>online/<br/>skola</th>
								<th>Ime i prezime</th>
								<th style="width:12%;">Broj telefona</th>
								<th style="width:12em;">Porodica</th>
      							<th style="width: 10em;">Grupa</th>
								<?php if($broj_lokacija > 1){ ?><th>Lokacija</th><?php } ?>
								<th style="width: 20%;">Akcija</th>
								</tr>
								</tfoot>
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
	<script src="../assets/js/bootstrap.bundle.min.js"></script>
	<!--plugins-->
	<script src="../assets/js/jquery.min.js"></script>
	<script src="../assets/plugins/simplebar/js/simplebar.min.js"></script>
	<script src="../assets/plugins/metismenu/js/metisMenu.min.js"></script>
	<script src="../assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
	<script src="../assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
	<script src="../assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>
	<script>
		// $(document).ready(function() {
		// 	$('#example').DataTable();
		//   } );
	</script>
	<!-- <script>
		$(document).ready(function() {
			var table = $('#example2').DataTable( {
				lengthChange: false,
				buttons: [  'excel', 'pdf', 'print']
			} );
		 
			table.buttons().container()
				.appendTo( '#example2_wrapper .col-md-6:eq(0)' );
		} );
	</script> -->
<script>

$(function () {

    const $table = $('#example2');
    if (!$table.length) return;
	 const profesorIme = "<?=$row_prof['firstname']?> <?=$row_prof['lastname']?>";
    const exportTitle = "Svi polaznici profesora " + profesorIme;

    $table.DataTable({

        // Layout
        dom:
            '<"row mb-3 align-items-center"' +
                '<"col-md-4"l>' +
                '<"col-md-4 text-center"f>' +
                '<"col-md-4 text-end"B>' +
            '>' +
            '<"row"' +
                '<"col-12"tr>' +
            '>' +
            '<"row mt-3"' +
                '<"col-md-5"i>' +
                '<"col-md-7"p>' +
            '>',


        // Pagination
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Sve"]],
        stateSave: true,

        // Dugmad (export samo prvih 6 kolona)
        buttons: [
            {
                extend: 'excel',
				title: exportTitle,
                className: 'btn btn-outline-success btn-sm',
                exportOptions: { columns: [0,1,2,3,4,5] }
            },
            {
                extend: 'pdf',
				title: exportTitle,
                className: 'btn btn-outline-danger btn-sm',
                exportOptions: { columns: [0,1,2,3,4,5] }
            },
            {
                extend: 'print',
				title: exportTitle,
                className: 'btn btn-outline-secondary btn-sm',
                exportOptions: { columns: [0,1,2,3,4,5] }
            }
        ],

        // Kolone
        columnDefs: [
            {
                targets: -1,              // poslednja kolona (Akcija)
                orderable: false,
                searchable: false,
                responsivePriority: 1,    // nikad ne sakrivaj
                width: "15%"
            }
        ],

        responsive: true,
        autoWidth: false,
        processing: true,

        language: {
            url: "../tabele/serbian.lang",
            searchPlaceholder: "Pretraga đaka...",
            search: ""
        }

    });

});
</script>
	<!--app JS-->
	<script src="../assets/js/app.js"></script>
</body>

</html>