document.addEventListener('DOMContentLoaded', function() {
    const addModal = document.getElementById('addModal');
    const modalForm = document.querySelector('.modal-form');

    if (addModal && modalForm) {
        function openAddModal() {
            addModal.classList.add('active');
        }

        function closeAddModal() {
            addModal.classList.remove('active');
        }

        window.openAddModal = openAddModal;
        window.closeAddModal = closeAddModal;

        window.deleteRecord = function(recordId, button) {
            if (!confirm('Are you sure you want to delete this medical record?')) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', recordId);

            fetch('process/medical_records_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const row = button.closest('tr');
                    row.remove();
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting record. Please try again.');
            });
        };

        modalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('process/medical_records_ajax.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    closeAddModal();
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error adding record. Please try again.');
            });
        });

        addModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAddModal();
            }
        });
    }
});
