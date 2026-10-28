
console.log("app.js is connected!");

const API_URL = 'api.php';

let allStudents = [];
let currentPage = 1;
const perPage = 5;

const tableBody = document.getElementById('studentTableBody');
const statusMessage = document.getElementById('statusMessage');
const pagination = document.getElementById('pagination');
const totalRecords = document.getElementById('totalRecords');
const filterForm = document.getElementById('filterForm');

// Load students from the PHP API
async function loadStudents() {
    try {
        statusMessage.textContent = 'Loading students...';

        const response = await fetch(API_URL);
        const result = await response.json();

        console.log('API response:', result);

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Unable to load students.');
        }

        allStudents = Array.isArray(result.data) ? result.data : [];

        currentPage = 1;
        applyFilters();

    } catch (error) {
        console.error('Error loading students:', error);

        tableBody.innerHTML =
            '<tr><td colspan="7">Unable to load students. Please try again.</td></tr>';

        statusMessage.textContent = error.message;
        pagination.innerHTML = '';
    }
}

// Prevent student information from being interpreted as HTML
function escapeHTML(value) {
    return String(value ?? '').replace(/[&<>"']/g, character => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;'
    })[character]);
}

// Search, filter, and sort the students in the browser
function applyFilters() {
    const formData = new FormData(filterForm);

    const search = String(formData.get('search') || '')
        .trim()
        .toLowerCase();

    const course = String(formData.get('course') || '');
    const year = String(formData.get('year') || '');
    const sort = String(formData.get('sort') || '');

    let filtered = allStudents.filter(student => {
        const matchesSearch =
            String(student.name || '').toLowerCase().includes(search) ||
            String(student.email || '').toLowerCase().includes(search) ||
            String(student.course || '').toLowerCase().includes(search);

        const matchesCourse =
            course === '' || String(student.course) === course;

        const matchesYear =
            year === '' || String(student.year) === year;

        return matchesSearch && matchesCourse && matchesYear;
    });

    if (sort === 'az') {
        filtered.sort((a, b) =>
            String(a.name).localeCompare(String(b.name))
        );
    } else if (sort === 'za') {
        filtered.sort((a, b) =>
            String(b.name).localeCompare(String(a.name))
        );
    } else if (sort === 'year') {
        filtered.sort((a, b) =>
            Number(a.year) - Number(b.year) ||
            String(a.name).localeCompare(String(b.name))
        );
    } else if (sort === 'course') {
        filtered.sort((a, b) =>
            String(a.course).localeCompare(String(b.course)) ||
            String(a.name).localeCompare(String(b.name))
        );
    } else {
        filtered.sort((a, b) => Number(b.id) - Number(a.id));
    }

    renderStudents(filtered);
}

// Display one page of student records
function renderStudents(students) {
    const pageCount = Math.max(1, Math.ceil(students.length / perPage));

    if (currentPage > pageCount) {
        currentPage = pageCount;
    }

    const start = (currentPage - 1) * perPage;
    const pageStudents = students.slice(start, start + perPage);

    totalRecords.textContent = students.length;
    statusMessage.textContent =
        students.length + ' student(s) found.';

    if (pageStudents.length === 0) {
        tableBody.innerHTML =
            '<tr><td colspan="7">No students found.</td></tr>';
    } else {
        tableBody.innerHTML = pageStudents.map(student => `
            <tr>
                <td>${escapeHTML(student.id)}</td>
                <td>${escapeHTML(student.name)}</td>
                <td>${escapeHTML(student.email)}</td>
                <td>${escapeHTML(student.phone)}</td>
                <td>${escapeHTML(student.course)}</td>
                <td>${escapeHTML(student.year)}</td>
                <td>
                    <a href="edit.php?id=${encodeURIComponent(student.id)}">
                        Edit
                    </a>
                    <button
                        type="button"
                        class="danger"
                        data-delete-id="${escapeHTML(student.id)}"
                    >
                        Delete
                    </button>
                </td>
            </tr>
        `).join('');
    }

    renderPagination(pageCount, students);
}

// Create pagination buttons
function renderPagination(pageCount, students) {
    pagination.innerHTML = '';

    for (let page = 1; page <= pageCount; page++) {
        const button = document.createElement('button');

        button.type = 'button';
        button.textContent = page;
        button.className = page === currentPage ? 'active' : '';
        button.setAttribute('aria-label', 'Go to page ' + page);

        button.addEventListener('click', () => {
            currentPage = page;
            renderStudents(students);
        });

        pagination.appendChild(button);
    }
}

// Apply search and filters without reloading the page
filterForm.addEventListener('submit', event => {
    event.preventDefault();
    currentPage = 1;
    applyFilters();
});

// Update results immediately when filters change
filterForm.querySelectorAll('select').forEach(select => {
    select.addEventListener('change', () => {
        currentPage = 1;
        applyFilters();
    });
});

// Allow the Delete button to call the API
tableBody.addEventListener('click', async event => {
    const button = event.target.closest('[data-delete-id]');

    if (!button) return;

    const id = Number(button.dataset.deleteId);

    if (!Number.isInteger(id) || id < 1) return;

    if (!confirm('Are you sure you want to delete this student?')) {
        return;
    }

    button.disabled = true;

    try {
        const response = await fetch(API_URL, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id })
        });

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Could not delete student.');
        }

        statusMessage.textContent = result.message;

        // Reload data from the API, without refreshing the webpage
        await loadStudents();

    } catch (error) {
        console.error('Delete error:', error);
        alert(error.message);
        button.disabled = false;
    }
});

// Start the application
loadStudents();
