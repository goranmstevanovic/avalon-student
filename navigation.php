<style>
.navbar .navbar-nav>.open>a, 
.navbar .navbar-nav>.open>a:focus, 
.navbar .navbar-nav>.open>a:hover {
    background-color:#ff7f27;
    color:#ffffff;
}
.navbar-collapse  {
    /* margin-top: 50px; */
    box-shadow: 0 0 5px gray;
    padding : 0 7px 0 5px;
}
</style>
<!-- navbar -->
<?php
            // check if users / customer was logged in
            // if user was logged in, show "Edit Profile", "Orders" and "Logout" options
if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']==true && $_SESSION['access_level']=='Customer')
{ ?>
<div class="navbar navbar-default navbar-static-top" role="navigation">
    <div class="container-fluid" style="background-color: #fffff !important;  ">

        <div class="navbar-header">
            <!-- to enable navigation dropdown when viewed in mobile device -->
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <!-- Change "Your Site" to your site name -->
            <a class="navbar-brand" href="<?php echo $home_url; ?>"><!-- Your Site --></a>
        </div>
       
        <div class="navbar-collapse collapse" style="background-color: #DCEDC8 !important; height: 45px "  >
            <ul class="nav navbar-nav">
                <li>
                    <nav>
                        <a  href="<?php echo $home_url; ?>index"><img src="<?php echo $home_url; ?>images/sms1.png" height="50"  /></a>
                    </nav>
                </li>
               
                <li class="dropdown" style="margin-left:30px">
                    <a href="#" data-toggle="dropdown" class="dropdown-toggle"><b>RASPORED</b> <b class="caret"></b></a>
                   
                    <ul class='dropdown-menu'>
                    <li class="divider"></li>
                        <li>    
                            <a href="<?php echo $home_url; ?>fullcalendar44/kalendar_svi">
                                <img src="<?php echo $home_url; ?>images/all.webp"  style="width: auto;  height: 18px; padding-left: 5px; padding-right: 15px;padding-top: 1px; ">
                                 Raspored svih profesora po danima 
                            </a>
                        </li> 
                        <li class="divider"></li>
                        <li>    
                            <a href="<?php echo $home_url; ?>fullcalendar4/kalendar_online">
                                <img src="<?php echo $home_url; ?>images/online.png"  style="width: auto;  height: 18px; padding-left: 5px; padding-right: 15px;padding-top: 1px; ">
                                 Raspored online nastave 
                            </a>
                        </li>
                        <li class="divider"></li>
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
                          // initialize objects
                         $user = new user($db);
                         $lokacija = new lokacija($db);
                         $broj_lokacija = $lokacija->count_all();
                         $stmt_lokacija = $lokacija->read_all();
                        while ($row_lokacija = $stmt_lokacija->fetch(PDO::FETCH_ASSOC)){
                            ?>
                            <li>
                                <a href="<?php echo $home_url; ?>fullcalendar44/kalendar_skola_lokacija?loc=<?=$row_lokacija['id'] ?> ">
                                <img src="<?php echo $home_url; ?>images/room2.jpg"  style="width: auto;  height: 18px; padding-left: 5px; padding-right: 15px;padding-top: 1px; ">
                                    Raspored nastave u skoli <?php if($broj_lokacija > 1){ echo $row_lokacija['ime']; } ?> </a>
                            </li>
                            <?php 
                        } ?>
                        <li class="divider"></li>
                        <?php
                           $stmt = $user->read_All_profesor(); ?>
                            <li class="divider"></li>
                            <li >
                                <a href="<?php echo $home_url; ?>fullcalendar4/kalendar3?prof=<?php echo $_SESSION['user_id']; ?>" >
                                <img src="<?php echo $home_url; ?>images/avatar.jpg"  style="width: auto;  height: 18px; padding-left: 5px; padding-right: 15px;padding-top: 1px; ">
                                Moj Raspored časova</a>
                            </li>
                            <li class="divider"></li>
                            <?php 
                            //  include_once 'objects/ucionica.php';
                            //  include_once ("objects/lokacija.php");
                                $ucionica = new ucionica($db);
                            //  $lokacija = new lokacija($db);
                                $smts_ucionica = $ucionica->read_all();
                        while ($row_ucionica = $smts_ucionica->fetch(PDO::FETCH_ASSOC)){ ?>
                            <li style='display:none;' ><a href="<?php echo $home_url; ?>fullcalendar4/kalendar_ucionica_profesor?ucionica=<?php echo $row_ucionica['id']; ?>" >
                            <?php
                            $stmt_lokacija = $lokacija->read_one($row_ucionica['fk_lokacija']);
                                 $row_lokacija =  $stmt_lokacija->fetch(PDO::FETCH_ASSOC); 
                                    if($row_ucionica['fk_lokacija'] == 1){
                                        $boja_vrste = "#B40431";
                                    }else{
                                        $boja_vrste = "#1C1C1C";
                                    }
                            ?>
                               <img src="<?php echo $home_url; ?>images/room.png"  style="width: auto;  height: 25px; padding-left: 5px; padding-right: 15px;padding-top: 1px; ">
                            <tab style="color: <?=$boja_vrste ?>">  Raspored za učionicu: <b> <?php echo $row_ucionica['ime']; if($broj_lokacija > 1) { echo " / ",$row_lokacija['ime']; } ?> </tab></b></a>
                          
                            </li>
                                         
                        <?php } ?>
                    </ul>
                </li>
                <li class="dropdown" style="margin-left:30px">
                    <a href="#" data-toggle="dropdown" class="dropdown-toggle">Saradnici <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo $home_url; ?>read_users">Spisak saradnika</a></li>
                        <!--  <li><a href="<?php // echo $home_url; ?>register.php" target="_blank">Dodaj novog saradnika</a></li> -->
                        <li class="divider"></li>
                        <li><a href="<?php echo $home_url; ?>update">Izmeni moje generalije</a></li>
                        <li class="divider"></li>
                        <li style='display:none;'><a href="<?php echo $home_url; ?>update_isplata_prof">Moja tabela isplata</a></li>
                    </ul>
                </li>
                <li class="dropdown" style="margin-left:30px">
                    <a href="#" data-toggle="dropdown" class="dropdown-toggle">Đaci <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                    <li><a href="<?php echo $home_url; ?>read_djak">
                        <img src="images/all.webp"  style="width: auto;  height: 20px; padding-left: 5px; padding-right: 15px;padding-top: 1px; ">
                        Spisak svih đaka</a></li>
                        <li><a href="<?php echo $home_url; ?>read_djak_online">
                        <img src="images/online.png"  style="width: auto;  height: 18px; padding-left: 5px; padding-right: 15px;padding-top: 1px; ">
                        Spisak online đaka</a></li>
                        <li><a href="<?php echo $home_url; ?>read_djak_skola">
                        <img src="images/room2.jpg"  style="width: auto;  height: 18px; padding-left: 5px; padding-right: 15px;padding-top: 1px; ">
                        Spisak đaka u školi</a></li>
                            <li class="divider"></li>
                            <li><a href="<?php echo $home_url; ?>novi_djak" >
                        <img src="<?php echo $home_url; ?>images/plus.jpg"  style="width: auto;  height: 18px; padding-left: 5px; padding-right: 15px;padding-top: 1px; ">
                        Dodaj novog đaka</a></li>  
                    </ul>
                </li>
                <li class="dropdown" style="margin-left:30px">
                    <a href="#" data-toggle="dropdown" class="dropdown-toggle">Grupe <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo $home_url; ?>read_grupa?dolazni=sa_custom">Sve grupe</a></li>
                        <li class="divider"></li>
                    </ul>
                </li>
                <li class="dropdown" style="margin-left:30px; display: none;">
                    <a href="#" data-toggle="dropdown" class="dropdown-toggle" > <b>Finansije:</b>  <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo $home_url; ?>statistika_profesora" style="color:red">Statistika i plata </a></li>
                        <li class="divider"></li>
                    </ul>
                </li>



            </ul>

        <?php 
} 
            // check if users / customer was logged in
            // if user was logged in, show "Edit Profile", "Orders" and "Logout" options
            if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']==true && $_SESSION['access_level']=='Customer')
            {
                ?>




                <ul class="nav navbar-nav navbar-right">
                    <li <?php echo $page_title=="Edit Profile" ? "class='active'" : ""; ?>>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" style='margin-right:10px;' aria-expanded="false">
                            <span class="glyphicon glyphicon-user" aria-hidden="true"></span>
                            &nbsp;&nbsp;<?php echo $_SESSION['firstname']; ?>
                            &nbsp;&nbsp;<span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="<?php echo $home_url; ?>logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
                <?php
            }

            // if user was not logged in, show the "login" and "register" options
            else{
                 $url = $home_url."login.php";
                header( "Location: $url" );
                ?>
                <ul class="nav navbar-nav navbar-right" style='margin-right:15px'>
                    <li <?php echo $page_title=="Login" ? "class='active'" : ""; ?>>
                        <a href="<?php echo $home_url; ?>login.php">
                            <span class="glyphicon glyphicon-log-in" style='margin-right:5px' ></span>Nastavi Log In
                        </a>
                    </li>


                </ul>
                <?php
            }
            ?>

        </div><!--/.nav-collapse -->

    </div>
</div>
<!-- /navbar -->