document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('editModal');

    if (editModal) {
        function openEditModal(id, username, email, role) {
            document.getElementById('editUserId').value = id;
            document.getElementById('editUsername').value = username;
            document.getElementById('editEmail').value = email;
            document.getElementById('editRole').value = role;
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
