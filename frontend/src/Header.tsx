import React from 'react';
import logo from './resources/img/doge-sleepy.png'

class Header extends React.Component {
    render() {
        return (
            <header>
                <div className="info-block">
                    <a className="much-wow"
                       title="Much wow! Let's go home"
                       href="/">
                        <img src={logo} alt="Much wow! Let's go home"/>
                    </a>
                    <span>{process.env.REACT_APP_AUTHOR_NAME}</span>
                </div>
                <nav className="navbar navbar-expand-sm navbar-dark bg-dark">
                    <div className="container-fluid">
                        <button className="navbar-toggler"
                                type="button"
                                data-bs-toggle="collapse"
                                data-bs-target="#main-menu"
                                aria-controls="main-menu"
                                aria-expanded="false"
                                aria-label="Toggle menu">
                            <span className="navbar-toggler-icon"> </span>
                        </button>

                        <div className="collapse navbar-collapse" id="main-menu">
                            <ul className="navbar-nav me-auto mb-2 mb-sm-0">
                                <li className="nav-item">
                                    <a className="nav-link"
                                       href="#"> Blog </a>
                                </li>
                                <li className

                                        ="nav-item">
                                    <a className="nav-link"
                                       href="#"> Github </a>
                                </li>
                                <li className="nav-item">
                                    <a className="nav-link" href="#"> Gaming </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </nav>
            </header>
        );
    }
}

export default Header;