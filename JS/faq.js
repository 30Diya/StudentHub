// ======================================================
// STUDENTHUB FAQ PAGE
// Fetch API + Search + Filter + Sorting + Pagination
// ======================================================


// ================= VARIABLES =================

let allFAQs = [];

let filteredFAQs = [];

let currentPage = 1;

const faqsPerPage = 5;


// ================= HTML ELEMENTS =================

const faqContainer =
    document.getElementById("faqContainer");

const searchInput =
    document.getElementById("search");

const categoryFilter =
    document.getElementById("categoryFilter");

const sortSelect =
    document.getElementById("sort");

const pagination =
    document.getElementById("pagination");

const resultMessage =
    document.getElementById("resultMessage");


// ======================================================
// FETCH FAQ DATA FROM JSON
// ======================================================

fetch("../JSON/faqs.json")

    .then(function(response) {

        if (!response.ok) {

            throw new Error(
                "Unable to load faqs.json. Status: " +
                response.status
            );

        }

        return response.json();

    })

    .then(function(data) {

        console.log("FAQ JSON loaded successfully.");

        console.log(data);


        // Store FAQ data

        allFAQs = data.faqs;


        // Copy data for filtering

        filteredFAQs = [...allFAQs];


        // Create category options

        createCategories();


        // Display FAQs

        displayFAQs();

    })

    .catch(function(error) {

        console.error("FAQ ERROR:", error);


        faqContainer.innerHTML = `

            <div class="no-results">

                <h3>
                    Unable to load FAQs
                </h3>

                <p>
                    ${error.message}
                </p>

            </div>

        `;


        resultMessage.textContent =
            "Unable to load FAQ data.";

    });


// ======================================================
// CREATE CATEGORY FILTER
// ======================================================

function createCategories() {

    // Get unique categories

    const categories = [
        ...new Set(
            allFAQs.map(function(faq) {
                return faq.category;
            })
        )
    ];


    // Sort categories alphabetically

    categories.sort();


    // Add categories to select

    categories.forEach(function(category) {

        const option =
            document.createElement("option");

        option.value = category;

        option.textContent = category;

        categoryFilter.appendChild(option);

    });

}


// ======================================================
// DISPLAY FAQS
// ======================================================

function displayFAQs() {

    // Clear previous FAQs

    faqContainer.innerHTML = "";


    // Calculate starting index

    const start =
        (currentPage - 1) * faqsPerPage;


    // Calculate ending index

    const end =
        start + faqsPerPage;


    // Get FAQs for current page

    const pageFAQs =
        filteredFAQs.slice(start, end);


    // If no FAQs found

    if (pageFAQs.length === 0) {

        faqContainer.innerHTML = `

            <div class="no-results">

                <h3>
                    No FAQs Found
                </h3>

                <p>
                    Try a different search or category.
                </p>

            </div>

        `;


        resultMessage.textContent =
            "No FAQs found.";

        pagination.innerHTML = "";

        return;

    }


    // Create FAQ cards

    pageFAQs.forEach(function(faq) {

        createFAQCard(faq);

    });


    // Update result message

    resultMessage.textContent =
        "Showing " +
        (start + 1) +
        " - " +
        Math.min(end, filteredFAQs.length) +
        " of " +
        filteredFAQs.length +
        " FAQs";


    // Create pagination

    createPagination();

}


// ======================================================
// CREATE FAQ CARD
// ======================================================

function createFAQCard(faq) {

    // Main box

    const box =
        document.createElement("div");

    box.className = "faq-box";


    // Number

    const number =
        document.createElement("div");

    number.className = "number";

    number.textContent =
        faq.number;


    // Question container

    const question =
        document.createElement("div");

    question.className = "question";


    // Question title

    const heading =
        document.createElement("h3");

    heading.textContent =
        faq.question;


    // Answer

    const answer =
        document.createElement("p");

    answer.textContent =
        faq.answer;


    // Category

    const category =
        document.createElement("small");

    category.textContent =
        "Category: " + faq.category;


    category.style.display = "block";

    category.style.marginTop = "8px";

    category.style.color = "#397c6b";


    // Add title and answer

    question.appendChild(heading);

    question.appendChild(answer);

    question.appendChild(category);


    // Button

    const button =
        document.createElement("button");

    button.textContent = "+";


    // Add everything to box

    box.appendChild(number);

    box.appendChild(question);

    box.appendChild(button);


    // Add box to page

    faqContainer.appendChild(box);


    // ==================================================
    // OPEN / CLOSE FAQ
    // ==================================================

    box.addEventListener("click", function() {

        // Close other boxes

        const allBoxes =
            document.querySelectorAll(".faq-box");


        allBoxes.forEach(function(item) {

            if (item !== box) {

                item.classList.remove("open");

                const itemButton =
                    item.querySelector("button");

                itemButton.textContent = "+";

            }

        });


        // Toggle current box

        box.classList.toggle("open");


        // Change button

        button.textContent =
            box.classList.contains("open")
                ? "−"
                : "+";

    });

}


