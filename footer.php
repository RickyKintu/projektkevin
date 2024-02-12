</div>
<footer class="site-footer">
    <div class="footer-content">
        <a href="<?php echo $_SITE['url'] ?>">Home</a> |
        <a href="articles/">Articles</a>
        <p>&copy; <?php echo date("Y"); ?> <?php echo $_SITE['name']; ?>.com</p>
        <p>tags, tags, tags</p>
        <div class="footer-logo">
            <img src="images/logo.png" alt="Logo" />
        </div>
    </div>
</footer>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const intervalTime = 700; //Time each thumbnail is shown

        document.querySelectorAll('.slideshow').forEach(img => {
            img.onmouseover = function() {
                const thumbnails = img.getAttribute('data-thumbnails').split(',');
                let currentIndex = 0;

                img.src = thumbnails[currentIndex]; //Set initial src

                const interval = setInterval(() => {
                    currentIndex = (currentIndex + 1) % thumbnails.length;
                    img.src = thumbnails[currentIndex];
                }, intervalTime);

                img.onmouseout = function() {
                    clearInterval(interval);
                    img.src = thumbnails[0]; //Reset to first thumbnail
                };
            };
        });
    });
</script>
</body>

</html>