@extends('layouts.app')

@section('title', 'LODIT - Settings')

@section('content')

<div class="settings-wrapper">
    <div class="settings-container">
        <!-- Header with Back Button -->
        <div class="settings-header">
            <a href="#" onclick="history.back(); return false;" class="back-btn">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            <h1>Settings</h1>
        </div>

        <!-- Main Settings Card -->
        <div class="settings-card">
            <!-- Logo Section -->
            <div class="settings-section">
                <h2>System Logo</h2>
                <div class="logo-upload-wrapper">
                    <div class="logo-preview-box">
                        <img id="logoPreview" src="/foto/logo.jpg" alt="System Logo" class="logo-img">
                    </div>
                    <div class="logo-upload-area">
                        <input type="file" id="systemLogo" accept="image/*" class="logo-input" onchange="handleLogoChange(event)">
                        <label for="systemLogo" class="upload-label">
                            <i class="bi bi-cloud-upload"></i>
                            <span>Click to upload or drag and drop</span>
                            <small>PNG, JPG, GIF up to 5MB</small>
                        </label>
                    </div>
                </div>
            </div>

            <!-- App Name Section -->
            <div class="settings-section">
                <h2>System Name</h2>
                <input type="text" id="appName" class="form-input" placeholder="Enter your system name (e.g., My Business)">
            </div>

            <!-- Owner Name Section -->
            <div class="settings-section">
                <h2>Owner Name</h2>
                <input type="text" id="ownerName" class="form-input" placeholder="Enter owner name">
            </div>

            <!-- Save Button -->
            <div class="settings-section button-section">
                <button class="save-btn" onclick="saveAllSettings()">
                    <i class="bi bi-check-circle"></i>
                    <span>Save Settings</span>
                </button>
                <div id="saveStatus" class="save-status"></div>
            </div>
        </div>
    </div>
</div>

@php echo view('footer-new'); @endphp

