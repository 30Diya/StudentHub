// STUDENTHUB PROFILE PAGE

const profileImage = document.getElementById("profileImage");
const studentName = document.getElementById("studentName");
const studentDepartment = document.getElementById("studentDepartment");
const studentId = document.getElementById("studentId");
const attendance = document.getElementById("attendance");
const progress = document.getElementById("progress");
const events = document.getElementById("events");
const email = document.getElementById("email");
const phone = document.getElementById("phone");
const department = document.getElementById("department");
const academicYear = document.getElementById("academicYear");
const subjects = document.getElementById("subjects");

fetch("../JSON/profile.json")
    .then(function (response) {
        if (!response.ok) {
            throw new Error("Unable to load profile.json");
        }
        return response.json();
    })
    .then(function (data) {
        displayProfile(data.students[0]);
    })
    .catch(function (error) {
        console.error("PROFILE ERROR:", error);
        studentName.textContent = "Unable to load profile";
        studentDepartment.textContent = "Please check profile.json";
    });

function displayProfile(student) {
    profileImage.src = student.profileImage;
    studentName.textContent = student.name;
    studentDepartment.textContent = student.department;
    studentId.textContent = "Student ID: " + student.studentId;

    attendance.textContent = student.attendance;
    progress.textContent = student.progress;
    events.textContent = student.events;

    email.textContent = student.email;
    phone.textContent = student.phone;
    department.textContent = student.department;
    academicYear.textContent = student.academicYear;

    displaySubjects(student.subjects);
}

function displaySubjects(subjectList) {
    subjects.innerHTML = "";

    subjectList.forEach(function (subject) {
        const label = document.createElement("label");
        label.innerHTML = subject.name + " <b>" + subject.progress + "%</b>";

        const bar = document.createElement("div");
        bar.className = "bar";

        const span = document.createElement("span");
        span.style.width = subject.progress + "%";

        bar.appendChild(span);
        subjects.appendChild(label);
        subjects.appendChild(bar);
    });
}

function editProfile() {
    alert("Edit Profile feature will be available soon.");
}