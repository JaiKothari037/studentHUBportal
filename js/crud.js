const API_URL = "api.php";

const crudForm = document.getElementById("student-crud-form");
const studentIdInput = document.getElementById("crud-student-id");
const nameInput = document.getElementById("crud-name");
const emailInput = document.getElementById("crud-email");
const courseInput = document.getElementById("crud-course");
const statusInput = document.getElementById("crud-status");
const statusContainer = document.getElementById("status-select-container");
const formTitle = document.getElementById("form-title");
const submitBtn = document.getElementById("crud-submit-btn");
const cancelBtn = document.getElementById("crud-cancel-btn");
const tableBody = document.getElementById("crud-table-body");
const statusMsg = document.getElementById("status-msg");

function showStatus(message, type = "info") {
    statusMsg.className = `alert alert-${type === 'error' ? 'danger' : type} d-block`;
    statusMsg.textContent = message;
    if (type !== "info") {
        setTimeout(() => { statusMsg.className = "alert d-none"; }, 4000);
    }
}

// 1. READ (GET) ALL STUDENTS
async function fetchStudents() {
    showStatus("Loading student records...", "info");
    try {
        const response = await fetch(API_URL);
        const result = await response.json();

        if (response.ok && result.status === "success") {
            renderTable(result.data);
            statusMsg.className = "alert d-none";
        } else {
            showStatus(result.message || "Failed to load students.", "error");
        }
    } catch (err) {
        showStatus("Network Error: Unable to reach API endpoint.", "error");
    }
}

function renderTable(students) {
    tableBody.innerHTML = "";
    if (!students || students.length === 0) {
        tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">No student records found.</td></tr>`;
        return;
    }

    students.forEach(student => {
        const tr = document.createElement("tr");
        const badgeClass = student.status === 'active' ? 'bg-success' : 'bg-danger';
        
        tr.innerHTML = `
            <td>${student.student_id}</td>
            <td>${escapeHtml(student.name)}</td>
            <td>${escapeHtml(student.email)}</td>
            <td>${escapeHtml(student.course)}</td>
            <td><span class="badge ${badgeClass}">${student.status.toUpperCase()}</span></td>
            <td>
                <button class="btn btn-sm btn-primary me-1" onclick="editStudent(${student.student_id})">Edit</button>
                <button class="btn btn-sm btn-danger" onclick="deleteStudent(${student.student_id})">Delete</button>
            </td>
        `;
        tableBody.appendChild(tr);
    });
}

// 2. CREATE (POST) AND UPDATE (PUT)
crudForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const studentId = studentIdInput.value;
    const isEdit = studentId !== "";

    const payload = {
        name: nameInput.value.trim(),
        email: emailInput.value.trim(),
        course: courseInput.value
    };

    if (isEdit) {
        payload.student_id = parseInt(studentId);
        payload.status = statusInput.value;
    }

    const method = isEdit ? "PUT" : "POST";
    showStatus(isEdit ? "Updating student..." : "Adding student...", "info");

    try {
        const response = await fetch(API_URL, {
            method: method,
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (response.ok && result.status === "success") {
            showStatus(result.message, "success");
            resetForm();
            fetchStudents();
        } else {
            showStatus(result.message || "Operation failed.", "error");
        }
    } catch (err) {
        showStatus("Network error occurred during submission.", "error");
    }
});

// 3. EDIT PREFILL
async function editStudent(id) {
    showStatus("Fetching details...", "info");
    try {
        const response = await fetch(`${API_URL}?id=${id}`);
        const result = await response.json();

        if (response.ok && result.status === "success") {
            const data = result.data;
            studentIdInput.value = data.student_id;
            nameInput.value = data.name;
            emailInput.value = data.email;
            courseInput.value = data.course;
            statusInput.value = data.status;

            formTitle.textContent = "Edit Student Details";
            submitBtn.textContent = "Update Student";
            submitBtn.className = "btn btn-primary";
            statusContainer.classList.remove("d-none");
            cancelBtn.classList.remove("d-none");
            statusMsg.className = "alert d-none";
        } else {
            showStatus(result.message, "error");
        }
    } catch (err) {
        showStatus("Failed to retrieve student data.", "error");
    }
}

// 4. DELETE (DELETE)
async function deleteStudent(id) {
    if (!confirm("Delete this student record asynchronously?")) return;

    showStatus("Deleting record...", "info");
    try {
        const response = await fetch(`${API_URL}?id=${id}`, { method: "DELETE" });
        const result = await response.json();

        if (response.ok && result.status === "success") {
            showStatus(result.message, "success");
            fetchStudents();
        } else {
            showStatus(result.message || "Failed to delete student.", "error");
        }
    } catch (err) {
        showStatus("Network error occurred while deleting.", "error");
    }
}

function resetForm() {
    studentIdInput.value = "";
    crudForm.reset();
    formTitle.textContent = "Add New Student";
    submitBtn.textContent = "Add Student";
    submitBtn.className = "btn btn-success";
    statusContainer.classList.add("d-none");
    cancelBtn.classList.add("d-none");
}

cancelBtn.addEventListener("click", resetForm);

function escapeHtml(str) {
    return str.replace(/&/g, "&").replace(/</g, "<").replace(/>/g, ">");
}

fetchStudents();