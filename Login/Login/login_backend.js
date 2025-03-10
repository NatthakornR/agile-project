const express = require('express');
const mongoose = require('mongoose');
const bcrypt = require('bcryptjs');
const jwt = require('jsonwebtoken');
const cors = require('cors');

// Initialize express app
const app = express();

// Middleware
app.use(express.json());
app.use(cors());  // Enable CORS for frontend requests

// MongoDB connection
mongoose.connect('mongodb://localhost:27017/e-tendering', {
    useNewUrlParser: true,
    useUnifiedTopology: true
})
.then(() => console.log('Connected to MongoDB'))
.catch(err => console.log('Error connecting to MongoDB:', err));

// User schema (for demonstration purposes, you can extend this based on your needs)
const userSchema = new mongoose.Schema({
    email: { type: String, required: true, unique: true },
    password: { type: String, required: true },
    role: { type: String, required: true }  // 'company' or 'city'
});

const User = mongoose.model('User', userSchema);

// Register route (for demonstration, register users with email and password)
app.post('/api/register', async (req, res) => {
    const { email, password, role } = req.body;

    // Check if email already exists
    const existingUser = await User.findOne({ email });
    if (existingUser) {
        return res.status(400).json({ message: 'Email already exists' });
    }

    // Hash password before saving
    const hashedPassword = await bcrypt.hash(password, 10);

    const newUser = new User({ email, password: hashedPassword, role });
    await newUser.save();

    res.status(201).json({ message: 'User registered successfully' });
});

// Login route
app.post('/api/login', async (req, res) => {
    const { email, password } = req.body;

    // Find user by email
    const user = await User.findOne({ email });
    if (!user) {
        return res.status(400).json({ message: 'Invalid email or password' });
    }

    // Check if password is correct
    const isMatch = await bcrypt.compare(password, user.password);
    if (!isMatch) {
        return res.status(400).json({ message: 'Invalid email or password' });
    }

    // Create JWT token
    const token = jwt.sign(
        { userId: user._id, role: user.role }, 
        'your_jwt_secret_key', // Secret key to sign JWT
        { expiresIn: '1h' } // Token expiration time
    );

    res.json({ message: 'Login successful', token });
});

// Protected route (example: fetch user data)
app.get('/api/user-data', async (req, res) => {
    const token = req.headers['authorization']?.split(' ')[1];

    if (!token) {
        return res.status(403).json({ message: 'No token provided' });
    }

    try {
        // Verify the token
        const decoded = jwt.verify(token, 'your_jwt_secret_key');
        
        // Fetch user data from MongoDB using decoded user ID
        const user = await User.findById(decoded.userId);
        if (!user) {
            return res.status(404).json({ message: 'User not found' });
        }

        res.json({
            email: user.email,
            role: user.role
        });
    } catch (err) {
        return res.status(401).json({ message: 'Invalid or expired token' });
    }
});

// Start server
const PORT = process.env.PORT || 5000;
app.listen(PORT, () => {
    console.log(`Server is running on port ${PORT}`);
});
