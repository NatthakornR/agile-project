document.querySelector('.login-button').addEventListener('click', function() {
    const email = document.querySelector('input[type="email"]').value;
    const password = document.querySelector('input[type="password"]').value;

    if (email && password) {
        alert("Logging in...");
    } else {
        alert("Please enter both email and password.");
    }
});

document.querySelector('.home').addEventListener('click', function() {
    alert("Navigating to home...");
});
