document.addEventListener('DOMContentLoaded', () => {
    const dateInput = document.querySelector('#date');
    if (dateInput) {
        window.fpInstance = flatpickr(dateInput, {
            altInput: true,
            altFormat: "d/m/Y",
            dateFormat: "Y-m-d",
            minDate: "today",
            locale: "vn"
        });
    }
});
