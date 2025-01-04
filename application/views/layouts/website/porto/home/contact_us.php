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

    <div class="about-section">
        <div class="container">
            <div class="row">

                <div class="col-lg-6">
                    <h2 class="mt-6 mb-1">Informasi Kontak</h2>
                    <p>
                        Silahkan hubungi kami untuk pertanyaan dan kerjasama. Kami senang mendengar dari anda.
                        <br>
                        <address>
                        <i class="fas fa-map-marker"></i> Alamat<br>
                        <?php echo $link['contact']['address']['office'].', '.$link['contact']['address']['city'];?>
                        <!-- <br><br>
                        <i class="fas fa-phone"></i> Telepon<br>
                        <?php #echo $link['contact']['phone'][0]['phone'];?> -->
                        <br><br>
                        <i class="fab fa-whatsapp"></i> WhatsApp<br>
                            <a href="https://wa.me/<?php echo $link['contact']['phone'][0]['phone'];?>" target="_blank"><?php echo $link['contact']['phone'][0]['phone'];?></a>
                        <br>
                        </address>                        
                        Ikuti<br>
                        <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>                         
                    </p>
                </div>

                <div class="col-lg-6">
                    <h2 class="mt-6 mb-2">Kirim Pesan</h2>

                    <form class="mb-0" action="#">
                        <div class="form-group">
                            <label class="mb-1" for="contact-name">Nama
                                <span class="required">*</span></label>
                            <input type="text" class="form-control" id="contact-name" name="contact-name"
                                required />
                        </div>

                        <div class="form-group">
                            <label class="mb-1" for="contact-email">Email
                                <span class="required">*</span></label>
                            <input type="email" class="form-control" id="contact-email" name="contact-email"
                                required />
                        </div>

                        <div class="form-group">
                            <label class="mb-1" for="contact-message">Pesan
                                <span class="required">*</span></label>
                            <textarea cols="30" rows="1" id="contact-message" class="form-control"
                                name="contact-message" required></textarea>
                        </div>

                        <div class="form-footer mb-0">
                            <button type="submit" class="btn btn-dark font-weight-normal">
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div><!-- End .container -->
    </div><!-- End .about-section -->
</main><!-- End .main -->
