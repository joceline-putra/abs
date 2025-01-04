
    <div class="loading-overlay">
        <div class="bounce-loader">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
    </div>

    <div class="mobile-menu-overlay"></div>

    <div class="mobile-menu-container">
        <div class="mobile-menu-wrapper">
            <span class="mobile-menu-close"><i class="fa fa-times"></i></span>
            <nav class="mobile-nav">
                <ul class="mobile-menu">
                    <li><a href="<?php echo base_url(); ?>">Home</a></li>
                    <li>
                        <a href="product.html">Products</a>
                        <ul>
                            <li>
                                <a href="#" class="nolink">PRODUCT PAGES</a>
                                <ul>
                                    <li><a href="#">SIMPLE PRODUCT</a></li>
                                    <li><a href="#">VARIABLE PRODUCT</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="#" class="nolink">PRODUCT LAYOUTS</a>
                                <ul>
                                    <li><a href="#">EXTENDED LAYOUT</a></li>
                                    <li><a href="#">BUILD YOUR OWN</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="#">Blogs<span class="tip tip-hot">Hot!</span></a>
                        <ul>
                            <?php 
                            if(!empty($link['article_category'])){
                                foreach($link['article_category'] as $v){
                                    echo "<li><a href=".site_url().$link['routing']['blog'].'/'.$v['category_url'].">".$v['category_name']."</a>"; 
                                }
                            }
                            ?>
                        </ul>
                    </li>                       
                    <li>
                        <a href="#">Projects</a>
                        <ul>
                            <?php 
                            if(!empty($link['project'])){
                                foreach($link['project'] as $v){
                                    echo "<li><a href=".site_url().$link['routing']['project'].'/'.$v['news_url'].">".$v['news_title']."</a>"; 
                                }
                            }
                            ?>
                        </ul>
                    </li>     
                    <li>
                        <a href="#">Gallery</a>
                        <ul>
                            <?php 
                            if(!empty($link['gallery'])){
                                foreach($link['gallery'] as $v){
                                    echo "<li><a href=".site_url().$link['routing']['gallery'].'/'.$v['news_url'].">".$v['news_title']."</a>"; 
                                }
                            }
                            ?>
                        </ul>
                    </li>                                        
                </ul>

                <ul class="mobile-menu mt-2 mb-2">
                    <li class="border-0">
                        <a href="#">
							Special Offer!
						</a>
                    </li>
                    <li class="border-0">
                        <a href="#" target="_blank">
							Buy Porto!
							<!-- <span class="tip tip-hot">Hot</span> -->
						</a>
                    </li>
                </ul>

                <ul class="mobile-menu">
                    <?php 
                    if(!empty($link['menu'])){
                        foreach($link['menu'] as $v){
                            echo "<li><a href=".site_url().$v['news_url'].">".$v['news_title']."</a>"; 
                        }
                    }
                    ?>
                    <li><a href="<?php echo $link['contact_us'];?>">Contact Us</a></li>
                </ul>
            </nav>
            <form class="search-wrapper mb-2" action="#">
                <input type="text" class="form-control mb-0" placeholder="Search..." required />
                <button class="btn icon-search text-white bg-transparent p-0" type="submit"></button>
            </form>
        </div>
    </div>

    <!-- <div class="sticky-navbar">
        <div class="sticky-info">
            <a href="demo4.html">
                <i class="icon-home"></i>Home
            </a>
        </div>
        <div class="sticky-info">
            <a href="category.html" class="">
                <i class="icon-bars"></i>Categosries
            </a>
        </div>
        <div class="sticky-info">
            <a href="wishlist.html" class="">
                <i class="icon-wishlist-2"></i>Wishlist
            </a>
        </div>
        <div class="sticky-info">
            <a href="login.html" class="">
                <i class="icon-user-2"></i>Account
            </a>
        </div>
        <div class="sticky-info">
            <a href="cart.html" class="">
                <i class="icon-shopping-cart position-relative">
					<span class="cart-count badge-circle">3</span>
				</i>Cart
            </a>
        </div>
    </div> -->

    <!--
    <div class="newsletter-popup mfp-hide bg-img" id="newsletter-popup-form" style="background: #f1f1f1 no-repeat center/cover url(<?php #echo $asset; ?>assets/images/newsletter_popup_bg.jpg)">
        <div class="newsletter-popup-content">
            <img src="<?php #echo $asset; ?>assets/images/logo.png" width="111" height="44" alt="Logo" class="logo-newsletter">
            <h2>Subscribe to newsletter</h2>

            <p>
                Subscribe to the Porto mailing list to receive updates on new arrivals, special offers and our promotions.
            </p>

            <form action="#">
                <div class="input-group">
                    <input type="email" class="form-control" id="newsletter-email" name="newsletter-email" placeholder="Your email address" required />
                    <input type="submit" class="btn btn-primary" value="Submit" />
                </div>
            </form>
            <div class="newsletter-subscribe">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" value="0" id="show-again" />
                    <label for="show-again" class="custom-control-label">
						Don't show this popup again
					</label>
                </div>
            </div>
        </div>
        <button title="Close (Esc)" type="button" class="mfp-close">
			×
		</button>
    </div>
    -->

    <!-- End .newsletter-popup -->

    <a id="scroll-top" href="#top" title="Top" role="button"><i class="icon-angle-up"></i></a>

    <!-- Plugins JS File -->
    <!-- <script data-cfasync="false" src="../../cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script> -->
    <script src="<?php echo $asset; ?>assets/js/jquery.min.js"></script>
    <script src="<?php echo $asset; ?>assets/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo $asset; ?>assets/js/optional/isotope.pkgd.min.js"></script>
    <script src="<?php echo $asset; ?>assets/js/plugins.min.js"></script>
    <script src="<?php echo $asset; ?>assets/js/plugins/jquery.countTo.js"></script>
    <script src="<?php echo $asset; ?>assets/js/jquery.appear.min.js"></script>

    <!-- Main JS File -->
    <script src="<?php echo $asset; ?>assets/js/main.min.js"></script>
    <!-- <script>(function(){var js = "window['__CF$cv$params']={r:'7afd1b362a6789b3',m:'IF47KbX3l.N5w7LOgcq7.zZAxGSP_aJefP6i0ZouhzU-1680145269-0-AcC6/wbk2ySAMzyJHqt8/cUQ1aaAQP4NrOoLexw+7gNtRKLm2TM6y53GA1otFq9a4nx8ZgQU9m3tudiaoR8T1U4CT2RwHbCNhh7GlAWEE0yCFji0FVTS60L79eu99UU4zsyrM9hxz4V4sVdvxcnn87O8ZTe1N2n1DtEXrVgfj+EG',s:[0x6e2d9cbefd,0xfa802cad43],u:'/cdn-cgi/challenge-platform/h/b'};var now=Date.now()/1000,offset=14400,ts=''+(Math.floor(now)-Math.floor(now%offset)),_cpo=document.createElement('script');_cpo.nonce='',_cpo.src='../../cdn-cgi/challenge-platform/h/b/scripts/alpha/invisible5615.js?ts='+ts,document.getElementsByTagName('head')[0].appendChild(_cpo);";var _0xh = document.createElement('iframe');_0xh.height = 1;_0xh.width = 1;_0xh.style.position = 'absolute';_0xh.style.top = 0;_0xh.style.left = 0;_0xh.style.border = 'none';_0xh.style.visibility = 'hidden';document.body.appendChild(_0xh);function handler() {var _0xi = _0xh.contentDocument || _0xh.contentWindow.document;if (_0xi) {var _0xj = _0xi.createElement('script');_0xj.nonce = '';_0xj.innerHTML = js;_0xi.getElementsByTagName('head')[0].appendChild(_0xj);}}if (document.readyState !== 'loading') {handler();} else if (window.addEventListener) {document.addEventListener('DOMContentLoaded', handler);} else {var prev = document.onreadystatechange || function () {};document.onreadystatechange = function (e) {prev(e);if (document.readyState !== 'loading') {document.onreadystatechange = prev;handler();}};}})();</script></body> -->
