import axios from "axios";
import {getAuthToken} from "../admin/Auth";

export interface Response<T> {
    status: number,
    data: T
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

function post<T>(uri: string, data: object): Promise<Response<T>> {
    return adapter.request({
        url: uri,
        method: 'POST',
        data: data
    })
}

function get<T>(uri: string): Promise<Response<T>> {
    return adapter.request({
        url: uri,
        method: 'GET',
    })
}

export const HttpClient = {
    post,
    get,
}
