import React, { useState, useEffect, Suspense } from 'react';
import TestComponent from './components/TestComponent';
import Inputs from './components/Inputs';
import axios from 'axios';
import './app.css'

const DataDisplay = React.lazy(() => import('./components/DataDisplay'));

function App() {
    const [counter, setCounter] = useState(0);
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);

    const handleClick = () => {
        setCounter(counter + 1);
    };

    useEffect(() => {
        const fetchData = async () => {
            try {
                console.log(window.rmaData.apiUrl);
                const response = await axios.get(window.rmaData.apiUrl + 'data', {
                    headers: {
                        'X-WP-Nonce': window.rmaData.nonce // Security nonce
                    }
                });
                console.log(response.data);
                setData(response.data);
            } catch (error) {
                console.error('Error:', error);
            } finally {
                setLoading(false);
            }
        };

        fetchData();
    }, []);

    return (
        <div>
            <TestComponent onClick={handleClick} />
            <p>Counter: {counter}</p>
            <div className="form_container">
                <Suspense fallback={<div className='loader'></div>}>
                    {loading ? (
                        <div className='loader'></div>
                    ) : (
                        <DataDisplay data={data} />
                    )}
                </Suspense>
                <div className='form__inputs'>
                    <Inputs formData={data?.current_user} />
                    <div className={`form__inputs_overlay ${loading ? 'form__inputs_overlay--active' : ''}`}></div>
                </div>
            </div>
        </div>
    );
}

export default App;
