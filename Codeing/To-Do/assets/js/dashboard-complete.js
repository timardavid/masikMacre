// Professional Dashboard - Complete Version
let currentUser = {};
let currentView = 'overview';
let departmentChart = null;
let salaryChart = null;
let revenueTrendChart = null;
let weeklyRevenueChart = null;
let allEmployees = [];
let filteredEmployees = [];

// Initialize dashboard
document.addEventListener('DOMContentLoaded', async () => {
    await checkAuth();
    await loadUserInfo();
    await initializeDashboard();
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
    
    // Show/hide menu items based on role
    if (currentUser.role === 'Admin' || currentUser.role === 'CEO') {
        document.getElementById('registerMenuItem').style.display = 'block';
        document.getElementById('financialMenuItem').style.display = 'block';
    }
}

// Load user info
async function loadUserInfo() {
    const response = await fetch('api/auth.php?action=check');
    const result = await response.json();
    if (result.authenticated) {
        currentUser = result.user;
    }
}

// Initialize dashboard
async function initializeDashboard() {
    await loadOverview();
    await loadEmployees();
}

// Load overview
async function loadOverview() {
    try {
        const [users, tasks, workStatus, financial] = await Promise.all([
            fetch('api/users.php').then(r => r.json()),
            fetch('api/tasks.php').then(r => r.json()),
            fetch('api/workstatus.php').then(r => r.json()),
            fetch('api/financial.php').then(r => r.json())
        ]);
        
        updateOverviewStats(users, tasks, financial);
        await checkWarnings(users, tasks);
        createCharts(users, financial);
    } catch (error) {
        console.error('Error loading overview:', error);
    }
}

function updateOverviewStats(users, tasks, financial) {
    const stats = document.getElementById('overviewStats');
    const activeUsers = users.filter(u => u.current_status === 'working').length;
    const criticalTasks = tasks.filter(t => t.priority === 'Critical').length;
    
    stats.innerHTML = `
        <div class="stat-card gradient-blue">
            <div class="stat-icon">👥</div>
            <div class="stat-value">${users.length}</div>
            <div class="stat-label">Összes dolgozó</div>
            <div class="stat-change positive">${activeUsers} munkában</div>
        </div>
        
        <div class="stat-card gradient-green">
            <div class="stat-icon">💼</div>
            <div class="stat-value">${activeUsers}</div>
            <div class="stat-label">Aktív dolgozók</div>
            <div class="stat-change positive">🔵 Jelenleg dolgoznak</div>
        </div>
        
        <div class="stat-card gradient-red">
            <div class="stat-icon">🚨</div>
            <div class="stat-value">${criticalTasks}</div>
            <div class="stat-label">Kritikus feladat</div>
            <div class="stat-change negative">Sürgős</div>
        </div>
        
        <div class="stat-card gradient-purple">
            <div class="stat-icon">💵</div>
            <div class="stat-value">${formatCurrency(financial?.monthlyRevenue || 0)}</div>
            <div class="stat-label">Havi bevétel</div>
            <div class="stat-change positive">Profit: ${formatCurrency(financial?.monthlyProfit || 0)}</div>
        </div>
    `;
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('hu-HU', { 
        style: 'currency', 
        currency: 'HUF',
        minimumFractionDigits: 0
    }).format(amount);
}

async function checkWarnings(users, tasks) {
    const alerts = document.getElementById('warningAlerts');
    alerts.innerHTML = '';
    
    for (const user of users) {
        try {
            const response = await fetch(`api/statistics.php?user_id=${user.id}&month=${new Date().toISOString().slice(0,7)}`);
            const stats = await response.json();
            
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
        } catch (error) {
            console.error('Error checking stats for user:', error);
        }
    }
}

// Create charts
function createCharts(users, financial) {
    // Department Chart
    const deptData = users.reduce((acc, user) => {
        acc[user.department] = (acc[user.department] || 0) + 1;
        return acc;
    }, {});
    
    const ctx1 = document.getElementById('departmentChart');
    if (ctx1 && !departmentChart) {
        departmentChart = new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: Object.keys(deptData),
                datasets: [{
                    data: Object.values(deptData),
                    backgroundColor: [
                        '#667eea', '#764ba2', '#f093fb', 
                        '#4facfe', '#00f2fe', '#43e97b'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }
    
    // Salary Chart
    const avgSalary = users.reduce((sum, u) => sum + (parseFloat(u.salary) || 0), 0) / users.length;
    const ctx2 = document.getElementById('salaryChart');
    if (ctx2 && !salaryChart) {
        salaryChart = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: ['Átlag fizetés'],
                datasets: [{
                    label: 'HUF',
                    data: [avgSalary],
                    backgroundColor: '#667eea'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: { 
                            callback: function(value) { 
                                return formatCurrency(value); 
                            }
                        }
                    }
                }
            }
        });
    }
}

// Load employees
async function loadEmployees() {
    const response = await fetch('api/users.php');
    allEmployees = await response.json();
    filteredEmployees = [...allEmployees];
    
    const grid = document.getElementById('employeeGrid');
    renderEmployees(filteredEmployees);
    
    updateEmployeeCount();
    updateDepartmentFilter();
}

function renderEmployees(employees) {
    const grid = document.getElementById('employeeGrid');
    grid.innerHTML = employees.map(user => {
        const statusMap = {
            'working': { text: 'Munkában', class: 'status-working', icon: '🟢' },
            'break': { text: 'Szünet', class: 'status-break', icon: '🟡' },
            'vacation': { text: 'Szabadság', class: 'status-vacation', icon: '🔵' },
            'sick_leave': { text: 'Táppénz', class: 'status-sick', icon: '🔴' },
            'no_work': { text: 'Nincs munkaidő', class: 'status-no-work', icon: '⚪' }
        };
        
        const status = statusMap[user.current_status] || statusMap['no_work'];
        const salary = user.salary || 0;
        
        return `
            <div class="employee-card">
                <div class="employee-header">
                    <div class="employee-avatar">${user.name.charAt(0)}</div>
                    <div>
                        <div class="employee-name">${user.name}</div>
                        <div class="employee-status ${status.class}">
                            ${status.icon} ${status.text}
                        </div>
                    </div>
                </div>
                <div class="employee-info">
                    <i class="fas fa-briefcase"></i> ${user.role}
                </div>
                <div class="employee-info">
                    <i class="fas fa-building"></i> ${user.department || 'Nincs részleg'}
                </div>
                <div class="employee-info">
                    <i class="fas fa-envelope"></i> ${user.email}
                </div>
                ${salary > 0 ? `
                <div class="employee-salary">
                    <i class="fas fa-money-bill-wave"></i> 
                    Havi fizetés: ${formatCurrency(salary)}
                </div>
                ` : ''}
                ${(currentUser.role === 'Admin' || currentUser.role === 'HR') ? `
                <div class="employee-actions">
                    <button class="btn-edit" onclick="editEmployee(${user.id})">
                        <i class="fas fa-edit"></i> Szerkesztés
                    </button>
                    <button class="btn-delete" onclick="deleteEmployee(${user.id})">
                        <i class="fas fa-trash"></i> Törlés
                    </button>
                </div>
                ` : ''}
            </div>
        `;
    }).join('');
}

function updateEmployeeCount() {
    const count = document.getElementById('employeeCount');
    if (count) {
        count.textContent = `${filteredEmployees.length} dolgozó`;
    }
}

function updateDepartmentFilter() {
    const select = document.getElementById('filterDepartment');
    const departments = [...new Set(allEmployees.map(e => e.department).filter(Boolean))];
    select.innerHTML = '<option value="">Minden részleg</option>' + 
        departments.map(dept => `<option value="${dept}">${dept}</option>`).join('');
}

function filterEmployees() {
    const search = document.getElementById('searchEmployees').value.toLowerCase();
    const dept = document.getElementById('filterDepartment').value;
    
    filteredEmployees = allEmployees.filter(emp => {
        const matchSearch = !search || emp.name.toLowerCase().includes(search) || 
                           emp.email.toLowerCase().includes(search);
        const matchDept = !dept || emp.department === dept;
        return matchSearch && matchDept;
    });
    
    renderEmployees(filteredEmployees);
    updateEmployeeCount();
}

// Navigation
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
    
    const target = document.getElementById(view);
    if (target) {
        target.classList.add('active');
        currentView = view;
        
        if (view === 'employees') loadEmployees();
        else if (view === 'tasks') loadTasks();
        else if (view === 'work-status') loadWorkStatus();
        else if (view === 'statistics') loadStatistics();
        else if (view === 'financial') loadFinancial();
    }
}

