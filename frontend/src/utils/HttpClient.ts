import axios from 'axios';
import { getAuthToken } from '../admin/Auth';
import Logger from './Logger';

type SystemMessage = {
    readonly code: number,
    readonly message: string
}

export interface Response<Data> {
    isSuccessful(): boolean,
    getData(): Data,
    getError(): SystemMessage,
}

class Result<Data> implements Response<Data> {
  data: Data | undefined;

  error: SystemMessage | undefined;

  constructor(data?: Data, error?: SystemMessage) {
    this.data = data;
    this.error = error;
  }

  isSuccessful(): boolean {
    return this.error === undefined;
  }

  getData(): Data {
    if (!this.isSuccessful()) {
      Logger.error('This method was not expected to be called for result is not successful.');
    }

    return this.data as Data;
  }

  getError(): SystemMessage {
    if (this.isSuccessful()) {
      Logger.error('This method was not expected to be called for result is successful.');
    }

    return this.error as SystemMessage;
  }
}

const adapter = axios.create();
adapter.interceptors.request.use((config) => {
  const token = getAuthToken();
  if (token !== undefined) {
    /* eslint-disable no-param-reassign */
    config.headers = {
      Authorization: token,
    };
  }

  return config;
});

adapter.interceptors.response.use(
  (response) => new Result(response.data),
  (error) => new Result(undefined, error.response.data),
);

function post<T>(uri: string, data: object): Promise<T> {
  return adapter.request({
    url: uri,
    method: 'POST',
    data,
  });
}

function get<T>(uri: string): Promise<T> {
  return adapter.request({
    url: uri,
    method: 'GET',
  });
}

export const HttpClient = {
  post,
  get,
};
