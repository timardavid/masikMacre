// Global state
let currentUser = {};
let currentView = 'overview';

// Initialize dashboard
document.addEventListener('DOMContentLoaded', async () => {
    await checkAuth();
    await loadUserInfo();
    await loadOverview();
    
    setupNavigation();
    setupEventListeners();
});

// Check authentication
async function checkAuth() {
    const response = await fetch('api/auth.php?action=check');
    const result = await response.json();
    
    if (!result.authenticated) {
        window.location.href = 'index.html';
        return;
    }
    
    currentUser = result.user;
    document.getElementById('userName').textContent = currentUser.name;
    document.getElementById('userRole').textContent = currentUser.role;
}

// Load user info
async function loadUserInfo() {
    const response = await fetch('api/auth.php?action=check');
    const result = await response.json();
    if (result.authenticated) {
        currentUser = result.user;
    }
}

// Load overview
async function loadOverview() {
    const [users, tasks, workStatus] = await Promise.all([
        fetch('api/users.php').then(r => r.json()),
        fetch('api/tasks.php').then(r => r.json()),
        fetch('api/workstatus.php').then(r => r.json())
    ]);
    
    // Update stats
    updateOverviewStats(users, tasks);
    
    // Show warnings
    checkWarnings(users, tasks);
}

function updateOverviewStats(users, tasks) {
    const stats = document.getElementById('overviewStats');
    stats.innerHTML = `
        <div class="stat-card">
            <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-value">${users.length}</div>
            <div class="stat-label">Összes dolgozó</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fef3c7; color: #f59e0b;">
                <i class="fas fa-user-clock"></i>
            </div>
            <div class="stat-value">${users.filter(u => u.current_status === 'working').length}</div>
            <div class="stat-label">Munkában</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #fee2e2; color: #ef4444;">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-value">${tasks.filter(t => t.priority === 'Critical').length}</div>
            <div class="stat-label">Kritikus feladat</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background: #d1fae5; color: #10b981;">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-value">${tasks.length}</div>
            <div class="stat-label">Összes feladat</div>
        </div>
    `;
}

async function checkWarnings(users, tasks) {
    const alerts = document.getElementById('warningAlerts');
    alerts.innerHTML = '';
    
    for (const user of users) {
        const stats = await fetch(`api/statistics.php?user_id=${user.id}&month=${new Date().toISOString().slice(0,7)}`).then(r => r.json());
        
        if (stats.hours_warning) {
            alerts.innerHTML += `
                <div class="alert-box">
                    <div class="alert-icon">⚠️</div>
                    <div class="alert-text">
                        <strong>${user.name}</strong> nem dolgozta le a heti 40 órát!<br>
                        <small>Jelenlegi órák: ${stats.week_hours.toFixed(1)} / 40 óra</small>
                    </div>
                </div>
            `;
        }
    }
}

// Setup navigation
function setupNavigation() {
    const navLinks = document.querySelectorAll('.nav-menu a');
    
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const view = link.dataset.view;
            switchView(view);
            
            navLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
        });
    });
}

function switchView(view) {
    const sections = document.querySelectorAll('.view-section');
    sections.forEach(section => section.classList.remove('active'));
    
    document.getElementById(view).classList.add('active');
    currentView = view;
    
    if (view === 'employees') loadEmployees();
    else if (view === 'tasks') loadTasks();
    else if (view === 'work-status') loadWorkStatus();
    else if (view === 'statistics') loadStatistics();
}

