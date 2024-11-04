import React from 'react';

function TestComponent ({onClick}) {
    return (
        <div className='test-component' onClick={onClick}>Hello World</div>
    );
}

export default TestComponent;
