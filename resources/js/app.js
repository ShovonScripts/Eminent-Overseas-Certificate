// Application initialization and global utilities

// ========== Global State ==========
window.App = {
    toasts: [],
    modals: [],
    dropdowns: [],
};

// ========== Utility Functions ==========
const $ = (selector, context = document) => context.querySelector(selector);
const $$ = (selector, context = document) => Array.from(context.querySelectorAll(selector));

function clamp(value, min, max) {
    return Math.max(min, Math.min(max, value));
}

function debounce(fn, delay) {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn.apply(this, args), delay);
    };
}

function throttle(fn, limit) {
    let inThrottle;
    return (...args) => {
        if (!inThrottle) {
            fn.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// ========== Toast System ==========
function showToast(message, type = 'info', title = '') {
    const container = getOrCreateToastContainer();
    const toast = createToast(message, type, title);
    container.appendChild(toast);
    
    // Trigger show animation
    requestAnimationFrame(() => {
        toast.classList.add('show');
    });
    
    // Auto dismiss after 5 seconds
    setTimeout(() => dismissToast(toast), 5000);
    
    return toast;
}

function getOrCreateToastContainer() {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    return container;
}

function createToast(message, type = 'info', title = '') {
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `
        <div class="toast-icon">
            ${getToastIcon(type)}
        </div>
        <div class="toast-content">
            ${title ? `<div class="toast-title">${escapeHtml(title)}</div>` : ''}
            <div class="toast-message">${escapeHtml(message)}</div>
        </div>
        <button class="toast-close" aria-label="Dismiss">&times;</button>
    `;
    
    const closeBtn = toast.querySelector('.toast-close');
    closeBtn.addEventListener('click', () => dismissToast(toast));
    
    return toast;
}

function getToastIcon(type) {
    const icons = {
        success: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>',
        danger: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>',
        warning: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="15"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>',
        info: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>'
    };
    return icons[type] || icons.info;
}

function dismissToast(toast) {
    toast.classList.remove('show');
    toast.addEventListener('transitionend', () => toast.remove(), { once: true });
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Expose globally
window.showToast = showToast;
window.dismissToast = dismissToast;

// ========== Modal System ==========
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (!modal) return;
    
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
    
    // Focus first focusable element
    const focusable = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    if (focusable) {
        setTimeout(() => focusable.focus(), 100);
    }
    
    // Trap focus
    modal._focusTrap = trapFocus(modal);
    
    return modal;
}

function closeModal(modal) {
    if (typeof modal === 'string') {
        modal = document.getElementById(modal);
    }
    if (!modal) return;
    
    modal.classList.remove('open');
    document.body.style.overflow = '';
    
    if (modal._focusTrap) {
        modal._focusTrap();
        modal._focusTrap = null;
    }
}

function closeAllModals() {
    document.querySelectorAll('.modal-overlay.open').forEach(closeModal);
}

// Focus trap for modals
function trapFocus(element) {
    const focusableElements = element.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    const firstElement = focusableElements[0];
    const lastElement = focusableElements[focusableElements.length - 1];
    
    function handleTab(e) {
        if (e.key !== 'Tab') return;
        
        if (e.shiftKey) {
            if (document.activeElement === firstElement) {
                e.preventDefault();
                lastElement.focus();
            }
        } else {
            if (document.activeElement === lastElement) {
                e.preventDefault();
                firstElement.focus();
            }
        }
    }
    
    element.addEventListener('keydown', handleTab);
    return () => element.removeEventListener('keydown', handleTab);
}

// Close modal on overlay click
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal-overlay')) {
        closeModal(e.target);
    }
});

// Close modal on Escape
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        closeAllModals();
    }
});

// ========== Dropdown System ==========
function initDropdowns() {
    document.querySelectorAll('.dropdown').forEach(dropdown => {
        const trigger = dropdown.querySelector('[data-dropdown-toggle]');
        const menu = dropdown.querySelector('.dropdown-menu');
        
        if (!trigger || !menu) return;
        
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            closeAllDropdowns();
            dropdown.classList.toggle('open');
        });
        
        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('open');
            }
        });
        
        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                dropdown.classList.remove('open');
            }
        });
    });
}

function closeAllDropdowns() {
    document.querySelectorAll('.dropdown.open').forEach(d => d.classList.remove('open'));
}

// ========== Sidebar Toggle (Mobile) ==========
function initSidebarToggle() {
    const toggle = document.querySelector('[data-sidebar-toggle]');
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.sidebar-overlay');
    
    if (!toggle || !sidebar) return;
    
    toggle.addEventListener('click', () => {
        sidebar.classList.toggle('open');
        if (overlay) overlay.classList.toggle('open');
    });
    
    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.remove('open');
        });
    }
    
    // Close on resize
    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            if (window.innerWidth >= 1024) {
                document.querySelector('.sidebar')?.classList.remove('open');
                document.querySelector('.sidebar-overlay')?.classList.remove('open');
            }
        }, 100);
    });
}

// ========== Activity Log Modal ==========
function initActivityLogModal() {
    const modal = document.getElementById('details-modal');
    if (!modal) return;
    
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal(modal);
    });
    
    const closeBtn = modal.querySelector('.modal-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => closeModal(modal));
    }
}

