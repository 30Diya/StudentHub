// ======================================================
// STUDENTHUB PROFILE PAGE
// Fetch API + Search + Filter + Sorting
// ======================================================


let allStudents = [];

let filteredStudents = [];


// Get HTML elements

const profileImage =
    document.getElementById("profileImage");

const studentName =
    document.getElementById("studentName");

const studentDepartment =
    document.getElementById("studentDepartment");

const studentId =
    document.getElementById("studentId");

const attendance =
    document.getElementById("attendance");

const progress =
    document.getElementById("progress");

const events =
    document.getElementById("events");

const email =
    document.getElementById("email");

const phone =
    document.getElementById("phone");

const department =
    document.getElementById("department");

const academicYear =
    document.getElementById("academicYear");

const subjects =
    document.getElementById("subjects");

const studentList =
    document.getElementById("studentList");

const search =
    document.getElementById("search");

const courseFilter =
    document.getElementById("courseFilter");

const yearFilter =
    document.getElementById("yearFilter");

const sort =
    document.getElementById("sort");


// ======================================================
// FETCH PROFILE JSON
// ======================================================

fetch("../JSON/profile.json")

    .then(function(response) {

        if (!response.ok) {

            throw new Error(
                "Unable to load profile.json"
            );

        }

        return response.json();

    })

    .then(function(data) {

        console.log(
            "Profile JSON loaded successfully."
        );

        allStudents = data.students;

        filteredStudents = [...allStudents];


        // Show first student

        displayProfile(allStudents[0]);


        // Create filter options

        createCourseOptions();

        createYearOptions();


        // Display student list

        displayStudents();

    })

    .catch(function(error) {

        console.error(
            "PROFILE ERROR:",
            error
        );

        studentName.textContent =
            "Unable to load profile";

        studentDepartment.textContent =
            "Please check profile.json";

    });


// ======================================================
// DISPLAY MAIN PROFILE
// ======================================================

function displayProfile(student) {

    profileImage.src =
        student.profileImage;

    studentName.textContent =
        student.name;

    studentDepartment.textContent =
        student.department;

    studentId.textContent =
        "Student ID: " +
        student.studentId;


    attendance.textContent =
        student.attendance;

    progress.textContent =
        student.progress;

    events.textContent =
        student.events;


    email.textContent =
        student.email;

    phone.textContent =
        student.phone;

    department.textContent =
        student.department;

    academicYear.textContent =
        student.academicYear;


    displaySubjects(
        student.subjects
    );

}


// ======================================================
// DISPLAY SUBJECT PROGRESS
// ======================================================

function displaySubjects(subjectList) {

    subjects.innerHTML = "";


    subjectList.forEach(function(subject) {

        const label =
            document.createElement("label");

        label.innerHTML =
            subject.name +
            " <b>" +
            subject.progress +
            "%</b>";


        const bar =
            document.createElement("div");

        bar.className = "bar";


        const span =
            document.createElement("span");

        span.style.width =
            subject.progress + "%";


        bar.appendChild(span);


        subjects.appendChild(label);

        subjects.appendChild(bar);

    });

}


// ======================================================
// COURSE OPTIONS
// ======================================================

function createCourseOptions() {

    const courses = [
        ...new Set(

            allStudents.map(function(student) {

                return student.course;

            })

        )
    ];


    courses.sort();


    courses.forEach(function(course) {

        const option =
            document.createElement("option");

        option.value = course;

        option.textContent = course;

        courseFilter.appendChild(option);

    });

}


// ======================================================
// YEAR OPTIONS
// ======================================================

function createYearOptions() {

    const years = [
        ...new Set(

            allStudents.map(function(student) {

                return student.year;

            })

        )
    ];


    years.sort(function(a, b) {

        return a - b;

    });


    years.forEach(function(year) {

        const option =
            document.createElement("option");

        option.value = year;

        option.textContent =
            year;

        yearFilter.appendChild(option);

    });

}


// ======================================================
// DISPLAY STUDENTS
// ======================================================

function displayStudents() {

    studentList.innerHTML = "";


    if (filteredStudents.length === 0) {

        studentList.innerHTML = `

            <div class="no-students">

                <h3>
                    No Students Found
                </h3>

                <p>
                    Try another search or filter.
                </p>

            </div>

        `;

        return;

    }


    filteredStudents.forEach(
        function(student) {

            const card =
                document.createElement("div");

            card.className =
                "student-result";


            card.innerHTML = `

                <img
                    src="${student.profileImage}"
                    alt="Student"
                >

                <div>

                    <h3>
                        ${student.name}
                    </h3>

                    <p>
                        ${student.department}
                    </p>

                    <span>
                        ${student.studentId}
                    </span>

                </div>

            `;


            card.addEventListener(
                "click",
                function() {

                    displayProfile(student);

                    window.scrollTo({

                        top: 0,

                        behavior: "smooth"

                    });

                }
            );


            studentList.appendChild(card);

        }
    );

}


// ======================================================
// SEARCH
// ======================================================

search.addEventListener(
    "input",
    function() {

        applyFilters();

    }
);


// ======================================================
// COURSE FILTER
// ======================================================

courseFilter.addEventListener(
    "change",
    function() {

        applyFilters();

    }
);


// ======================================================
// YEAR FILTER
// ======================================================

yearFilter.addEventListener(
    "change",
    function() {

        applyFilters();

    }
);


// ======================================================
// SORT
// ======================================================

sort.addEventListener(
    "change",
    function() {

        applyFilters();

    }
);


// ======================================================
// APPLY SEARCH + FILTER + SORT
// ======================================================

function applyFilters() {

    const searchValue =
        search.value
            .toLowerCase()
            .trim();


    const selectedCourse =
        courseFilter.value;


    const selectedYear =
        yearFilter.value;


    filteredStudents =
        allStudents.filter(
            function(student) {

                const name =
                    student.name
                        .toLowerCase();

                const department =
                    student.department
                        .toLowerCase();

                const course =
                    student.course
                        .toLowerCase();


                const matchesSearch =

                    name.includes(
                        searchValue
                    )

                    ||

                    department.includes(
                        searchValue
                    )

                    ||

                    course.includes(
                        searchValue
                    );


                const matchesCourse =

                    selectedCourse === "all"

                    ||

                    student.course ===
                    selectedCourse;


                const matchesYear =

                    selectedYear === "all"

                    ||

                    student.year ==
                    selectedYear;


                return (

                    matchesSearch &&

                    matchesCourse &&

                    matchesYear

                );

            }
        );


    // Sorting

    const sortValue =
        sort.value;


    if (sortValue === "az") {

        filteredStudents.sort(
            function(a, b) {

                return a.name.localeCompare(
                    b.name
                );

            }
        );

    }


    if (sortValue === "za") {

        filteredStudents.sort(
            function(a, b) {

                return b.name.localeCompare(
                    a.name
                );

            }
        );

    }


    if (sortValue === "year") {

        filteredStudents.sort(
            function(a, b) {

                return a.year - b.year;

            }
        );

    }


    if (sortValue === "course") {

        filteredStudents.sort(
            function(a, b) {

                return a.course.localeCompare(
                    b.course
                );

            }
        );

    }


    displayStudents();

}


// ======================================================
// EDIT PROFILE
// ======================================================

function editProfile() {

    alert(
        "Edit Profile feature will be available soon."
    );

}