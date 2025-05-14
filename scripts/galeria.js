document.addEventListener("DOMContentLoaded", function () {
    const slides = document.querySelectorAll('.slide');
    const prevBtn = document.querySelector('.prev');
    const nextBtn = document.querySelector('.next');
    let current = 0;

    function updateSlide() {
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === current);
        });
    }

    prevBtn.addEventListener('click', () => {
        current = (current - 1 + slides.length) % slides.length;
        updateSlide();
    });

    nextBtn.addEventListener('click', () => {
        current = (current + 1) % slides.length;
        updateSlide();
    });
});