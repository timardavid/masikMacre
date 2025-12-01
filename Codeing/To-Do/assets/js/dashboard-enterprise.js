// Enterprise Dashboard - Premium Version + Extra Features
let currentUser = {};
let charts = {};
let notifications = [];
let isDarkMode = false;
let allEmployees = [];

document.addEventListener('DOMContentLoaded', async () => {
    await checkAuth();
    await loadDashboard();
    setupNavigation();
    setupEventListeners();
    setupNotifications();
    setupDarkMode();
    setupNotificationsPanel();
    setupQuickActions();
    initPeriodicUpdates();
});

async function checkAuth() {
    try {
        const response = await fetch('api/auth.php?action=check');
        const result = await response.json();
        
        if (!result.authenticated) {
            window.location.href = 'index.html';
            return;
        }
        
        currentUser = result.user;
        document.getElementById('headerUserName').textContent = currentUser.name;
        document.getElementById('headerUserRole').textContent = currentUser.role;
        
        if (currentUser.role === 'Admin' || currentUser.role === 'CEO') {
            document.getElementById('adminSection').style.display = 'block';
        }
    } catch (error) {
        console.error('Auth error:', error);
    }
}

async function loadDashboard() {
    try {
        const [users, financial] = await Promise.all([
            fetch('api/users.php').then(r => r.json()),
            fetch('api/financial.php').then(r => r.json())
        ]);
        
        loadPortfolioCards(users, financial);
        loadActiveChart(users);
        loadTimeline(users);
    } catch (error) {
        console.error('Error loading dashboard:', error);
    }
}

function loadPortfolioCards(users, financial) {
    const container = document.getElementById('portfolioCards');
    const workingCount = users.filter(u => u.current_status === 'working').length;
    const totalSalary = users.reduce((sum, u) => sum + parseFloat(u.salary || 0), 0);
    const avgSalary = totalSalary / users.length;
    
    container.innerHTML = `
        <div class="portfolio-card">
            <div class="card-icon-wrapper yellow">
                💰
            </div>
            <div class="card-label">Dolgozók száma</div>
            <div class="card-value">${users.length}</div>
            <div class="card-change positive">
                <i class="fas fa-arrow-up"></i> ${workingCount} munkában
            </div>
        </div>
        
        <div class="portfolio-card">
            <div class="card-icon-wrapper pink">
                💼
            </div>
            <div class="card-label">Átlagos Fizetés</div>
            <div class="card-value">${formatCurrency(avgSalary)}</div>
            <div class="card-change positive">
                Növekedés: 14.1%
            </div>
            <button class="card-btn">Teljes Kimutatás</button>
        </div>
        
        <div class="portfolio-card">
            <div class="card-icon-wrapper green">
                💵
            </div>
            <div class="card-label">Havi Bevétel</div>
            <div class="card-value">${formatCurrency(financial?.monthlyRevenue || 0)}</div>
            <div class="card-change ${financial?.monthlyProfit > 0 ? 'positive' : 'negative'}">
                <i class="fas fa-arrow-${financial?.monthlyProfit > 0 ? 'up' : 'down'}"></i> 
                ${financial?.monthlyProfit > 0 ? 'Növekedés' : 'Csökkenés'}: ${formatCurrency(Math.abs(financial?.monthlyProfit || 0))}
            </div>
        </div>
    `;
}

function loadActiveChart(users) {
    const workingCount = users.filter(u => u.current_status === 'working').length;
    const totalUsers = users.length;
    const percentage = Math.round((workingCount / totalUsers) * 100);
    
    document.getElementById('activePercentage').textContent = percentage + '%';
    document.getElementById('activeChange').textContent = `+${workingCount}`;
    document.getElementById('employeesCount').textContent = totalUsers;
    document.getElementById('workingCount').textContent = workingCount;
    document.getElementById('breakCount').textContent = users.filter(u => u.current_status === 'break').length;
    
    // Update progress bar
    const progress = document.getElementById('employeesProgress');
    if (progress) {
        progress.style.width = percentage + '%';
    }
    
    // Create mini chart
    const ctx = document.getElementById('activeChart');
    if (ctx && !charts.activeChart) {
        charts.activeChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Hétfő', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek'],
                datasets: [{
                    label: 'Aktív dolgozók',
                    data: [workingCount - 2, workingCount + 1, workingCount, workingCount + 2, workingCount],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, display: false }
                }
            }
        });
    }
}

