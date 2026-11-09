<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<style>
    .flatpickr-calendar { font-family: inherit; border-radius: 12px; box-shadow: 0 12px 32px rgba(35, 22, 80, .18); }
    .flatpickr-day.selected, .flatpickr-day.selected:hover { background: #9164f8; border-color: #9164f8; }
    .flatpickr-time input:focus { background: #f2ebff; }
</style>
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script>
    document.querySelectorAll('.signing-expiry').forEach(function (input) {
        flatpickr(input, {
            enableTime: true,
            time_24hr: false,
            dateFormat: 'Y-m-d H:i',
            altInput: true,
            altFormat: 'F j, Y at h:i K',
            altInputClass: 'form-control',
            allowInput: false,
            minuteIncrement: 5
        });
    });
</script>
