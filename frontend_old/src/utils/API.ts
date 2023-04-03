import { HttpClient, Response } from './HttpClient';

export interface Preview {
  readonly title: string,
  readonly slug: string,
  readonly preview: string,
  readonly createdAt: string,
  readonly updatedAt: string|null,
  readonly publishedAt: string|null,
  readonly tags: string[]
}

export interface PostModel extends Preview {
  readonly content: string,
}

export interface Comment {
  readonly guid: string,
  readonly createdAt: string,
  readonly comment: string
}

export interface CommentBranch extends Comment {
  readonly replies: CommentBranch[];
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
  return HttpClient.post(`${process.env.REACT_APP_BACKEND_URL}/api/auth/tokens`, {
    login,
    password,
  });
}

function createPost(
  title: string,
  preview: string,
  content: string,
  tags: string[],
): Promise<Response<{ slug: string }>> {
  return HttpClient.post(`${process.env.REACT_APP_BACKEND_URL}/api/posts`, {
    title,
    preview,
    content,
    tags,
  });
}

function editPost(
  slug: string,
  newData: {
    preview: string;
    title: string;
    content: string;
    tags: string[],
  },
): Promise<Response<PostModel>> {
  return HttpClient.patch(`${process.env.REACT_APP_BACKEND_URL}/api/posts/${slug}`, {
    title: newData.title,
    preview: newData.preview,
    content: newData.content,
    tags: newData.tags,
  });
}

function publishPost(slug: string): Promise<Response<any>> {
  return HttpClient.post(`${process.env.REACT_APP_BACKEND_URL}/api/posts/${slug}/releases`, []);
}

function getPost(slug: string): Promise<Response<PostModel>> {
  return HttpClient.get(`${process.env.REACT_APP_BACKEND_URL}/api/posts/${slug}`);
}

function getPosts(
  page: number,
  limit: number,
  tag?: string | null,
): Promise<Response<PaginatedCollection<Preview>>> {
  const filter = [];
  filter.push(`limit=${limit}`);
  filter.push(`offset=${limit * (page - 1)}`);

  if (tag) {
    filter.push(`tag=${tag}`);
  }

  return HttpClient.get(`${process.env.REACT_APP_BACKEND_URL}/api/posts?${filter.join('&')}`);
}

function getCommentsTree(postSlug: string): Promise<Response<CommentBranch[]>> {
  return HttpClient.get(`${process.env.REACT_APP_BACKEND_URL}/api/posts/${postSlug}/comments`);
}

function addComment(
  toPost: string,
  comment: string,
  replyTo?: Comment,
): Promise<Response<Comment>> {
  let endpoint = `${process.env.REACT_APP_BACKEND_URL}/api/posts/${toPost}/comments`;

  if (replyTo !== undefined) {
    endpoint += `/${replyTo.guid}`;
  }

  return HttpClient.post(endpoint, {
    text: comment,
  });
}

const API = {
  createToken,
  createPost,
  editPost,
  publishPost,
  getPost,
  getPosts,
  getCommentsTree,
  addComment,
};

export default API;
