// profile.js
document.addEventListener('DOMContentLoaded', function () {
    const editProfileBtn = document.querySelector('.edit-button');
    const modal = document.getElementById('editProfileModal');
    const form = document.getElementById('profileForm');
    const fileInput = form?.querySelector('input[type="file"]');
    const preview = form?.querySelector('.file-preview');

    // Show modal
    editProfileBtn?.addEventListener('click', () => {
        modal.classList.add('show');
        document.body.style.overflow = 'hidden'; // Disable scrolling while modal is open
    });

    // Close modal
    document.querySelectorAll('.modal-close').forEach((btn) => {
        btn.addEventListener('click', () => {
            modal.classList.remove('show');
            document.body.style.overflow = ''; // Enable scrolling
        });
    });

    // File preview
    fileInput?.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                preview.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" 
                         style="max-width: 200px; border-radius: 8px;">
                `;
            };
            reader.readAsDataURL(file);
        } else {
            preview.innerHTML = `<p>No file chosen</p>`;
        }
    });

    // Form submit
    form?.addEventListener('submit', async (e) => {
        e.preventDefault();

        try {
            // Show loading spinner
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            submitButton.innerHTML = 'Saving...';

            const formData = new FormData(form);
            const response = await fetch(`${baseUrl}/profile/update`, {
                method: 'POST',
                body: formData,
            });

            const data = await response.json();

            if (data.success) {
                showToast('Profile updated successfully', 'success');

                // Update profile picture immediately without refreshing
                const profilePicture = document.querySelector('.profile-picture');
                if (profilePicture) {
                    profilePicture.src = data.profile_picture;
                }

                // Close modal and reset form
                modal.classList.remove('show');
                form.reset();
                preview.innerHTML = '';
            } else {
                throw new Error(data.message || 'Failed to update profile');
            }
        } catch (error) {
            showToast(error.message, 'error');
        } finally {
            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = false;
            submitButton.innerHTML = 'Save';
        }
    });

    // Toast notification
    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast ${type}`;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('visible');
        }, 100);

        setTimeout(() => {
            toast.classList.remove('visible');
            toast.addEventListener('transitionend', () => toast.remove());
        }, 3000);
    }
});
