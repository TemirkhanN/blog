import { ReactNode } from 'react';
import { useAuthContext } from './Auth';

export default function AdminAccess({ children }: { children: ReactNode }) {
  const User = useAuthContext();

  if (!User.isLoggedIn()) {
    return null;
  }

  /* eslint-disable react/jsx-no-useless-fragment */
  return (
    <>
      { children }
    </>
  );
}
