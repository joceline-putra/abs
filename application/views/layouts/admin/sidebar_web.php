<?php
$icon = 'none;';
// $icon = 'inline;';
?>
<div id="main-menu" class="page-sidebar col-md-2" style="padding-bottom: 30px!important;display: block!important;">
    <div class="page-sidebar-wrapper scrollbar-dynamic" id="main-menu-wrapper">
        <p class="menu-title sm" style="padding-top:0px!important;margin:0px 0px 0px!important;">
        </p>
        <ul id="sidebar" class="sidebarz">        
            <li class="start"> 
                <a href="<?php echo base_url('admin'); ?>">
                    <i class="fas fa-home"></i>
                    <span class="title">Menu</span> <span class="selected"></span>
                </a> 
                <li class="start open">
                    <ul class="open sub-menu" style="display:block;">
                        <li><a href="<?php echo site_url('attendance')?>"><i class="fas fa-hdd" style="display:<?php echo $icon; ?>"></i> CheckIn & Out</a></li>                        
                    </ul>
                </li>                       
            </li>            
            <li class="start"> 
                <a href="<?php echo base_url('admin'); ?>">
                    <i class="fas fa-cogs"></i>
                    <span class="title">Laporan</span> <span class="selected"></span>
                </a> 
                <li class="start open">
                    <ul class="open sub-menu" style="display:block;">
                        <li><a href="<?php echo site_url('report/attendance')?>"><i class="fas fa-hdd" style="display:<?php echo $icon; ?>"></i> Absensi</a></li>                          
                    </ul>
                </li>                       
            </li>  
            <li class="start"> 
                <a href="<?php echo base_url('admin'); ?>">
                    <i class="fas fa-cogs"></i>
                    <span class="title">Pengaturan</span> <span class="selected"></span>
                </a> 
                <li class="start open">
                    <ul class="open sub-menu" style="display:block;">
                        <li><a href="<?php echo site_url('user')?>"><i class="fas fa-hdd" style="display:<?php echo $icon; ?>"></i> Karyawan</a></li>    
                        <li><a href="<?php echo site_url('user/group')?>"><i class="fas fa-hdd" style="display:<?php echo $icon; ?>"></i> Group Karyawan</a></li>                                 
                        <li><a href="<?php echo site_url('product/warehouse')?>"><i class="fas fa-hdd" style="display:<?php echo $icon; ?>"></i> Zona Absensi</a></li>                                                              
                        <li><a href="<?php echo site_url('configuration/branch')?>"><i class="fas fa-hdd" style="display:<?php echo $icon; ?>"></i> Perusahaan</a></li>
                    </ul>
                </li>                       
            </li>              
        </ul>
        <div class="clearfix"></div>
        <br><br>
    </div>
</div>
<!-- <a href="#" class="scrollup">Scroll</a> -->

