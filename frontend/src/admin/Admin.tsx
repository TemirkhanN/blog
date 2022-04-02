import { useState } from 'react';
import { Button } from 'react-bootstrap';
import SignIn from './SignIn';
import AddPost from '../post/AddPost';
import { isAuthenticated, signOut } from './Auth';

export default function Admin() {
  const [signedIn, setSignedIn] = useState<boolean>(isAuthenticated());

  const handleSignOut = () => {
    signOut();
    setSignedIn(false);
  };

  if (!signedIn) {
    return <SignIn stateObserver={(isSignedIn: boolean) => setSignedIn(isSignedIn)} />;
  }

  return (
    <>
      <div>
        <AddPost />
      </div>
      <div>
        <Button onClick={handleSignOut}>Sign out</Button>
      </div>
    </>
  );
}