import { Button } from 'react-bootstrap';
import SignIn from './SignIn';
import AddPost from '../post/AddPost';
import { useAuthContext } from './Auth';
import AdminAccess from './AdminAccess';

export default function Admin() {
  const User = useAuthContext();

  if (!User.isLoggedIn()) {
    return <SignIn />;
  }

  return (
    <AdminAccess>
      <AddPost />
      <Button onClick={() => User.logout()}>Sign out</Button>
    </AdminAccess>
  );
}