// Load employees
async function loadEmployees() {
    const response = await fetch('api/users.php');
    const users = await response.json();
    
    const grid = document.getElementById('employeeGrid');
    grid.innerHTML = users.map(user => {
        const statusMap = {
            'working': { text: 'Munkában', class: 'status-working' },
            'break': { text: 'Szünet', class: 'status-break' },
            'vacation': { text: 'Szabadság', class: 'status-vacation' },
            'sick_leave': { text: 'Táppénz', class: 'status-sick' },
            'no_work': { text: 'Nincs munkaidő', class: 'status-no-work' }
        };
        
        const status = statusMap[user.current_status] || statusMap['no_work'];
        
        return `
            <div class="employee-card">
                <div class="employee-header">
                    <div>
                        <div class="employee-name">${user.name}</div>
                        <div class="employee-status ${status.class}">${status.text}</div>
                    </div>
                </div>
                <div class="employee-info"><i class="fas fa-briefcase"></i> ${user.role}</div>
                <div class="employee-info"><i class="fas fa-building"></i> ${user.department}</div>
                <div class="employee-info"><i class="fas fa-envelope"></i> ${user.email}</div>
                ${currentUser.role === 'Admin' || currentUser.role === 'HR' ? `
                <div class="employee-actions">
                    <button class="btn-icon btn-edit" onclick="editEmployee(${user.id})">
                        <i class="fas fa-edit"></i> Szerkesztés
                    </button>
                    <button class="btn-icon btn-delete" onclick="deleteEmployee(${user.id})">
                        <i class="fas fa-trash"></i> Törlés
                    </button>
                </div>
                ` : ''}
            </div>
        `;
    }).join('');
}

// Load tasks
async function loadTasks() {
    const response = await fetch('api/tasks.php');
    const tasks = await response.json();
    
    const list = document.getElementById('taskList');
    list.innerHTML = tasks.map(task => {
        const priorityMap = {
            'Critical': { class: 'priority-critical', badge: 'Kritikus' },
            'Very Urgent': { class: 'priority-very-urgent', badge: 'Nagyon sürgős' },
            'Urgent': { class: 'priority-urgent', badge: 'Sürgős' },
            'Not Urgent': { class: 'priority-not-urgent', badge: 'Nem sürgős' }
        };
        
        const priority = priorityMap[task.priority] || priorityMap['Not Urgent'];
        const statusText = task.status === 'pending' ? 'Folyamatban' : task.status === 'in_progress' ? 'Elkészítve' : 'Befejezve';
        
        return `
            <div class="task-card ${priority.class}">
                <div class="task-header">
                    <div class="task-title">${task.task_title}</div>
                    <div class="task-priority ${priority.class}">${priority.badge}</div>
                </div>
                <div class="task-info">
                    <i class="fas fa-building"></i> ${task.client_name || 'Nincs ügyfél'}
                </div>
                <div class="task-info">
                    <i class="fas fa-user"></i> ${task.user_name || 'Nincs felelős'}
                </div>
                <div class="task-info">
                    <i class="fas fa-flag"></i> ${statusText}
                </div>
                ${task.description ? `<div class="task-info">${task.description}</div>` : ''}
                <div class="task-actions">
                    <button class="btn-icon btn-edit" onclick="editTask(${task.id})">
                        <i class="fas fa-edit"></i> Szerkesztés
                    </button>
                    <button class="btn-icon btn-delete" onclick="deleteTask(${task.id})">
                        <i class="fas fa-trash"></i> Törlés
                    </button>
                </div>
            </div>
        `;
    }).join('');
}

// Load work status
async function loadWorkStatus() {
    const response = await fetch('api/workstatus.php');
    const statuses = await response.json();
    
    const list = document.getElementById('workStatusList');
    const statusMap = {
        'working': { text: 'Munkában', icon: '🟢', color: '#10b981' },
        'break': { text: 'Szünet', icon: '🟡', color: '#f59e0b' },
        'vacation': { text: 'Szabadság', icon: '🔵', color: '#3b82f6' },
        'sick_leave': { text: 'Táppénz', icon: '🔴', color: '#ef4444' },
        'no_work': { text: 'Nincs munkaidő', icon: '⚪', color: '#6b7280' }
    };
    
    list.innerHTML = statuses.slice(0, 20).map(status => {
        const statusInfo = statusMap[status.status] || statusMap['no_work'];
        return `
            <div class="status-item">
                <div>
                    <div class="status-user">${status.user_name}</div>
                    <div class="status-time">${new Date(status.created_at).toLocaleString('hu-HU')}</div>
                </div>
                <div class="employee-status" style="background: ${statusInfo.color}20; color: ${statusInfo.color};">
                    ${statusInfo.icon} ${statusInfo.text}
                </div>
            </div>
        `;
    }).join('');
}

