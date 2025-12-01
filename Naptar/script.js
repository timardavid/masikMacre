// Naptár és teendőkezelés alkalmazás (Electron + Node/SQLite API)

class CalendarApp {
    constructor() {
        this.currentDate = new Date();
        this.selectedDate = null;
        this.currentEditingTask = null;
        this.currentTasksForSelectedDate = [];
        this.monthTaskCountsCache = {};
        this.apiBaseUrl = 'http://localhost:3000/api';
        
        this.init();
    }

    async init() {
        await this.renderCalendar();
        this.setupEventListeners();
        // Ma kiválasztása alapértelmezettként
        const today = new Date();
        setTimeout(() => {
            const todayEl = document.querySelector('.day.today');
            this.selectDate(today, todayEl || null);
            if (todayEl) {
                todayEl.classList.add('selected');
            }
        }, 100);
    }

    // API hívások
    async apiGet(path) {
        const res = await fetch(`${this.apiBaseUrl}${path}`);
        if (!res.ok) throw new Error('Hálózati hiba');
        return res.json();
    }
    async apiPost(path, body) {
        const res = await fetch(`${this.apiBaseUrl}${path}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        if (!res.ok) throw new Error('Hálózati hiba');
        return res.json();
    }
    async apiPut(path, body) {
        const res = await fetch(`${this.apiBaseUrl}${path}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        });
        if (!res.ok) throw new Error('Hálózati hiba');
        return res.json();
    }
    async apiDelete(path) {
        const res = await fetch(`${this.apiBaseUrl}${path}`, { method: 'DELETE' });
        if (!res.ok) throw new Error('Hálózati hiba');
        return res.json();
    }

    // Dátum kulcs generálása (YYYY-MM-DD formátum)
    getDateKey(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Hónap neve magyarul
    getMonthName(month) {
        const months = [
            'Január', 'Február', 'Március', 'Április', 'Május', 'Június',
            'Július', 'Augusztus', 'Szeptember', 'Október', 'November', 'December'
        ];
        return months[month];
    }

    // Naptár renderelése
    async renderCalendar() {
        const year = this.currentDate.getFullYear();
        const month = this.currentDate.getMonth();
        
        // Hónap neve megjelenítése
        document.getElementById('currentMonth').textContent = 
            `${this.getMonthName(month)} ${year}`;

        // Hónap első napja és utolsó napja
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const daysInMonth = lastDay.getDate();
        const startingDayOfWeek = firstDay.getDay() === 0 ? 6 : firstDay.getDay() - 1; // Hétfő = 0

        // Havi feladatok száma (pontokhoz)
        const monthKey = `${year}-${String(month + 1).padStart(2, '0')}`;
        let counts = this.monthTaskCountsCache[monthKey];
        if (!counts) {
            try {
                const data = await this.apiGet(`/tasks?month=${monthKey}`);
                counts = data.counts || {};
                this.monthTaskCountsCache[monthKey] = counts;
            } catch (e) {
                counts = {};
            }
        }

        const calendarDays = document.getElementById('calendarDays');
        calendarDays.innerHTML = '';

        // Előző hónap napjai
        const prevMonth = new Date(year, month, 0);
        const daysInPrevMonth = prevMonth.getDate();
        for (let i = startingDayOfWeek - 1; i >= 0; i--) {
            const day = daysInPrevMonth - i;
            const date = new Date(year, month - 1, day);
            this.createDayElement(date, true, calendarDays, false, counts);
        }

        // Aktuális hónap napjai
        const today = new Date();
        for (let day = 1; day <= daysInMonth; day++) {
            const date = new Date(year, month, day);
            const isToday = this.isSameDay(date, today);
            this.createDayElement(date, false, calendarDays, isToday, counts);
        }

        // Következő hónap napjai (hogy a rács teljes legyen)
        const totalCells = calendarDays.children.length;
        const remainingCells = 42 - totalCells; // 6 hét * 7 nap
        for (let day = 1; day <= remainingCells; day++) {
            const date = new Date(year, month + 1, day);
            this.createDayElement(date, true, calendarDays, false, counts);
        }
    }

    // Nap elem létrehozása
    createDayElement(date, isOtherMonth, container, isToday = false, monthCounts = {}) {
        const dayDiv = document.createElement('div');
        dayDiv.className = 'day';
        
        if (isOtherMonth) {
            dayDiv.classList.add('other-month');
        }
        
        if (isToday) {
            dayDiv.classList.add('today');
        }

        const dateKey = this.getDateKey(date);
        const totalTasksForDay = monthCounts[dateKey] || 0;
        
        dayDiv.innerHTML = `
            <div class="day-number">${date.getDate()}</div>
            <div class="task-indicator">
                ${Array.from({ length: Math.min(3, totalTasksForDay) }).map(() => '<div class="task-dot"></div>').join('')}
                ${totalTasksForDay > 3 ? `<div style="font-size: 0.7em; color: #3b82f6;">+${totalTasksForDay - 3}</div>` : ''}
            </div>
        `;

        dayDiv.addEventListener('click', () => this.selectDate(date, dayDiv));
        container.appendChild(dayDiv);
    }

    // Dátum kiválasztása
    async selectDate(date, clickedEl = null) {
        this.selectedDate = date;
        const dateKey = this.getDateKey(date);
        
        // Kiválasztott nap kiemelése
        document.querySelectorAll('.day').forEach(day => {
            day.classList.remove('selected');
        });
        if (clickedEl) {
            clickedEl.classList.add('selected');
        }

        // Teendők megjelenítése
        await this.renderTasks(dateKey);
        
        // Dátum cím frissítése
        const dayNames = ['Vasárnap', 'Hétfő', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek', 'Szombat'];
        const dayName = dayNames[date.getDay()];
        document.getElementById('selectedDateTitle').textContent = 
            `${dayName}, ${date.getDate()}. ${this.getMonthName(date.getMonth()).toLowerCase()} ${date.getFullYear()}.`;
    }

    // Teendők renderelése
    async renderTasks(dateKey) {
        const taskList = document.getElementById('taskList');
        let dayTasks = [];
        try {
            const data = await this.apiGet(`/tasks?date=${dateKey}`);
            dayTasks = data.tasks || [];
        } catch (e) {
            dayTasks = [];
        }
        this.currentTasksForSelectedDate = dayTasks;

        if (dayTasks.length === 0) {
            taskList.innerHTML = '<div class="empty-state">Nincs teendő erre a napra</div>';
            return;
        }

        taskList.innerHTML = dayTasks.map((task, index) => `
            <div class="task-item ${task.completed ? 'completed' : ''}" data-index="${index}" data-id="${task.id}">
                <span class="task-text">${this.escapeHtml(task.text || '')}</span>
            </div>
        `).join('');

        // Kattintás események
        taskList.querySelectorAll('.task-item').forEach(item => {
            item.addEventListener('click', () => {
                const index = parseInt(item.dataset.index);
                this.editTask(dateKey, index);
            });
        });
    }

    // HTML escape
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Teendő hozzáadása
    async addTask() {
        if (!this.selectedDate) {
            alert('Kérlek, válassz ki egy napot a naptárból!');
            return;
        }

        const taskInput = document.getElementById('taskInput');
        const taskText = taskInput.value.trim();

        if (!taskText) {
            return;
        }

        const dateKey = this.getDateKey(this.selectedDate);
        try {
            await this.apiPost('/tasks', { date: dateKey, text: taskText });
            taskInput.value = '';
            await this.renderTasks(dateKey);
            await this.renderCalendar();
        } catch (e) {
            alert('Hiba a teendő hozzáadásakor.');
        }
    }

    // Teendő szerkesztése
    editTask(dateKey, index) {
        const task = this.currentTasksForSelectedDate[index];
        this.currentEditingTask = { dateKey, index };

        const modal = document.getElementById('taskModal');
        const editInput = document.getElementById('editTaskInput');
        editInput.value = task.text || '';
        modal.style.display = 'block';
    }

    // Teendő mentése
    async saveTask() {
        if (!this.currentEditingTask) return;

        const { dateKey, index } = this.currentEditingTask;
        const editInput = document.getElementById('editTaskInput');
        const newText = editInput.value.trim();

        if (newText) {
            try {
                const taskId = this.currentTasksForSelectedDate[index].id;
                await this.apiPut(`/tasks/${taskId}`, { text: newText });
                await this.renderTasks(dateKey);
                await this.renderCalendar();
            } catch (e) {
                alert('Hiba a teendő mentésekor.');
            }
        }

        this.closeModal();
    }

    // Teendő törlése
    async deleteTask() {
        if (!this.currentEditingTask) return;

        const { dateKey, index } = this.currentEditingTask;

        if (confirm('Biztosan törölni szeretnéd ezt a teendőt?')) {
            try {
                const taskId = this.currentTasksForSelectedDate[index].id;
                await this.apiDelete(`/tasks/${taskId}`);
                await this.renderTasks(dateKey);
                await this.renderCalendar();
            } catch (e) {
                alert('Hiba a teendő törlésekor.');
            }
        }

        this.closeModal();
    }

    // Modal bezárása
    closeModal() {
        const modal = document.getElementById('taskModal');
        modal.style.display = 'none';
        this.currentEditingTask = null;
    }

    // Ugyanaz a nap?
    isSameDay(date1, date2) {
        return date1.getFullYear() === date2.getFullYear() &&
               date1.getMonth() === date2.getMonth() &&
               date1.getDate() === date2.getDate();
    }

    // Előző hónap
    async previousMonth() {
        this.currentDate.setMonth(this.currentDate.getMonth() - 1);
        await this.renderCalendar();
    }

    // Következő hónap
    async nextMonth() {
        this.currentDate.setMonth(this.currentDate.getMonth() + 1);
        await this.renderCalendar();
    }

    // Eseménykezelők beállítása
    setupEventListeners() {
        // Hónap navigáció
        document.getElementById('prevMonth').addEventListener('click', () => this.previousMonth());
        document.getElementById('nextMonth').addEventListener('click', () => this.nextMonth());

        // Teendő hozzáadása
        document.getElementById('addTaskBtn').addEventListener('click', () => this.addTask());
        document.getElementById('taskInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                this.addTask();
            }
        });

        // Modal kezelés
        document.querySelector('.close').addEventListener('click', () => this.closeModal());
        document.getElementById('saveTaskBtn').addEventListener('click', () => this.saveTask());
        document.getElementById('deleteTaskBtn').addEventListener('click', () => this.deleteTask());

        // Modal bezárása kattintásra kívülre
        window.addEventListener('click', (e) => {
            const modal = document.getElementById('taskModal');
            if (e.target === modal) {
                this.closeModal();
            }
        });
    }
}

// Alkalmazás indítása
document.addEventListener('DOMContentLoaded', () => {
    new CalendarApp();
});

