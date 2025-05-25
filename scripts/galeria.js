// Czekamy na załadowanie całej struktury DOM
document.addEventListener("DOMContentLoaded", function () {
    // Pobieramy wszystkie slajdy oraz przyciski nawigacji
    const slides = document.querySelectorAll('.slide');
    const prevBtn = document.querySelector('.prev');
    const nextBtn = document.querySelector('.next');
    // Zmienna przechowująca indeks aktualnego slajdu
    let current = 0;

    // Funkcja aktualizująca widoczność slajdów
    function updateSlide() {
        // Dla każdego slajdu sprawdzamy czy powinien być aktywny
        slides.forEach((slide, i) => {
            slide.classList.toggle('active', i === current);
        });
    }

    // Obsługa kliknięcia przycisku "poprzedni"
    prevBtn.addEventListener('click', () => {
        // Obliczamy indeks poprzedniego slajdu (z zapętleniem)
        current = (current - 1 + slides.length) % slides.length;
        updateSlide();
    });

    // Obsługa kliknięcia przycisku "następny"
    nextBtn.addEventListener('click', () => {
        // Obliczamy indeks następnego slajdu (z zapętleniem)
        current = (current + 1) % slides.length;
        updateSlide();
    });
});