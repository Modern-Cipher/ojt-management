document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('searchInput');
    const cardContainer = document.getElementById('cardContainer');
    const timelineContainer = document.querySelector('.timeline');
    let trainerData = [];

    function capitalizeWords(str) {
        return str.toLowerCase().split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ');
    }

    function loadTrainers() {
        fetch('fetch_trainers.php')
            .then(response => response.json())
            .then(data => {
                trainerData = data;
                displayTrainers(trainerData);
            })
            .catch(error => console.error('Error fetching trainers:', error));
    }

    function displayTrainers(trainers) {
        cardContainer.innerHTML = '';
        trainers.forEach((trainer) => {
            const card = document.createElement('div');
            card.classList.add('custom-card', 'shadow-sm', 'p-3', 'mb-3');
            card.setAttribute('data-id', trainer.users_id);

            card.innerHTML = `
                <div class="left-section" >
                    <img src="${trainer.image_profile}" alt="Profile" class="profile-image">
                    <div class="text-info">
                        <p class="establishment mb-0"><strong>${capitalizeWords(trainer.hte_name)}</strong></p>
                        <p class="fullname mb-0">${capitalizeWords(trainer.fname)} ${capitalizeWords(trainer.lname)}</p>
                    </div>
                </div>
                <div class="right-section text-end">
                    <p class="role mb-0"><strong>${capitalizeWords(trainer.role)}</strong></p>
                    <p class="designation mb-0">${capitalizeWords(trainer.designation)}</p>
                </div>
            `;

            // On click → highlight + load timeline
            card.addEventListener('click', () => {
                // Remove previous highlight
                cardContainer.querySelectorAll('.custom-card').forEach(c => {
                    c.classList.remove('active');
                });
                // Add highlight to selected
                card.classList.add('active');

                loadTimeline(trainer.users_id);
                showToast(`${capitalizeWords(trainer.fname)} ${capitalizeWords(trainer.lname)} selected`, 'success');
            });

            cardContainer.appendChild(card);
        });
    }

    function loadTimeline(trainerId) {
        fetch(`fetch_filename.php?trainer_id=${trainerId}`)
            .then(response => response.json())
            .then(data => displayTimeline(data))
            .catch(error => console.error('Timeline Error:', error));
    }

    function displayTimeline(files) {
        timelineContainer.innerHTML = '';
        if (files.length === 0) {
            timelineContainer.innerHTML = '<p class="text-muted">No documents available.</p>';
            return;
        }

        files.forEach(file => {
            const statusClass = file.upload_status === 'accepted' ? 'success' : 'danger';
            const filename = file.filename || '';
            const timestamp = file.updated_on || '';
            const filepath = file.filepath || '';

            const item = document.createElement('div');
            item.classList.add('timeline-item', 'mb-3');

            item.innerHTML = `
                <div class="timeline-left">
                    <div class="timeline-dot ${statusClass}">
                        <i class="fa-solid ${statusClass === 'success' ? 'fa-check' : 'fa-xmark'}"></i>
                    </div>
                    <div class="timeline-line"></div>
                </div>
                <div class="file-card p-3 shadow-sm" >
                    <div class="file-card-header d-flex justify-content-between align-items-center mb-2">
                        <div class="d-flex align-items-center">
                            <span class="filename">${filename}</span>
                        </div>
                        <small class="timestamp text-muted">${timestamp}</small>
                    </div>
                    <div class="file-card-actions mb-2 text-end">
                        ${filepath ? `
                        <a href="${filepath}" target="_blank"><i class="fa-solid fa-eye me-2"></i></a>
                        <a href="${filepath}" download><i class="fa-solid fa-download me-2"></i></a>` : ''}
                        <i class="fa-solid fa-chevron-down toggle-comments"></i>
                    </div>
                </div>
            `;
            timelineContainer.appendChild(item);
        });
    }

    searchInput.addEventListener('input', () => {
        const keyword = searchInput.value.toLowerCase();
        const filtered = trainerData.filter(trainer =>
            `${trainer.fname} ${trainer.lname}`.toLowerCase().includes(keyword) ||
            trainer.hte_name.toLowerCase().includes(keyword) ||
            trainer.designation.toLowerCase().includes(keyword)
        );
        displayTrainers(filtered);
    });

    loadTrainers();

    /** GLOBAL TOAST **/
    window.showToast = function (message, type = 'success') {
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
            document.body.appendChild(toastContainer);
        }

        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        toastContainer.appendChild(toast);

        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
});
