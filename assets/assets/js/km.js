function filterLinks() {
    const searchInput = document.getElementById('search');
    const filter = searchInput.value.toLowerCase();
    const navLinks = document.querySelectorAll('.nav-links a');

    navLinks.forEach(link => {
        const text = link.textContent.toLowerCase();
        if (text.includes(filter)) {
            link.style.display = 'inline-flex';
        } else {
            link.style.display = 'none';
        }
    });
}

function initSlider() {
    const slides = document.querySelectorAll('.bg-slider .slide');
    if (slides.length <= 1) return;

    let currentSlide = 0;

    function nextSlide() {
        const previousSlide = slides[currentSlide];
        previousSlide.classList.remove('active');
        previousSlide.classList.add('exit');

        currentSlide = (currentSlide + 1) % slides.length;
        const incomingSlide = slides[currentSlide];
        incomingSlide.classList.remove('exit');
        incomingSlide.classList.add('active');

        setTimeout(() => {
            previousSlide.classList.remove('exit');
        }, 1200);
    }

    setInterval(nextSlide, 4000);
}

function initGetStartedButton() {
    const getStartedBtn = document.getElementById('get-started-btn');
    if (getStartedBtn) {
        getStartedBtn.addEventListener('click', function() {
            const landingSection = document.getElementById('landing-section');
            const loginForm = document.getElementById('login-form');
            if (landingSection) landingSection.style.display = 'none';
            if (loginForm) loginForm.style.display = 'block';
        });
    }
}

function togglePatientFields() {
    const role = document.getElementById('role');
    const patientFields = document.getElementById('patient-fields');
    const staffCodeField = document.getElementById('staff-code-field');
    const staffCodeInput = document.getElementById('registration_code');

    if (!role) return;

    if (patientFields) {
        const patientInputs = patientFields.querySelectorAll('input, select');

        if (role.value === 'patient') {
            patientFields.style.display = 'block';
            patientInputs.forEach(input => input.required = true);
        } else {
            patientFields.style.display = 'none';
            patientInputs.forEach(input => input.required = false);
        }
    }

    if (staffCodeField && staffCodeInput) {
        if (role.value === 'staff') {
            staffCodeField.style.display = 'block';
            staffCodeInput.required = true;
        } else {
            staffCodeField.style.display = 'none';
            staffCodeInput.required = false;
            staffCodeInput.value = '';
        }
    }
}

function initPatientFieldsToggle() {
    const roleSelect = document.getElementById('role');
    if (roleSelect) {
        roleSelect.addEventListener('change', togglePatientFields);
        togglePatientFields();
    }
}

function initSecurityQuestionFetch() {
    const usernameInput = document.getElementById('username');
    const questionDisplay = document.getElementById('security_question_display');
    const questionContainer = document.getElementById('security-question-container');

    if (usernameInput && questionDisplay) {
        usernameInput.addEventListener('blur', function() {
            const username = this.value.trim();
            if (username.length > 0) {
                fetch('process/get_security_question.php?username=' + encodeURIComponent(username))
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            questionDisplay.value = data.security_question;
                            if (questionContainer) {
                                questionContainer.style.display = 'block';
                            }
                        } else {
                            questionDisplay.value = '';
                            if (questionContainer) {
                                questionContainer.style.display = 'none';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        questionDisplay.value = '';
                        if (questionContainer) {
                            questionContainer.style.display = 'none';
                        }
                    });
            }
        });
    }
}

function initStatsAnimation() {
    const stats = document.querySelectorAll('.stat-number');
    if (stats.length === 0) return;

    const animateStats = () => {
        stats.forEach(stat => {
            const target = parseInt(stat.getAttribute('data-target'));
            const increment = target / 50;
            let current = 0;

            const updateStat = () => {
                if (current < target) {
                    current += increment;
                    stat.textContent = Math.ceil(current);
                    requestAnimationFrame(updateStat);
                } else {
                    stat.textContent = target;
                }
            };
            updateStat();
        });
    };

    const statsSection = document.querySelector('.stats-section');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateStats();
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    if (statsSection) {
        observer.observe(statsSection);
    }
}

function openStockModal(medicineId, medicineName, type) {
    const modal = document.getElementById('stockModal');
    const modalMedicineId = document.getElementById('modalMedicineId');
    const modalMedicineName = document.getElementById('modalMedicineName');
    const modalAction = document.getElementById('modalAction');
    const modalTitle = document.getElementById('modalTitle');

    if (modal && modalMedicineId && modalMedicineName && modalAction && modalTitle) {
        modal.style.display = 'block';
        modalMedicineId.value = medicineId;
        modalMedicineName.value = medicineName;
        modalAction.value = 'stock_' + type;
        modalTitle.textContent = type === 'in' ? 'Stock In' : 'Stock Out';
    }
}

function closeStockModal() {
    const modal = document.getElementById('stockModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function updateQuickAction() {
    const typeSelect = document.querySelector('select[name="type"]');
    const quickAction = document.getElementById('quickAction');
    if (typeSelect && quickAction) {
        quickAction.value = 'stock_' + typeSelect.value;
    }
}

function initStockModal() {
    window.onclick = function(event) {
        const modal = document.getElementById('stockModal');
        if (event.target === modal) {
            closeStockModal();
        }
    };

    updateQuickAction();

    const typeSelect = document.querySelector('select[name="type"]');
    if (typeSelect) {
        typeSelect.addEventListener('change', updateQuickAction);
    }
}

function toggleUserMenu() {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown) {
        dropdown.classList.toggle('active');
    }
}

function initUserDropdown() {
    const userProfile = document.querySelector('.nav-user-profile');
    const dropdown = document.getElementById('userDropdown');

    if (userProfile && dropdown) {
        userProfile.addEventListener('click', function(event) {
            event.stopPropagation();
            toggleUserMenu();
        });

        document.addEventListener('click', function(event) {
            if (!userProfile.contains(event.target)) {
                dropdown.classList.remove('active');
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.bg-slider')) {
        initSlider();
    }

    if (document.getElementById('get-started-btn')) {
        initGetStartedButton();
    }

    if (document.getElementById('role')) {
        initPatientFieldsToggle();
    }

    if (document.getElementById('username') && document.getElementById('security_question_display')) {
        initSecurityQuestionFetch();
    }

    if (document.querySelector('.stat-number')) {
        initStatsAnimation();
    }

    if (document.getElementById('stockModal')) {
        initStockModal();
    }

    if (document.querySelector('.nav-user-profile')) {
        initUserDropdown();
    }
});
