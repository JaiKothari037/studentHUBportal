const togglebtn = document.getElementById('toggletheme')
const body = document.body

const currentTheme = localStorage.getItem('dark-mode')

if (currentTheme == 'enabled')
{
    body.classList.add('dark-mode')
}

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

/* -------- */
const faqquestions = document.querySelectorAll('.faqq')

faqquestions.forEach(function(question) {
    question.addEventListener('click', function() {
        const answer = question.nextElementSibling
        if (answer)
        {
            answer.classList.toggle('show')
        }
    })
})

/* ------- */

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

/* ------ */
const banner = document.getElementById('notificationbanner')
const closebtn = document.getElementById('close-notif-banner')

if (banner && closebtn)
{
    closebtn.addEventListener('click', function() {
        banner.style.display = 'none';
        console.log('banner close clicked')
    });
}

/* ---------- */

const hamburgerBtn = document.getElementById('hamburger-btn');
const navMenu = document.getElementById('nav-menu');

if (hamburgerBtn && navMenu) {
    hamburgerBtn.addEventListener('click', function() {
        navMenu.classList.toggle('show');
    });
}