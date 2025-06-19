document.querySelectorAll('input[name="user_type"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        if (this.value === 'client') {
            window.location.href = 'singupclient.php';
        } else if (this.value === 'avocat') {
            window.location.href = 'singupAvocat.php';
        }
    });
});
document.querySelectorAll('input[name="user_type"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        if (this.value === 'client') {
            window.location.href = 'singupclient.php';
        } else if (this.value === 'avocat') {
            window.location.href = 'singupAvocat.php';
        }
    });
});