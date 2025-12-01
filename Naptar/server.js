const express = require('express');
const sqlite3 = require('sqlite3').verbose();
const path = require('path');
const cors = require('cors');

const app = express();
const PORT = 3000;

// Middleware
app.use(cors());
app.use(express.json());

// Adatbázis inicializálása
const dbPath = path.join(__dirname, 'data', 'naptar.db');
const db = new sqlite3.Database(dbPath, (err) => {
    if (err) {
        console.error('Adatbázis hiba:', err.message);
    } else {
        console.log('Adatbázis kapcsolat létrejött.');
        initDatabase();
    }
});

// Adatbázis inicializálás
function initDatabase() {
    db.run(`CREATE TABLE IF NOT EXISTS tasks (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        task_date TEXT NOT NULL,
        task_text TEXT NOT NULL,
        completed INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )`, (err) => {
        if (err) {
            console.error('Tábla létrehozási hiba:', err.message);
        }
    });
}

// Teendők lekérése dátum szerint
app.get('/api/tasks', (req, res) => {
    const { date, month } = req.query;

    if (date) {
        // Egy adott nap teendői
        db.all('SELECT id, task_text, completed, created_at FROM tasks WHERE task_date = ? ORDER BY created_at ASC',
            [date], (err, rows) => {
                if (err) {
                    return res.status(500).json({ error: err.message });
                }
                res.json({
                    tasks: rows.map(row => ({
                        id: row.id,
                        date: date,
                        text: row.task_text,
                        completed: Boolean(row.completed),
                        createdAt: row.created_at
                    }))
                });
            });
    } else if (month) {
        // Hónap teendői dátum szerint csoportosítva
        db.all(`SELECT task_date, COUNT(*) as count FROM tasks 
                WHERE strftime('%Y-%m', task_date) = ? GROUP BY task_date`,
            [month], (err, rows) => {
                if (err) {
                    return res.status(500).json({ error: err.message });
                }
                const counts = {};
                rows.forEach(row => {
                    counts[row.task_date] = row.count;
                });
                res.json({ counts });
            });
    } else {
        // Összes teendő
        db.all('SELECT id, task_date, task_text, completed, created_at FROM tasks ORDER BY task_date ASC, created_at ASC',
            [], (err, rows) => {
                if (err) {
                    return res.status(500).json({ error: err.message });
                }
                res.json({
                    tasks: rows.map(row => ({
                        id: row.id,
                        date: row.task_date,
                        text: row.task_text,
                        completed: Boolean(row.completed),
                        createdAt: row.created_at
                    }))
                });
            });
    }
});

// Új teendő létrehozása
app.post('/api/tasks', (req, res) => {
    const { date, text } = req.body;

    if (!date || !text || !text.trim()) {
        return res.status(400).json({ error: 'A dátum és a teendő szövege kötelező!' });
    }

    db.run('INSERT INTO tasks (task_date, task_text) VALUES (?, ?)',
        [date, text.trim()], function(err) {
            if (err) {
                return res.status(500).json({ error: err.message });
            }
            res.status(201).json({
                success: true,
                task: {
                    id: this.lastID,
                    date: date,
                    text: text.trim(),
                    completed: false
                }
            });
        });
});

// Teendő frissítése
app.put('/api/tasks/:id', (req, res) => {
    const { id } = req.params;
    const { text, completed } = req.body;

    if (text !== undefined) {
        // Szöveg frissítés
        db.run('UPDATE tasks SET task_text = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?',
            [text.trim(), id], function(err) {
                if (err) {
                    return res.status(500).json({ error: err.message });
                }
                res.json({ success: true });
            });
    } else if (completed !== undefined) {
        // Státusz frissítés
        db.run('UPDATE tasks SET completed = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?',
            [completed ? 1 : 0, id], function(err) {
                if (err) {
                    return res.status(500).json({ error: err.message });
                }
                res.json({ success: true });
            });
    } else {
        res.status(400).json({ error: 'Hiányzó adatok!' });
    }
});

// Teendő törlése
app.delete('/api/tasks/:id', (req, res) => {
    const { id } = req.params;

    db.run('DELETE FROM tasks WHERE id = ?', [id], function(err) {
        if (err) {
            return res.status(500).json({ error: err.message });
        }
        res.json({ success: true });
    });
});

// Szerver indítása
app.listen(PORT, () => {
    console.log(`Backend szerver fut a ${PORT} porton`);
});

// Graceful shutdown
process.on('SIGINT', () => {
    db.close((err) => {
        if (err) {
            console.error(err.message);
        }
        console.log('Adatbázis kapcsolat bezárva.');
        process.exit(0);
    });
});