// Load statistics
async function loadStatistics() {
    const response = await fetch('api/users.php');
    const users = await response.json();
    
    const grid = document.getElementById('statisticsGrid');
    
    const statsPromises = users.map(async (user) => {
        const response = await fetch(`api/statistics.php?user_id=${user.id}&month=${new Date().toISOString().slice(0,7)}`);
        return response.json();
    });
    
    const allStats = await Promise.all(statsPromises);
    
    grid.innerHTML = allStats.map((stats, index) => {
        const weekProgress = (stats.week_hours / 40) * 100;
        const monthProgress = (stats.total_hours / 160) * 100; // Assuming 160 hours per month
        
        return `
            <div class="stat-chart">
                <h3>${stats.name} (${stats.role})</h3>
                <div class="chart-bar">
                    <div class="chart-label">Havi órák:</div>
                    <div class="chart-bar-visual">
                        <div class="chart-bar-fill" style="width: ${Math.min(monthProgress, 100)}%"></div>
                    </div>
                    <div style="min-width: 60px; text-align: right;">${stats.total_hours.toFixed(1)}h</div>
                </div>
                <div class="chart-bar">
                    <div class="chart-label">Heti órák:</div>
                    <div class="chart-bar-visual">
                        <div class="chart-bar-fill" style="width: ${Math.min(weekProgress, 100)}%; background: ${stats.week_hours < 40 ? '#ef4444' : '#10b981'};"></div>
                    </div>
                    <div style="min-width: 60px; text-align: right;">${stats.week_hours.toFixed(1)}h/40h</div>
                </div>
                <div style="margin-top: 15px;">
                    <div class="employee-info">📅 Munkanapok: ${stats.days_worked}</div>
                    <div class="employee-info">🎯 Függőben: ${stats.pending_tasks} feladat</div>
                    <div class="employee-info">⚡ Folyamatban: ${stats.active_tasks} feladat</div>
                </div>
            </div>
        `;
    }).join('');
}

// Modals
function openModal(type) {
    const modal = document.getElementById('modal');
    const modalBody = document.getElementById('modalBody');
    
    if (type === 'employee') {
        modalBody.innerHTML = `
            <h2>Új Dolgozó</h2>
            <form onsubmit="saveEmployee(event)">
                <input type="hidden" id="employee_id" value="">
                <div class="form-group">
                    <label>Név</label>
                    <input type="text" id="employee_name" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" id="employee_email" required>
                </div>
                <div class="form-group">
                    <label>Jelszó</label>
                    <input type="password" id="employee_password" required>
                </div>
                <div class="form-group">
                    <label>Szerepkör</label>
                    <select id="employee_role" required>
                        <option value="IT">IT</option>
                        <option value="HR">HR</option>
                        <option value="Finance">Pénzügy</option>
                        <option value="CEO">Ügyvezető</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Részleg</label>
                    <input type="text" id="employee_department">
                </div>
                <div class="form-group">
                    <label>Telefonszám</label>
                    <input type="text" id="employee_phone">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Mégse</button>
                    <button type="submit" class="btn-save">Mentés</button>
                </div>
            </form>
        `;
    } else if (type === 'task') {
        modalBody.innerHTML = `
            <h2>Új Feladat</h2>
            <form onsubmit="saveTask(event)">
                <input type="hidden" id="task_id" value="">
                <div class="form-group">
                    <label>Feladat címe</label>
                    <input type="text" id="task_title" required>
                </div>
                <div class="form-group">
                    <label>Ügyfél neve</label>
                    <input type="text" id="task_client">
                </div>
                <div class="form-group">
                    <label>Leírás</label>
                    <textarea id="task_description"></textarea>
                </div>
                <div class="form-group">
                    <label>Felelős</label>
                    <select id="task_user_id" required></select>
                </div>
                <div class="form-group">
                    <label>Prioritás</label>
                    <select id="task_priority" required>
                        <option value="Critical">Kritikus</option>
                        <option value="Very Urgent">Nagyon sürgős</option>
                        <option value="Urgent">Sürgős</option>
                        <option value="Not Urgent">Nem sürgős</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Státusz</label>
                    <select id="task_status">
                        <option value="pending">Folyamatban</option>
                        <option value="in_progress">Elkészítve</option>
                        <option value="completed">Befejezve</option>
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">Mégse</button>
                    <button type="submit" class="btn-save">Mentés</button>
                </div>
            </form>
        `;
        
        // Load users for task assignment
        fetch('api/users.php').then(r => r.json()).then(users => {
            const select = document.getElementById('task_user_id');
            select.innerHTML = users.map(u => `<option value="${u.id}">${u.name}</option>`).join('');
        });
    }
    
    modal.style.display = 'block';
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
}

