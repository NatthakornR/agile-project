document.querySelector('.login-button').addEventListener('click', function() {
    const email = document.querySelector('input[type="email"]').value;
    const password = document.querySelector('input[type="password"]').value;

    if (!email || !password) {
        alert('Please fill in both fields.');
        return;
    }

    const data = { email, password };

    fetch('http://localhost:5000/api/login', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Login failed');
        }
        return response.json();
    })
    .then(data => {
        alert('Login successful!');
        console.log(data); // Log user data or token if returned
        
        return fetch('http://localhost:5000/api/user-data', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${data.token}`
            }
        });
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to fetch user data');
        }
        return response.json();
    })
    .then(userData => {
        console.log('Fetched user data:', userData);
        // You can process and display user data here
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + error.message);
    });
});
