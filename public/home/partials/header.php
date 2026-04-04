<!-- Header Section Start -->
        <header class="header--section header--style-4">
            <!-- Header Topbar Start -->
            <div class="header--topbar bg--color-1">
                <div class="container">
                    <div class="float--left float--xs-none text-xs-center">
                        <!-- Header Topbar Info Start -->
                        <ul class="header--topbar-info nav">
                            <!-- <li><i class="fa fm fa-map-marker"></i>New York</li>
                            <li><i class="fa fm fa-mixcloud"></i>21<sup>0</sup> C</li> -->
                            <li><i class="fa fm fa-calendar"></i>Today (Sunday 19 April 2017)</li>
                            <li><i class="fa fm fa-mixcloud"></i> Website Resmi Yayasan Ihsanul Amal Alabio</li> 
                        </ul>
                        <!-- Header Topbar Info End -->
                    </div>

                    <div class="float--right float--xs-none text-xs-center">
                        <!-- Header Topbar Action Start -->
                        <ul class="header--topbar-action nav">
                            <li><a href="../admin/authentication-signin.php" target="_blank"><i class="fa fm fa-user-o"></i>Login Admin</a></li>
                        </ul>
                        <!-- Header Topbar Action End -->
                        
                        <!-- Header Topbar Language Start -->
                        <!-- <ul class="header--topbar-lang nav">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fm fa-language"></i>English<i class="fa flm fa-angle-down"></i></a>

                                <ul class="dropdown-menu">
                                    <li><a href="#">English</a></li>
                                    <li><a href="#">Spanish</a></li>
                                    <li><a href="#">French</a></li>
                                </ul>
                            </li>
                        </ul> -->
                        <!-- Header Topbar Language End -->

                        <!-- Header Topbar Social Start -->
                        <ul class="header--topbar-social nav hidden-sm hidden-xxs">
                            <li><a href="#"><i class="fa fa-facebook"></i></a></li>
                            <!-- <li><a href="#"><i class="fa fa-twitter"></i></a></li>
                            <li><a href="#"><i class="fa fa-google-plus"></i></a></li>-->
                            <li><a href="#"><i class="fa fa-instagram"></i></a></li> 
                            <li><a href="#"><i class="fa fa-youtube-play"></i></a></li>
                        </ul>
                        <!-- Header Topbar Social End -->
                    </div>
                </div>
            </div>
            <!-- Header Topbar End -->

            <!-- Header Navbar Start -->
            <div class="header--navbar navbar bd--color-1 bg--color-0" data-trigger="sticky">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#headerNav" aria-expanded="false" aria-controls="headerNav">
                            <span class="sr-only">Toggle Navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>

                        <!-- Header Logo Start -->
                        <div class="header--logo text-center">
                            <h1 class="h1">
                                <a href="../public/home.php" class="btn-link">
                                    <img src="img/logoyysn.png" alt="Ihsanul Amal Logo" width="80px">
                                    <span class="hidden">Logo</span>
                                </a>
                            </h1>
                        </div>
                        <!-- Header Logo End -->
                    </div>

                    <!-- Header Search Form Start -->
                    <form action="#" class="header--search-form float--right" data-form="validate">
                        <input type="search" name="search" placeholder="Search..." class="header--search-control form-control" required>

                        <button type="submit" class="header--search-btn btn"><i class="header--search-icon fa fa-search"></i></button>
                    </form>
                    <!-- Header Search Form End -->

                    <?php

                        // require_once '../interface/koneksi_db.php';
                        include __DIR__ . '/../../../admin/koneksi_db.php';

                        // Memeriksa koneksi
                        if ($conn->connect_error) {
                            die("Koneksi gagal: " . $conn->connect_error);
                        }

                        // Mengambil data dari tabel menu_utama
                        $sql = "SELECT * FROM menu_utama WHERE enable = 1 ORDER BY urutan";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {

                            echo '<div id="headerNav" class="navbar-collapse collapse float--right">
                            <ul class="header--menu-links nav navbar-nav" data-trigger="hoverIntent">';
                            
                            while($row = $result->fetch_assoc()) {

                                // cek anak
                                $nama_menu = $row["nama_menu"];

                                $sql_sub_menu = "SELECT * FROM sub_menu WHERE enable = 1 AND menu_utama = '$nama_menu' ORDER BY urutan";
                                $result_2 = $conn->query($sql_sub_menu);

                                // Periksa apakah query berhasil
                                if ($result_2->num_rows > 0) {
                                    $var_class = 'class="dropdown active"';
                                    $komponen_a = 'href="#" class="dropdown-toggle" data-toggle="dropdown"';
                                    $komponen_i = '<i class="fa flm fa-angle-down"></i>';
                                    $komponen_ul = '<ul class="dropdown-menu">';

                                } else {
                                    // echo "";
                                    $var_class = '';
                                    $komponen_a = 'href="'. $row["link_menu"].'"';
                                    $komponen_i = '';
                                    $komponen_ul = '';
                                }

                                echo '<li '.$var_class.'>
                                    <a style = "padding-left: 30px; padding-right: 30px;" '.$komponen_a.'>' . $row["nama_menu"].$komponen_i. '</a>'.$komponen_ul;
                                    // Looping untuk membaca data yang dipilih
                                    while($row_2 = $result_2->fetch_assoc()) {
                                        
                                        echo '<li><a href="'.$row_2["link_menu"].'">'.$row_2["nama_menu"].'</a></li>';
                            
                                    }

                                    if ($komponen_ul != '') {
                                        echo '</ul>';
                                    }
                                echo '</li>';

                            }

                            echo '</ul>
                            </div>';
                            // echo "</table>";
                        } else {
                            echo "0 results";
                        }

                        // Menutup koneksi
                        $conn->close();
                    ?>


                    <!-- <div id="headerNav" class="navbar-collapse collapse float--right">
                        <ul class="header--menu-links nav navbar-nav" data-trigger="hoverIntent"> 
                            
                            <li class="dropdown active">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Home<i class="fa flm fa-angle-down"></i></a>

                                <ul class="dropdown-menu">
                                    <li><a href="home-1.html">Home Default</a></li>
                                    <li><a href="home-1-boxed.html">Home Boxed</a></li>
                                    <li><a href="home-1-rtl.html">Home RTL</a></li>
                                    <li><a href="home-1-rtl-boxed.html">Home RTL Boxed</a></li>
                                    <li class="dropdown active">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Home Versions<i class="fa flm fa-angle-right"></i></a>

                                        <ul class="dropdown-menu">
                                            <li><a href="home-1.html">Home Version 01</a></li>
                                            <li><a href="home-2.html">Home Version 02</a></li>
                                            <li><a href="home-3.html">Home Version 03</a></li>
                                            <li class="active"><a href="home-4.html">Home Version 04</a></li>
                                            <li><a href="home-5.html">Home Version 05</a></li>
                                            <li><a href="home-6.html">Home Version 06</a></li>
                                            <li><a href="home-7.html">Home Version 07</a></li>
                                            <li><a href="home-8.html">Home Version 08</a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Menu Versions<i class="fa flm fa-angle-right"></i></a>

                                        <ul class="dropdown-menu">
                                            <li><a href="home-1.html">Home Menu V1</a></li>
                                            <li><a href="home-menu-v2.html">Home Menu V2</a></li>
                                            <li><a href="home-menu-v3.html">Home Menu V3</a></li>
                                            <li><a href="home-menu-v4.html">Home Menu V4</a></li>
                                            <li><a href="home-menu-v5.html">Home Menu V5</a></li>
                                            <li><a href="home-menu-v6.html">Home Menu V6</a></li>
                                            <li><a href="home-menu-v7.html">Home Menu V7</a></li>
                                            <li><a href="home-menu-v8.html">Home Menu V8</a></li>
                                            <li><a href="home-menu-v9.html">Home Menu V9</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>

                            <li class="dropdown megamenu filter">
                                <a href="world-news.html" class="dropdown-toggle" data-toggle="dropdown">Worlds News<i class="fa flm fa-angle-down"></i></a>

                                <ul class="dropdown-menu">
                                    <li>
                                        <div class="row">
                                            <div class="col-md-2">
                                               
                                                <div class="megamenu--filter">
                                                    <ul class="nav">
                                                        <li class="active">
                                                            <a href="#" data-action="load_cat_posts" data-cat="all">All<i class="fa fa-angle-right"></i></a>
                                                        </li>
                                                        <li>
                                                            <a href="#" data-action="load_cat_posts" data-cat="latin-america">Latitn America<i class="fa fa-angle-right"></i></a>
                                                        </li>
                                                        <li>
                                                            <a href="#" data-action="load_cat_posts" data-cat="africa">Africa<i class="fa fa-angle-right"></i></a>
                                                        </li>
                                                        <li>
                                                            <a href="#" data-action="load_cat_posts" data-cat="middle-east">Middle East<i class="fa fa-angle-right"></i></a>
                                                        </li>
                                                        <li>
                                                            <a href="#" data-action="load_cat_posts" data-cat="europe">Europe<i class="fa fa-angle-right"></i></a>
                                                        </li>
                                                    </ul>
                                                </div>
                                           
                                            </div>

                                            <div class="col-md-10">
                                          
                                                <div class="megamenu--posts" data-ajax-content="outer">
                                                    <ul class="row" data-ajax-content="inner">
                                                        <li class="col-md-3">
                                                            <div class="img">
                                                                <a href="news-single-v1.html" class="thumb">
                                                                    <img src="img/megamenu-img/post-01.jpg" alt="">
                                                                </a>

                                                                <a href="#" class="cat">Beach</a>

                                                                <a href="#" class="icon"><i class="fa fa-eye"></i></a>
                                                            </div>

                                                            <a href="news-single-v1.html" class="title">It is a long established fact that a reader will be distracted by</a>
                                                        </li>

                                                        <li class="col-md-3">
                                                            <div class="img">
                                                                <a href="news-single-v1.html" class="thumb">
                                                                    <img src="img/megamenu-img/post-02.jpg" alt="">
                                                                </a>

                                                                <a href="#" class="cat">News</a>

                                                                <a href="#" class="icon"><i class="fa fa-star-o"></i></a>
                                                            </div>
                                                            
                                                            <a href="news-single-v1.html" class="title">It is a long established fact that a reader will be distracted by</a>
                                                        </li>

                                                        <li class="col-md-3">
                                                            <div class="img">
                                                                <a href="news-single-v1.html" class="thumb">
                                                                    <img src="img/megamenu-img/post-03.jpg" alt="">
                                                                </a>

                                                                <a href="#" class="cat">Ice Hiking</a>

                                                                <a href="#" class="icon"><i class="fa fa-flash"></i></a>
                                                            </div>
                                                            
                                                            <a href="news-single-v1.html" class="title">It is a long established fact that a reader will be distracted by</a>
                                                        </li>

                                                        <li class="col-md-3">
                                                            <div class="img">
                                                                <a href="news-single-v1.html" class="thumb">
                                                                    <img src="img/megamenu-img/post-04.jpg" alt="">
                                                                </a>

                                                                <a href="#" class="cat">Mountain</a>

                                                                <a href="#" class="icon"><i class="fa fa-heart-o"></i></a>
                                                            </div>
                                                            
                                                            <a href="news-single-v1.html" class="title">It is a long established fact that a reader will be distracted by</a>
                                                        </li>
                                                    </ul>

                                                 
                                                    <div class="preloader bg--color-0--b" data-preloader="1">
                                                        <div class="preloader--inner"></div>
                                                    </div>
                                          
                                                </div>
                                       
                                                <div class="megamenu--pagination" data-ajax="tab">
                                                    <a href="#" class="prev" data-ajax-action="load_prev_posts">
                                                        <i class="fa fa-long-arrow-left"></i>
                                                    </a>

                                                    <a href="world-news.html" class="all" title="View All" data-toggle="tooltip">
                                                        <i class="fa fa-th-large"></i>
                                                    </a>

                                                    <a href="#" class="next" data-ajax-action="load_next_posts">
                                                        <i class="fa fa-long-arrow-right"></i>
                                                    </a>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </li>

                            <li><a href="national.html">National</a></li>
                            <li><a href="technology.html">Technology</a></li>

                            <li class="dropdown megamenu posts">
                                <a href="travel.html" class="dropdown-toggle" data-toggle="dropdown">Travel<i class="fa flm fa-angle-down"></i></a>

                                <ul class="dropdown-menu">
                                    <li>
                                       
                                        <div class="megamenu--posts" data-ajax-content="outer">
                                            <ul class="row" data-ajax-content="inner">
                                                <li class="col-md-3">
                                                    <div class="img">
                                                        <a href="news-single-v1.html" class="thumb">
                                                            <img src="img/megamenu-img/post-01.jpg" alt="">
                                                        </a>

                                                        <a href="#" class="cat">Beach</a>

                                                        <a href="#" class="icon"><i class="fa fa-eye"></i></a>
                                                    </div>

                                                    <a href="news-single-v1.html" class="title">It is a long established fact that a reader will be distracted by</a>
                                                </li>

                                                <li class="col-md-3">
                                                    <div class="img">
                                                        <a href="news-single-v1.html" class="thumb">
                                                            <img src="img/megamenu-img/post-02.jpg" alt="">
                                                        </a>

                                                        <a href="#" class="cat">News</a>

                                                        <a href="#" class="icon"><i class="fa fa-star-o"></i></a>
                                                    </div>
                                                    
                                                    <a href="news-single-v1.html" class="title">It is a long established fact that a reader will be distracted by</a>
                                                </li>

                                                <li class="col-md-3">
                                                    <div class="img">
                                                        <a href="news-single-v1.html" class="thumb">
                                                            <img src="img/megamenu-img/post-03.jpg" alt="">
                                                        </a>

                                                        <a href="#" class="cat">Ice Hiking</a>

                                                        <a href="#" class="icon"><i class="fa fa-flash"></i></a>
                                                    </div>
                                                    
                                                    <a href="news-single-v1.html" class="title">It is a long established fact that a reader will be distracted by</a>
                                                </li>

                                                <li class="col-md-3">
                                                    <div class="img">
                                                        <a href="news-single-v1.html" class="thumb">
                                                            <img src="img/megamenu-img/post-04.jpg" alt="">
                                                        </a>

                                                        <a href="#" class="cat">Mountain</a>

                                                        <a href="#" class="icon"><i class="fa fa-heart-o"></i></a>
                                                    </div>
                                                    
                                                    <a href="news-single-v1.html" class="title">It is a long established fact that a reader will be distracted by</a>
                                                </li>
                                            </ul>

                                        
                                            <div class="preloader bg--color-0--b" data-preloader="1">
                                                <div class="preloader--inner"></div>
                                            </div>
                                          
                                        </div>
                                   
                                        <div class="megamenu--pagination" data-ajax="tab">
                                            <a href="#" class="prev" data-ajax-action="load_prev_posts">
                                                <i class="fa fa-long-arrow-left"></i>
                                            </a>

                                            <a href="travel.html" class="all" title="View All" data-toggle="tooltip">
                                                <i class="fa fa-th-large"></i>
                                            </a>
                                            <a href="#" class="next" data-ajax-action="load_next_posts">
                                                <i class="fa fa-long-arrow-right"></i>
                                            </a>
                                        </div>
                                 
                                    </li>
                                </ul>
                            </li>

                            <li><a href="sports.html">Sports</a></li>

                            <li class="dropdown megamenu">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Catagory<i class="fa flm fa-angle-down"></i></a>

                                <ul class="dropdown-menu">
                                    <li class="dropdown">
                                        <a href="#">Worldâ€™s News</a>

                                        <ul class="dropdown-menu">
                                            <li><a href="#">US &amp; Canada</a></li>
                                            <li><a href="#">Europe</a></li>
                                            <li><a href="#">Africa</a></li>
                                            <li><a href="#">Asia</a></li>
                                            <li><a href="#">Middle East</a></li>
                                            <li><a href="#">Asia Pacific</a></li>
                                        </ul>
                                    </li>

                                    <li class="dropdown">
                                        <a href="#">Documantation</a>

                                        <ul class="dropdown-menu">
                                            <li><a href="#">Featured Documantation</a></li>
                                            <li><a href="#">People &amp; Power</a></li>
                                            <li><a href="#">Rebel Education</a></li>
                                            <li><a href="#">Rewind</a></li>
                                            <li><a href="#">Fault Lines</a></li>
                                            <li><a href="#">News 360 Degree Worldâ€™s</a></li>
                                        </ul>
                                    </li>

                                    <li class="dropdown">
                                        <a href="#">Sports</a>

                                        <ul class="dropdown-menu">
                                            <li><a href="#">Football</a></li>
                                            <li><a href="#">Cricket</a></li>
                                            <li><a href="#">Hocky</a></li>
                                        </ul>
                                    </li>

                                    <li class="dropdown">
                                        <a href="#">Business</a>

                                        <ul class="dropdown-menu">
                                            <li><a href="#">US Business</a></li>
                                            <li><a href="#">Middle East Business</a></li>
                                            <li><a href="#">Europe Business</a></li>
                                        </ul>
                                    </li>

                                    <li class="dropdown">
                                        <a href="#">Education</a>

                                        <ul class="dropdown-menu">
                                            <li><a href="#">Africa Child Education</a></li>
                                            <li><a href="#">Bangladeshi Education</a></li>
                                            <li><a href="#">Middle East Education</a></li>
                                        </ul>
                                    </li>

                                    <li class="dropdown">
                                        <a href="#">Humanities</a>

                                        <ul class="dropdown-menu">
                                            <li><a href="#">Help For Syrian Refugees</a></li>
                                            <li><a href="#">Help For Afgan Children</a></li>
                                            <li><a href="#">Help For African Children</a></li>
                                        </ul>
                                    </li>

                                    <li class="dropdown">
                                        <a href="#">Movies</a>

                                        <ul class="dropdown-menu">
                                            <li><a href="#">Hollywood</a></li>
                                            <li><a href="#">Dollywood</a></li>
                                        </ul>
                                    </li>

                                    <li class="dropdown">
                                        <a href="#">Weather</a>

                                        <ul class="dropdown-menu">
                                            <li><a href="#">North Pole</a></li>
                                            <li><a href="#">South Pole</a></li>
                                        </ul>
                                    </li>

                                    <li class="dropdown">
                                        <a href="#">Health</a>

                                        <ul class="dropdown-menu">
                                            <li><a href="#">Africa Poor Child Healt</a></li>
                                            <li><a href="#">Fitness and Health</a></li>
                                        </ul>
                                    </li>

                                    <li class="dropdown">
                                        <a href="#">Animals</a>

                                        <ul class="dropdown-menu">
                                            <li><a href="#">African Animals</a></li>
                                            <li><a href="#">Australian Animals</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Pages<i class="fa flm fa-angle-down"></i></a>

                                <ul class="dropdown-menu">
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Blog<i class="fa flm fa-angle-right"></i></a>

                                        <ul class="dropdown-menu">
                                            <li><a href="blog-v1.html">Blog 1</a></li>
                                            <li><a href="blog-v2.html">Blog 2</a></li>
                                            <li><a href="blog-v3.html">Blog 3</a></li>
                                            <li><a href="archives-v1.html">Archives 1</a></li>
                                            <li><a href="archives-v2.html">Archives 2</a></li>
                                            <li><a href="archives-v3.html">Archives 3</a></li>
                                            <li><a href="tag.html">Tag</a></li>
                                            <li><a href="search.html">Search</a></li>
                                            <li><a href="author.html">Author</a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">News Single<i class="fa flm fa-angle-right"></i></a>

                                        <ul class="dropdown-menu">
                                            <li><a href="news-single-v1.html">News Single 1</a></li>
                                            <li><a href="news-single-v2.html">News Single 2</a></li>
                                            <li><a href="news-single-v3.html">News Single 3</a></li>
                                            <li><a href="news-single-v4.html">News Single 4</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="about.html">About</a></li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Widgets<i class="fa flm fa-angle-right"></i></a>

                                        <ul class="dropdown-menu">
                                            <li><a href="ad-widgets.html">Ad Widgets</a></li>
                                            <li><a href="social-widgets.html">Social Widgets</a></li>
                                            <li><a href="subscribe-widgets.html">Subscirbe Widgets</a></li>
                                            <li><a href="post-widgets.html">Post Widgets</a></li>
                                            <li><a href="poll-widgets.html">Poll Widgets</a></li>
                                            <li><a href="search-widgets.html">Search Widgets</a></li>
                                            <li><a href="links-widgets.html">Links Widgets</a></li>
                                            <li><a href="tag-widgets.html">Tag Widgets</a></li>
                                            <li><a href="profile-widgets.html">Profile Widgets</a></li>
                                            <li><a href="cart-widgets.html">Cart Widgets</a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Contributors<i class="fa flm fa-angle-right"></i></a>

                                        <ul class="dropdown-menu">
                                            <li><a href="contributors.html">Contributors 1</a></li>
                                            <li><a href="contributors-2.html">Contributors 2</a></li>
                                            <li><a href="contributors-3.html">Contributors 3</a></li>
                                            <li><a href="contributors-4.html">Contributors 4</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="pricing.html">Pricing</a></li>
                                    <li><a href="login.html">Login</a></li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Shop<i class="fa flm fa-angle-right"></i></a>

                                        <ul class="dropdown-menu">
                                            <li><a href="shop-1.html">Shop 1</a></li>
                                            <li><a href="shop-2.html">Shop 2</a></li>
                                            <li><a href="shop-details.html">Shop Details</a></li>
                                            <li><a href="cart.html">Cart</a></li>
                                            <li><a href="checkout.html">Checkout</a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Contact<i class="fa flm fa-angle-right"></i></a>

                                        <ul class="dropdown-menu">
                                            <li><a href="contact.html">Contact 1</a></li>
                                            <li><a href="contact-2.html">Contact 2</a></li>
                                        </ul>
                                    </li>
                                    <li><a href="coming-soon.html">Coming Soon</a></li>
                                    <li><a href="404.html">404</a></li>
                                </ul>
                            </li> 

                        </ul>
                    </div> -->
                        
                    
                </div>
            </div>
            <!-- Header Navbar End -->
        </header>
        <!-- Header Section End -->