async function loadTimeline(users) {
    try {
        const [tasks, workStatus] = await Promise.all([
            fetch('api/tasks.php').then(r => r.json()),
            fetch('api/workstatus.php').then(r => r.json())
        ]);
        
        document.getElementById('pendingTasks').textContent = tasks.filter(t => t.status === 'pending').length;
        document.getElementById('completedTasks').textContent = tasks.filter(t => t.status === 'completed').length;
        
        const timeline = document.getElementById('timelineList');
        const recentStatus = workStatus.slice(0, 8);
        
        timeline.innerHTML = recentStatus.map((item, idx) => {
            const colors = ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#ec4899'];
            const dotColor = colors[idx % colors.length];
            
            return `
                <div class="timeline-item">
                    <div class="timeline-dot" style="background: ${dotColor}"></div>
                    <div class="timeline-content">
                        <div class="timeline-text">${item.user_name}</div>
                        <div class="timeline-time">${new Date(item.created_at).toLocaleString('hu-HU')}</div>
                    </div>
                    ${idx === 2 ? '<span class="timeline-badge" style="background: #ef4444; color: white;">NEW</span>' : ''}
                </div>
            `;
        }).join('');
    } catch (error) {
        console.error('Error loading timeline:', error);
    }
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('hu-HU', { 
        style: 'currency', 
        currency: 'HUF',
        minimumFractionDigits: 0
    }).format(amount);
}

function setupNavigation() {
    const navItems = document.querySelectorAll('.nav-item');
    const sections = document.querySelectorAll('.view-section');
    
    navItems.forEach(item => {
        item.addEventListener('click', (e) => {
            e.preventDefault();
            const view = item.dataset.view;
            
            navItems.forEach(nav => nav.classList.remove('active'));
            item.classList.add('active');
            
            sections.forEach(sec => sec.classList.remove('active'));
            const target = document.getElementById(view);
            if (target) {
                target.classList.add('active');
                loadViewContent(view);
            }
        });
    });
}

async function loadViewContent(view) {
    switch(view) {
        case 'employees':
            await loadEmployeesView();
            break;
        case 'tasks':
            await loadTasksView();
            break;
        case 'statistics':
            await loadStatisticsView();
            break;
        case 'financial':
            await loadFinancialView();
            break;
        case 'register':
            loadRegisterView();
            break;
    }
}

async function loadEmployeesView() {
    const response = await fetch('api/users.php');
    const users = await response.json();
    allEmployees = users;
    
    const grid = document.getElementById('employeeGrid');
    grid.innerHTML = users.map(user => {
        const statusColors = {
            'working': '#10b981',
            'break': '#f59e0b',
            'vacation': '#3b82f6',
            'sick_leave': '#ef4444'
        };
        const statusColor = statusColors[user.current_status] || '#9ca3af';
        const salary = user.salary || 0;
        
        return `
            <div class="employee-card-modern">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                    <div style="width: 50px; height: 50px; background: ${statusColor}20; color: ${statusColor}; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 20px;">
                        ${user.name.charAt(0)}
                    </div>
                    <div>
                        <div style="font-weight: bold; font-size: 18px; margin-bottom: 5px;">${user.name}</div>
                        <div style="color: #6b7280; font-size: 14px;">
                            <span style="background: ${statusColor}20; color: ${statusColor}; padding: 4px 10px; border-radius: 8px; font-size: 12px;">
                                ${user.current_status === 'working' ? '🟢 Munkában' : 
                                  user.current_status === 'break' ? '🟡 Szünet' : 
                                  user.current_status === 'vacation' ? '🔵 Szabadság' : 
                                  user.current_status === 'sick_leave' ? '🔴 Táppénz' : '⚪ Nincs munkaidő'}
                            </span>
                        </div>
                    </div>
                </div>
                <div style="margin-bottom: 10px;">
                    <div style="color: #6b7280; font-size: 12px; margin-bottom: 5px;">Szerepkör</div>
                    <div style="font-weight: 600;">${user.role}</div>
                </div>
                <div style="margin-bottom: 10px;">
                    <div style="color: #6b7280; font-size: 12px; margin-bottom: 5px;">Részleg</div>
                    <div style="font-weight: 600;">${user.department || 'Nincs'}</div>
                </div>
                ${salary > 0 ? `
                <div style="background: #f0fdf4; padding: 12px; border-radius: 10px; margin-top: 15px;">
                    <div style="color: #059669; font-size: 12px; margin-bottom: 5px;">Havi fizetés</div>
                    <div style="font-weight: bold; color: #059669; font-size: 18px;">${formatCurrency(salary)}</div>
                </div>
                ` : ''}
            </div>
        `;
    }).join('');
}