// Register new user
async function registerNewUser(event) {
    event.preventDefault();
    
    const data = {
        name: document.getElementById('reg_name').value,
        email: document.getElementById('reg_email').value,
        password: document.getElementById('reg_password').value,
        role: document.getElementById('reg_role').value,
        department: document.getElementById('reg_department').value,
        phone: document.getElementById('reg_phone').value,
        salary: document.getElementById('reg_salary').value || 0,
        status: 'active'
    };
    
    try {
        const response = await fetch('api/users.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('✅ Dolgozó sikeresen regisztrálva!');
            resetRegisterForm();
            loadEmployees();
        } else {
            alert('❌ Hiba: ' + (result.error || 'Ismeretlen hiba'));
        }
    } catch (error) {
        alert('❌ Kapcsolódási hiba');
    }
}

function resetRegisterForm() {
    document.getElementById('registerForm').reset();
}

// Financial data
async function loadFinancial() {
    try {
        const response = await fetch('api/financial.php');
        const data = await response.json();
        
        if (data.success) {
            updateFinancialStats(data);
            createFinancialCharts(data);
        }
    } catch (error) {
        console.error('Error loading financial data:', error);
    }
}

function updateFinancialStats(data) {
    const container = document.getElementById('financialStats');
    if (!container) return;
    
    const profitPercent = ((data.monthlyProfit / data.monthlyRevenue) * 100) || 0;
    
    container.innerHTML = `
        <div class="financial-card revenue">
            <div class="card-icon">💰</div>
            <div>
                <div class="card-label">Havi Bevétel</div>
                <div class="card-value">${formatCurrency(data.monthlyRevenue)}</div>
            </div>
        </div>
        
        <div class="financial-card profit">
            <div class="card-icon">💵</div>
            <div>
                <div class="card-label">Havi Profit</div>
                <div class="card-value ${data.monthlyProfit > 0 ? 'positive' : 'negative'}">
                    ${formatCurrency(data.monthlyProfit)}
                </div>
                <div class="card-percent">${profitPercent.toFixed(1)}%</div>
            </div>
        </div>
        
        <div class="financial-card salary">
            <div class="card-icon">💼</div>
            <div>
                <div class="card-label">Összes Fizetés</div>
                <div class="card-value">${formatCurrency(data.totalSalary)}</div>
            </div>
        </div>
        
        <div class="financial-card products">
            <div class="card-icon">📦</div>
            <div>
                <div class="card-label">Szolgáltatások</div>
                <div class="card-value">${data.totalProducts}</div>
            </div>
        </div>
    `;
}

