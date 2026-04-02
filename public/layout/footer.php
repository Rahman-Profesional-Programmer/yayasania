    <!-- Footer Section Start -->
    <footer class="footer--section">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="footer--widget">
                        <div class="footer--logo">
                            <a href="<?= PUBLIC_URL ?>home.php">
                                <img src="<?= PUBLIC_ASSETS ?>img/logoyysn.png" alt="Logo Yayasan IA" width="80px">
                            </a>
                        </div>
                        <p>Website Resmi Yayasan Ihsanul Amal Alabio</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer--bottom">
            <div class="container">
                <div class="text-center">
                    <p>&copy; <?= date('Y') ?> Yayasan Ihsanul Amal Alabio. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>
    <!-- Footer Section End -->

</div><!-- .wrapper -->

<!-- jQuery -->
<script src="<?= PUBLIC_ASSETS ?>js/jquery-3.2.1.min.js"></script>
<!-- Bootstrap JS -->
<script src="<?= PUBLIC_ASSETS ?>js/bootstrap.min.js"></script>
<!-- StickyJS Plugin -->
<script src="<?= PUBLIC_ASSETS ?>js/jquery.sticky.min.js"></script>
<!-- HoverIntent Plugin -->
<script src="<?= PUBLIC_ASSETS ?>js/jquery.hoverIntent.min.js"></script>
<!-- Marquee Plugin -->
<script src="<?= PUBLIC_ASSETS ?>js/jquery.marquee.min.js"></script>
<!-- Validation Plugin -->
<script src="<?= PUBLIC_ASSETS ?>js/jquery.validate.min.js"></script>
<!-- Isotope Plugin -->
<script src="<?= PUBLIC_ASSETS ?>js/isotope.min.js"></script>
<!-- Resize Sensor Plugin -->
<script src="<?= PUBLIC_ASSETS ?>js/resizesensor.min.js"></script>
<!-- Sticky Sidebar Plugin -->
<script src="<?= PUBLIC_ASSETS ?>js/theia-sticky-sidebar.min.js"></script>
<!-- Zoom Plugin -->
<script src="<?= PUBLIC_ASSETS ?>js/jquery.zoom.min.js"></script>
<!-- Bar Rating Plugin -->
<script src="<?= PUBLIC_ASSETS ?>js/jquery.barrating.min.js"></script>
<!-- Countdown Plugin -->
<script src="<?= PUBLIC_ASSETS ?>js/jquery.countdown.min.js"></script>
<!-- RetinaJS Plugin -->
<script src="<?= PUBLIC_ASSETS ?>js/retina.min.js"></script>
<!-- Main JS -->
<script src="<?= PUBLIC_ASSETS ?>js/main.js"></script>
<!-- SweetAlert2 -->
<script src="<?= PUBLIC_ASSETS ?>plugins/sweetalert2/js/sweetalert2.min.js"></script>
<!-- Select2 -->
<script src="<?= PUBLIC_ASSETS ?>plugins/select2/js/select2.min.js"></script>
<script>
window.addEventListener('load', function () {
    var preloader = document.getElementById('preloader');

    if (!preloader) {
        return;
    }

    if (window.jQuery) {
        window.jQuery(preloader).delay(100).fadeOut(250);
    } else {
        preloader.style.display = 'none';
    }
});

setTimeout(function () {
    var preloader = document.getElementById('preloader');

    if (preloader) {
        preloader.style.display = 'none';
    }
}, 3000);
</script>

</body>
</html>
