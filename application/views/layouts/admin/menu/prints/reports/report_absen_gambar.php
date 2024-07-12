<link href="<?php echo base_url();?>assets/core/plugins/bootstrapv3/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url();?>assets/core/plugins/bootstrapv3/css/bootstrap-theme.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url();?>assets/core/plugins/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />   
<link href="<?php echo base_url();?>assets/core/css/_print.css?_=<?php echo date('d-m-Y');?>" rel="stylesheet" type="text/css" />   
<style>
    body{
        font-family: monospace;
    }
</style>
<div class="container-fluid">
    <title><?php echo $title;?></title>      
    <div id="print-paper" class="col-md-20" style="">
    <div class="col-md-12">
        <!-- <div class="col-md-2 col-sm-12" style="padding-left:0px;">
            <img src="<?php echo site_url('upload/branch/default_logo.png');?>" style="width:150px;" class="img-responsive">
        </div> -->
        <!-- <div class="col-md-10 col-sm-12" style="padding-left:0px;"> -->
            <a href='#' onclick="window.print();">
                <?php echo $title; ?> <?php echo $branch['branch_name']; ?>
            </a>
            <br>
            <!-- Periode : <?php #echo $branch['branch_name']; ?>
            <br> -->
            Periode : <?php echo $periode; ?>
            <br>
            <?php 
                if(!empty($userr)){
                    echo 'Karyawan : ' . $userr['user_fullname'].' ['.$userr['user_code'].']'.'<br>';
                }
            ?>        
        <!-- </div> -->
    </div>
      <!-- Content -->
     <!-- <div id="print-content" class="col-md-12 col-sm-12 col-xs-12">-->
        <!--<div class="col-md-12 col-sm-12 col-xs-12 padding-remove-side">-->

            <table class="table table-bordered table-hover">
                <thead>
                    <tr style="background-color: #ebebeb;">
                        <th>Tanggal</th>
                        <th>Jenis</th>                     
                        <th>Karyawan</th>
                        <th>Zona Terdaftar</th>
                        <th>Alamat</th>
                        <th>Catatan</th> 
                        <th>Kordinat</th>                     
                        <th>Gambar</th>                                    
                 	</tr>
            </thead>
            <tbody>
                <?php 
                $total_ci = 0;
                $total_co = 0;
                $total_sk = 0;
                $total_iz = 0;             
                foreach($content as $v):
                
                    if($v['att_type'] == 1){
                       $type_name = 'Check IN';
                       $total_ci = $total_ci + 1;
                    }else if($v['att_type'] == 2){
                        $type_name = 'Check OUT';
                        $total_co = $total_co + 1;                                                  
                    }else if($v['att_type'] == 3){
                        $type_name = 'Post Gambar';
                    }else if($v['att_type'] == 4){
                        $type_name = 'Sakit';
                        $total_sk = $total_sk + 1;                        
                    }else if($v['att_type'] == 5){
                        $type_name = 'Izin';
                        $total_iz = $total_iz + 1;                        
                    }else{
                        $type_name = '-';
                    }
                ?>
                <tr>
                     <td><?php echo date("d-M-Y, H:i", strtotime($v['att_date_created'])); ?></td>
                     <td>
                        <?php echo $type_name;
                        ?>
                    </td>
                     <td><?php echo $v['user_fullname'];?></td>                     
                     <td><?php echo $v['location_name'];?></td>
                     <td><?php echo $v['att_address'];?></td>    
                     <td><?php echo $v['att_note'];?></td>
                     <td><a href="https://google.com/search?q=<?php echo $v['att_lat'].','.$v['att_lng']; ?>" target="_blank"><?php echo $v['att_lat'].','.$v['att_lng'];?></a></td>                     
                     <td>
                        <?php if(!empty($v['att_image'])){ ?>
                            <!--<a href="<?php echo base_url($v['att_image'])?>" target="_blank">Lihat Gambar</a>-->
                            <img src="<?php echo base_url($v['att_image'])?>" class="img-responsive" style="width:150px;">
                        <?php } ?>
                    </td>                                          
                 </tr>  
                 <?php               
                 endforeach;
                 ?>
                <tr style="background-color: #ebebeb;">
                    <td colspan="8"><b>Ringkasan</b></td>
                </tr>                   
            </tbody>
            <tfoot>
                <tr>
                    <td>Periode</td>
                    <td><?php echo $periode_ringkasan; ?></td>                    
                </tr>
                <tr>
                    <td>Check IN</td>
                    <td><?php echo $total_ci;?></td>                    
                </tr>
                <tr>
                    <td>Check OUT</td>
                    <td><?php echo $total_co;?></td>
                </tr>
                <tr>
                    <td>Sakit</td>
                    <td><?php echo $total_sk;?></td>    
                </tr>
                <tr>
                    <td>Izin</td>
                    <td><?php echo $total_iz;?></td>   
                </tr>                                                
            </tfoot>
        </table> 
        <!-- </div> -->
    <!-- </div>    -->

        <!-- Footer -->
        <!--<div id="print-footer" class="col-md-12 col-sm-12 col-xs-12">
          <div>Dicetak :  <?php echo ucfirst($session['user_data']['user_name']);?> | <?php echo date("d-m-Y H:i:s");?></div>
      </div> -->                           
  </div>    

</div>