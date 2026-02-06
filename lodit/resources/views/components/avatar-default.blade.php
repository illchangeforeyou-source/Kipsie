<!-- Default Avatar SVG Generator
     This can be used as a fallback when user has no profile picture
     Copy this to public/default-avatar.svg or reference the data URI directly -->

<svg width="150" height="150" viewBox="0 0 150 150" xmlns="http://www.w3.org/2000/svg">
    <!-- Background -->
    <rect width="150" height="150" fill="#1e3a8a"/>
    
    <!-- Circle background for avatar -->
    <circle cx="75" cy="60" r="40" fill="#ffffff" opacity="0.9"/>
    
    <!-- User icon -->
    <path d="M 75 40 C 85 40 93 48 93 58 C 93 68 85 76 75 76 C 65 76 57 68 57 58 C 57 48 65 40 75 40 Z" fill="#1e3a8a"/>
    
    <!-- Body -->
    <ellipse cx="75" cy="110" rx="35" ry="30" fill="#ffffff" opacity="0.9"/>
    
    <!-- Text fallback -->
    <text x="75" y="135" font-family="Arial, sans-serif" font-size="14" fill="#1e3a8a" text-anchor="middle" font-weight="bold">
        User
    </text>
</svg>

<!-- Usage in Blade:
     <img src="{{ asset('default-avatar.svg') }}" alt="Default Avatar" class="profile-avatar">
     
     Or use as fallback in JavaScript:
     .profile-avatar.error {
         src: url('{{ asset("default-avatar.svg") }}');
     }
     
     Or generate dynamically with initials (recommended):
-->

<!-- Advanced: Avatar with User Initials -->
<div class="avatar-placeholder">
    <svg width="150" height="150" viewBox="0 0 150 150" xmlns="http://www.w3.org/2000/svg">
        <defs>
            <linearGradient id="avatarGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" style="stop-color:#1e3a8a;stop-opacity:1" />
                <stop offset="100%" style="stop-color:#1e40af;stop-opacity:1" />
            </linearGradient>
        </defs>
        <rect width="150" height="150" fill="url(#avatarGradient)"/>
        <text x="75" y="85" font-family="Arial, sans-serif" font-size="48" fill="#ffffff" text-anchor="middle" font-weight="bold">
            <!-- Initials go here -->
        </text>
    </svg>
</div>

<style>
    /* Avatar placeholder styles */
    .avatar-placeholder {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
    }

    /* Fallback avatar image */
    .profile-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #1e3a8a;
        background-color: #f5f5f5;
    }

    .profile-avatar.large {
        width: 150px;
        height: 150px;
        border: 4px solid #1e3a8a;
    }

    /* Loading state */
    .avatar-loading {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }
</style>

<!-- JavaScript Helper for Avatar Generation -->
<script>
    // Generate avatar with user initials
    function generateAvatarWithInitials(name) {
        const initials = name
            .split(' ')
            .map(part => part[0])
            .join('')
            .toUpperCase()
            .substring(0, 2);

        const colors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', 
            '#98D8C8', '#F7DC6F', '#BB8FCE', '#85C1E2'
        ];

        const hash = name.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0);
        const color = colors[hash % colors.length];

        const svg = `
            <svg width="150" height="150" viewBox="0 0 150 150" xmlns="http://www.w3.org/2000/svg">
                <rect width="150" height="150" fill="${color}"/>
                <text x="75" y="85" font-family="Arial, sans-serif" font-size="48" fill="#ffffff" 
                      text-anchor="middle" font-weight="bold">${initials}</text>
            </svg>
        `;

        return 'data:image/svg+xml;base64,' + btoa(svg);
    }

    // Use in your profile component
    function setProfileAvatarWithFallback(imagePath, userName) {
        const img = document.querySelector('.profile-avatar');

        if (!img) return;

        img.onerror = function() {
            // Fallback to initials avatar
            this.src = generateAvatarWithInitials(userName);
        };

        if (imagePath) {
            // Append cache-busting query to force browser to fetch updated image
            const url = imagePath + (imagePath.includes('?') ? '&' : '?') + 'v=' + Date.now();
            img.src = url;
        } else {
            img.src = generateAvatarWithInitials(userName);
        }
    }

    // Example usage:
    // setProfileAvatarWithFallback('/storage/avatars/user-123.png', 'John Doe');
</script>
