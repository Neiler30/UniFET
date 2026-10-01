<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if(isset($_SESSION['alerta_html'])): ?>
    <script>
        <?= $_SESSION['alerta_html'] ?>
    </script>
<?php 
    unset($_SESSION['alerta_html']);
endif; 
?>

<script src="<?= defined('BASE_URL') ? BASE_URL : '' ?>/assets/js/app.js"></script>

</body>
</html>
