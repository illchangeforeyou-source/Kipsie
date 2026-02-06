/**
 * Permission Enforcement System
 * Checks user permissions and hides/shows elements accordingly
 */

class PermissionEnforcer {
    constructor() {
        this.userPermissions = {};
        this.permissionCategories = {};
        this.isInitialized = false;
    }

    /**
     * Initialize the permission system
     * Fetch permissions for current user and apply them to the page
     */
    async init() {
        try {
            console.log('🔐 Initializing Permission Enforcer...');
            
            const response = await fetch('/permissions/api/current-user-permissions');
            
            if (!response.ok) {
                console.warn('⚠️ Could not fetch user permissions, using default visibility');
                this.isInitialized = true;
                return;
            }

            const data = await response.json();
            
            if (data.success && data.permissions) {
                this.userPermissions = data.permissions;
                console.log('✅ Permissions loaded:', this.userPermissions);
                
                // Apply permissions to page elements
                this.applyPermissions();
                this.isInitialized = true;
            } else {
                console.warn('⚠️ No permissions found for user');
                this.isInitialized = true;
            }
        } catch (error) {
            console.error('❌ Error initializing permissions:', error);
            this.isInitialized = true; // Still mark as initialized to prevent loops
        }
    }

    /**
     * Check if user has a specific permission
     * @param {string} permissionKey - The permission to check (e.g., 'view_medicines')
     * @returns {boolean} True if user has permission
     */
    hasPermission(permissionKey) {
        if (!this.isInitialized) {
            console.warn('⚠️ Permission system not initialized yet');
            return true; // Default to allowing access if not initialized
        }

        const hasAccess = this.userPermissions[permissionKey] === true || this.userPermissions[permissionKey] === 1;
        console.log(`🔍 Checking permission "${permissionKey}": ${hasAccess}`);
        return hasAccess;
    }

    /**
     * Hide an element if user doesn't have permission
     * @param {string} selector - CSS selector of element to hide
     * @param {string} permissionKey - Permission to check
     */
    hideIfNoPermission(selector, permissionKey) {
        const element = document.querySelector(selector);
        if (!element) {
            console.warn(`⚠️ Element not found: ${selector}`);
            return;
        }

        if (!this.hasPermission(permissionKey)) {
            element.style.display = 'none';
            element.setAttribute('data-hidden-by-permission', permissionKey);
            console.log(`👁️ Hiding "${selector}" due to missing permission "${permissionKey}"`);
        } else {
            element.style.display = '';
            element.removeAttribute('data-hidden-by-permission');
        }
    }

    /**
     * Show an element only if user has permission
     * @param {string} selector - CSS selector of element to show
     * @param {string} permissionKey - Permission to check
     */
    showIfHasPermission(selector, permissionKey) {
        const element = document.querySelector(selector);
        if (!element) {
            console.warn(`⚠️ Element not found: ${selector}`);
            return;
        }

        if (this.hasPermission(permissionKey)) {
            element.style.display = '';
            console.log(`👁️ Showing "${selector}" - user has permission "${permissionKey}"`);
        } else {
            element.style.display = 'none';
            element.setAttribute('data-hidden-by-permission', permissionKey);
        }
    }

    /**
     * Apply permission-based hiding to all elements with data-permission attribute
     * Example: <button data-permission="view_medicines">View Medicines</button>
     */
    applyPermissions() {
        console.log('📋 Applying permissions to elements with [data-permission] attribute...');
        
        const elementsWithPermission = document.querySelectorAll('[data-permission]');
        console.log(`📊 Found ${elementsWithPermission.length} elements with permission requirements`);

        elementsWithPermission.forEach((element, index) => {
            const requiredPermission = element.getAttribute('data-permission');
            const permissionAction = element.getAttribute('data-permission-action') || 'show'; // 'show' or 'hide'
            
            if (!requiredPermission) return;

            const hasAccess = this.hasPermission(requiredPermission);
            const shouldShow = permissionAction === 'show' ? hasAccess : !hasAccess;

            if (shouldShow) {
                element.style.display = '';
                element.classList.remove('permission-denied');
                console.log(`✅ [${index}] Showing element - has permission: ${requiredPermission}`);
            } else {
                element.style.display = 'none';
                element.classList.add('permission-denied');
                element.setAttribute('data-denied-reason', requiredPermission);
                console.log(`🚫 [${index}] Hiding element - missing permission: ${requiredPermission}`);
            }
        });

        console.log('✅ Permission enforcement complete');
    }

    /**
     * Enable/disable an element based on permissions
     * @param {string} selector - CSS selector
     * @param {string} permissionKey - Permission to check
     * @param {boolean} disable - If true, disable when no permission
     */
    controlElement(selector, permissionKey, disable = false) {
        const element = document.querySelector(selector);
        if (!element) return;

        const hasAccess = this.hasPermission(permissionKey);
        
        if (disable) {
            element.disabled = !hasAccess;
            element.style.opacity = hasAccess ? '1' : '0.5';
            element.style.cursor = hasAccess ? 'pointer' : 'not-allowed';
            element.title = hasAccess ? '' : `Requires: ${permissionKey}`;
        } else {
            element.style.display = hasAccess ? '' : 'none';
        }
    }

    /**
     * Check multiple permissions (AND condition)
     * User must have ALL permissions
     * @param {array} permissionKeys - Array of permissions
     * @returns {boolean}
     */
    hasAllPermissions(permissionKeys) {
        return permissionKeys.every(perm => this.hasPermission(perm));
    }

    /**
     * Check multiple permissions (OR condition)
     * User must have at least ONE permission
     * @param {array} permissionKeys - Array of permissions
     * @returns {boolean}
     */
    hasAnyPermission(permissionKeys) {
        return permissionKeys.some(perm => this.hasPermission(perm));
    }

    /**
     * Get all user permissions
     * @returns {object} Object of all permissions and their values
     */
    getAllPermissions() {
        return this.userPermissions;
    }

    /**
     * Reload permissions from server
     */
    async reload() {
        console.log('🔄 Reloading permissions...');
        await this.init();
    }
}

// Global instance
const permissionEnforcer = new PermissionEnforcer();

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        permissionEnforcer.init();
    });
} else {
    permissionEnforcer.init();
}

// Expose to window for debugging
window.permissionEnforcer = permissionEnforcer;
window.checkPermission = (perm) => permissionEnforcer.hasPermission(perm);