async function saveEmployee(event) {
    event.preventDefault();
    
    const data = {
        id: document.getElementById('employee_id').value,
        name: document.getElementById('employee_name').value,
        email: document.getElementById('employee_email').value,
        password: document.getElementById('employee_password').value,
        role: document.getElementById('employee_role').value,
        department: document.getElementById('employee_department').value,
        phone: document.getElementById('employee_phone').value,
        status: 'active'
    };
    
    const response = await fetch('api/users.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    });
    
    const result = await response.json();
    
    if (result.success) {
        closeModal();
        loadEmployees();
    }
}

async function saveTask(event) {
    event.preventDefault();
    
    const data = {
        id: document.getElementById('task_id').value,
        task_title: document.getElementById('task_title').value,
        client_name: document.getElementById('task_client').value,
        description: document.getElementById('task_description').value,
        user_id: document.getElementById('task_user_id').value,
        priority: document.getElementById('task_priority').value,
        status: document.getElementById('task_status').value,
        deadline: new Date().toISOString().slice(0, 16)
    };
    
    const response = await fetch('api/tasks.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    });
    
    const result = await response.json();
    
    if (result.success) {
        closeModal();
        loadTasks();
    }
}

async function deleteEmployee(id) {
    if (!confirm('Biztosan törölni szeretné ezt a dolgozót?')) return;
    
    const response = await fetch(`api/users.php?id=${id}`, { method: 'DELETE' });
    const result = await response.json();
    
    if (result.success) {
        loadEmployees();
    }
}

async function deleteTask(id) {
    if (!confirm('Biztosan törölni szeretné ezt a feladatot?')) return;
    
    const response = await fetch(`api/tasks.php?id=${id}`, { method: 'DELETE' });
    const result = await response.json();
    
    if (result.success) {
        loadTasks();
    }
}

async function updateMyStatus() {
    const status = prompt('Válasszon státuszt:\n1. Munkában\n2. Szünet\n3. Szabadság\n4. Táppénz');
    const statusMap = {
        '1': 'working',
        '2': 'break',
        '3': 'vacation',
        '4': 'sick_leave'
    };
    
    if (status && statusMap[status]) {
        const response = await fetch('api/workstatus.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                user_id: currentUser.id,
                status: statusMap[status]
            })
        });
        
        if (response.ok) {
            alert('Státusz frissítve!');
            loadWorkStatus();
        }
    }
}

// Logout
async function logout() {
    const response = await fetch('api/auth.php?action=logout');
    window.location.href = 'index.html';
}

// Event listeners
function setupEventListeners() {
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('modal');
        if (event.target === modal) {
            closeModal();
        }
    }
}
