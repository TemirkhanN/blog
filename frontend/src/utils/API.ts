import {HttpClient, Response} from "./HttpClient";

export interface PostModel {
    readonly slug: string,
    readonly title: string,
    readonly content: string,
    readonly publishedAt: string,
    readonly tags: string[]
}

export interface Preview {
    readonly title: string,
    readonly slug: string,
    readonly preview: string,
    readonly publishedAt: string,
    readonly tags: string[]
}

export interface CommentBranch extends Comment{
    readonly replies: CommentBranch[]
}

export interface Comment {
    readonly guid: string,
    readonly createdAt: string,
    readonly comment: string
}

export interface PaginatedCollection<T> {
    readonly data: T[],
    readonly pagination: {
        readonly limit: number,
        readonly offset: number,
        readonly total: number
    }
}

function createToken(login: string, password: string): Promise<Response<{ token: string }>> {
    return HttpClient.post(process.env.REACT_APP_BACKEND_URL + '/api/auth/tokens', {
        login: login,
        password: password
    });
}

function createPost(title: string, preview: string, content: string, tags: string[]): Promise<Response<{ slug: string }>> {
    return HttpClient.post(process.env.REACT_APP_BACKEND_URL + '/api/posts', {
        title: title,
        preview: preview,
        content: content,
        tags: tags
    });
}

function getPost(slug: string): Promise<Response<PostModel>> {
    return HttpClient.get(process.env.REACT_APP_BACKEND_URL + "/api/posts/" + slug);
}

function getPosts(page: number, limit: number, tag?: string | null): Promise<Response<PaginatedCollection<Preview>>> {
    let filter = [];
    filter.push('limit=' + limit);
    filter.push('offset=' + limit * (page - 1));

    if (tag) {
        filter.push('tag=' + tag);
    }

    return HttpClient.get(process.env.REACT_APP_BACKEND_URL + "/api/posts?" + filter.join('&'))
}

function getCommentsTree(postSlug: string): Promise<Response<CommentBranch[]>> {
    return HttpClient.get(process.env.REACT_APP_BACKEND_URL + "/api/posts/" + postSlug + "/comments");
}

function addComment(toPost: string, comment: string, replyTo?: Comment): Promise<Response<Comment>> {
    let endpoint = process.env.REACT_APP_BACKEND_URL + "/api/posts/" + toPost + "/comments";

    if (replyTo !== undefined) {
        endpoint += '/' + replyTo.guid;
    }

    return HttpClient.post(endpoint, {
        text: comment
    });
}

export const API = {
    createToken,
    createPost,
    getPost,
    getPosts,
    getCommentsTree,
    addComment
}
