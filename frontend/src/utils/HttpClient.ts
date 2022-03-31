import axios from "axios";
import {getAuthToken} from "../admin/Auth";

type SystemMessage = {
    readonly code: number,
    readonly message: string
}

export interface Response<Data> {
    isSuccessful(): boolean,
    getData(): Data,
    getError(): SystemMessage,
}

class Result<Data> implements Response<Data>{
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
            console.log('This method was not expected to be called for result is not successful.');
        }

        return <Data>this.data;
    }

    getError(): SystemMessage {
        if (this.isSuccessful()) {
            console.log('This method was not expected to be called for result is successful.');
        }

        return <SystemMessage>this.error;
    }
}

const adapter = axios.create();
adapter.interceptors.request.use((config) => {
    const token = getAuthToken();
    if (token !== undefined) {
        config.headers = {
            Authorization: token
        };
    }

    return config;
})

adapter.interceptors.response.use(
    (response) => new Result(response.data),
    (error) => new Result(undefined, error.response.data)
);

function post<T>(uri: string, data: object): Promise<T> {
    return adapter.request({
        url: uri,
        method: 'POST',
        data: data
    });
}

function get<T>(uri: string): Promise<T> {
    return adapter.request({
        url: uri,
        method: 'GET',
    })
}

export const HttpClient = {
    post,
    get,
}
