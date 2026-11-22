document.addEventListener('DOMContentLoaded', function() {
    const addModal = document.getElementById('addModal');
    const editModal = document.getElementById('editModal');

    if (addModal) {
        function openAddModal() {
            addModal.classList.add('active');
        }

        function closeAddModal() {
            addModal.classList.remove('active');
        }

        window.openAddModal = openAddModal;
        window.closeAddModal = closeAddModal;

        addModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddModal();
            }
        });
    }

    if (editModal) {
        function openEditModal(id, medicineName, genericName, category, quantity, unit, expiryDate, price, manufacturer, supplier, inventor) {
            document.getElementById('editMedicineId').value = id;
            document.getElementById('editMedicineName').value = medicineName;
            document.getElementById('editGenericName').value = genericName;
            document.getElementById('editCategory').value = category;
            document.getElementById('editQuantity').value = quantity;
            document.getElementById('editUnit').value = unit;
            document.getElementById('editExpiryDate').value = expiryDate;
            document.getElementById('editPrice').value = price;
            document.getElementById('editManufacturer').value = manufacturer;
            document.getElementById('editSupplier').value = supplier;
            document.getElementById('editInventor').value = inventor;
            editModal.classList.add('active');
        }

        function closeEditModal() {
            editModal.classList.remove('active');
        }

        window.openEditModal = openEditModal;
        window.closeEditModal = closeEditModal;

        editModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });
    }
});
