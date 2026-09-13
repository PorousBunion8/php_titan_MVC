// script frontend para mascaras e formatacoes de campos monetarios

document.addEventListener('DOMContentLoaded', function () {
    const priceInputs = document.querySelectorAll('#price_input');

    priceInputs.forEach(input => {
        input.addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value === '') {
                e.target.value = '';
                return;
            }
            value = (parseInt(value, 10) / 100).toFixed(2);
            value = value.replace('.', ',');
            value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
            e.target.value = value;
        });
    });
});