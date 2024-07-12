<style>
    .li-report > ul > li > a{
        margin-left: 60px!important; 
        color:red;
    }
    #report > ul{
        padding-top: 0px!important;
    }
    #report > ul > li{
        padding: 2px 0px;
    }
    #report > ul > li > ul > li > a{
        padding: 3px 0px!important;
    }
</style>
<?php
$next = true;
?>
<div id="main-menu" class="page-sidebar col-md-2" style="padding-bottom: 30px!important;display: block!important;">
    <div class="page-sidebar-wrapper scrollbar-dynamic" id="main-menu-wrapper">
        <p class="menu-title sm" style="padding-top:0px!important;margin:0px 0px 0px!important;">
        </p>
        <ul id="sidebar" style="" class="sidebarz">
            <li class="start"> 
                <a href="<?php echo base_url('admin'); ?>">
                  <!-- <i class="fa fa-home" style=""></i> -->
                    <i class="fas fa-home"></i>
                    <span class="title">Beranda</span> <span class="selected"></span>
                </a>
                <li class="start open visible-xs visible-sm hidden-md hidden-lg"> 
                    <ul class="open sub-menu" style="display:block;">
                        <li> 
                            <a href="#" class="btn-header-stock">
                                <i class="fas fa-cubes"></i> Cari Stok
                            </a> 
                        </li>
                        <li> 
                            <a href="#" class="btn-header-product-history">
                                <i class="fas fa-search-dollar"></i> Riwayat Harga
                            </a> 
                        </li>
                        <li> 
                            <a href="#" class="btn-header-stock-minimal">
                                <i class="fas fa-file-upload"></i> Stok Habis
                            </a> 
                        </li> 
                        <li> 
                            <a href="#" class="btn-header-trans-over-due">
                                <i class="fas fa-calendar-week"></i> Jatuh Tempo
                            </a> 
                        </li>
                        <li> 
                            <a href="#" class="btn-header-down-payment">
                                <i class="fas fa-money-bill"></i> Cari Down Payment
                            </a> 
                        </li>                                                                                    
                    </ul>
                </li>                
            </li>

            <?php
            //Start of Only Joe
            if($session['user_data']['user_group_id'] == 1){
            ?>
            <li class="start"> 
                <a href="#">
                    <i class="fas fa-wallet"></i>
                    <span class="title">Kasir</span> <span class="selected"></span>
                </a>
                <!-- <li class="start open visible-xs visible-sm hidden-md hidden-lg">  -->
                <li class="start open">
                    <ul class="open sub-menu" style="display:block;">
                        <li><a href="<?php echo site_url('sales/pos')?>">POS</a></li>
                        <li><a href="<?php echo site_url('sales/pos2')?>">POS 2</a></li>
                        <li><a href="<?php echo site_url('sales/pos3')?>">POS 3</a></li>
                    </ul>
                </li>
            </li>
            <?php                
            }
            //End of Only Joe
            if ($next) {
            }
            ?>  
            <li id="report" class="start open" style="display:none;"> 
                <a href="<?php echo base_url('report'); ?>">
                  <!-- <i class="fa fa-home" style=""></i> -->
                    <span class="material-icons">summarize</span>
                    <span class="title">Laporan</span> <span class="selected"></span>
                </a>
                <ul class="open sub-menu" style="display:block;">

                    <!-- Bisnis -->          
                    <li class="start open li-report"> 
                        <a id="" data-id="" href="" style="padding-left: 38px!important;"><span class="title">Bisnis</span> <span class="selected"></span></a> 
                        <ul class="open sub-menu" style="display:block;">
                            <!-- <li class="start"> 
                              <a id="" data-id="" href="" style=""><span class="title">Arus Kas</span> <span class="selected"></span></a> 
                            </li>    -->             
                            <li class="start"> 
                                <a id="" data-id="" href="<?php echo base_url('report/finance/journal'); ?>" style=""><span class="title">Jurnal</span> <span class="selected"></span></a> 
                            </li>      
                            <li class="start"> 
                                <a id="" data-id="" href="<?php echo base_url('report/finance/ledger'); ?>" style=""><span class="title">Buku Besar</span> <span class="selected"></span></a> 
                            </li>     
                            <li class="start">
                                <a id="" data-id="" href="<?php echo base_url('report/finance/trial_balance'); ?>" style=""><span class="title">Neraca Saldo</span> <span class="selected"></span></a>
                            </li> 
                            <li class="start">
                                <a id="" data-id="" href="<?php echo base_url('report/finance/worksheet'); ?>" style=""><span class="title">Kertas Kerja</span> <span class="selected"></span></a>
                            </li>               
                            <li class="start">
                                <a id="" data-id="" href="<?php echo base_url('report/finance/profit_loss'); ?>" style=""><span class="title">Laba Rugi</span> <span class="selected"></span></a>
                            </li>
                            <li class="start">
                                <a id="" data-id="" href="<?php echo base_url('report/finance/balance'); ?>" style=""><span class="title">Neraca</span> <span class="selected"></span></a>
                            </li> 
                        </ul>                 
                    </li> 

                    <!-- Pembelian -->          
                    <li class="start open li-report"> 
                        <a id="" data-id="" href="" style="padding-left: 38px!important;"><span class="title">Pembelian</span> <span class="selected"></span></a> 
                        <ul class="open sub-menu" style="display:block;">
                            <li class="start"> 
                                <a id="" data-id="" href="<?php echo base_url('report/purchase/buy/recap'); ?>" style=""><span class="title">Pembelian Rekap</span> <span class="selected"></span></a> 
                            </li>  
                            <li class="start"> 
                                <a id="" data-id="" href="<?php echo base_url('report/purchase/buy/detail'); ?>" style=""><span class="title">Pembelian Rinci</span> <span class="selected"></span></a> 
                            </li>     
                            <li class="start"> 
                                <a id="" data-id="" href="<?php echo base_url('report/purchase/buy/account_payable'); ?>" style=""><span class="title">Hutang Supplier</span> <span class="selected"></span></a> 
                            </li> 
                            <!-- <li class="start">  -->
                              <!-- <a id="" data-id="" href="" style=""><span class="title">Usia Hutang</span> <span class="selected"></span></a>  -->
                            <!-- </li>                                             -->
                        </ul>                 
                    </li> 

                    <!-- Penjualan -->
                    <li class="start open li-report"> 
                        <a id="" data-id="" href="" style="padding-left: 38px!important;"><span class="title">Penjualan</span> <span class="selected"></span></a> 
                        <ul class="open sub-menu" style="display:block;">
                            <li class="start"> 
                                <a id="" data-id="" href="<?php echo base_url('report/sales/sell/recap'); ?>" style=""><span class="title">Penjualan Rekap</span> <span class="selected"></span></a> 
                            </li>  
                            <li class="start"> 
                                <a id="" data-id="" href="<?php echo base_url('report/sales/sell/detail'); ?>" style=""><span class="title">Penjualan Rinci</span> <span class="selected"></span></a> 
                            </li>     
                            <li class="start"> 
                                <a id="" data-id="" href="<?php echo base_url('report/sales/sell/account_receivable'); ?>" style=""><span class="title">Piutang Customer</span> <span class="selected"></span></a> 
                            </li> 
                            <!-- <li class="start">  -->
                              <!-- <a id="" data-id="" href="" style=""><span class="title">Usia Piutang</span> <span class="selected"></span></a>  -->
                            <!-- </li>                   -->
                        </ul>                 
                    </li>

                    <!-- Produksi -->
                    <li class="start open li-report"> 
                        <a id="" data-id="" href="" style="padding-left: 38px!important;"><span class="title">Produksi</span> <span class="selected"></span></a> 
                        <ul class="open sub-menu" style="display:block;">
                            <li class="start"> 
                                <a id="" data-id="" href="<?php echo base_url('report/production/product/detail'); ?>" style=""><span class="title">Produk Jadi Rinci</span> <span class="selected"></span></a> 
                            </li>
                            <!-- <li class="start">  -->
                              <!-- <a id="" data-id="" href="" style=""><span class="title">Usia Piutang</span> <span class="selected"></span></a>  -->
                            <!-- </li>-->
                        </ul>                 
                    </li>

                    <!-- Produk -->
                    <li class="start open li-report"> 
                        <a id="" data-id="" href="" style="padding-left: 38px!important;"><span class="title">Stok</span> <span class="selected"></span></a> 
                        <ul class="open sub-menu" style="display:block;">
                            <!-- <li class="start">  -->
                              <!-- <a id="" data-id="" href="" style=""><span class="title">Ringkasan Persediaan</span> <span class="selected"></span></a>  -->
                            <!-- </li>       -->
                            <!-- <li class="start">  -->
                              <!-- <a id="" data-id="" href="" style=""><span class="title">Nilai Persediaan</span> <span class="selected"></span></a>  -->
                            <!-- </li>  -->
                            <!-- <li class="start">  -->
                              <!-- <a id="" data-id="" href="" style=""><span class="title">Rincian Persediaan</span> <span class="selected"></span></a>  -->
                            <!-- </li>    -->
                            <li class="start"> 
                                <a id="" data-id="" href="<?php echo base_url('report/inventory/product/stock_warehouse'); ?>" style=""><span class="title">Kuantitas Stok Gudang</span> <span class="selected"></span></a> 
                            </li>                                            
                            <!-- <li class="start">  -->
                              <!-- <a id="" data-id="" href="" style=""><span class="title">Nilai Stok Gudang</span> <span class="selected"></span></a>  -->
                            <!-- </li>                                                           -->
                            <li class="start"> 
                                <a id="" data-id="" href="<?php echo base_url('report/inventory/product/stock_moving'); ?>" style=""><span class="title">Pergerakan Stok</span> <span class="selected"></span></a> 
                            </li>                                                                        
                        </ul>                 
                    </li>

                    <!-- Asset -->
                    <li class="hide start open li-report"> 
                        <a id="" data-id="" href="" style="padding-left: 38px!important;"><span class="title">Asset</span> <span class="selected"></span></a> 
                        <ul class="open sub-menu" style="display:block;">
                            <li class="start"> 
                                <a id="" data-id="" href="" style=""><span class="title">Ringkasan Aset</span> <span class="selected"></span></a> 
                            </li>      
                            <li class="start"> 
                                <a id="" data-id="" href="" style=""><span class="title">Detail Aset</span> <span class="selected"></span></a> 
                            </li>                
                        </ul>                 
                    </li>

                    <!-- Pajak -->
                    <li class="hide start open li-report"> 
                        <a id="" data-id="" href="" style="padding-left: 38px!important;"><span class="title">Pajak</span> <span class="selected"></span></a> 
                        <ul class="open sub-menu" style="display:block;">
                            <li class="start"> 
                                <a id="" data-id="" href="" style=""><span class="title">Pajak Pemotongan</span> <span class="selected"></span></a> 
                            </li>
                            <li class="start"> 
                                <a id="" data-id="" href="" style=""><span class="title">Pajak Penjualan</span> <span class="selected"></span></a> 
                            </li>                
                        </ul>                 
                    </li>
                </ul>        
            </li>
        </ul>
        <div class="clearfix"></div>
        <br><br>
    </div>
</div>
<!-- <a href="#" class="scrollup">Scroll</a> -->

