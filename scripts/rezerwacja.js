// Ustawienie minimalnej daty przyjazdu na dzisiejszą
document.addEventListener('DOMContentLoaded', function() {
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(today.getDate() + 1);
    
    const formattedToday = today.toISOString().split('T')[0];
    const formattedTomorrow = tomorrow.toISOString().split('T')[0];
    
    document.getElementById('data_przyjazdu').setAttribute('min', formattedToday);
    document.getElementById('data_wyjazdu').setAttribute('min', formattedTomorrow);
    
    // Ustawienie daty wyjazdu na minimum dzień po przyjeździe
    document.getElementById('data_przyjazdu').addEventListener('change', function() {
        const przyjazd = new Date(this.value);
        const minWyjazd = new Date(przyjazd);
        minWyjazd.setDate(przyjazd.getDate() + 1);
        document.getElementById('data_wyjazdu').setAttribute('min', minWyjazd.toISOString().split('T')[0]);
        
        // Jeśli data wyjazdu jest wcześniejsza niż nowe minimum, zaktualizuj ją
        const dataWyjazdu = document.getElementById('data_wyjazdu');
        if (dataWyjazdu.value && new Date(dataWyjazdu.value) <= przyjazd) {
            dataWyjazdu.value = minWyjazd.toISOString().split('T')[0];
        }
        
        aktualizujPodsumowanie();
    });
    
    // Obsługa aktualizacji podsumowania
    const formInputs = document.querySelectorAll('#rezerwacja-form input, #rezerwacja-form select');
    formInputs.forEach(input => {
        input.addEventListener('change', aktualizujPodsumowanie);
    });
    
    // Inicjalna aktualizacja, jeśli domek został wybrany z URL
    if (document.getElementById('domek').value) {
        aktualizujPodsumowanie();
    }
});

function aktualizujPodsumowanie() {
    const domekSelect = document.getElementById('domek');
    const dataPrzyjazdu = document.getElementById('data_przyjazdu').value;
    const dataWyjazdu = document.getElementById('data_wyjazdu').value;
    const iloscOsob = document.getElementById('ilosc_osob').value;
    
    // Aktualizacja podsumowania
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
        
        let cenaDzien = 0;
        switch(domekSelect.value) {
            case 'sloneczny': cenaDzien = 350; break;
            case 'brzozowy': cenaDzien = 280; break;
            case 'premium': cenaDzien = 550; break;
        }
        
        const suma = dni * cenaDzien;
        document.getElementById('podsumowanie-suma').textContent = suma.toFixed(2) + ' zł';
    } else {
        document.getElementById('podsumowanie-dni').textContent = '-';
        document.getElementById('podsumowanie-suma').textContent = '0.00 zł';
    }
}