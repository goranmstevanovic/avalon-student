
<html>
    <head>
        <!-- <title>Upload materijala</title> -->
        <!-- <link rel="icon" href="images/kalen.png" type="image/png"/> -->
        <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    </head>
<body>
    

<?php

include_once "config/core.php";
include_once "login_checker.php";
// include classes
include_once 'config/database.php';
include_once 'config/autoload.php';
include_once 'config/funkcije.php';
// include page header HTML
include_once "layout_head77.php";
// get database connection
$database = new Database();
$db = $database->getConnection();
$profesor = new user($db);
$grupa = new grupa($db);
$povezivanje = new povezivanje($db);
$djak = new djak($db);
$zaduzenje = new zaduzenje($db);
$nivo_znanja = new nivo_znanja($db);
$velicina_grupe = new velicina($db);
$jezik = new jezik($db);
$uzrast = new uzrast($db);
?>
<div class="row shadow-sm" style="width: 95%; margin-left: 2%; border: 1px solid silver; padding: 15px; border-radius: 5px; " >
<h3 style="padding: 15px;">Dodaj novi dokument u aplikaciju</h3>
<form id="formaDokument" enctype="multipart/form-data">
    <div class="form-group">
        <label>Naziv dokumenta</label>
        <input type="text" name="naziv" class="form-control" required>
    </div>

    <div class="form-group">
        <label>Opis</label>
        <textarea name="opis" class="form-control"></textarea>
    </div>

    <div class="form-group">
        <label>Fajl</label>
        <input type="file" name="fajl" class="form-control" required>
    </div>

    <hr>

    <h4>Filter dokumenta</h4>

    <div class="form-group">
        <label>Jezik</label>
        <select name="fk_jezik" id="fk_jezik" class="form-control">
            <option value="0">Svi jezici</option>
           <?php $stm_jezik = $jezik->read_all();
                        while ($row_category_jezik = $stm_jezik->fetch(PDO::FETCH_ASSOC)){?>
                                <option value = "<?php echo $row_category_jezik['id']; ?>"  > 
                                <?php echo $row_category_jezik['ime']; ?> </option>
                        <?php } ?>
        </select>
    </div>

    <div class="form-group">
        <label>Nivo znanja</label>
        <select name="fk_nivo_znanja" id="fk_nivo_znanja" class="form-control">
            <option value="0">Svi nivoi</option>
            <?php  $stm_nivo = $nivo_znanja->read_all(); 
            while ($row_category_nivo = $stm_nivo->fetch(PDO::FETCH_ASSOC)){?>
                        <option value = "<?php echo $row_category_nivo['id']; ?>"  > 
                        <?php echo $row_category_nivo['ime']; ?> </option>
                    <?php } ?>  
        </select>
    </div>

    <div class="form-group">
        <label>Uzrast</label>
        <select name="fk_uzrast" id="fk_uzrast" class="form-control">
            <option value="0">Svi uzrasti</option>
             <?php $stmt_uzrast = $uzrast->read_all();
                while ($row_category_uzrast =  $stmt_uzrast->fetch(PDO::FETCH_ASSOC)){ ?>
                            <option value = "<?php echo $row_category_uzrast['id']; ?>" > <?php echo $row_category_uzrast['ime'];  ?> </option>

                        <?php  } ?>
        </select>
    </div>
    <label>Važi do:</label>
    <input type="datetime-local" name="datum_vazenja_do" id="datum_vazenja_do"  class="form-control" required >

    <button type="button" id="ucitajGrupe" class="btn btn-info">Prikaži grupe</button>

    <hr>

    <h4>Ko vidi</h4>

    <div id="grupeWrapper"  style="display:none;" ></div>

    <hr>

    <div class="checkbox">
        <label>
            <input type="checkbox" name="vidljiv_profesorima" value="1" checked>
            Dokument mogu da vide i kolege profesori
        </label>
    </div>

    <div class="checkbox">
        <label>
            <input type="checkbox" name="group_download" value="1">
            Grupe mogu da downloaduju
        </label>
    </div>

    <div class="checkbox">
        <label>
            <input type="checkbox" name="student_download" value="1">
            Direktno odabrani đaci mogu da downloaduju
        </label>
    </div>

    <div class="checkbox hidden" >
        <label>
            <input type="checkbox" name="profesor_download" value="1" checked>
            Profesori mogu da downloaduju
        </label>
    </div>

    <button type="submit" class="btn btn-primary">Snimi dokument</button>
</form>
</div>
<div>
<div id="uploadPoruka" style="margin-top:15px;"></div>

