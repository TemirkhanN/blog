import {HttpClient, Response} from "./HttpClient";

function createToken(login: string, password: string): Promise<Response<{token: string}>>{
    return HttpClient.post(process.env.REACT_APP_BACKEND_URL + '/api/auth/tokens', {
        login: login,
        password: password
    });
}

function createPost(title: string, preview: string, content: string, tags: string[]): Promise<Response<{slug: string}>> {
    return HttpClient.post(process.env.REACT_APP_BACKEND_URL + '/api/posts', {
        title: title,
        preview: preview,
        content: content,
        tags: tags
    });
}

export const API = {
    createToken,
    createPost
}