<style>
    * {
        box-sizing: border-box;
    }

    .settings-wrapper {
        min-height: 100vh;
        padding: 40px 20px;
        background: var(--bg-color, #0f0f0f);
    }

    .settings-container {
        max-width: 700px;
        margin: 0 auto;
        margin-top: 40px;
    }

    .settings-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 40px;
    }

    .back-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        background-color: var(--navbar-bg, #1a1a1a);
        border: 1px solid var(--navbar-border, #333);
        border-radius: 6px;
        color: var(--text-primary, #fff);
        text-decoration: none;
        cursor: pointer;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .back-btn:hover {
        background-color: var(--profile-bg, #2a2a2a);
        border-color: var(--text-secondary, #888);
    }

    .settings-header h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 600;
        color: var(--text-primary, #fff);
    }

    .settings-card {
        background-color: var(--navbar-bg, #1a1a1a);
        border: 1px solid var(--navbar-border, #333);
        border-radius: 12px;
        overflow: hidden;
    }

    .settings-section {
        padding: 30px;
        border-bottom: 1px solid var(--navbar-border, #333);
    }

    .settings-section:last-child {
        border-bottom: none;
    }

    .settings-section h2 {
        margin: 0 0 20px 0;
        font-size: 16px;
        font-weight: 600;
        color: var(--text-primary, #fff);
    }

    /* Logo Upload */
    .logo-upload-wrapper {
        display: grid;
        grid-template-columns: 150px 1fr;
        gap: 30px;
        align-items: start;
    }

    .logo-preview-box {
        width: 150px;
        height: 150px;
        border-radius: 8px;
        background-color: var(--profile-bg, #2a2a2a);
        border: 1px solid var(--navbar-border, #333);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .logo-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 10px;
    }

    .logo-upload-area {
        position: relative;
        border: 2px dashed var(--navbar-border, #333);
        border-radius: 8px;
        padding: 40px 20px;
        text-align: center;
        transition: all 0.3s ease;
        background-color: var(--profile-bg, #2a2a2a);
    }

    .logo-upload-area:hover {
        border-color: var(--text-secondary, #888);
        background-color: rgba(255, 255, 255, 0.02);
    }

    .logo-input {
        display: none;
    }

    .upload-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        color: var(--text-secondary, #888);
        cursor: pointer;
        font-size: 14px;
    }

    .upload-label:hover {
        color: var(--text-primary, #fff);
    }

    .upload-label i {
        font-size: 24px;
    }

    .upload-label small {
        font-size: 12px;
        color: var(--text-secondary, #888);
    }

    /* Form Input */
    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid var(--navbar-border, #333);
        border-radius: 6px;
        background-color: var(--profile-bg, #2a2a2a);
        color: var(--text-primary, #fff);
        font-size: 14px;
        transition: all 0.3s ease;
        font-family: inherit;
    }

    .form-input:focus {
        outline: none;
        border-color: #1e3a8a;
        box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        background-color: rgba(255, 255, 255, 0.02);
    }

    .form-input::placeholder {
        color: var(--text-secondary, #888);
    }

    /* Button Section */
    .button-section {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .save-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 12px 24px;
        background-color: #1e3a8a;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .save-btn:hover {
        background-color: #1e40af;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.3);
    }

    .save-btn:active {
        transform: translateY(0);
    }

    .save-status {
        text-align: center;
        font-size: 13px;
        min-height: 20px;
    }

    .save-status.success {
        color: #10b981;
    }

    .save-status.error {
        color: #ef4444;
    }

    @media (max-width: 640px) {
        .settings-wrapper {
            padding: 20px 15px;
        }

        .settings-container {
            margin-top: 20px;
        }

        .settings-header {
            gap: 12px;
            margin-bottom: 25px;
        }

        .settings-header h1 {
            font-size: 24px;
        }

        .settings-section {
            padding: 20px;
        }

        .logo-upload-wrapper {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .logo-preview-box {
            width: 100%;
            height: 200px;
        }

        .logo-upload-area {
            padding: 30px 15px;
        }
    }
</style>

<script>
    let selectedLogoFile = null;

    // Load settings on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadSettings();
    });

    function loadSettings() {
        fetch('/api/app-settings')
            .then(r => r.json())
            .then(data => {
                if (data.settings) {
                    // Load logo
                    if (data.settings.logo) {
                        document.getElementById('logoPreview').src = data.settings.logo;
                    }

                    // Load app name
                    if (data.settings.app_name) {
                        document.getElementById('appName').value = data.settings.app_name;
                    }

                    // Load owner name
                    if (data.settings.owner_name) {
                        document.getElementById('ownerName').value = data.settings.owner_name;
                    }
                }
            })
            .catch(err => console.error('Error loading settings:', err));
    }

    function handleLogoChange(event) {
        const file = event.target.files[0];
        if (file) {
            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                showStatus('File size must be less than 5MB', 'error');
                return;
            }

            selectedLogoFile = file;
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('logoPreview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    function saveAllSettings() {
        const appName = document.getElementById('appName').value.trim();
        const ownerName = document.getElementById('ownerName').value.trim();

        // Validate inputs
        if (!appName) {
            showStatus('Please enter a system name', 'error');
            return;
        }

        if (!ownerName) {
            showStatus('Please enter an owner name', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('app_name', appName);
        formData.append('owner_name', ownerName);
        
        if (selectedLogoFile) {
            formData.append('logo', selectedLogoFile);
        }

        const saveBtn = document.querySelector('.save-btn');
        saveBtn.disabled = true;
        saveBtn.style.opacity = '0.6';

        fetch('/api/update-app-settings', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                showStatus('Settings saved successfully!', 'success');
                
                // Update sidebar if elements exist
                const sidebarLogo = document.getElementById('sidebarLogo');
                const sidebarTitle = document.getElementById('sidebarTitle');
                
                if (sidebarLogo && data.settings.logo) {
                    sidebarLogo.src = data.settings.logo;
                }
                if (sidebarTitle && data.settings.app_name) {
                    sidebarTitle.textContent = data.settings.app_name;
                }

                // Clear file input
                selectedLogoFile = null;
                document.getElementById('systemLogo').value = '';

                // Refresh page after 1 second
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showStatus(data.message || 'Error saving settings', 'error');
            }
        })
        .catch(err => {
            console.error('Error saving settings:', err);
            showStatus('Error saving settings. Please try again.', 'error');
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.style.opacity = '1';
        });
    }

    function showStatus(message, type) {
        const statusDiv = document.getElementById('saveStatus');
        statusDiv.textContent = message;
        statusDiv.className = 'save-status ' + type;

        if (type === 'success') {
            setTimeout(() => {
                statusDiv.textContent = '';
                statusDiv.className = 'save-status';
            }, 3000);
        }
    }
</script>
@endsection