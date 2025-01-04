<main class="main info-box-wrapper">
    <!-- Carousel -->
    <div style="top:-117px;" class="home-slider slide-animate owl-carousel owl-theme show-nav-hover nav-big mb-2 text-uppercase" data-owl-options="{'loop': false}">
    <?php 
    foreach($link['blog'] as $i => $v){
        echo $v['file_url'];
    ?>
        <div class="home-slide home-slide1 banner">
            <img class="slide-bg" src="<?php echo base_url().$v['news_image']; ?>" width="1903" height="499" alt="slider image">
            <div class="container d-flex align-items-center">
                <div class="banner-layer appear-animate" data-animation-name="fadeInUpShorter">
                    <h4 class="text-transform-none m-b-3" style="text-align:center;color:white;"><?php echo $v['news_title'];?></h4>
                    <!-- <h2 class="text-transform-none m-b-3" style="text-align:center;font-family:inherit;">.</h2> -->
                    <?php echo $v['news_short'];?>
                    <p class="text-transform-none m-b-3" style="text-align:center;">
                        <br>
                        <a href="<?php echo base_url('contact-us');?>" class="btn btn-primary btn-lg" style="text-align:center;">Hubungi Kami</a>
                    </p>
                </div>
            </div>
        </div>
    <?php 
    } 
    ?>
        <!--         
        <div class="home-slide home-slide1 banner">
            <img class="slide-bg" src="<?php #echo $asset; ?>assets/images/demoes/demo4/slider/slide-1.jpg" width="1903" height="499" alt="slider image">
            <div class="container d-flex align-items-center">
                <div class="banner-layer appear-animate" data-animation-name="fadeInUpShorter">
                    <h4 class="text-transform-none m-b-3" style="text-align:center;">Batik dengan Sentuhan Modern</h4>
                    <h2 class="text-transform-none m-b-3" style="text-align:center;font-family:inherit;">Batik Nusantara Premium</h2>
                    <p class="text-transform-none m-b-3" style="text-align:center;">
                        Memberikan keindahan batik nusantara yang dirancang dengan kualitas tinggi dan elegan, 
                        merepresentasikan tradisi Indonesia di panggung dunia.
                        <br>
                        <a href="category.html" class="btn btn-dark btn-lg" style="text-align:center;">Hubungi Kami</a>
                    </p>
                </div>
            </div>
        </div> 
        -->
    </div>

    <!-- Layanan Utama Kami -->
    <div class="section-elements" style="background: #ffffff;">
        <div class="container">
            <h5 class="text-primary">Layanan Kualitas Unggul</h5>
            <h2 class="mb-5 elements">Layanan Utama Kami</h2>
            <div class="row justify-content-center">
                <div class="row">
                    <?php 
                    foreach($link['product_category'] as $i => $v){
                        if($i < 3){
                        // if($v['news_id'] == 46){
                        //     $stitle     = $v['news_title'];
                        //     $scontent   = $v['news_short'];   
                        //     $surl   = base_url().$v['news_url'];  
                        //     $simg = base_url().$v['file_url'];                                      
                        // }
                        ?>                    
                    <div class="col-md-4">
                        <div class="info-box info-box-img">
                            <img src="<?php echo base_url().$v['category_image']?>" alt="info-box-image" width="800" height="524">
                            <div class="info-box-content">
                                <h4><?php echo $v['category_name']?></h4>
                                <p><?php echo $v['category_short']?>
                                    <br><br><a href="<?php echo base_url('produk/').$v['category_url']?>" style="color:black;">Baca Selengkapnya <i class="fas fa-greater-than" style="font-size:1rem;color:black;"></i></a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php 
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Keunggulan Kami -->
    <div class="section-elements" style="background: #f1f1fd;">
        <div class="container">
            <?php 
            foreach($link['menu'] as $v){
                if($v['news_id'] == 46){
                    $stitle     = $v['news_title'];
                    $scontent   = $v['news_short'];   
                    $surl   = base_url().$v['news_url'];  
                    $simg = base_url().$v['file_url'];                                      
                }
            }
            ?>
            <div class="row justify-content-left">
                <div class="col-md-5">
                    <div class="info-box info-box-img">
                        <div class="info-box-content">
                            <h5 class="text-primary"><?php echo $stitle; ?></h5>
                            <div style="text-align: left;">
                                <p><?php echo $scontent; ?></p>
                                <br>
                                <div class="btn btn-primary btn-ellipse btn-md mt-2"><a href="<?php echo $surl;?>" style="color:white;">Baca Selengkapnya</a></div>
                            </div>    
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="info-box info-box-img">
                        <img src="<?php echo $simg;?>" alt="info-box-image" style="border-radius:20px;">
                    </div>
                </div>                
            </div>
        </div>
    </div>
    
    <!-- <article class="post">
        <div class="post-media">
            <a href="single.html">
                <img src="<?php echo $asset ; ?>assets//images/blog/home/post-2.jpg" alt="Post" width="225" height="280">
            </a>
            <div class="post-date">
                <span class="day">26</span>
                <span class="month">Feb</span>
            </div>
        </div>

        <div class="post-body">
            <h2 class="post-title">
                <a href="single.html">Fashion Trends</a>
            </h2>
            <div class="post-content">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras non
                    placerat mi.
                    Etiam non tellus sem. Aenean...</p>
            </div>
            <a href="single.html" class="post-comment">0 Comments</a>
        </div>
    </article>
    -->

    <!-- Tentang Kami --> 
    <div class="section-elements" style="background: #ffffff;">
        <div class="container">
            <?php 
            foreach($link['menu'] as $v){
                if($v['news_id'] == 1){
                    $stitle     = $v['news_title'];
                    $scontent   = $v['news_short'];   
                    $surl   = base_url().$v['news_url'];  
                    $simg = base_url().$v['file_url'];                                          
                }
            }
            ?>
            <div class="row justify-content-left">
                <div class="col-md-7">
                    <div class="info-box info-box-img">
                        <img src="<?php echo $simg;?>" alt="info-box-image" style="border-radius:20px;">
                    </div>
                </div> 
                <div class="col-md-5">
                    <div class="info-box info-box-img">
                        <div class="info-box-content">
                            <h5 class="text-primary" style="text-align: left;"><?php echo $stitle; ?></h5>
                            <div style="text-align: left;">
                                <p><?php echo $scontent; ?></p>
                                <br>
                                <div class="btn btn-primary btn-ellipse btn-md mt-2"><a href="<?php echo $surl;?>" style="color:white;">Baca Selengkapnya</a></div>
                            </div>  
                        </div>
                    </div>
                </div>               
            </div>
        </div>
    </div>

    <!-- Gallery Kami -->
    <div class="section-elements" style="background: #f1f1fd;">
        <div class="container">
            <h5 class="text-primary">Gallery Kami</h5>
            <h2 class="mb-5 elements">Ragam Batik Nusantara</h2>
            <div class="row justify-content-center">
                <?php 
                foreach($link['gallery'] as $v){
                        $stitle     = $v['news_title'];
                        $scontent   = $v['news_short'];   
                        $surl   = base_url().$v['news_url'];  
                        $simg = base_url().$v['file_url']; 
                ?>
                <div class="col-md-4 mb-4">
                    <div class="info-box info-box-img">
                        <img src="<?php echo $simg;?>" style="border-radius:20px;" alt="<?php echo $stitle;?>">
                        <div class="info-box-content">
                        </div>
                    </div>
                </div>
                <?php 
                }
                ?>                              
            </div>
        </div>
    </div>

    <!-- Bergabunglah dalam melestarikan Batik Nusantara -->
    <div class="section-elements" style="background: #ffffff;">
        <div class="container">
            <!-- <h4 class="elements">Bergabunglah dalam melestarikan Batik Nusantara</h4> -->
            <!-- <p class="mb-5">Daftar sekarang untuk mendapatkan update dan promosi terbaru dari koleksi batik kami</p> -->
            <div class="row justify-content-center">
                <div class="">
					<!-- <h3>Small</h3> -->
					<div class="row">
						<div class="col-lg-2">
						</div>
						<div class="col-lg-8">
                            <div class="cta-simple cta-border">
								<h4 class="elements">Bergabunglah dalam melestarikan Batik Nusantara</h4>
								<p>Daftar sekarang untuk mendapatkan update dan promosi terbaru dari koleksi batik kami</p>
								<div class="btn btn-primary btn-ellipse btn-md mt-2"><a href="<?php echo $link['contact_us'];?>" style="color:white;">Hubungi Kami</a></div>
							</div>
						</div>
						<div class="col-lg-2">
						</div>
					</div>
				</div>
            </div>
        </div>
    </div>

</main>