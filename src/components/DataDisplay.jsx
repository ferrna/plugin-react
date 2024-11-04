import React from 'react';
import './dataDisplay.css';

function DataDisplay({ data }) {
    return (
        <div id='data_display'>
            <div className="container">
                <p>Data: {data?.external_data ? data?.external_data[0].name: ''}</p>
                <p>User: {data?.current_user?.user_nicename}</p>
            </div>
        </div>
    );
}

export default DataDisplay;