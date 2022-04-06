import { Button } from 'react-bootstrap';
import SignIn from './SignIn';
import AddPost from '../post/AddPost';
import { useAuthContext } from './Auth';

export default function Admin() {
  const User = useAuthContext();

  if (!User.isLoggedIn()) {
    return <SignIn />;
  }

  return (
    <>
      <div>
        <AddPost />
      </div>
      <div>
        <Button onClick={() => User.logout()}>Sign out</Button>
      </div>
    </>
  );
}
