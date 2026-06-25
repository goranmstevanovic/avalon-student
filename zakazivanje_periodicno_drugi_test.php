<?php
// echo " Pisemo Od: ",$sati_od12,":",$minuti_od12," /DO: ",$sati_do12,":",$minuti_do12,"<br/>";
                            $datum_tog_termina = $i->format("Y-m-d");
                            $termin->pocetak=$datum_tog_termina.' '.$sati_od12.":".$minuti_od12.":"."00";
                            $termin->kraj=$datum_tog_termina.' '.$sati_do12.":".$minuti_do12.":"."00";

                            if($postojanje_rasporeda == 1 || $postojanje_rasporeda == 2){
                                $broj_termina_grupe_na_dan = $termin->count_provera_postojanja_termina_djak_dan($datum_tog_termina, $termin->grupa); 
                                if($broj_termina_grupe_na_dan == 0){
                                    if(isset( $termin->ucionica) &&  $termin->ucionica != 2000 ){
                                        $begin22 = new DateTime( $termin->pocetak );
                                        // Set end date
                                        $end22 = new DateTime( $termin->kraj );
                                        // Set interval
                                        $interval = new DateInterval('PT9M');
                                        // Create daterange
                                        $daterange = new DatePeriod($begin22, $interval ,$end22);
                                        // Loop through range
                                        // $krk=2;
                                        foreach($daterange as $date){
                                            // Output date and time
                                            $broj_istovremenih = $termin->provera_zauzetosti_ucionice_termin( $date->format("Y-m-d H:i:s"), $termin->ucionica );
                                             //   echo $date->format("Y-m-d H:i:s")," broj termina za aparat " ,$row_category_aparat11['ime'],"&nbsp; : ",$broj_istovremenih, "<br>";
                                            if($broj_istovremenih > 0){
                                                $krk = 1;
                                                $zauzeta_ucionica = 1;
                                                $_SESSION['poruka_zauzeta_ucionica'] = "Učionica je zauzet u terminu: " . date("d.m.Y", strtotime($datum_tog_termina)) . " od: " .  $sati_od12 . ":" . $minuti_od12 . " do: " . $sati_do12 . ":" . $minuti_do12;
                                            }
                                        }
                                    }
                                     // $brojka_zauzetosti_profesora = 0;
                                    $brojka_zauzetosti_profesora = $termin->count_zauzetosti_profesor($termin->pocetak, $termin->kraj,  $grupni_profesor);
                                    if( $brojka_zauzetosti_profesora > 0){
                                        $zauzet_profesor = 1;
                                        $krk=1;
                                        $_SESSION['poruka_zauzet_profesor'] = "Profesor je zauzet u terminu: " . date("d.m.Y", strtotime($datum_tog_termina)) . " od: " .  $sati_od12 . ":" . $minuti_od12 . " do: " . $sati_do12 . ":" . $minuti_do12;
                                      
                                    }
                                }
                                if($broj_termina_grupe_na_dan > 0){
                                    if($postojanje_rasporeda == 1){
                                      //  $stmt_brisi_termine_grupa_dan = $termin->brisi_termine_grupa_dan($datum_tog_termina, $termin->grupa); 
                                        //   $row_brisiii = $stmt_brisi_termine_grupa_dan->fetch(PDO::FETCH_ASSOC);
                                        // echo "bese li termina";
                                        //  //  var_dump($row_brisiii);
                                        // var_dump($stmt_brisi_termine_grupa_dan);
                                        if(isset( $termin->ucionica) &&  $termin->ucionica != 2000 ){
                                            $begin22 = new DateTime( $termin->pocetak );
                                            // Set end date
                                            $end22 = new DateTime( $termin->kraj );
                                            // Set interval
                                            $interval = new DateInterval('PT9M');
                                            // Create daterange
                                            $daterange = new DatePeriod($begin22, $interval ,$end22);
                                            // Loop through range
                                            // $krk=2;
                                            foreach($daterange as $date){
                                                // Output date and time
                                                $broj_istovremenih = $termin->provera_zauzetosti_ucionice_termin( $date->format("Y-m-d H:i:s"), $termin->ucionica );
                                               //   echo $date->format("Y-m-d H:i:s")," broj termina za aparat " ,$row_category_aparat11['ime'],"&nbsp; : ",$broj_istovremenih, "<br>";
                                                if($broj_istovremenih > 0){
                                                    $krk = 1;
                                                    $zauzeta_ucionica = 1;
                                                    $_SESSION['poruka_zauzeta_ucionica'] = "Učionica je zauzet u terminu: " . date("d.m.Y", strtotime($datum_tog_termina)) .  "od: " .  $sati_od12 . ":" . $minuti_od12 . " do: " . $sati_do12 . ":" . $minuti_do12;
                                                }
                                            }
                                        }
                                                   
                                        // $brojka_zauzetosti_profesora = 0;
                                        $stmt_provera_zauzetosti_profesora = $termin->provera_zauzetosti_profesor($termin->pocetak, $termin->kraj,  $grupni_profesor);
                                        $row_provera_zauzetosti_profesora =  $stmt_provera_zauzetosti_profesora->fetch(PDO::FETCH_ASSOC);
                                      //  var_dump($row_provera_zauzetosti_profesora);
                                        if( $row_provera_zauzetosti_profesora != false && (($row_provera_zauzetosti_profesora['taj_profesor'] != $grupni_profesor && $row_provera_zauzetosti_profesora['fk_profesor'] == NULL) || $row_provera_zauzetosti_profesora['fk_profesor'] != $grupni_profesor )){
                                            $zauzet_profesor = 1;
                                            $krk=1;
                                            $_SESSION['poruka_zauzet_profesor'] = "Profesor je zauzet u terminu: " . date("d.m.Y", strtotime($datum_tog_termina)) . " od: " .  $sati_od . ":" . $minuti_od . " do: " . $sati_do . ":" . $minuti_do;
                                           
                                        }
                                    }
                                }
                            }
                           
                            if($postojanje_rasporeda == 3 ){
                                if(isset( $termin->ucionica) &&  $termin->ucionica != 2000 ){
                                    $begin22 = new DateTime( $termin->pocetak );
                                    // Set end date
                                    $end22 = new DateTime( $termin->kraj );
                                    // Set interval
                                    $interval = new DateInterval('PT9M');
                                    // Create daterange
                                    $daterange = new DatePeriod($begin22, $interval ,$end22);
                                    // Loop through range
                                    // $krk=2;
                                    foreach($daterange as $date){
                                        // Output date and time
                                        $broj_istovremenih = $termin->provera_zauzetosti_ucionice_termin( $date->format("Y-m-d H:i:s"), $termin->ucionica );
                                    //   echo $date->format("Y-m-d H:i:s")," broj termina za aparat " ,$row_category_aparat11['ime'],"&nbsp; : ",$broj_istovremenih, "<br>";
                                        if($broj_istovremenih > 0){
                                            $krk = 1;
                                            $zauzeta_ucionica = 1;
                                            $_SESSION['poruka_zauzeta_ucionica'] = "Učionica je zauzet u terminu: " . date("d.m.Y", strtotime($datum_tog_termina)) . " od: " .  $sati_od12 . ":" . $minuti_od12 . " do: " . $sati_do12 . ":" . $minuti_do12;
                                        
                                        }
                        
                                    }
                                }
                    
                    
                               // $brojka_zauzetosti_profesora = 0;
                                $brojka_zauzetosti_profesora = $termin->count_zauzetosti_profesor($termin->pocetak, $termin->kraj,  $grupni_profesor);
                                if( $brojka_zauzetosti_profesora > 0){
                                    $zauzet_profesor = 1;
                                    $krk=1;
                                    $_SESSION['poruka_zauzet_profesor'] = "Profesor je zauzet u terminu: " . date("d.m.Y", strtotime($datum_tog_termina)) . " od: " .  $sati_od12 . ":" . $minuti_od12 . " do: " . $sati_do12 . ":" . $minuti_do12;
                                      
                                }

                            }
                            ?>