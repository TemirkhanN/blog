import { Button, Form } from 'react-bootstrap';
import { useState } from 'react';
import Logger from '../utils/Logger';
import { useAuthContext } from './Auth';

export default function SignIn() {
  const User = useAuthContext();

  const [login, setLogin] = useState<string>('');
  const [password, setPassword] = useState<string>('');
  const [inProgress, setInProgress] = useState<boolean>(false);

  if (User.isLoggedIn()) {
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

    User.login(login, password)
      .catch((err) => Logger.error(err))
      .finally(() => setInProgress(false));
  };

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
  );
}