window.showDetails = function(logId) {
    fetch(`/admin/activity-logs/${logId}`)
        .then(response => response.json())
        .then(data => {
            const body = document.getElementById('modal-body');
            let html = `
                <dl class="detail-grid">
                    <dt>Action</dt>
                    <dd><span class="badge badge-${data.action_color}">${data.action}</span></dd>
                    <dt>User</dt>
                    <dd>${data.user?.name ?? 'System'} (${data.user?.email ?? 'â€”'})</dd>
                    <dt>Time</dt>
                    <dd>${data.created_at}</dd>
                    <dt>IP Address</dt>
                    <dd class="mono">${data.ip_address ?? 'â€”'}</dd>
                    <dt>User Agent</dt>
                    <dd class="muted" style="font-size: 11px; word-break: break-all;">${data.user_agent ?? 'â€”'}</dd>
            `;
            
            if (data.model_type) {
                html += `
                    <dt>Target</dt>
                    <dd>${data.model_type} #${data.model_id ?? 'â€”'}</dd>
                `;
            }
            
            if (data.old_values) {
                html += `
                    <dt>Old Values</dt>
                    <dd><pre class="muted" style="font-size: 11px; max-height: 200px; overflow: auto;">${JSON.stringify(data.old_values, null, 2)}</pre></dd>
                `;
            }
            
            if (data.new_values) {
                html += `
                    <dt>New Values</dt>
                    <dd><pre class="muted" style="font-size: 11px; max-height: 200px; overflow: auto;">${JSON.stringify(data.new_values, null, 2)}</pre></dd>
                `;
            }
            
            html += '</dl>';
            document.getElementById('modal-body').innerHTML = html;
            
            const modal = document.getElementById('details-modal');
            modal.classList.add('open');
            document.body.style.overflow = 'hidden';
        })
        .catch(console.error);
};

window.closeModal = function(modal) {
    if (typeof modal === 'string') modal = document.getElementById(modal);
    if (!modal) return;
    modal.classList.remove('open');
    document.body.style.overflow = '';
};

// ========== Table Row Click Handler ==========
function initTableRows() {
    document.querySelectorAll('table.table tbody tr[data-href]').forEach(row => {
        row.style.cursor = 'pointer';
        row.addEventListener('click', (e) => {
            if (e.target.closest('a, button, .btn, [data-dropdown-toggle]')) return;
            window.location.href = row.dataset.href;
        });
    });
}

// ========== Form Validation Feedback ==========
function initFormValidation() {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', (e) => {
            if (e.defaultPrevented) return;
            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.disabled = true;
                submitBtn.dataset.originalText = submitBtn.textContent;
                submitBtn.innerHTML = '<span class="spinner spinner-sm"></span> Savingâ€¦';
            }
        });
        
        form.addEventListener('invalid', (e) => {
            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = submitBtn.dataset.originalText || 'Save';
            }
        }, true);
    });
}

// ========== File Upload Preview ==========
function initFileUploadPreview() {
    document.querySelectorAll('input[type="file"][accept*="image"]').forEach(input => {
        input.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (!file) return;
            
            if (!file.type.startsWith('image/')) {
                showToast('Please select an image file', 'danger');
                e.target.value = '';
                return;
            }
            
            if (file.size > 2 * 1024 * 1024) {
                showToast('File size must be less than 2MB', 'danger');
                e.target.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = (e) => {
                let preview = e.target.closest('.field').querySelector('.current-asset');
                if (!preview) {
                    preview = document.createElement('div');
                    preview.className = 'current-asset';
                    e.target.closest('.field').appendChild(preview);
                }
                preview.innerHTML = `
                    <img src="${e.target.result}" alt="Preview" style="max-height: 60px; border-radius: 8px;">
                    <span>New image selected</span>
                `;
            };
            reader.readAsDataURL(file);
        });
    });
}

// ========== Copy to Clipboard ==========
function copyToClipboard(text, successMessage = 'Copied to clipboard') {
    navigator.clipboard.writeText(text).then(() => {
        showToast(successMessage, 'success');
    }).catch(() => {
        showToast('Failed to copy', 'danger');
    });
}

// ========== Confirm Dialogs ==========
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

window.confirmAction = confirmAction;

// ========== Initialize on DOM Ready ==========
document.addEventListener('DOMContentLoaded', () => {
    // Initialize all components
    initDropdowns();
    initSidebarToggle();
    initActivityLogModal();
    initTableRows();
    initFormValidation();
    initFileUploadPreview();
    
    // Initialize tooltips
    document.querySelectorAll('[data-tooltip]').forEach(el => {
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip';
        tooltip.textContent = el.dataset.tooltip;
        document.body.appendChild(tooltip);
        
        const showTooltip = () => {
            const rect = el.getBoundingClientRect();
            tooltip.style.left = `${rect.left + rect.width / 2}px`;
            tooltip.style.top = `${rect.top - 40}px`;
            tooltip.classList.add('show');
        };
        
        const hideTooltip = () => tooltip.classList.remove('show');
        
        el.addEventListener('mouseenter', showTooltip);
        el.addEventListener('mouseleave', hideTooltip);
        el.addEventListener('focus', showTooltip);
        el.addEventListener('blur', hideTooltip);
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        // Ctrl/Cmd + K for search (if search input exists)
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            const searchInput = document.querySelector('input[name="q"], input[type="search"]');
            if (searchInput) {
                e.preventDefault();
                searchInput.focus();
            }
        }
        
        // Escape to close modals/dropdowns
        if (e.key === 'Escape') {
            closeAllModals();
            closeAllDropdowns();
        }
    });
    
    console.log('Eminent Certificate System initialized');
});

// ========== Export for use in other scripts ==========
window.App = {
    showToast,
    dismissToast,
    openModal,
    closeModal,
    closeAllModals,
    showDetails: window.showDetails,
    closeModal: window.closeModal,
    confirmAction,
    copyToClipboard: (text, msg) => navigator.clipboard.writeText(text).then(() => showToast(msg || 'Copied')),
};
