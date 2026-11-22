document.addEventListener('DOMContentLoaded', function() {
    const patientProfileModal = document.getElementById('patientProfileModal');
    const qrCodeModal = document.getElementById('qrCodeModal');
    const profilePictureInput = document.getElementById('profilePictureInput');
    const profilePicture = document.getElementById('profilePicture');
    const profileInitials = document.getElementById('profileInitials');
    const profileWrapper = document.querySelector('.profile-picture-wrapper');

    // Set random color for initials if no profile picture
    if (profileInitials && !profilePicture) {
        const colors = [
            'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
            'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
            'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)',
            'linear-gradient(135deg, #fa709a 0%, #fee140 100%)',
            'linear-gradient(135deg, #a8edea 0%, #fed6e3 100%)',
            'linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%)',
            'linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%)'
        ];
        const randomColor = colors[Math.floor(Math.random() * colors.length)];
        profileInitials.style.background = randomColor;
    }

    // Handle file upload
    if (profilePictureInput) {
        profilePictureInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('profile_picture', file);

            // Show loading state
            profileWrapper.style.opacity = '0.5';

            fetch('process/upload_profile_picture.php', {
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
                profileWrapper.style.opacity = '1';

                if (data.success) {
                    // Update the profile picture display
                    if (profileInitials) {
                        profileInitials.style.display = 'none';
                    }

                    if (profilePicture) {
                        profilePicture.src = 'uploads/profile_pictures/' + data.filename + '?t=' + Date.now();
                    } else {
                        // Create new image element if it doesn't exist
                        const newImg = document.createElement('img');
                        newImg.src = 'uploads/profile_pictures/' + data.filename + '?t=' + Date.now();
                        newImg.alt = 'Profile Picture';
                        newImg.className = 'profile-picture';
                        newImg.id = 'profilePicture';
                        profileWrapper.insertBefore(newImg, profileWrapper.firstChild);
                    }

                    alert('Profile picture updated successfully!');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                profileWrapper.style.opacity = '1';
                console.error('Error:', error);
                alert('Error uploading profile picture. Please try again.');
            });
        });
    }

    if (patientProfileModal) {
        function dashboard_openPatientProfile(patientId) {
            console.log('openPatientProfile called with ID:', patientId);

            fetch('process/patients_ajax.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=get_patient&id=' + patientId
            })
            .then(response => {
                console.log('Response received');
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);
                if (data.success) {
                    const patient = data.patient;
                    const content = document.getElementById('patientProfileContent');

                    content.innerHTML = `
                        <div class="profile-modal-header">
                            ${patient.profile_picture ?
                                `<img src="uploads/profile_pictures/${patient.profile_picture}" alt="Profile" class="profile-modal-picture">` :
                                `<div class="profile-modal-initials">${patient.first_name.charAt(0).toUpperCase()}</div>`
                            }
                            <div class="profile-modal-info">
                                <h3>${patient.first_name} ${patient.last_name}</h3>
                                <div class="profile-modal-badges">
                                    <span class="patient-gender">${patient.gender}</span>
                                    ${patient.age ? `<span class="patient-age">${patient.age} years old</span>` : ''}
                                </div>
                            </div>
                        </div>

                        <div class="profile-modal-details">
                            <div class="profile-detail-section">
                                <h4>Personal Information</h4>
                                <div class="detail-row">
                                    <span class="detail-label">Patient ID:</span>
                                    <span class="detail-value">#${patient.id}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">First Name:</span>
                                    <span class="detail-value">${patient.first_name}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Last Name:</span>
                                    <span class="detail-value">${patient.last_name}</span>
                                </div>
                                ${patient.middle_name ? `
                                <div class="detail-row">
                                    <span class="detail-label">Middle Name:</span>
                                    <span class="detail-value">${patient.middle_name}</span>
                                </div>
                                ` : ''}
                                <div class="detail-row">
                                    <span class="detail-label">Birth Date:</span>
                                    <span class="detail-value">${patient.birth_date}</span>
                                </div>
                                ${patient.age ? `
                                <div class="detail-row">
                                    <span class="detail-label">Age:</span>
                                    <span class="detail-value">${patient.age} years old</span>
                                </div>
                                ` : ''}
                                <div class="detail-row">
                                    <span class="detail-label">Gender:</span>
                                    <span class="detail-value">${patient.gender}</span>
                                </div>
                            </div>

                            <div class="profile-detail-section">
                                <h4>Contact Information</h4>
                                <div class="detail-row">
                                    <span class="detail-label">Contact Number:</span>
                                    <span class="detail-value">${patient.contact_number}</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Address:</span>
                                    <span class="detail-value">${patient.address}</span>
                                </div>
                            </div>

                            ${patient.guardian_name ? `
                            <div class="profile-detail-section">
                                <h4>Guardian Information</h4>
                                <div class="detail-row">
                                    <span class="detail-label">Guardian Name:</span>
                                    <span class="detail-value">${patient.guardian_name}</span>
                                </div>
                            </div>
                            ` : ''}

                            ${patient.qr_code ? `
                            <div class="profile-detail-section qr-section">
                                <h4>QR Code</h4>
                                <div class="qr-display">
                                    <img src="uploads/qr_codes/${patient.qr_code}" alt="QR Code">
                                </div>
                            </div>
                            ` : ''}
                        </div>
                    `;

                    console.log('Modal element:', patientProfileModal);
                    patientProfileModal.classList.add('active');
                    console.log('Modal opened successfully');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading patient profile. Please try again.');
            });
        }

        function dashboard_closePatientProfileModal() {
            patientProfileModal.classList.remove('active');
        }

        function dashboard_openProfile(patientId, userId) {
            console.log('openProfile called with:', patientId, userId);
            dashboard_openPatientProfile(patientId);
        }

        window.openPatientProfile = dashboard_openPatientProfile;
        window.closePatientProfileModal = dashboard_closePatientProfileModal;
        window.openProfile = dashboard_openProfile;
    }

    if (qrCodeModal) {
        function dashboard_viewQRCode(patientId, qrFilename) {
            const qrUrl = 'uploads/qr_codes/' + qrFilename;
            document.getElementById('qrCodeImage').src = qrUrl;
            qrCodeModal.classList.add('active');
        }

        function dashboard_closeQRModal() {
            qrCodeModal.classList.remove('active');
        }

        function dashboard_downloadQRCode() {
            const qrImage = document.getElementById('qrCodeImage');
            const link = document.createElement('a');
            link.href = qrImage.src;
            link.download = 'patient_qr_code.png';
            link.click();
        }

        window.viewQRCode = dashboard_viewQRCode;
        window.closeQRModal = dashboard_closeQRModal;
        window.downloadQRCode = dashboard_downloadQRCode;
    }

    function dashboard_generateQRCode(patientId) {
        if (confirm('Generate QR code for this patient?')) {
            const form = new FormData();
            form.append('action', 'generate_qr');
            form.append('id', patientId);

            fetch('process/patients_ajax.php', {
                method: 'POST',
                body: form
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error generating QR code. Please try again.');
            });
        }
    }

    window.generateQRCode = dashboard_generateQRCode;

    // Mobile menu toggle functionality
    function toggleMobileMenu() {
        const sidebar = document.querySelector('aside');
        const toggleBtn = document.querySelector('.mobile-menu-toggle');

        if (sidebar) {
            sidebar.classList.toggle('mobile-open');

            // Update button appearance
            if (toggleBtn) {
                if (sidebar.classList.contains('mobile-open')) {
                    toggleBtn.style.background = '#dc2626';
                } else {
                    toggleBtn.style.background = '#0f766e';
                }
            }
        }
    }

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        const sidebar = document.querySelector('aside');
        const toggleBtn = document.querySelector('.mobile-menu-toggle');

        if (sidebar && sidebar.classList.contains('mobile-open')) {
            if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
                if (toggleBtn) {
                    toggleBtn.style.background = '#0f766e';
                }
            }
        }
    });

    // Close mobile menu on window resize if switching to desktop
    window.addEventListener('resize', function() {
        const sidebar = document.querySelector('aside');
        const toggleBtn = document.querySelector('.mobile-menu-toggle');

        if (window.innerWidth > 767 && sidebar) {
            sidebar.classList.remove('mobile-open');
            if (toggleBtn) {
                toggleBtn.style.background = '#0f766e';
            }
        }
    });

    window.toggleMobileMenu = toggleMobileMenu;
});