function createFinancialCharts(data) {
    // Revenue Trend
    const ctx1 = document.getElementById('revenueTrendChart');
    if (ctx1 && !revenueTrendChart) {
        revenueTrendChart = new Chart(ctx1, {
            type: 'line',
            data: {
                labels: data.monthlyTrend.map(m => m.month),
                datasets: [{
                    label: 'Bevétel (Ft)',
                    data: data.monthlyTrend.map(m => m.amount),
                    borderColor: '#667eea',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        ticks: {
                            callback: function(value) { return formatCurrency(value); }
                        }
                    }
                }
            }
        });
    }
}

// Load tasks, work status, statistics (simplified)
async function loadTasks() {
    const response = await fetch('api/tasks.php');
    const tasks = await response.json();
    const list = document.getElementById('taskList');
    if (list) {
        list.innerHTML = tasks.map(task => createTaskCard(task)).join('');
    }
}

function createTaskCard(task) {
    const priorityColors = {
        'Critical': '#ef4444',
        'Very Urgent': '#f97316',
        'Urgent': '#f59e0b',
        'Not Urgent': '#10b981'
    };
    
    return `
        <div class="task-card" style="border-left-color: ${priorityColors[task.priority] || '#gray'}">
            <div class="task-header">
                <h3>${task.task_title}</h3>
                <span class="task-priority" style="background: ${priorityColors[task.priority]}20; color: ${priorityColors[task.priority]};">
                    ${task.priority}
                </span>
            </div>
            <p class="task-client">👤 ${task.client_name || 'Nincs ügyfél'}</p>
            <p class="task-description">${task.description || ''}</p>
        </div>
    `;
}

async function loadWorkStatus() {
    const response = await fetch('api/workstatus.php');
    const statuses = await response.json();
    const list = document.getElementById('workStatusList');
    if (list) {
        list.innerHTML = statuses.slice(0, 20).map(s => `
            <div class="status-item">
                <div><strong>${s.user_name}</strong><br>
                <small>${new Date(s.created_at).toLocaleString('hu-HU')}</small></div>
                <span class="status-badge ${s.status}">${s.status}</span>
            </div>
        `).join('');
    }
}

async function loadStatistics() {
    const response = await fetch('api/users.php');
    const users = await response.json();
    const grid = document.getElementById('statisticsGrid');
    if (grid) {
        grid.innerHTML = '<p>Statisztikák betöltése...</p>';
    }
}

// Event listeners
function setupEventListeners() {
    window.onclick = (event) => {
        const modal = document.getElementById('modal');
        if (event.target === modal) closeModal();
    }
}

// Utility functions
async function editEmployee(id) {
    alert('Szerkesztési funkció hamarosan elérhető!');
}

async function deleteEmployee(id) {
    if (!confirm('Biztosan törölni szeretné ezt a dolgozót?')) return;
    
    const response = await fetch(`api/users.php?id=${id}`, { method: 'DELETE' });
    const result = await response.json();
    
    if (result.success) {
        loadEmployees();
    }
}

function openModal(type) {
    alert('Modal: ' + type);
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
}

async function updateMyStatus() {
    const status = prompt('Válasszon státuszt:\n1. Munkában\n2. Szünet\n3. Szabadság\n4. Táppénz');
    alert('Státusz: ' + status);
}

async function logout() {
    await fetch('api/auth.php?action=logout');
    window.location.href = 'index.html';
}

function filterTasks(priority) {
    // Filter tasks by priority
    console.log('Filter tasks:', priority);
}

