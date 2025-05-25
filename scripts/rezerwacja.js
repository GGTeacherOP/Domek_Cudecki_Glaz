// Ustawienie minimalnej daty przyjazdu na dzisiejszą
document.addEventListener('DOMContentLoaded', function() {
    // Pobranie aktualnej daty
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(today.getDate() + 1);
    
    // Formatowanie dat do formatu YYYY-MM-DD
    const formattedToday = today.toISOString().split('T')[0];
    const formattedTomorrow = tomorrow.toISOString().split('T')[0];
    
    // Ustawienie minimalnych dat w formularzu
    document.getElementById('data_przyjazdu').setAttribute('min', formattedToday);
    document.getElementById('data_wyjazdu').setAttribute('min', formattedTomorrow);
    
    // Obsługa zmiany daty przyjazdu
    document.getElementById('data_przyjazdu').addEventListener('change', function() {
        // Aktualizacja minimalnej daty wyjazdu
        const przyjazd = new Date(this.value);
        const minWyjazd = new Date(przyjazd);
        minWyjazd.setDate(przyjazd.getDate() + 1);
        document.getElementById('data_wyjazdu').setAttribute('min', minWyjazd.toISOString().split('T')[0]);
        
        // Jeśli data wyjazdu jest wcześniejsza niż nowe minimum, zaktualizuj ją
        const dataWyjazdu = document.getElementById('data_wyjazdu');
        if (dataWyjazdu.value && new Date(dataWyjazdu.value) <= przyjazd) {
            dataWyjazdu.value = minWyjazd.toISOString().split('T')[0];
        }
        
        // Aktualizacja podsumowania
        aktualizujPodsumowanie();
    });
    
    // Dodanie nasłuchiwania zmian dla wszystkich pól formularza
    const formInputs = document.querySelectorAll('#rezerwacja-form input, #rezerwacja-form select');
    formInputs.forEach(input => {
        input.addEventListener('change', aktualizujPodsumowanie);
    });
    
    // Aktualizacja podsumowania przy załadowaniu strony
    if (document.getElementById('domek').value) {
        aktualizujPodsumowanie();
    }
});

// Funkcja aktualizująca podsumowanie rezerwacji
function aktualizujPodsumowanie() {
    // Pobranie wartości z formularza
    const domekSelect = document.getElementById('domek');
    const dataPrzyjazdu = document.getElementById('data_przyjazdu').value;
    const dataWyjazdu = document.getElementById('data_wyjazdu').value;
    const iloscOsob = document.getElementById('ilosc_osob').value;
    
    // Aktualizacja tekstów w podsumowaniu
    document.getElementById('podsumowanie-domek').textContent = domekSelect.options[domekSelect.selectedIndex]?.text.split('-')[0].trim() || '-';
    document.getElementById('podsumowanie-przyjazd').textContent = dataPrzyjazdu || '-';
    document.getElementById('podsumowanie-wyjazd').textContent = dataWyjazdu || '-';
    document.getElementById('podsumowanie-osoby').textContent = iloscOsob || '-';
    
    // Obliczenie liczby dni i kosztu
    if (dataPrzyjazdu && dataWyjazdu) {
        const przyjazd = new Date(dataPrzyjazdu);
        const wyjazd = new Date(dataWyjazdu);
        const dni = Math.round((wyjazd - przyjazd) / (1000 * 60 * 60 * 24));
        
        document.getElementById('podsumowanie-dni').textContent = dni;
        
        // Ustalenie ceny za dobę w zależności od wybranego domku
        let cenaDzien = 0;
        switch(domekSelect.value) {
            case 'sloneczny': cenaDzien = 350; break;
            case 'brzozowy': cenaDzien = 280; break;
            case 'premium': cenaDzien = 550; break;
        }
        
        // Obliczenie i wyświetlenie sumy
        const suma = dni * cenaDzien;
        document.getElementById('podsumowanie-suma').textContent = suma.toFixed(2) + ' zł';
    } else {
        document.getElementById('podsumowanie-dni').textContent = '-';
        document.getElementById('podsumowanie-suma').textContent = '0.00 zł';
    }
}