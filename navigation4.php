<!-- navbar -->
<div class="navbar navbar-default navbar-static-top" role="navigation">
    <div class="container-fluid" style="background-color: #DCEDC8  !important;  ">

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
        <?php
            // check if users / customer was logged in
            // if user was logged in, show "Edit Profile", "Orders" and "Logout" options
            if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']==true && $_SESSION['access_level']=='Customer')
            { ?>
        <div class="navbar-collapse collapse" style="background-color: #DCEDC8 !important; height: 45px "  >
            <ul class="nav navbar-nav">
                <li>
                    <nav>
                        <a  href="<?php echo $home_url; ?>index"><img src="<?php echo $home_url; ?>images/logoSMS.png" height="50"  /></a>
                    </nav>
                </li>
               
                <li class="dropdown" style="margin-left:30px">
                    <a href="#" data-toggle="dropdown" class="dropdown-toggle"><b>RASPORED</b> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo $home_url; ?>fullcalendar4/kalendar2">
                        <img src="<?php echo $home_url; ?>images/all.webp"  style="width: auto;  height: 20px; padding-left: 5px; padding-right: 15px;padding-top: 1px; ">
                        Objedinjeni raspored</a></li>
                        <?php

                        // core configuration
                        include_once "config/core.php";

                        // check if logged in as admin
                        include_once "login_checker.php";

                        // include classes
                        include_once 'config/database.php';
                        include_once 'objects/user.php';

                        // get database connection
                        $database = new Database();
                        $db = $database->getConnection();

                        // initialize objects
                        $user = new User($db);
                        $stmt = $user->readAllP($from_record_num, $records_per_page); ?>
                            <li class="divider"></li>
                            <li>
                            
                            <a href="<?php echo $home_url; ?>fullcalendar4/kalendar3?prof=<?php echo $_SESSION['user_id']; ?>" >
                            <img src="<?php echo $home_url; ?>images/avatar.jpg"  style="width: auto;  height: 18px; padding-left: 5px; padding-right: 15px;padding-top: 1px; ">
                            Moj Raspored časova</a></li>
                            <li class="divider"></li>
                            <?php 
                        include_once 'objects/ucionica.php';
                        $ucionica = new ucionica($db);
                        $smts_ucionica = $ucionica->read_all();


                        while ($row_ucionica = $smts_ucionica->fetch(PDO::FETCH_ASSOC)){ ?>
                            <li><a href="<?php echo $home_url; ?>fullcalendar4/kalendar_ucionica_profesor?ucionica=<?php echo $row_ucionica['id']; ?>" >
                                <img src="<?php echo $home_url; ?>images/room.png"  style="width: auto;  height: 25px; padding-left: 5px; padding-right: 15px;padding-top: 1px; ">
                            Raspored za učionicu: <b> <?php echo $row_ucionica['ime']; ?> </b></a></li>
                                         
                        <?php } ?>
                    </ul>
                </li>
                <li class="dropdown" style="margin-left:30px">
                    <a href="#" data-toggle="dropdown" class="dropdown-toggle">Saradnici <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo $home_url; ?>read_users.php">Spisak saradnika</a></li>
                        <!--  <li><a href="<?php // echo $home_url; ?>register.php" target="_blank">Dodaj novog saradnika</a></li> -->
                        <li class="divider"></li>
                    </ul>
                </li>
                <li class="dropdown" style="margin-left:30px">
                    <a href="#" data-toggle="dropdown" class="dropdown-toggle">Djaci <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                    <li><a href="<?php echo $home_url; ?>read_djak">
                        <img src="images/all.webp"  style="width: auto;  height: 20px; padding-left: 5px; padding-right: 15px;padding-top: 1px; ">
                 
                        Spisak svih djaka</a></li>
                        <li><a href="<?php echo $home_url; ?>read_djak_online">
                        <img src="images/online.png"  style="width: auto;  height: 18px; padding-left: 5px; padding-right: 15px;padding-top: 1px; ">
                        Spisak online djaka</a></li>
                        <li><a href="<?php echo $home_url; ?>read_djak_skola">
                        <img src="images/room2.jpg"  style="width: auto;  height: 18px; padding-left: 5px; padding-right: 15px;padding-top: 1px; ">
         
                        Spisak djaka u školi</a></li>
                            <li class="divider"></li>
                            <li><a href="<?php echo $home_url; ?>novi_djak" >
                        <img src="images/plus.jpg"  style="width: auto;  height: 18px; padding-left: 5px; padding-right: 15px;padding-top: 1px; ">
         
                        Dodaj novog djaka</a></li>    
                    </ul>
                </li>
                <li class="dropdown" style="margin-left:30px">
                    <a href="#" data-toggle="dropdown" class="dropdown-toggle">Grupe <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo $home_url; ?>read_grupa?dolazni=sa_custom">Sve grupe</a></li>
                        <li><a href="<?php echo $home_url; ?>nova_grupa?dolazni=sa_custom"  >Dodaj novu grupu</a></li>
                        <li class="divider"></li>

                    </ul>

                </li>


                <li class="dropdown" style="margin-left:30px">
                    <a href="#" data-toggle="dropdown" class="dropdown-toggle" style="color:red">Finsije: <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                      
                       	<li><a href="<?php echo $home_url; ?>kartica_profesora" style="color:red">Finasijka kartica </a></li>
                        <li><a href="<?php echo $home_url; ?>statistika_profesora" style="color:red">Statistika i plata </a></li>
                        <li class="divider"></li>
                    </ul>
                </li>



            </ul>

        <?php } ?>
            <?php
            // check if users / customer was logged in
            // if user was logged in, show "Edit Profile", "Orders" and "Logout" options
            if(isset($_SESSION['logged_in']) && $_SESSION['logged_in']==true && $_SESSION['access_level']=='Customer')
            {
                ?>




                <ul class="nav navbar-nav navbar-right">
                    <li <?php echo $page_title=="Edit Profile" ? "class='active'" : ""; ?>>
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
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
                ?>
                <ul class="nav navbar-nav navbar-right">
                    <li <?php echo $page_title=="Login" ? "class='active'" : ""; ?>>
                        <a href="<?php echo $home_url; ?>login.php">
                            <span class="glyphicon glyphicon-log-in"></span> Log In
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