async function loadTasksView() {
    const response = await fetch('api/tasks.php');
    const tasks = await response.json();
    const container = document.getElementById('taskList');
    
    container.innerHTML = `
        <div style="display: grid; gap: 20px;">
            ${tasks.map(task => {
                const priorityColors = {
                    'Critical': '#ef4444',
                    'Very Urgent': '#f97316',
                    'Urgent': '#f59e0b',
                    'Not Urgent': '#10b981'
                };
                return `
                    <div style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-left: 5px solid ${priorityColors[task.priority] || '#gray'}">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                            <h3 style="font-size: 18px;">${task.task_title}</h3>
                            <span style="background: ${priorityColors[task.priority]}20; color: ${priorityColors[task.priority]}; padding: 5px 12px; border-radius: 8px; font-size: 12px; font-weight: 600;">
                                ${task.priority}
                            </span>
                        </div>
                        <p style="color: #6b7280; margin-bottom: 10px;">👤 ${task.client_name || 'Nincs ügyfél'}</p>
                        <p style="color: #6b7280; font-size: 14px;">${task.description || ''}</p>
                    </div>
                `;
            }).join('')}
        </div>
    `;
}

async function loadStatisticsView() {
    try {
        const response = await fetch('api/users.php');
        const users = await response.json();
        const container = document.getElementById('statisticsContent');
        
        if (!container) return;
        
        container.innerHTML = `
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 25px;">
                <div style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                    <div style="font-size: 48px; margin-bottom: 15px;">📊</div>
                    <div style="font-size: 32px; font-weight: bold; margin-bottom: 10px;">${users.length}</div>
                    <div style="color: #6b7280; font-size: 14px;">Összes dolgozó</div>
                </div>
                <div style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                    <div style="font-size: 48px; margin-bottom: 15px;">💼</div>
                    <div style="font-size: 32px; font-weight: bold; margin-bottom: 10px;">${users.filter(u => u.current_status === 'working').length}</div>
                    <div style="color: #6b7280; font-size: 14px;">Aktívan dolgozik</div>
                </div>
                <div style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                    <div style="font-size: 48px; margin-bottom: 15px;">📈</div>
                    <div style="font-size: 32px; font-weight: bold; margin-bottom: 10px;">${formatCurrency(users.reduce((sum, u) => sum + (parseFloat(u.salary) || 0), 0))}</div>
                    <div style="color: #6b7280; font-size: 14px;">Össz fizetés</div>
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Error loading statistics:', error);
    }
}

async function loadFinancialView() {
    try {
        const response = await fetch('api/financial.php');
        const data = await response.json();
        const container = document.getElementById('financialContent');
        
        if (!container || !data.success) {
            container.innerHTML = '<p>Pénzügyi adatok nem elérhetők.</p>';
            return;
        }
        
        container.innerHTML = `
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
                <div style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); padding: 30px; border-radius: 20px; color: white;">
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Havi Bevétel</div>
                    <div style="font-size: 48px; font-weight: bold; margin-bottom: 10px;">${formatCurrency(data.monthlyRevenue)}</div>
                </div>
                <div style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); padding: 30px; border-radius: 20px; color: white;">
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Havi Profit</div>
                    <div style="font-size: 48px; font-weight: bold; margin-bottom: 10px;">${formatCurrency(data.monthlyProfit)}</div>
                </div>
                <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border-radius: 20px; color: white;">
                    <div style="font-size: 14px; opacity: 0.9; margin-bottom: 10px;">Szolgáltatások</div>
                    <div style="font-size: 48px; font-weight: bold; margin-bottom: 10px;">${data.totalProducts}</div>
                </div>
            </div>
            <div style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                <h3 style="margin-bottom: 20px; font-size: 20px; font-weight: bold;">Részleg szerinti elemzés</h3>
                <div style="display: grid; gap: 15px;">
                    ${data.departmentData.map(dept => `
                        <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f9fafb; border-radius: 10px;">
                            <span style="font-weight: 600;">${dept.department}</span>
                            <span style="font-size: 20px; font-weight: bold; color: #667eea;">${dept.count} fő</span>
                        </div>
                    `).join('')}
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Error loading financial view:', error);
        document.getElementById('financialContent').innerHTML = '<p>Hiba történt az adatok betöltésekor.</p>';
    }
}

