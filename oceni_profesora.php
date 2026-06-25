<!doctype html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" href="images/kalen.png" type="image/png"/>
	<link href="assets/plugins/vectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet"/>
	<link href="assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
	<link href="assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
	<link href="assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet"/>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
	<link href="assets/css/pace.min.css" rel="stylesheet"/>
	<script src="assets/js/pace.min.js"></script>
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
	<link href="assets/css/bootstrap-extended.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
	<link href="assets/css/app.css" rel="stylesheet">
	<link href="assets/css/icons.css" rel="stylesheet">
	<link rel="stylesheet" href="assets/css/dark-theme.css"/>
	<link rel="stylesheet" href="assets/css/semi-dark.css"/>
	<link rel="stylesheet" href="assets/css/header-colors.css"/>
	<title>SMS - Oceni profesora</title>
	<style>
		.profesor-card { transition: box-shadow 0.2s; }
		.profesor-card:hover { box-shadow: 0 4px 16px rgba(30,58,138,0.13) !important; }
		.avatar-circle {
			width: 52px; height: 52px; border-radius: 50%;
			display: flex; align-items: center; justify-content: center;
			color: #fff; font-weight: bold; font-size: 1.3rem; flex-shrink: 0;
		}
		.star-rating { display: flex; flex-direction: row-reverse; justify-content: center; gap: 4px; }
		.star-rating input { display: none; }
		.star-rating label {
			font-size: 2rem; color: #ccc; cursor: pointer; transition: color 0.15s;
		}
		.star-rating input:checked ~ label,
		.star-rating label:hover,
		.star-rating label:hover ~ label { color: #f5a623; }
	</style>
</head>

<body>
	<div class="wrapper">

		<?php
		include_once "config/core.php";
		include_once "login_checker.php";
		include_once 'config/database.php';
		include_once 'config/autoload.php';
		include __DIR__ . '/app/sidebar.php'; ?>

		<header>
		<?php include __DIR__ . '/app/header.php'; ?>
		</header>

		<div class="page-wrapper">
			<div class="page-content">
				<div class="row">
					<div class="col-12 d-flex">
						<div class="card radius-10 w-100 shadow-sm">
							<div class="card-body p-4">

								<?php
								$idd = $_SESSION['user_id'];

								$database = new Database();
								$db = $database->getConnection();
								$povezivanje = new povezivanje($db);
								$ocena_obj   = new ocena_profesora($db);

								$stmt    = $povezivanje->read_all_profesori_za_djaka($idd);
								$profesori = $stmt->fetchAll(PDO::FETCH_ASSOC);
								?>

								<h4 class="mb-4"><i class='bx bx-star me-2'></i>Oceni profesora</h4>

								<?php if (empty($profesori)): ?>
									<div class="alert alert-info">Trenutno nemaš aktivnih profesora.</div>
								<?php else: ?>
									<div class="row g-3">
										<?php foreach ($profesori as $prof):
											$vec_ocenjen = $ocena_obj->already_rated_today($idd, $prof['id_profesora']);
										?>
											<div class="col-md-4 col-sm-6">
												<div class="card border-0 shadow-sm profesor-card h-100">
													<div class="card-body d-flex flex-column gap-3">

														<div class="d-flex align-items-center gap-3">
															<div class="avatar-circle" style="background: <?= htmlspecialchars($prof['color_prof'] ?? '#1e3a8a') ?>;">
																<?= strtoupper(substr($prof['firstname'] ?? '?', 0, 1)) ?>
															</div>
															<div>
																<h6 class="mb-0"><?= htmlspecialchars($prof['firstname'] . ' ' . $prof['lastname']) ?></h6>
																<small class="text-muted"><?= htmlspecialchars($prof['jezici'] ?? '') ?></small>
															</div>
														</div>

														<div>
															<?php foreach (explode(', ', $prof['grupe_alias']) as $alias): ?>
																<span class="badge bg-light text-dark border me-1"><?= htmlspecialchars(trim($alias)) ?></span>
															<?php endforeach; ?>
														</div>

														<div class="mt-auto">
															<?php if ($vec_ocenjen): ?>
																<button class="btn btn-success btn-sm w-100" disabled>
																	<i class='bx bx-check me-1'></i> Već ocenjen danas.
																</button>
															<?php else: ?>
																<button class="btn btn-outline-primary btn-sm w-100 btn-oceni"
																	data-prof-id="<?= (int)$prof['id_profesora'] ?>"
																	data-prof-ime="<?= htmlspecialchars($prof['firstname'] . ' ' . $prof['lastname']) ?>"
																	data-bs-toggle="modal"
																	data-bs-target="#modalOcena">
																	<i class='bx bx-star me-1'></i> Oceni
																</button>
															<?php endif; ?>
														</div>

													</div>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="overlay toggle-icon"></div>
		<a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>

		<footer class="page-footer">
			<?php include __DIR__ . '/app/footer.php'; ?>
		</footer>
	</div>

	<!-- Modal za ocenu -->
	<div class="modal fade" id="modalOcena" tabindex="-1" aria-labelledby="modalOcenaLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalOcenaLabel"><i class='bx bx-star me-2'></i>Oceni profesora</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<p class="text-muted mb-3">Profesor: <strong id="modal_prof_ime"></strong></p>

					<div class="mb-4 text-center">
						<div class="star-rating" id="starRating">
							<input type="radio" name="ocena" id="star5" value="5"><label for="star5">&#9733;</label>
							<input type="radio" name="ocena" id="star4" value="4"><label for="star4">&#9733;</label>
							<input type="radio" name="ocena" id="star3" value="3"><label for="star3">&#9733;</label>
							<input type="radio" name="ocena" id="star2" value="2"><label for="star2">&#9733;</label>
							<input type="radio" name="ocena" id="star1" value="1"><label for="star1">&#9733;</label>
						</div>
						<small class="text-muted" id="ocena_tekst">Izaberi ocenu</small>
					</div>

					<div class="mb-3">
						<label class="form-label">Komentar <span class="text-muted">(opciono)</span></label>
						<textarea class="form-control" id="modal_komentar" rows="3" placeholder="Napiši komentar..."></textarea>
					</div>

					<div id="modal_poruka"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Otkaži</button>
					<button type="button" class="btn btn-primary" id="btn_sacuvaj_ocenu">
						<i class='bx bx-save me-1'></i> Sačuvaj ocenu
					</button>
				</div>
			</div>
		</div>
	</div>

	<div class="switcher-wrapper" style="display:none;">
		<div class="switcher-btn"><i class='bx bx-cog bx-spin'></i></div>
		<div class="switcher-body">
			<div class="d-flex align-items-center">
				<h5 class="mb-0 text-uppercase">Theme Customizer</h5>
				<button type="button" class="btn-close ms-auto close-switcher" aria-label="Close"></button>
			</div>
			<hr/>
			<h6 class="mb-0">Theme Styles</h6>
			<hr/>
			<div class="d-flex align-items-center justify-content-between">
				<div class="form-check">
					<input class="form-check-input" type="radio" name="flexRadioDefault" id="lightmode" checked>
					<label class="form-check-label" for="lightmode">Light</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="flexRadioDefault" id="darkmode">
					<label class="form-check-label" for="darkmode">Dark</label>
				</div>
				<div class="form-check">
					<input class="form-check-input" type="radio" name="flexRadioDefault" id="semidark">
					<label class="form-check-label" for="semidark">Semi Dark</label>
				</div>
			</div>
			<hr/>
		</div>
	</div>

	<script src="assets/js/bootstrap.bundle.min.js"></script>
	<script src="assets/js/jquery.min.js"></script>
	<script src="assets/plugins/simplebar/js/simplebar.min.js"></script>
	<script src="assets/plugins/metismenu/js/metisMenu.min.js"></script>
	<script src="assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script>
	<script src="assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js"></script>
	<script src="assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js"></script>
	<script src="assets/plugins/chartjs/js/chart.js"></script>
	<script src="assets/js/index.js"></script>
	<script src="assets/js/app.js"></script>
	<script>new PerfectScrollbar(".app-container")</script>

	<script>
	const ocenaOpisi = ['', 'Loše', 'Može bolje', 'Dobro', 'Vrlo dobro', 'Odlično'];

	let trenutni_prof_id = null;

	$('.btn-oceni').on('click', function(){
		trenutni_prof_id = $(this).data('prof-id');
		$('#modal_prof_ime').text($(this).data('prof-ime'));
		$('input[name="ocena"]').prop('checked', false);
		$('#modal_komentar').val('');
		$('#modal_poruka').html('');
		$('#ocena_tekst').text('Izaberi ocenu');
	});

	$('input[name="ocena"]').on('change', function(){
		$('#ocena_tekst').text(ocenaOpisi[$(this).val()]);
	});

	$('#btn_sacuvaj_ocenu').on('click', function(){
		const ocena    = $('input[name="ocena"]:checked').val();
		const komentar = $('#modal_komentar').val();

		if (!ocena) {
			$('#modal_poruka').html('<div class="alert alert-warning">Molim te izaberi ocenu.</div>');
			return;
		}

		$(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Čuvam...');

		$.ajax({
			type: 'POST',
			url: 'sacuvaj_ocenu.php',
			data: { prof_id: trenutni_prof_id, ocena: ocena, komentar: komentar },
			success: function(resp){
				const r = (typeof resp === 'string') ? JSON.parse(resp) : resp;
				if (r.status === 'ok') {
					$('#modal_poruka').html('<div class="alert alert-success">Ocena je sačuvana!</div>');
					setTimeout(() => location.reload(), 1200);
				} else {
					$('#modal_poruka').html('<div class="alert alert-danger">' + r.poruka + '</div>');
					$('#btn_sacuvaj_ocenu').prop('disabled', false).html('<i class="bx bx-save me-1"></i> Sačuvaj ocenu');
				}
			},
			error: function(){
				$('#modal_poruka').html('<div class="alert alert-danger">Greška pri čuvanju.</div>');
				$('#btn_sacuvaj_ocenu').prop('disabled', false).html('<i class="bx bx-save me-1"></i> Sačuvaj ocenu');
			}
		});
	});
	</script>
</body>

</html>
