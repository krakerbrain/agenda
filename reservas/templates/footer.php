</div> <!-- Cierre de .container -->
<script>
    const baseUrl = "<?php echo $baseUrl; ?>";
    const company_days_available = <?php echo $calendar_days_available ?>;
</script>
<script src="<?php echo $baseUrl; ?>assets/vendors/js/flatpickr/flatpickr.min.js"></script>
<script src="<?php echo $baseUrl; ?>assets/vendors/js/flatpickr/es.js"></script>
<script src="<?php echo $baseUrl; ?>assets/vendors/js/bootstrap/bootstrap.bundle.min.js"></script>
<script src="<?php echo $baseUrl; ?>assets/js/form_reserva/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>