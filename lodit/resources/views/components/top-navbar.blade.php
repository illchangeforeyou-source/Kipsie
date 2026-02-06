<!-- Top Profile Navigation Bar -->
<div class="top-navbar">
    <div class="navbar-left">
        <!-- Logo removed - displayed in sidebar instead -->
    </div>

    <div class="navbar-center">
        <h2 class="page-title" id="pageTitle">Dashboard</h2>
    </div>

    <div class="navbar-right">
        <!-- Notification Bell -->
        <div class="navbar-item notification-item">
            <a href="/notifications/list" class="notification-link">
                <i class="bi bi-bell"></i>
                <span class="notification-exclamation" id="notificationExclamation" style="display: none;">!</span>
                <span class="notification-badge-count" id="notificationBadgeCount" style="display: none;">0</span>
                <span class="notification-dot" id="notificationDot" style="display: none;"></span>
            </a>
        </div>

        <!-- Dark/Light Mode Toggle -->
        <div class="navbar-item theme-toggle">
            <button class="theme-btn" onclick="toggleTheme()" title="Toggle Dark/Light Mode">
                <i class="bi bi-moon-fill" id="themeIcon"></i>
            </button>
        </div>

        <!-- Profile Dropdown -->
        <div class="navbar-item profile-item">
            <button class="profile-button" onclick="toggleProfileMenu()">
                <img src="" alt="Profile" class="profile-avatar" id="profileAvatar">
                <span class="profile-name" id="profileName">User</span>
                <i class="bi bi-chevron-down"></i>
            </button>

            <!-- Profile Dropdown Menu -->
            <div class="profile-menu" id="profileMenu">
                <a href="#" onclick="openProfileModal(); return false;" class="profile-menu-item">
                    <i class="bi bi-person-circle"></i>
                    <span>My Profile</span>
                </a>
                <a href="/settings" class="profile-menu-item">
                    <i class="bi bi-gear"></i>
                    <span>Settings</span>
                </a>
                <div class="profile-menu-divider"></div>
                <a href="/logout" class="profile-menu-item logout">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Profile Modal -->
<div class="profile-modal" id="profileModal">
    <div class="profile-modal-content">
        <button class="modal-close" onclick="closeProfileModal()">&times;</button>
        
        <div class="profile-modal-header">
            <h3>My Profile</h3>
        </div>

        <div class="profile-modal-body">
            <!-- Profile Picture Section -->
            <div class="profile-picture-section">
                <div class="profile-picture-container">
                    <img src="" alt="Profile" class="large-profile-pic" id="largeProfilePic">
                    <button class="edit-pic-btn" onclick="openImageCropper()">
                        <i class="bi bi-camera"></i>
                    </button>
                </div>
            </div>

            <!-- User Info Section -->
            <div class="user-info-section">
                <div class="info-item">
                    <label>Name</label>
                    <p id="modalUserName">-</p>
                </div>
                <div class="info-item">
                    <label>Email</label>
                    <p id="modalUserEmail">-</p>
                </div>
                <div class="info-item">
                    <label>Role</label>
                    <p id="modalUserRole">-</p>
                </div>
                <div class="info-item">
                    <label>Member Since</label>
                    <p id="modalUserJoined">-</p>
                </div>
            </div>

            <button class="btn-edit-profile" onclick="openProfileSettings()">
                <i class="bi bi-pencil"></i> Edit Profile
            </button>
        </div>
    </div>
</div>

<!-- Profile Settings Modal -->
<div class="profile-modal" id="profileSettingsModal">
    <div class="profile-modal-content" style="max-width: 500px;">
        <button class="modal-close" onclick="closeProfileSettings()">&times;</button>
        
        <div class="profile-modal-header">
            <h3>Edit Profile</h3>
        </div>

        <div class="profile-modal-body">
            <form id="profileForm" onsubmit="saveProfileChanges(event)">
                <div class="form-group">
                    <label for="editName">Full Name</label>
                    <input type="text" id="editName" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="editEmail">Email</label>
                    <input type="email" id="editEmail" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="editPhone">Phone (Optional)</label>
                    <input type="tel" id="editPhone" class="form-control">
                </div>

                <div class="form-group">
                    <label>Profile Picture</label>
                    <button type="button" class="btn-secondary" onclick="openImageCropper()">
                        <i class="bi bi-upload"></i> Change Picture
                    </button>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeProfileSettings()">Cancel</button>
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Cropper Modal -->
<div class="crop-modal" id="cropModal">
    <div class="crop-modal-content">
        <div class="crop-modal-header">
            <h3>Crop Profile Picture</h3>
            <button class="modal-close" onclick="closeCropModal()">&times;</button>
        </div>

        <div class="crop-modal-body">
            <div class="crop-container">
                <img id="imageToCrop" src="" alt="Image to crop" style="max-width: 100%;">
            </div>
        </div>

        <div class="crop-modal-footer">
            <button type="button" class="btn-cancel" onclick="closeCropModal()">Cancel</button>
            <button type="button" class="btn-save" onclick="saveCroppedImage()">Done</button>
        </div>
    </div>
