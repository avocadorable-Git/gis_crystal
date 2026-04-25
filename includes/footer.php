</main>
</div><!-- end layout -->

<script>
// Bootstrap-style validation
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.needs-validation').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
});
</script>
</body>
</html>