function loadRegisterView() {
    const container = document.getElementById('registerForm');
    
    if (!container) return;
    
    container.innerHTML = `
        <div style="background: white; padding: 30px; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); max-width: 800px; margin: 0 auto;">
            <form onsubmit="registerUser(event)" style="display: grid; gap: 20px;">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Név *</label>
                        <input type="text" id="regName" required style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Email *</label>
                        <input type="email" id="regEmail" required style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 14px;">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Jelszó *</label>
                        <input type="password" id="regPass" required style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 14px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Telefonszám</label>
                        <input type="text" id="regPhone" style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 14px;">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Szerepkör *</label>
                        <select id="regRole" required style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 14px;">
                            <option value="">Válasszon...</option>
                            <option value="Admin">Admin</option>
                            <option value="IT">IT</option>
                            <option value="HR">HR</option>
                            <option value="Finance">Pénzügy</option>
                            <option value="CEO">Ügyvezető</option>
                            <option value="Financial Advisor">Pénzügyi Tanácsadó</option>
                            <option value="Accountant">Könyvelő</option>
                        </select>
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">Részleg</label>
                        <input type="text" id="regDept" style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 14px;">
                    </div>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">Havi fizetés (Ft)</label>
                    <input type="number" id="regSalary" min="0" step="1000" style="width: 100%; padding: 12px; border: 2px solid #e5e7eb; border-radius: 10px; font-size: 14px;">
                </div>
                <div style="display: flex; gap: 15px; justify-content: flex-end;">
                    <button type="button" onclick="document.getElementById('registerForm').innerHTML = ''" style="padding: 12px 24px; background: #e5e7eb; border: none; border-radius: 10px; font-weight: 600; cursor: pointer;">Mégse</button>
                    <button type="submit" style="padding: 12px 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 10px; font-weight: 600; cursor: pointer;">Regisztrálás</button>
                </div>
            </form>
        </div>
    `;
}

async function registerUser(event) {
    event.preventDefault();
    
    const data = {
        name: document.getElementById('regName').value,
        email: document.getElementById('regEmail').value,
        password: document.getElementById('regPass').value,
        phone: document.getElementById('regPhone').value,
        role: document.getElementById('regRole').value,
        department: document.getElementById('regDept').value,
        salary: document.getElementById('regSalary').value || 0,
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
            showToast('success', 'Dolgozó sikeresen regisztrálva!');
            document.getElementById('registerForm').innerHTML = '';
            if (currentView === 'employees') await loadEmployeesView();
        } else {
            showToast('error', 'Hiba történt: ' + (result.error || 'Ismeretlen hiba'));
        }
    } catch (error) {
        showToast('error', 'Kapcsolódási hiba');
    }
}

function setupEventListeners() {
    // Menu toggle
    const menuToggle = document.getElementById('menuToggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            document.getElementById('sidebar').classList.toggle('collapsed');
        });
    }
    
    // Global search
    const globalSearch = document.getElementById('globalSearch');
    if (globalSearch) {
        globalSearch.addEventListener('input', (e) => {
            console.log('Search:', e.target.value);
        });
    }
    
    // Keyboard shortcuts
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey || e.metaKey) {
            if (e.key === 'k') {
                e.preventDefault();
                globalSearch?.focus();
            }
            if (e.key === 'd') {
                e.preventDefault();
                toggleDarkMode();
            }
        }
    });
}

// Notification System
function setupNotifications() {
    // Add sample notifications
    notifications = [
        { type: 'warning', message: 'Kovács Anna nem dolgozta le a heti 40 órát!', time: '5 perce' },
        { type: 'success', message: 'Új feladat hozzáadva: ABC Kft projekt', time: '10 perce' },
        { type: 'info', message: 'Havi zárás sikeresen elkészült', time: '1 órája' }
    ];
}

function setupNotificationsPanel() {
    const bell = document.getElementById('notificationBtn');
    if (bell) {
        bell.addEventListener('click', () => {
            showNotificationsPanel();
        });
    }
}

function showNotificationsPanel() {
    const panel = `
        <div style="position: fixed; right: 20px; top: 80px; background: white; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); width: 400px; max-height: 500px; overflow-y: auto; z-index: 9999;">
            <div style="padding: 20px; border-bottom: 2px solid #e5e7eb;">
                <h3 style="font-size: 20px; font-weight: bold;">🔔 Értesítések (${notifications.length})</h3>
            </div>
            <div style="padding: 10px;">
                ${notifications.map(n => `
                    <div style="padding: 15px; margin: 10px 0; background: #f9fafb; border-radius: 10px; cursor: pointer; transition: all 0.3s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#f9fafb'">
                        <div style="display: flex; align-items: start; gap: 10px;">
                            <div style="font-size: 24px;">${n.type === 'warning' ? '⚠️' : n.type === 'success' ? '✅' : 'ℹ️'}</div>
                            <div style="flex: 1;">
                                <div style="font-size: 14px; margin-bottom: 5px;">${n.message}</div>
                                <div style="font-size: 12px; color: #6b7280;">${n.time}</div>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', panel);
    
    // Close on outside click
    setTimeout(() => {
        document.addEventListener('click', function closePanel(e) {
            if (!e.target.closest('.notification-panel') && !e.target.closest('.icon-btn')) {
                const panels = document.querySelectorAll('[style*="position: fixed"][style*="right: 20px"]');
                panels.forEach(p => p.remove());
                document.removeEventListener('click', closePanel);
            }
        });
    }, 100);
}

