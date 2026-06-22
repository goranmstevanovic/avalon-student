<!doctype html>
<html lang="en">

<head>
	<!-- Required meta tags -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!--favicon-->
	<link rel="icon" href="images/kalen.png" type="image/png"/>
	<!--plugins-->
	<link href="assets/plugins/vectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet"/>
	<link href="assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
	<link href="assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
	<link href="assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet"/>
	  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
	<!-- loader-->
	<link href="assets/css/pace.min.css" rel="stylesheet"/>
	<script src="assets/js/pace.min.js"></script>
	<!-- Bootstrap CSS -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/css/bootstrap-extended.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
	<link href="assets/css/app.css" rel="stylesheet">
	<link href="assets/css/icons.css" rel="stylesheet">
	<!-- Theme Style CSS -->
	<link rel="stylesheet" href="assets/css/dark-theme.css"/>
	<link rel="stylesheet" href="assets/css/semi-dark.css"/>
	<link rel="stylesheet" href="assets/css/header-colors.css"/>
	<title>SMS</title>
</head>

<body>
	<!--wrapper-->
	<div class="wrapper">
		  <!--sidebar wrapper -->
		
        <?php 
		
			$site =  $_SERVER['DOCUMENT_ROOT'];
			$site =  $_SERVER['DOCUMENT_ROOT'];
			//echo $site,"<br/>";
			include_once ("config/core.php");
			include_once ("config/database.php");
			include_once ("config/autoload.php");
			$slika_put = $home_url."/assets/images/sms1.png";
			$database = new Database();
			$db = $database->getConnection();
			$lokacija = new lokacija($db);
			if( $_SESSION['access_level'] == "Customer"){ 
				include_once ('app/sidebar.php'); 
			}else{
				include_once ( 'admin/app/sidebar.php'); 
			} 
		
		?>
		  <!--end sidebar wrapper -->
		  <!--start header -->
        <header>
            <?php 
			if( $_SESSION['access_level'] == "Customer"){ 
				include_once ('app/header.php'); 
			}else{
				include 'admin/app/header.php'; 
			}
			
			
			//include  'admin/app/header.php'; 
			?>
        </header>
				<!--end header -->
		
	
    <div class="page-wrapper">
        <div class="page-content" >
            <div class="row">
                <div class="col-12 d-flex">
                    <div class="card radius-10 shadow-sm" style="width: 100%;">
                        <div class="card-body p-4">

                            <?php include_once 'dogadjaj_old.php'; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="page-footer">
			
            <?php include 'admin/app/footer.php'; ?>
		</footer>
   </div>
	<!--end switcher-->
	<!-- Bootstrap JS -->
	<script src="assets/js/bootstrap.bundle.min.js"></script>
	<!--plugins-->
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
	<script src="assets/plugins/metismenu/js/metisMenu.min.js"></script>
	<script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
	<script src="assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js"></script>
	<script src="assets/plugins/chartjs/js/chart.js"></script>
	<!-- <script src="assets/js/index.js"></script> -->
	<!--app JS-->
	<script src="assets/js/app.js"></script>
	<script>
		new PerfectScrollbar(".app-container")
	</script>
</body>

</html>