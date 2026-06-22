<style>
/* =====================================
   LIGHT NAVY SIDEBAR SYSTEM
===================================== */

:root {
  /* LIGHT MODE */
  --sidebar-bg: #eaf1f8;        /* svetlo plavo-sivo */
  --sidebar-hover: #dbe7f3;     
  --sidebar-text: #1e293b;      /* tamno siva za tekst */
  --sidebar-icon: #334155;
  --sidebar-active: #1e3a8a;    /* mornarsko plava */
  --sidebar-active-text: #ffffff;
  --sidebar-border: #d0d9e4;
}

/* DARK MODE */
[data-theme="dark"] {
  --sidebar-bg: #0f172a;        
  --sidebar-hover: #1e293b;
  --sidebar-text: #e2e8f0;
  --sidebar-icon: #94a3b8;
  --sidebar-active: #3b82f6;
  --sidebar-active-text: #ffffff;
  --sidebar-border: rgba(255,255,255,0.08);
}

/* =====================================
   SIDEBAR STYLING
===================================== */

.sidebar-wrapper {
  background: var(--sidebar-bg) !important;
  border-right: 1px solid var(--sidebar-border);
}

.sidebar-wrapper a,
.sidebar-wrapper .menu-title {
  color: var(--sidebar-text) !important;
}

.sidebar-wrapper .parent-icon i {
  color: var(--sidebar-icon) !important;
}

/* Hover */
.sidebar-wrapper .metismenu a:hover {
  background: var(--sidebar-hover);
  border-radius: 6px;
}

/* Active */
.sidebar-wrapper .mm-active > a,
.sidebar-wrapper .metismenu .active > a {
  background: var(--sidebar-active) !important;
  color: var(--sidebar-active-text) !important;
  border-radius: 6px;
}

/* =====================================
   ACTIVE CHILD (normalna stavka)
===================================== */

.sidebar-wrapper .metismenu li.mm-active > a:not(.has-arrow) i {
  color: var(--sidebar-active-text) !important;
}

.sidebar-wrapper .metismenu li.mm-active > a:not(.has-arrow) .menu-title {
  color: var(--sidebar-active-text) !important;
}

/* =====================================
   ACTIVE PARENT (has-arrow)
===================================== */

.sidebar-wrapper .metismenu li.mm-active > a.has-arrow i,
.sidebar-wrapper .metismenu a.has-arrow[aria-expanded="true"] i {
  color: var(--sidebar-active-text) !important;
}

.sidebar-wrapper .metismenu li.mm-active > a.has-arrow .menu-title,
.sidebar-wrapper .metismenu a.has-arrow[aria-expanded="true"] .menu-title {
  color: var(--sidebar-active-text) !important;
}

/* Arrow */
.sidebar-wrapper .metismenu li.mm-active > a.has-arrow::after,
.sidebar-wrapper .metismenu a.has-arrow[aria-expanded="true"]::after {
  color: var(--sidebar-active-text) !important;
}

/* ===============================
   OPEN / ACTIVE PARENT (has-arrow)
================================= */

/* ===============================
   ACTIVE / OPEN HAS-ARROW
================================= */

/* Ako je LI aktivan */
.sidebar-wrapper .metismenu li.mm-active > a.has-arrow {
  background: var(--sidebar-active) !important;
  color: #fff !important;
}

/* Ako je aria-expanded true */
.sidebar-wrapper .metismenu a.has-arrow[aria-expanded="true"] {
  background: var(--sidebar-active) !important;
  color: #fff !important;
}

/* Tekst unutar */
.sidebar-wrapper .metismenu li.mm-active > a.has-arrow .menu-title,
.sidebar-wrapper .metismenu a.has-arrow[aria-expanded="true"] .menu-title {
  color: #fff !important;
}

/* Ikone */
.sidebar-wrapper .metismenu li.mm-active > a.has-arrow i,
.sidebar-wrapper .metismenu a.has-arrow[aria-expanded="true"] i {
  color: #fff !important;
}

/* Arrow */
.sidebar-wrapper .metismenu li.mm-active > a.has-arrow::after,
.sidebar-wrapper .metismenu a.has-arrow[aria-expanded="true"]::after {
  color: #fff !important;
}
</style>
<div class="sidebar-wrapper" data-simplebar="true" >
			<?php 
			//  $site =  $_SERVER['DOCUMENT_ROOT'];
			//  $site =  $_SERVER['DOCUMENT_ROOT']."/sms";
			//echo $site,"<br/>";
			
			// include_once ($site."/config/database.php");

			// include_once "config/autoload.php";
			// //$slika_put = $home_url."/assets/images/sms1.png";
			// $database = new Database();
			// $db = $database->getConnection();
			// $lokacija = new lokacija($db);
			//exit;
			?>		


			<div class="sidebar-header"  >
				<div>
					<img src="<?=$home_url?>assets/images/sms1.png" class="logo-icon" style="width:60%;" alt="logo icon">
				
				</div>
				<div>
					<h4 class="logo-text"></h4>
				</div>
				<div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i>
				</div>
			 </div>
			<!--navigation-->
			<ul class="metismenu" id="menu"  >
				<li>
					<a href="<?php echo $home_url; ?>index" class="has-arrow">
						<div class="parent-icon"><i class='bx bx-home-alt'></i>
						</div>
						<div class="menu-title">Home</div>
					</a>
					
				</li>
				<li>
					<a href="javascript:;" class="has-arrow">
						<div class="parent-icon"><i class='bx bx-calendar'></i>
						</div>
						<div class="menu-title">Časovi</div>
					</a>
					<ul>
						<li> <a href="<?php  echo $home_url; ?>odrzani_casovi"><i class='bx bx-radio-circle'></i>Održani</a>
						</li>
						<li> <a href="<?php  echo $home_url; ?>zakazani_casovi"><i class='bx bx-radio-circle'></i>Zakazani</a>
						</li>
						
							
							
						
					</ul>
				</li>
						<li>
					<a href="<?php echo $home_url; ?>oceni_profesora">
						<div class="parent-icon"><i class='bx bx-star'></i>
						</div>
						<div class="menu-title">Oceni profesora</div>
					</a>
				</li>
				
			
				
				<li class="menu-label">Finansije</li>
			
			
					<ul>
						<li> <a href="<?php echo $home_url; ?>kartica_djak"><i class='bx bx-radio-circle'></i>Finasijska kartica</a>
						</li>
						<!-- <li> <a href="table-datatable.html"><i class='bx bx-radio-circle'></i>Data Table</a>
						</li> -->
					</ul>
					<ul>
						<li> <a href="<?php echo $home_url; ?>mesecna_zaduzenja"><i class='bx bx-radio-circle'></i>Zaduženja</a>
						</li>
						<!-- <li> <a href="table-datatable.html"><i class='bx bx-radio-circle'></i>Data Table</a>
						</li> -->
					</ul>
				

		

			</ul>
			<!--end navigation-->
		</div>