// Dark Mode
function setupDarkMode() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', toggleDarkMode);
    }
    
    // Check saved preference
    const savedMode = localStorage.getItem('darkMode');
    if (savedMode === 'true') {
        toggleDarkMode();
    }
}

function toggleDarkMode() {
    isDarkMode = !isDarkMode;
    document.body.classList.toggle('dark-mode', isDarkMode);
    localStorage.setItem('darkMode', isDarkMode);
    
    // Update icon
    const icon = document.querySelector('.fa-moon, .fa-sun');
    if (icon) {
        icon.classList.toggle('fa-moon');
        icon.classList.toggle('fa-sun');
    }
}

// Quick Actions
function setupQuickActions() {
    const fab = document.querySelector('.fab');
    if (fab) {
        fab.addEventListener('click', () => {
            showQuickActions();
        });
    }
}

function showQuickActions() {
    const actions = [
        { icon: '➕', label: 'Új Dolgozó', action: () => loadViewContent('register') },
        { icon: '📝', label: 'Új Feladat', action: () => loadViewContent('tasks') },
        { icon: '📊', label: 'Jelentés', action: () => exportReport() },
        { icon: '📥', label: 'Export', action: () => exportData() }
    ];
    
    const panel = `
        <div style="position: fixed; bottom: 100px; right: 30px; background: white; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); padding: 20px; z-index: 9999;">
            <h4 style="margin-bottom: 15px; font-size: 16px; font-weight: bold;">Gyors műveletek</h4>
            ${actions.map((a, idx) => `
                <div onclick="${a.action.toString()}" style="padding: 12px 15px; cursor: pointer; border-radius: 10px; margin-bottom: 8px; display: flex; align-items: center; gap: 12px; transition: all 0.3s;" onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'">
                    <span style="font-size: 20px;">${a.icon}</span>
                    <span style="font-size: 14px; font-weight: 600;">${a.label}</span>
                </div>
            `).join('')}
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', panel);
    
    // Remove after 5 seconds or click
    setTimeout(() => {
        const panels = document.querySelectorAll('[style*="position: fixed"][style*="bottom: 100px"]');
        panels.forEach(p => p.remove());
    }, 5000);
}

// Periodic Updates
function initPeriodicUpdates() {
    // Update dashboard every 30 seconds
    setInterval(async () => {
        if (document.querySelector('.view-section.active')?.id === 'overview') {
            await loadDashboard();
        }
    }, 30000);
    
    // Check for new notifications every minute
    setInterval(() => {
        checkNewNotifications();
    }, 60000);
}

async function checkNewNotifications() {
    try {
        const response = await fetch('api/workstatus.php');
        const statuses = await response.json();
        
        if (statuses.length > notifications.length) {
            addNotification('info', 'Új munkaidő bejegyzések érkeztek!');
        }
    } catch (error) {
        console.error('Error checking notifications:', error);
    }
}

function addNotification(type, message) {
    notifications.unshift({ type, message, time: 'Most' });
    if (notifications.length > 10) {
        notifications.pop();
    }
    showToast(type, message);
}

function showToast(type, message) {
    const colors = {
        success: '#10b981',
        warning: '#f59e0b',
        error: '#ef4444',
        info: '#3b82f6'
    };
    
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%);
        background: ${colors[type]}; color: white; padding: 15px 25px;
        border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        z-index: 99999; animation: slideUp 0.3s;
    `;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideDown 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Export Functions
function exportReport() {
    window.print();
}

function exportData() {
    const data = {
        users: allEmployees.length,
        financial: 'data-export',
        timestamp: new Date().toISOString()
    };
    
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `company-report-${Date.now()}.json`;
    a.click();
    
    showToast('success', 'Adatok exportálva!');
}

function openModal(type) {
    console.log('Open modal:', type);
}

function updateMyStatus() {
    console.log('Update status');
}

function logout() {
    window.location.href = 'index.html';
}

