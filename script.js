const form = document.getElementById('personForm');
const nameInput = document.getElementById('name');
const ageInput = document.getElementById('age');
const tableBody = document.getElementById('peopleTableBody');
const message = document.getElementById('message');

function showMessage(text, type = '') {
    message.textContent = text;
    message.className = `message ${type}`;
}

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function renderPeople(people) {
    tableBody.innerHTML = '';

    if (people.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="empty">No records yet.</td>
            </tr>
        `;
        return;
    }

    people.forEach(person => {
        const row = document.createElement('tr');

        row.innerHTML = `
            <td>${person.id}</td>
            <td>${escapeHtml(person.name)}</td>
            <td>${person.age}</td>
            <td id="status-${person.id}">
                <span class="status ${person.status === 1 ? 'active' : 'inactive'}">
                    ${person.status}
                </span>
            </td>
            <td>
                <button
                    class="toggle-btn"
                    type="button"
                    data-id="${person.id}"
                >
                    Toggle
                </button>
            </td>
        `;

        tableBody.appendChild(row);
    });
}

async function loadPeople() {
    try {
        const response = await fetch('api.php');
        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message);
        }

        renderPeople(result.data);
    } catch (error) {
        showMessage(error.message || 'Could not load records.', 'error');
    }
}

form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const name = nameInput.value.trim();
    const age = Number(ageInput.value);

    if (!name || !Number.isInteger(age) || age < 1 || age > 120) {
        showMessage('Please enter a valid name and age.', 'error');
        return;
    }

    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'add',
                name,
                age
            })
        });

        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message);
        }

        form.reset();
        showMessage('Person added successfully.', 'success');
        await loadPeople();
        nameInput.focus();

    } catch (error) {
        showMessage(error.message || 'Could not add the person.', 'error');
    }
});

tableBody.addEventListener('click', async (event) => {
    const button = event.target.closest('.toggle-btn');

    if (!button) {
        return;
    }

    const id = Number(button.dataset.id);

    button.disabled = true;

    try {
        const response = await fetch('api.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'toggle',
                id
            })
        });

        const result = await response.json();

        if (!result.success) {
            throw new Error(result.message);
        }

        // Update only the changed status on the page.
        const statusCell = document.getElementById(`status-${id}`);

        if (statusCell) {
            const status = result.data.status;
            statusCell.innerHTML = `
                <span class="status ${status === 1 ? 'active' : 'inactive'}">
                    ${status}
                </span>
            `;
        }

        showMessage('Status updated successfully.', 'success');

    } catch (error) {
        showMessage(error.message || 'Could not update status.', 'error');
    } finally {
        button.disabled = false;
    }
});

loadPeople();
