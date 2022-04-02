import Cookies from 'js-cookie';

export function getAuthToken() {
  return Cookies.get('_authToken');
}

export function saveAuthToken(token: string) {
  return Cookies.set('_authToken', token);
}

export function signOut() {
  Cookies.remove('_authToken');
}

export function isAuthenticated() {
  const token = getAuthToken();
  if (token === undefined) {
    return false;
  }

  return token !== '';
}
