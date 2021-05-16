import React from 'react';

class Footer extends React.Component {
    render() {
        return (
            <footer className="footer mt-auto py-3 bg-light">
                <div className="container">
                    <span className="text-muted">&copy; {process.env.REACT_APP_AUTHOR_NAME}</span>
                </div>
            </footer>
        );
    }
}

export default Footer;