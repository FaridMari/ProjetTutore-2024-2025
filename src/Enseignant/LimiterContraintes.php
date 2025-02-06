<script>
    function limiterContraintes() {
        let checkboxes = document.querySelectorAll('input[type="checkbox"]');
        let checkedCount = 0;

        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                checkedCount++;
            }
        });

        if (checkedCount > 4) {
            alert("Vous ne pouvez sélectionner que 4 contraintes au maximum.");

            // Désactive la dernière case cochée
            event.target.checked = false;
        }
    }
</script>
