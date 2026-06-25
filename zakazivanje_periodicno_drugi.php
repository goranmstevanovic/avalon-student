<?php
echo " Pisemo Od: ",$sati_od12,":",$minuti_od12," /DO: ",$sati_do12,":",$minuti_do12,"<br/>";
                            $datum_tog_termina = $i->format("Y-m-d");
                            $termin->pocetak=$datum_tog_termina.' '.$sati_od12.":".$minuti_od12.":"."00";
                            $termin->kraj=$datum_tog_termina.' '.$sati_do12.":".$minuti_do12.":"."00";

                            if($postojanje_rasporeda == 1 || $postojanje_rasporeda == 2){
                                $broj_termina_grupe_na_dan = $termin->count_provera_postojanja_termina_djak_dan($datum_tog_termina, $termin->grupa); 
                                if($broj_termina_grupe_na_dan == 0){
                                    if($termin->create_termin()){
                                        echo "<div class='alert alert-info'>";
                                        echo "Uspešno ste zakazali termin proverite u kalendaru";
                                        echo "</div>";
                                        $_POST=array();
                                    }else{
                                        echo "<div class='alert alert-danger' role='alert'>";
                                            echo "\nPDO::errorInfo():\n";
                                            print_r($db->errorInfo());
                                        echo "</div>";
                                    }
                                }
                                if($broj_termina_grupe_na_dan > 0){
                                    if($postojanje_rasporeda == 1){
                                        $stmt_brisi_termine_grupa_dan = $termin->brisi_termine_grupa_dan($datum_tog_termina, $termin->grupa); 
                                     //   $row_brisiii = $stmt_brisi_termine_grupa_dan->fetch(PDO::FETCH_ASSOC);
                                        echo "bese li termina";
                                      //  var_dump($row_brisiii);
                                        var_dump($stmt_brisi_termine_grupa_dan);
                                        if($termin->create_termin()){
                                            echo "<div class='alert alert-info'>";
                                            echo "Uspešno ste zakazali termin proverite u kalendaru";
                                            echo "</div>";
                                            $_POST=array();
                                        }else{
                                            echo "<div class='alert alert-danger' role='alert'>";
                                                echo "\nPDO::errorInfo():\n";
                                                print_r($db->errorInfo());
                                            echo "</div>";
                                        }
                                    }
                                }
                            }
                           
                            if($postojanje_rasporeda == 3 ){
                                if($termin->create_termin()){
                                    echo "<div class='alert alert-info'>";
                                    echo "Uspešno ste zakazali termin proverite u kalendaru";
                                    echo "</div>";
                                    $_POST=array();
                                }else{
                                    echo "<div class='alert alert-danger' role='alert'>";
                                        echo "\nPDO::errorInfo():\n";
                                        print_r($db->errorInfo());
                                    echo "</div>";
                                }

                            }
                            ?>