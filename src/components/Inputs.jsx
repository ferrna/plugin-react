import React, { useState, useEffect } from 'react';
import './inputs.css';
import axios from 'axios';

function Inputs({ formData }) {
    const [inputs, setInputs] = useState({
        name: '',
        email: '',
        username: '',
    });

    useEffect(() => {
        if (formData) {
            setInputs({
                name: formData?.user_nicename || '',
                email: formData?.user_email || '',
                username: formData?.username || '',
            });
        }
    }, [formData]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setInputs((prevInputs) => ({
            ...prevInputs,
            [name]: value,
        }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        submitData();
    }

    const submitData = async () => {
        try {
            console.log(window.rmaData.apiUrl);
            const response = await axios.post(window.rmaData.apiUrl + 'data', inputs, {
                headers: {
                    'X-WP-Nonce': window.rmaData.nonce, // Security nonce
                    'Content-Type': 'application/json', // Ensure the request is sent as JSON
                },
            });
            console.log(response.data);
        } catch (error) {
            console.error('Error:', error);
        }
    };

    return (
        <div className='inputs-component'>
            <input
                type="text"
                name="name"
                value={inputs.name}
                onChange={handleChange}
                placeholder="Name"
            />
            <input
                type="email"
                name="email"
                value={inputs.email}
                onChange={handleChange}
                placeholder="Email"
            />
            <input
                type="text"
                name="username"
                value={inputs.username}
                onChange={handleChange}
                placeholder="Username"
            />
            <button className='inputs-component__btn' onClick={handleSubmit}>Enviar</button>
        </div>
    );
}

export default Inputs;