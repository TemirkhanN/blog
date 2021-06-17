import {Link} from "react-router-dom";

function Footer() {
    return (
        <footer className="footer mt-auto py-3 bg-light">
            <div className="container">
                &copy;
                <Link className="text-muted" to="/cv">
                    Temirkhan Nasukhov
                </Link>
            </div>
        </footer>
    );
}

export default Footer;
