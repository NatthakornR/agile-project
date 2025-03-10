document.querySelector('.login-button').addEventListener('click', () => {
    const email = document.querySelector('input[type="email"]').value;
    const password = document.querySelector('input[type="password"]').value;

    fetch('http://localhost:3000/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email, password }),
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Login failed');
        }
        return response.json();
    })
    .then(data => {
        console.log('Login successful', data);
        // Save token to local storage or handle as needed
        localStorage.setItem('token', data.token);
        alert('Login successful!');
        // Redirect to another page if necessary
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Invalid email or password');
    });
});
