</div> <!-- Cierre de .container -->
<script>
    const baseUrl = "<?php echo $baseUrl; ?>";
    const company_days_available = <?php echo json_encode($company['calendar_days_available']); ?>;
</script>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
</script>
<script src="<?php echo $baseUrl; ?>assets/js/form_reserva/index.js?v=<?php echo time(); ?>"></script>
</body>

</html>