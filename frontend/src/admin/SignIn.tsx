import {Button, Form} from "react-bootstrap";
import {useState} from "react";
import {API} from "../utils/API";
import {isAuthenticated, saveAuthToken} from "./Auth";

export default function SignIn(props: {stateObserver: (isSignedIn: boolean) => void}) {
    const [login, setLogin] = useState<string>('');
    const [password, setPassword] = useState<string>('');
    const [inProgress, setInProgress] = useState<boolean>(false);
    const [signedIn, setSignedIn] = useState<boolean>(isAuthenticated());
    const setSignedInWrapper = (isSignedIn: boolean) => {
        setSignedIn(isSignedIn);
        props.stateObserver(isSignedIn);
    }

    if (signedIn) {
        return null;
    }

    const handleSignIn = () => {
        if (inProgress) {
            return;
        }

        if (login === '' || password === '') {
            return;
        }

        setInProgress(true);

        API.createToken(login, password)
            .then((response) => {
                if (response.status === 201) {
                    saveAuthToken(response.data.token);
                    setSignedInWrapper(true);
                }
            })
            .catch((err) => {
                console.log(err);
            }).finally(() => setInProgress(false))
    }

    return (
        <>
            <Form.Group className="mb-3" controlId="login">
                <Form.Label>Email address</Form.Label>
                <Form.Control
                    type="text"
                    placeholder="Enter login"
                    onChange={(e) => setLogin(e.target.value)}
                />
            </Form.Group>
            <Form.Group className="mb-3" controlId="password">
                <Form.Label>Password</Form.Label>
                <Form.Control
                    type="password"
                    placeholder="Password"
                    onChange={(e) => setPassword(e.target.value)}
                />
            </Form.Group>
            <Button variant="primary" type="submit" onClick={handleSignIn} disabled={inProgress}>
                Sign in
            </Button>
        </>
    )
}