<script>
$(document).ready(function(){

        $('#ucitajGrupe').on('click', function(){

            var wrapper = $('#grupeWrapper');

            // ako je otvoreno → zatvori
           if (wrapper.is(':visible') && $.trim(wrapper.html()) !== '') {
                wrapper.slideUp();
                return;
            }

            // ako vec ima sadrzaj → samo otvori
            if ($.trim(wrapper.html()) !== '') {
                wrapper.slideDown();
                return;
            }

            var fk_jezik = $('#fk_jezik').val();
            var fk_nivo_znanja = $('#fk_nivo_znanja').val();
            var fk_uzrast = $('#fk_uzrast').val();

            wrapper.html('<div class="alert alert-info">Učitavam grupe...</div>').slideDown();

            $.ajax({
                url: 'ajax_grupe_za_dokument.php',
                type: 'POST',
                data: {
                    fk_jezik: fk_jezik,
                    fk_nivo_znanja: fk_nivo_znanja,
                    fk_uzrast: fk_uzrast
                },
                success: function(response){
                    wrapper.html(response);
                },
                error: function(xhr){
                    wrapper.html('<div class="alert alert-danger">Greška pri učitavanju grupa.</div>');
                    console.log(xhr.responseText);
                }
            });

        });

        $('#fk_jezik, #fk_nivo_znanja, #fk_uzrast').on('change', function(){
            $('#grupeWrapper').html('').hide();
        });

    $(document).on('click', '.prikazi-djake', function(){
        var grupaId = $(this).data('grupa');
        var wrapper = $('#djaci_' + grupaId);

        if (wrapper.is(':visible')) {
            wrapper.slideUp();
            return;
        }

        if ($.trim(wrapper.html()) !== '') {
            wrapper.slideDown();
            return;
        }

        wrapper.html('<div class="alert alert-info">Učitavam đake...</div>').slideDown();

        $.ajax({
            url: 'ajax_djaci_za_grupu.php',
            type: 'POST',
            data: { fk_grupa: grupaId },
            success: function(response){
                wrapper.html(response);
            },
            error: function(xhr){
                wrapper.html('<div class="alert alert-danger">Greška pri učitavanju đaka.</div>');
                console.log(xhr.responseText);
            }
        });
    });

    // $('#formaDokument').on('submit', function(e){
    //     e.preventDefault();
    //     alert('atartovano');
    //     var formData = new FormData(this);

    //     $('#uploadPoruka').html('<div class="alert alert-info">Upload u toku...</div>');

    //     $.ajax({
    //         url: 'upload_dokument.php',
    //         type: 'POST',
    //         data: formData,
    //         processData: false,
    //         contentType: false,
    //         success: function(response){
    //             $('#uploadPoruka').html(response);
    //              $('#formaDokument')[0].reset();
    //                // odčekiraj sve
    //             // $('.grupa-check').prop('checked', false);
    //             // $('.djak-check').prop('checked', false);
    //             // $('.cekiraj-sve-djake').prop('checked', false);
    //             // $('#cekirajSveGrupe').prop('checked', false);

    //             // // zatvori sve spiskove đaka
    //             // $('.djaci-wrapper').slideUp().html('');

    //             // zatvori kompletan blok grupa
    //            // $('#grupeWrapper').slideUp().html('');
    //            // $('#grupeWrapper').slideUp();
    //            $('#grupeWrapper').html('').hide();
    //         },
    //         error: function(xhr){
    //             $('#uploadPoruka').html('<div class="alert alert-danger">Greška pri uploadu.</div>');
    //             console.log(xhr.responseText);
    //         }
    //     });
    // });

    $('#formaDokument').on('submit', function(e){
        e.preventDefault();

        var fileInput = $('input[name="fajl"]')[0];

        if (fileInput.files.length === 0) {
            alert('Morate izabrati fajl.');
            return;
        }

        var file = fileInput.files[0];
        var maxSize = 10 * 1024 * 1024; // 10MB

        var allowedTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'
        ];

        if (file.size > maxSize) {
            alert('Fajl je prevelik. Maksimalno 10MB.');
            return;
        }

        if (!allowedTypes.includes(file.type)) {
            alert('Nedozvoljen tip fajla.');
            return;
        }

        var formData = new FormData(this);

        $('#uploadPoruka').html('<div class="alert alert-info">Upload u toku...</div>');

        $.ajax({
            url: 'admin/upload_dokument.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response){
                $('#uploadPoruka').html(response);
                $('#formaDokument')[0].reset();
                $('#grupeWrapper').html('').hide();
            }
        });
    });


    // da cekiram sve grupe
    // $(document).on('change', '#cekirajSveGrupe', function(){
    //     var checked = $(this).is(':checked');

    //     $('.grupa-check').prop('checked', checked).trigger('change');
    // });

    $(document).on('change', '.grupa-check', function(){

        var grupaId = $(this).data('grupa');
        var checked = $(this).is(':checked');
        var wrapper = $('#djaci_' + grupaId);

        function cekirajDjake() {
            wrapper.find('.djak-check').prop('checked', checked);
        }

        // ako djaci nisu jos ucitani → ucitaj ih
        if ($.trim(wrapper.html()) === '') {

            wrapper.html('<div class="alert alert-info">Učitavam đake...</div>').show();

            $.ajax({
                url: 'ajax_djaci_za_grupu.php',
                type: 'POST',
                data: { fk_grupa: grupaId },
                success: function(response){
                    wrapper.html(response).show();
                    cekirajDjake();
                },
                error: function(xhr){
                    wrapper.html('<div class="alert alert-danger">Greška pri učitavanju đaka.</div>');
                    console.log(xhr.responseText);
                }
            });

        } else {
            wrapper.show();
            cekirajDjake();
        }

    });

    $(document).on('change', '#cekirajSveGrupe', function(){

        var checked = $(this).is(':checked');

        $('.grupa-check').each(function(){
            $(this).prop('checked', checked).trigger('change');
        });

    });

    $(document).on('change', '.cekiraj-sve-djake', function(){
        var grupaId = $(this).data('grupa');
        var checked = $(this).is(':checked');

        $('.djak-grupa-' + grupaId).prop('checked', checked);
    });


});
</script>
<script>
document.addEventListener('DOMContentLoaded', function(){

    let input = document.getElementById('datum_vazenja_do');

    let now = new Date();

    // max = +2 meseca
    let maxDate = new Date();
    maxDate.setMonth(maxDate.getMonth() + 2);

    // format YYYY-MM-DDTHH:MM
    function formatDate(d){
        let pad = n => n.toString().padStart(2,'0');
        return d.getFullYear() + '-' +
               pad(d.getMonth()+1) + '-' +
               pad(d.getDate()) + 'T' +
               pad(d.getHours()) + ':' +
               pad(d.getMinutes());
    }

    input.max = formatDate(maxDate);
    input.min = formatDate(now);

});
</script>
</body>    
</html>