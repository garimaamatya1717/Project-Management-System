// Modal helpers
function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'flex';
}
function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.style.display = 'none';
}

// Close modal on overlay click
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.style.display = 'none';
    }
});

// Notification dropdown
function toggleNotifs() {
    const d = document.getElementById('notifDropdown');
    if (!d) return;
    d.style.display = d.style.display === 'none' ? 'block' : 'none';
}

// Close notif dropdown on outside click
document.addEventListener('click', function(e) {
    const btn = document.getElementById('notifBtn');
    const dropdown = document.getElementById('notifDropdown');
    if (dropdown && btn && !btn.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.style.display = 'none';
    }
});

// Role selector for register page (visual toggle)
document.querySelectorAll('.role-option').forEach(function(label) {
    label.addEventListener('click', function() {
        document.querySelectorAll('.role-option').forEach(l => l.classList.remove('selected'));
        this.classList.add('selected');
    });
});

// Global search filter (client-side, best-effort)
const searchInput = document.getElementById('globalSearch');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const q = this.value.toLowerCase();
        // Filter task cards
        document.querySelectorAll('.task-card, .deadline-item, .project-card, .project-row').forEach(function(el) {
            const text = el.textContent.toLowerCase();
            el.style.display = !q || text.includes(q) ? '' : 'none';
        });
    });
}

// Escape key closes modals
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay').forEach(function(m) {
            m.style.display = 'none';
        });
    }
});
