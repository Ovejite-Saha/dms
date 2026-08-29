</div><!-- /.dms-body -->
<footer class="dms-footer text-center">
    <div class="container">
        <p class="mb-0">&copy; <?= date('Y') ?> <strong>PWD Survey Division.</strong> All rights reserved | Powered By Ⓟ <a target="_blank" href="https://www.youtube.com/c/FireONBD">FireON</a></p>
    </div>
</footer>
<script>
(function() {
    const TIMEOUT = 15 * 60 * 1000; // 15 minutes in milliseconds
    let timer;

    function resetTimer() {
        clearTimeout(timer);
        timer = setTimeout(function() {
            // Redirect to login page
            window.location.href = '<?= url_path("../index.php") ?>?timeout=1';
        }, TIMEOUT);
    }

    // Reset timer on any user activity
    ['mousemove', 'mousedown', 'keypress', 'scroll', 'touchstart', 'click'].forEach(function(evt) {
        document.addEventListener(evt, resetTimer, true);
    });

    // Start the timer
    resetTimer();
})();
</script>
<script src="<?= $base ?>/assets/js/bootstrap.bundle.min.js"></script>
<script src="<?= $base ?>/assets/js/main.js"></script>
</body>
</html>
