<table class='table table-responsive'>
        <tr>
        <td class='width-30-percent'>Termin casa prve nedelje</td>
          
            <?php
              $stmt_sati = $termin->read_all_sati();
              $sati_sat1 = $sati_sat;
              $sati_min1 = $sati_min ; 
              if(isset( $sati_od) && $sati_od > $sati_sat1){$sati_sat1 = $sati_od;}
              if(isset( $minuti_od) && $minuti_od > $sati_min1 ){ $sati_min1 = $minuti_od;}
              $sati_sat_do = ($sati_sat ?? 0) + 1 ;  
              if(isset( $sati_do ) && $sati_do > $sati_sat_do ){$sati_sat_do = $sati_do;}
              $sati_min_do = $sati_min;
              if(isset( $minuti_do) && $minuti_do > $sati_min_do ){ $sati_min_do = $minuti_do;}
              $sati_sat12 = $sati_sat;
              if(isset( $sati_od12) && $sati_od12 > $sati_sat12){$sati_sat12 = $sati_od12;}
              $sati_min_od12 =  $sati_min;  
              if(isset( $minuti_od12) && $minuti_od12 > $sati_min_od12 ){$sati_min_od12 = $minuti_od12;}
              $sati_sat_do12 = ($sati_sat ?? 0) + 1 ;  
              if(isset( $sati_do12) && $sati_do12 > $sati_sat_do12 ){$sati_sat_do12 = $sati_do12;}
              $sati_min_do12 = $sati_min;
              if(isset( $minuti_do12) &&  $minuti_do12 > $sati_min_do12){$sati_min_do12 = $minuti_do12;}
            
            ?>
            <td align="right" style="width: 10%">OD:</td>
            <td style="width: 10%">
                <select   class='form-control' name="sati_od_1_n">
                    <?php while($row_category_sati_od = $stmt_sati->fetch(PDO::FETCH_ASSOC)){ ?>
                            <option value = "<?=$row_category_sati_od['sat'] ?>" <?php if(isset( $sati_od) &&  $sati_od == $row_category_sati_od['sat'] ){echo "selected";}elseif( $row_category_sati_od['sat'] == $sati_sat1){echo 'selected';} ?> >
                                <?php echo $row_category_sati_od['sat']; ?>
                            </option>
                        <?php 
                    } ?>
                </select>
                   
            </td>
            <td style="width: 10%">
            <?php 
                $stmt_minuti = $termin->read_all_minuti();
            ?>    
                <select  class='form-control' name="minuti_od_1_n">
                    <?php while($row_category_minuti_od = $stmt_minuti->fetch(PDO::FETCH_ASSOC)){ ?>
                        <option value = "<?=$row_category_minuti_od['minut'] ?>" <?php if(isset( $minuti_od) &&  $minuti_od == $row_category_minuti_od['minut'] ){echo "selected";}elseif( $sati_min1 == $row_category_minuti_od['minut']){echo "selected='selected'";} ?> >
                            <?php echo $row_category_minuti_od['minut']; ?>
                        </option>
                        <?php 
                    } ?>
                </select>
                


            </td>
            <td align="right" style="width: 10%">DO:</td>
            <?php
                $stmt_sati_doo = $termin->read_all_sati();
             
            ?>
            <td align="center" style="width: 10%">
                <select  class='form-control' name="sati_do_1_n">
                <?php while($row_category_sati_do = $stmt_sati_doo->fetch(PDO::FETCH_ASSOC)){ ?>
                    <option value = "<?=$row_category_sati_do['sat'] ?>" <?php if(isset( $sati_do) &&  $sati_do == $row_category_sati_do['sat'] ){echo "selected";}elseif( $row_category_sati_do['sat'] == $sati_sat_do){echo 'selected';} ?> >
                        <?php echo $row_category_sati_do['sat']; ?>
                    </option>
                    <?php } ?>
                   
                </select>
               
            </td>
            <?php 
                $stmt_minuti = $termin->read_all_minuti();
            ?>    
            <td style="width: 10%">
                <select  class='form-control' name="minuti_do_1_n">
                <?php while($row_category_minuti_do = $stmt_minuti->fetch(PDO::FETCH_ASSOC)){ ?>
                    <option value = "<?=$row_category_minuti_do['minut'] ?>" <?php if(isset( $minuti_do) &&  $minuti_do == $row_category_minuti_do['minut'] ){echo "selected";}elseif( $sati_min_do == $row_category_minuti_do['minut']){echo "selected='selected'";} ?> >
                        <?php echo $row_category_minuti_do['minut']; ?></option>
                    <?php } ?>    
                </select>
                
            </td>
        </tr>
        </table>
        <table class='table table-responsive'>
        <tr>
        <td class='width-30-percent'>Termin casa druge nedelje</td>
            <?php
              $stmt_sati = $termin->read_all_sati();
            ?>
            <td align="right" style="width: 10%">OD:</td>
            <td style="width: 10%">
                <select  class='form-control' name="sati_od_12_n">
                    <?php while($row_category_sati_od12 = $stmt_sati->fetch(PDO::FETCH_ASSOC)){ ?>
                            <option value = "<?=$row_category_sati_od12['sat'] ?>" <?php if(isset( $sati_od12) &&  $sati_od12 == $row_category_sati_od12['sat'] ){echo "selected";}elseif( $row_category_sati_od12['sat'] == $sati_sat12){echo 'selected';} ?> >
                                <?php echo $row_category_sati_od12['sat']; ?>
                            </option>
                        <?php 
                    } ?>
                </select>
            </td>
            <td style="width: 10%">
            <?php 
                $stmt_minuti = $termin->read_all_minuti();
            ?>    
                <select  class='form-control' name="minuti_od_12_n">
                <?php while($row_category_minuti_od12 = $stmt_minuti->fetch(PDO::FETCH_ASSOC)){ ?>
                    <option value = "<?=$row_category_minuti_od12['minut'] ?>" <?php if(isset( $minuti_od12) &&  $minuti_od12 == $row_category_minuti_od12['minut'] ){echo "selected";}elseif( $sati_min_od12 == $row_category_minuti_od12['minut']){echo "selected='selected'";} ?> >
                        <?php echo $row_category_minuti_od12['minut']; ?>
                    </option>
                    <?php 
                } ?>
                </select>
            </td>
            <td align="right" style="width: 10%">DO:</td>
            <?php
                $stmt_sati_do = $termin->read_all_sati();
                
            ?>
            <td align="center" style="width: 10%">
                <select  class='form-control' name="sati_do_12_n">
                <?php while($row_category_sati_do12 = $stmt_sati_do->fetch(PDO::FETCH_ASSOC)){ ?>
                    <option value = "<?=$row_category_sati_do12['sat'] ?>" <?php if(isset( $sati_do12) &&  $sati_do12 == $row_category_sati_do12['sat'] ){echo "selected";}elseif( $row_category_sati_do12['sat'] == $sati_sat_do12){echo 'selected';} ?> >
                        <?php echo $row_category_sati_do12['sat']; ?></option>
                    <?php } ?>
                   
                </select>
            </td>
            <?php 
                $stmt_minuti = $termin->read_all_minuti();
            ?>    
            <td style="width: 10%">
                <select  class='form-control' name="minuti_do_12_n">
                <?php while($row_category_minuti_do12 = $stmt_minuti->fetch(PDO::FETCH_ASSOC)){ ?>
                    <option value = "<?=$row_category_minuti_do12['minut'] ?>" <?php  if(isset( $minuti_do12) &&  $minuti_do12 == $row_category_minuti_do12['minut'] ){echo "selected";}elseif( isset($sati_min) && $sati_min_do12 == $row_category_minuti_do12['minut']){echo "selected='selected'";} ?> >
                        <?php echo $row_category_minuti_do12['minut']; ?></option>
                    <?php } ?>    
                </select>
                
            </td>
        </tr>
        
        </table>