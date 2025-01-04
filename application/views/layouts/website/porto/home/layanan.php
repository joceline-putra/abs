
<main class="main about">
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
		
	<!-- <div class="page-header page-header-bg text-left"
		style="background: 50%/cover #D4E1EA url('<?php echo $asset; ?>assets/images/page-header-bg.jpg');">
		<div class="container">
			<h1><span>ABOUT US</span>
				OUR COMPANY</h1>
			<a href="contact.html" class="btn btn-dark">Contact</a>
		</div>
	</div> -->

	<!-- <nav aria-label="breadcrumb" class="breadcrumb-nav">
		<div class="container">
			<ol class="breadcrumb">
				<li class="breadcrumb-item"><a href="demo4.html"><i class="icon-home"></i></a></li>
				<li class="breadcrumb-item active" aria-current="page">About Us</li>
			</ol>
		</div>
	</nav> -->

    <!-- Layanan Utama Kami -->
    <div class="section-elements" style="background: #ffffff;">
        <div class="container">

            <div class="row justify-content-center">
                <div class="row">
					<div class="col-md-6">
						<h5 class="text-primary text-left">Layanan Kualitas Unggul</h5>
						<h2 class="mb-5 elements text-left">Layanan Utama Kami</h2>						
					</div>
					<div class="col-md-6">
						<p class="text-left">Florina Vania Indonesia berkomitmen untuk memberikan layanan terbaik dalam setiap aspek moda,
							dari desain hingga produksi hingga promosi batik asli Indonesia ke pasar internasional
						</p>
					</div>										
                    <?php 
                    foreach($link['product_category'] as $i => $v){
                        if($i < 4){
                        ?>                    
                    <div class="col-md-6 mb-8">
                        <div class="info-box info-box-img">
                            <img src="<?php echo base_url().$v['category_image']?>" alt="info-box-image" width="800" height="524">
                            <div class="info-box-content">
                                <h3 class="text-left mb-2"><?php echo $v['category_name']?></h3>
                                <p class="text-left"><?php echo $v['category_short']?>
                                    <!-- <br><br><a href="<?php echo base_url('produk/').$v['category_url']?>" style="color:black;">Baca Selengkapnya <i class="fas fa-greater-than" style="font-size:1rem;color:black;"></i></a> -->
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
</main><!-- End .main -->