// ======================================================
// SEARCH FAQ
// ======================================================

searchInput.addEventListener(
    "input",
    function() {

        currentPage = 1;

        applyFilters();

    }
);


// ======================================================
// CATEGORY FILTER
// ======================================================

categoryFilter.addEventListener(
    "change",
    function() {

        currentPage = 1;

        applyFilters();

    }
);


// ======================================================
// SORT FAQ
// ======================================================

sortSelect.addEventListener(
    "change",
    function() {

        currentPage = 1;

        applyFilters();

    }
);


// ======================================================
// APPLY SEARCH + CATEGORY + SORT
// ======================================================

function applyFilters() {

    const searchValue =
        searchInput.value
            .toLowerCase()
            .trim();


    const selectedCategory =
        categoryFilter.value;


    // Filter

    filteredFAQs =
        allFAQs.filter(function(faq) {

            const question =
                faq.question.toLowerCase();

            const answer =
                faq.answer.toLowerCase();

            const category =
                faq.category.toLowerCase();


            // Search condition

            const matchesSearch =
                question.includes(searchValue) ||
                answer.includes(searchValue) ||
                category.includes(searchValue);


            // Category condition

            const matchesCategory =
                selectedCategory === "all" ||
                faq.category === selectedCategory;


            return matchesSearch &&
                   matchesCategory;

        });


    // ================= SORT =================

    const sortValue =
        sortSelect.value;


    if (sortValue === "az") {

        filteredFAQs.sort(function(a, b) {

            return a.question.localeCompare(
                b.question
            );

        });

    }


    if (sortValue === "za") {

        filteredFAQs.sort(function(a, b) {

            return b.question.localeCompare(
                a.question
            );

        });

    }


    // Display

    displayFAQs();

}


// ======================================================
// PAGINATION
// ======================================================

function createPagination() {

    pagination.innerHTML = "";


    const totalPages =
        Math.ceil(
            filteredFAQs.length /
            faqsPerPage
        );


    // If only one page

    if (totalPages <= 1) {

        return;

    }


    // ================= PREVIOUS =================

    const previousButton =
        document.createElement("button");

    previousButton.textContent =
        "← Previous";


    previousButton.disabled =
        currentPage === 1;


    previousButton.addEventListener(
        "click",
        function() {

            if (currentPage > 1) {

                currentPage--;

                displayFAQs();

                window.scrollTo({
                    top: 500,
                    behavior: "smooth"
                });

            }

        }
    );


    pagination.appendChild(
        previousButton
    );


    // ================= PAGE NUMBERS =================

    for (
        let i = 1;
        i <= totalPages;
        i++
    ) {

        const pageButton =
            document.createElement("button");


        pageButton.textContent =
            i;


        if (i === currentPage) {

            pageButton.classList.add(
                "active-page"
            );

        }


        pageButton.addEventListener(
            "click",
            function() {

                currentPage = i;

                displayFAQs();

                window.scrollTo({
                    top: 500,
                    behavior: "smooth"
                });

            }
        );


        pagination.appendChild(
            pageButton
        );

    }


    // ================= NEXT =================

    const nextButton =
        document.createElement("button");

    nextButton.textContent =
        "Next →";


    nextButton.disabled =
        currentPage === totalPages;


    nextButton.addEventListener(
        "click",
        function() {

            if (currentPage < totalPages) {

                currentPage++;

                displayFAQs();

                window.scrollTo({
                    top: 500,
                    behavior: "smooth"
                });

            }

        }
    );


    pagination.appendChild(
        nextButton
    );

}