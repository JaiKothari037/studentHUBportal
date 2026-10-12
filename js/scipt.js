/* theme toggle */
const togglebtn = document.getElementById('toggletheme')
const body = document.body

const currentTheme = localStorage.getItem('dark-mode')

if (currentTheme == 'enabled')
{
    body.classList.add('dark-mode')
}

if (togglebtn)
{
    togglebtn.addEventListener('click', function() {
        body.classList.toggle('dark-mode')

        if (body.classList.contains('dark-mode'))
        {
            localStorage.setItem('dark-mode', 'enabled')
        }
        else
        {
            localStorage.setItem('dark-mode', 'disabled')
        }
    })
}


/* collapsible FAQ */

// const faqquestions = document.querySelectorAll('.faqq')

// faqquestions.forEach(function(question) {
//     question.addEventListener('click', function() {
//         const answer = question.nextElementSibling
//         if (answer)
//         {
//             answer.classList.toggle('show')
//         }
//     })
// })

/* modal popup */

const openModalBtn = document.getElementById('open-modal-btn')
const closeModalBtn = document.getElementById('close-modal-btn')
const modalOverlay = document.getElementById('timetable-modal')


if (openModalBtn && closeModalBtn && modalOverlay)
{
    openModalBtn.addEventListener('click', function() {
        modalOverlay.classList.add('show')
    })

    closeModalBtn.addEventListener('click', function() {
        modalOverlay.classList.remove('show');
});

}

/* notification banner */
const banner = document.getElementById('notificationbanner')
const closebtn = document.getElementById('close-notif-banner')

if (banner && closebtn)
{
    closebtn.addEventListener('click', function() {
        banner.style.display = 'none';
        console.log('banner close clicked')
    });
}

/* hamburger menu */

const hamburgerBtn = document.getElementById('hamburger-btn');
const navMenu = document.getElementById('nav-menu');

if (hamburgerBtn && navMenu) {
    hamburgerBtn.addEventListener('click', function() {
        navMenu.classList.toggle('show');
    });
}

/*sign up page */

const form = document.getElementById("signupform")

if (form)
{
    form.addEventListener('submit', function(e) {
        e.preventDefault()
        const name = document.getElementById("name").value.trim()
        const namerule = /^[^0-9]*$/
        if (!namerule.test(name))
        {
            alert("name cannot be a number")
            return;
        }
        const email = document.getElementById("email").value.trim()
        const emailrule = /@gmail.com$/
        if (!emailrule.test(email))
        {
            alert("email should have @gmail.com")
            return;
        }
        const number = document.getElementById("number").value.trim()
        const numberrule = /^[^a-zA-Z]*$/
        if (!numberrule.test(number))
        {
            alert("number cannot have alphabet")
            return
        }
        const password = document.getElementById("password").value.trim()
        const passwordrule = /^[0-9]+.*[^a-zA-Z0-9]+$/
        if (!passwordrule.test(password))
        {
            alert("password must start with number and end with special character")
            return
        }
        const confpassword = document.getElementById("confpass").value.trim()
        if(confpassword !== password)
        {
            alert("reentered password is not the same as password")
            return
        }

        alert("registration successful")
        form.reset()
    })
}

/* jSON */

let allFAQs = [];

function loadFAQs()
{
    fetch("faqs.json")

    .then( function (response) {
        return response.json();
    })

    .then( function (data) {
        console.log(data);
        
        allFAQs = data;
        renderFAQs(allFAQs)

        const searchInput = document.getElementById('faq-search')

        if (searchInput)
        {
            searchInput.addEventListener('input', filterFAQs)
        }

        const categorySelect = document.getElementById('faq-category')

        if (categorySelect)
        {
            categorySelect.addEventListener('change', filterFAQs)
        }
    })

    .catch(function (error) {
        console.error("failed", error)
    })
}

loadFAQs();

function renderFAQs(faqList)
{
    const container = document.getElementById("faq-container")
    if (!container) return;

    container.innerHTML = ""

    faqList.forEach(function (item) {
        const faqCard = document.createElement('div')
    
        faqCard.innerHTML = `
            <button class ="faqq"> ${item.question}</button>
            <div class="faqa">
                <p> ${item.answer} </p>
            </div>
        `;

        const question = faqCard.querySelector('.faqq')

        question.addEventListener('click', function() {
            const answer = question.nextElementSibling
            if (answer)
            {
                answer.classList.toggle('show')
            }
        })

        container.appendChild(faqCard)
    })
}

//searching and filtering data

function filterFAQs()
{
    //search and category filtering
    const searchInput = document.getElementById("faq-search")
    const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : ''

    const categorySelect = document.getElementById('faq-category')
    const selectedCategory = categorySelect ? categorySelect.value : 'All'


    //filtering master array
    const filteredList = allFAQs.filter(function(item) {
        const categoryMatch = (selectedCategory === 'All') || (item.category == selectedCategory)
        

        const questionMatch = item.question.toLowerCase().includes(searchTerm)
        const answerMatch = item.answer.toLowerCase().includes(searchTerm)
        const matchesSearch = questionMatch || answerMatch

        return categoryMatch && matchesSearch
    })

    //sorting
    filteredList.sort(function(a,b){
        return a.question.localeCompare(b.question)
    })

    renderFAQs(filteredList)
}   

//pagination
