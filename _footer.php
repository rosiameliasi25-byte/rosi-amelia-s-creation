<?php // _footer.php ?>
</div><!-- /page-wrap -->

<footer class="gh-footer">
  <div class="gh-footer-inner">
    <div class="gh-footer-brand">
      <svg width="14" height="14" fill="none" viewBox="0 0 24 24">
        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
      </svg>
      &copy; <?php echo date('Y'); ?> RosiMarket Hub
    </div>
    <div class="gh-footer-links">
      <a href="#">Privasi</a>
      <a href="#">Ketentuan</a>
      <a href="#">Bantuan</a>
      <a href="#">Tentang</a>
    </div>
    <div class="gh-footer-status">Semua sistem berjalan normal</div>
  </div>
</footer>

<script>
// Restore theme on every page load (before first paint via inline script in _navbar)
// This re-applies in case the script above hasn't run yet
(function(){
  var s = localStorage.getItem('rosimarket-theme') || 'dark';
  document.documentElement.setAttribute('data-theme', s);
})();
</script>
</body>
</html>