</div>

<!-- Hidden File Input -->
<input type="file" id="imageInput" accept="image/*" style="display: none;" onchange="loadImageForCrop(event)">

<script>
    // Load user data and update profile
    document.addEventListener('DOMContentLoaded', () => {
        loadUserProfile();
        updateNotificationDot();
        setPageTitle();

        // Close menus when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.profile-item')) {
                closeProfileMenu();
            }
        });

        // Refresh notification badge every 10 seconds for real-time feel
        setInterval(updateNotificationDot, 10000);
    });

    function loadUserProfile() {
        // Try authenticated API first, then fall back to session-based endpoint
        function applyUserData(data) {
            if (!data || !data.user) return;
            const user = data.user;
            const avatar = document.getElementById('profileAvatar');
            const name = document.getElementById('profileName');
            const largeAvatar = document.getElementById('largeProfilePic');
            // Use cache-busting param to ensure updated images show immediately
            const profileUrl = user.profile_picture ? (user.profile_picture + (user.profile_picture.includes('?') ? '&' : '?') + 'v=' + Date.now()) : '/foto/kaister.jpg';
            if (avatar) avatar.src = profileUrl;
            if (name) name.textContent = user.name;
            if (largeAvatar) largeAvatar.src = profileUrl;

            const modalName = document.getElementById('modalUserName');
            const modalEmail = document.getElementById('modalUserEmail');
            const modalRole = document.getElementById('modalUserRole');
            const modalJoined = document.getElementById('modalUserJoined');
            if (modalName) modalName.textContent = user.name;
            if (modalEmail) modalEmail.textContent = user.email || '-';
            if (modalRole) modalRole.textContent = user.role || 'User';
            if (modalJoined) modalJoined.textContent = formatDate(user.created_at);

            // Load form fields
            const editName = document.getElementById('editName');
            const editEmail = document.getElementById('editEmail');
            const editPhone = document.getElementById('editPhone');
            if (editName) editName.value = user.name || '';
            if (editEmail) editEmail.value = user.email || '';
            if (editPhone) editPhone.value = user.phone || '';
        }

        fetch('/api/user-profile-session', { credentials: 'same-origin' })
            .then(r => {
                if (r.ok) return r.json();
                throw new Error('Profile fetch failed');
            })
            .then(data => applyUserData(data))
            .catch(() => {
                // fallback: try again
                fetch('/api/user-profile-session', { credentials: 'same-origin' })
                    .then(r => r.json())
                    .then(data => applyUserData(data))
                    .catch(() => console.log('Could not load user profile'));
            });

        // Load system logo and name from settings
        fetch('/api/app-settings', { credentials: 'same-origin' })
            .then(r => r.json())
            .then(data => {
                if (data.settings) {
                    const logo = data.settings.logo || '/foto/baobei.jpg';
                    const name = data.settings.app_name || 'LODIT';
                    
                    const logoEl = document.getElementById('systemLogo');
                    const nameEl = document.getElementById('systemName');
                    if (logoEl) logoEl.src = logo;
                    if (nameEl) nameEl.textContent = name;
                }
            })
            .catch(() => {
                // Use defaults if API fails
                const logoEl = document.getElementById('systemLogo');
                const nameEl = document.getElementById('systemName');
                if (logoEl) logoEl.src = '/foto/baobei.jpg';
                if (nameEl) nameEl.textContent = 'LODIT';
            });

        // Load theme preference
        const savedTheme = localStorage.getItem('theme') || 'dark';
        applyTheme(savedTheme);
    }

    function toggleTheme() {
        const currentTheme = localStorage.getItem('theme') || 'dark';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        localStorage.setItem('theme', newTheme);
        applyTheme(newTheme);
        // Persist preference for logged-in users
        try {
            fetch('/settings/user-theme', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ theme: newTheme }),
                credentials: 'same-origin'
            }).catch(() => {});
        } catch(e) {}
    }

    function applyTheme(theme) {
        const html = document.documentElement;
        const icon = document.getElementById('themeIcon');
        const body = document.body;
        
        if (theme === 'dark') {
            html.style.colorScheme = 'dark';
            html.classList.remove('light-mode');
            html.classList.add('dark-mode');
            body.classList.remove('light-mode');
            body.classList.add('dark-mode');
            icon.className = 'bi bi-moon-fill';
        } else {
            html.style.colorScheme = 'light';
            html.classList.remove('dark-mode');
            html.classList.add('light-mode');
            body.classList.remove('dark-mode');
            body.classList.add('light-mode');
            icon.className = 'bi bi-sun-fill';
        }
    }

    function updateNotificationDot() {
        fetch('/notifications/list', { credentials: 'same-origin' })
            .then(r => r.json())
            .then(data => {
                const unreadCount = data.unread_count || 0;
                const badgeCount = document.getElementById('notificationBadgeCount');
                const exclamation = document.getElementById('notificationExclamation');
                const sidebarBadge = document.getElementById('notificationBadge');
                const dot = document.getElementById('notificationDot');
                
                if (unreadCount > 0) {
                    // Show exclamation mark
                    exclamation.style.display = 'flex';
                    
                    // Also show count badge in navbar
                    badgeCount.textContent = unreadCount > 99 ? '99+' : unreadCount;
                    badgeCount.style.display = 'flex';
                    
                    // Update sidebar badge
                    sidebarBadge.textContent = unreadCount;
                    sidebarBadge.style.display = 'flex';
                    
                    // Hide the old dot style
                    dot.style.display = 'none';
                } else {
                    exclamation.style.display = 'none';
                    badgeCount.style.display = 'none';
                    sidebarBadge.style.display = 'none';
                    dot.style.display = 'none';
                }
            })
            .catch(() => {});
    }

    function setPageTitle() {
        const path = window.location.pathname;
        const titles = {
            '/admin/dashboard': 'Dashboard',
            '/yao': 'POS System',
            '/medicines': 'Medicines',
            '/medtransactions': 'Transactions',
            '/users': 'Users',
            '/reports': 'Reports',
            '/permissions/manage': 'Permissions',
            '/settings': 'Settings',
            '/notifications/list': 'Notifications'
        };

        for (let [route, title] of Object.entries(titles)) {
            if (path.includes(route)) {
                document.getElementById('pageTitle').textContent = title;
                break;
            }
        }
    }

    function toggleProfileMenu() {
        const menu = document.getElementById('profileMenu');
        const btn = event.target.closest('.profile-button');
        menu.classList.toggle('active');
        btn.classList.toggle('active');
    }

    function closeProfileMenu() {
        document.getElementById('profileMenu').classList.remove('active');
        document.querySelector('.profile-button').classList.remove('active');
    }

    function openProfileModal() {
        closeProfileMenu();
        document.getElementById('profileModal').classList.add('active');
    }

    function closeProfileModal() {
        document.getElementById('profileModal').classList.remove('active');
    }

    function openProfileSettings() {
        closeProfileMenu();
        closeProfileModal();
        document.getElementById('profileSettingsModal').classList.add('active');
    }

    function closeProfileSettings() {
        document.getElementById('profileSettingsModal').classList.remove('active');
    }

    function openImageCropper() {
        document.getElementById('imageInput').click();
    }

    function loadImageForCrop(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('imageToCrop').src = e.target.result;
                document.getElementById('cropModal').classList.add('active');
                initCropper();
            };
            reader.readAsDataURL(file);
        }
    }

    function initCropper() {
        const image = document.getElementById('imageToCrop');
        if (window.cropper) {
            window.cropper.destroy();
        }
        window.cropper = new Cropper(image, {
            aspectRatio: 1,
            viewMode: 1,
            autoCropArea: 1,
            responsive: true,
            guides: true,
            cropBoxMovable: true,
            cropBoxResizable: true
        });
    }

    function saveCroppedImage() {
        if (!window.cropper) return;
        
        const canvas = window.cropper.getCroppedCanvas({
            maxWidth: 500,
            maxHeight: 500,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high'
        });

        canvas.toBlob(blob => {
            const formData = new FormData();
            formData.append('profile_picture', blob, 'profile.png');
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            fetch('/api/update-profile-picture-session', { 
                method: 'POST', 
                body: formData, 
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            })
            .then(r => {
                if (!r.ok) {
                    return r.text().then(text => {
                        console.error('Server error response:', r.status, text);
                        throw new Error(`Server error ${r.status}: ${text.substring(0, 200)}`);
                    });
                }
                return r.json();
            })
            .then(data => {
                console.log('Profile picture response:', data);
                if (data.success) {
                    closeCropModal();

                    // Use returned URL immediately and cache-bust so browser fetches the new image
                    if (data.profile_picture) {
                        const newUrl = data.profile_picture + (data.profile_picture.includes('?') ? '&' : '?') + 'v=' + Date.now();

                        // Update all avatar elements on the page
                        document.querySelectorAll('.profile-avatar').forEach(img => {
                            try { img.src = newUrl; } catch(e) {}
                        });

                        const large = document.getElementById('largeProfilePic');
                        if (large) large.src = newUrl;

                        const profileAvatar = document.getElementById('profileAvatar');
                        if (profileAvatar) profileAvatar.src = newUrl;
                    }

                    // Also refresh profile data to keep everything in sync
                    loadUserProfile();
                    showSuccess('Profile picture updated!');
                } else {
                    console.error('Upload failed:', data.error);
                    alert('Failed to update profile picture: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(err => {
                console.error('Error uploading profile picture:', err);
                alert('Error uploading profile picture: ' + err.message);
            });
        });
    }

    function closeCropModal() {
        document.getElementById('cropModal').classList.remove('active');
        if (window.cropper) {
            window.cropper.destroy();
            window.cropper = null;
        }
    }

    function saveProfileChanges(e) {
        e.preventDefault();

        const formData = new FormData();
        formData.append('name', document.getElementById('editName').value);
        formData.append('email', document.getElementById('editEmail').value);
        formData.append('phone', document.getElementById('editPhone').value);

        fetch('/api/update-profile-session', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(r => {
            if (r.ok) return r.json();
            throw new Error('Update failed');
        })
        .then(data => {
            if (data.success) {
                closeProfileSettings();
                loadUserProfile();
                showSuccess('Profile updated successfully!');
            }
        })
        .catch(err => console.error('Error updating profile:', err));
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        return new Date(dateString).toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }

    function toggleSidebar() {
        document.querySelector('.sidebar').classList.toggle('collapsed');
    }

    // Initialize sidebar branding on page load
    function initializeSidebarBranding() {
        const sidebarLogo = document.getElementById('sidebarLogo');
        const sidebarTitle = document.getElementById('sidebarTitle');

        // Check localStorage first for cached values
        const cachedLogo = localStorage.getItem('appLogo');
        const cachedName = localStorage.getItem('appName');

        if (cachedLogo && sidebarLogo) {
            sidebarLogo.src = cachedLogo;
        }
        if (cachedName && sidebarTitle) {
            sidebarTitle.textContent = cachedName;
        }

        // Fetch fresh settings from API
        fetch('/api/app-settings')
            .then(r => r.json())
            .then(data => {
                if (data.settings) {
                    const logo = data.settings.logo || '/foto/logo.jpg';
                    const name = data.settings.app_name || 'LODIT';

                    // Cache-bust branding images so updates appear immediately
                    const logoUrl = logo + (logo.includes('?') ? '&' : '?') + 'v=' + Date.now();
                    if (sidebarLogo && sidebarLogo.src !== logoUrl) {
                        sidebarLogo.src = logoUrl;
                        localStorage.setItem('appLogo', logoUrl);
                    }
                    if (sidebarTitle && sidebarTitle.textContent !== name) {
                        sidebarTitle.textContent = name;
                        localStorage.setItem('appName', name);
                    }
                }
            })
            .catch(err => console.error('Error loading sidebar branding:', err));
    }

    // Run on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeSidebarBranding);
    } else {
        initializeSidebarBranding();
    }

    function showSuccess(message) {
        // Show success message (implement based on your notification system)
        alert(message);
    }
</script>
