import Cookies from 'js-cookie';
import API from '../utils/API';
import Logger from '../utils/Logger';

export function getAuthToken(): string | undefined {
  return Cookies.get('_authToken');
}

export function isLoggedIn(): boolean {
  const token = getAuthToken();
  if (token === undefined) {
    return false;
  }

  return token !== '';
}

export function login(name: string, password: string): Promise<boolean> {
  return API.createToken(name, password)
    .then((response) => {
      if (!response.isSuccessful()) {
        return false;
      }

      Cookies.set('_authToken', response.getData().token);

      return true;
    })
    .catch((err) => {
      Logger.error(err);

      return false;
    });
}

export function logout(): void {
  Cookies.remove('_authToken');
}
