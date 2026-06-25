<table class='table table-responsive'>
        <tr>
        <td class='width-30-percent'>Termin casa prve nedelje</td>
          
            <?php
              $stmt_sati = $termin->read_all_sati();
            
            ?>
            <td align="right" style="width: 10%">OD:</td>
            <td style="width: 10%">
                <select   class='form-control' name="sati_od_2_n">
                    <?php while($row_category_sati = $stmt_sati->fetch(PDO::FETCH_ASSOC)){ ?>
                            <option value = "<?php echo $row_category_sati['sat'] ?>" <?php if( $row_category_sati['sat'] == $sati_sat){echo 'selected';} ?> ><?php echo $row_category_sati['sat']; ?></option>
                        <?php 
                    } ?>
                </select>
                  
            </td>
            <td style="width: 10%">
            <?php 
                $stmt_minuti = $termin->read_all_minuti();
            ?>    
                <select  class='form-control' name="minuti_od_2_n">
                <?php while($row_category_minuti = $stmt_minuti->fetch(PDO::FETCH_ASSOC)){ ?>
                    <option value = "<?php echo $row_category_minuti['minut'] ?>" <?php if( $sati_min == $row_category_minuti['minut']){echo "selected='selected'";} ?> ><?php echo $row_category_minuti['minut']; ?></option>
                    <?php } ?>
                  
                </select>
               


            </td>
            <td align="right" style="width: 10%">DO:</td>
            <?php
                $stmt_sati_do = $termin->read_all_sati();
                $sati_do = ($sati_sat ?? 0) + 1 ;  
            ?>
            <td align="center" style="width: 10%">
                <select  class='form-control' name="sati_do_2_n">
                <?php while($row_category_sati_do = $stmt_sati_do->fetch(PDO::FETCH_ASSOC)){ ?>
                    <option value = "<?php echo $row_category_sati_do['sat'] ?>" <?php if( $row_category_sati_do['sat'] == $sati_do){echo 'selected';} ?> ><?php echo $row_category_sati_do['sat']; ?></option>
                    <?php } ?>
                   
                </select>
               
            </td>
            <?php 
                $stmt_minuti = $termin->read_all_minuti();
            ?>    
            <td style="width: 10%">
                <select  class='form-control' name="minuti_do_2_n">
                <?php while($row_category_minuti = $stmt_minuti->fetch(PDO::FETCH_ASSOC)){ ?>
                    <option value = "<?php echo $row_category_minuti['minut'] ?>" <?php if( $sati_min == $row_category_minuti['minut']){echo "selected='selected'";} ?> ><?php echo $row_category_minuti['minut']; ?></option>
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
                <select   class='form-control' name="sati_od_22_n">
                    <?php while($row_category_sati = $stmt_sati->fetch(PDO::FETCH_ASSOC)){ ?>
                            <option value = "<?php echo $row_category_sati['sat'] ?>" <?php if( $row_category_sati['sat'] == $sati_sat){echo 'selected';} ?> ><?php echo $row_category_sati['sat']; ?></option>
                        <?php 
                    } ?>
                </select>
                   
            </td>
            <td style="width: 10%">
            <?php 
                $stmt_minuti = $termin->read_all_minuti();
            ?>    
                <select  class='form-control' name="minuti_od_22_n">
                <?php while($row_category_minuti = $stmt_minuti->fetch(PDO::FETCH_ASSOC)){ ?>
                    <option value = "<?php echo $row_category_minuti['minut'] ?>" <?php if( $sati_min == $row_category_minuti['minut']){echo "selected='selected'";} ?> ><?php echo $row_category_minuti['minut']; ?></option>
                    <?php } ?>
                  
                </select>
               


            </td>
            <td align="right" style="width: 10%">DO:</td>
            <?php
                $stmt_sati_do = $termin->read_all_sati();
                $sati_do = ($sati_sat ?? 0) + 1 ;  
            ?>
            <td align="center" style="width: 10%">
                <select class='form-control' name="sati_do_22_n">
                <?php while($row_category_sati_do = $stmt_sati_do->fetch(PDO::FETCH_ASSOC)){ ?>
                    <option value = "<?php echo $row_category_sati_do['sat'] ?>" <?php if( $row_category_sati_do['sat'] == $sati_do){echo 'selected';} ?> ><?php echo $row_category_sati_do['sat']; ?></option>
                    <?php } ?>
                   
                </select>
              
            </td>
            <?php 
                $stmt_minuti = $termin->read_all_minuti();
            ?>    
            <td style="width: 10%">
                <select  class='form-control' name="minuti_do_22_n">
                <?php while($row_category_minuti = $stmt_minuti->fetch(PDO::FETCH_ASSOC)){ ?>
                    <option value = "<?php echo $row_category_minuti['minut'] ?>" <?php if( $sati_min == $row_category_minuti['minut']){echo "selected='selected'";} ?> ><?php echo $row_category_minuti['minut']; ?></option>
                    <?php } ?>    
                </select>
                
            </td>
        </tr>
        
        </table>