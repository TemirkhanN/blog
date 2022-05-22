import { Helmet } from 'react-helmet-async';
import { Alert, Spinner } from 'react-bootstrap';
import { useEffect, useState } from 'react';
import TagList from './TagList';
import HttpError from '../basetypes/HttpError';
import Disclaimer from '../Disclaimer';
import CommentsTree from '../comment/CommentsTree';
import API, { PostModel } from '../utils/API';
import PostControl from '../admin/PostControl';
import Markdown from '../utils/Markdown';

export default function PostView(props: { match: { params: { slug: string } } }) {
  const [error, setError] = useState<HttpError | null>();
  const [isLoading, setLoading] = useState(true);
  const [post, setPost] = useState<PostModel | null>(null);
  const { match: { params } } = props;

  useEffect(() => {
    API.getPost(params.slug)
      .then((result) => {
        if (result.isSuccessful()) {
          setPost(result.getData());
        } else {
          setError(result.getError());
        }
      })
      .then(() => setLoading(false));
  }, [params.slug]);

  if (error) {
    return (
      <div>
        <Helmet>
          <title>Error</title>
        </Helmet>
        <Alert variant="danger">
          Error:
          {' '}
          {error.message}
        </Alert>
      </div>
    );
  }

  if (isLoading) {
    return (
      <div>
        <Helmet>
          <title>...</title>
        </Helmet>
        <Spinner animation="grow" variant="success" />
      </div>
    );
  }

  if (post === null) {
    return (
      <div>
        <Helmet>
          <title>Error</title>
        </Helmet>
        <Alert variant="danger">
          Unexpected workflow error occurred! Post is null!
        </Alert>
      </div>
    );
  }

  const content = Markdown.renderExtended(post.content);

  const publishedAt = (new Date(post.publishedAt ?? post.createdAt))
    .toLocaleDateString(
      'en-gb',
      {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      },
    );

  /* eslint-disable react/no-danger */
  return (
    <>
      <div className="post">
        <Helmet>
          <title>{post.title}</title>
        </Helmet>
        <h1>{post.title}</h1>
        <PostControl post={post} />
        <div>
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="15"
            height="15"
            fill="currentColor"
            className="bi bi-calendar"
            viewBox="0 0 16 16"
          >
            <path
              d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"
            />
          </svg>
          <span className="pub-date">{publishedAt}</span>
        </div>
        <TagList tags={post.tags} />
        <div className="content" dangerouslySetInnerHTML={{ __html: content }} />
      </div>
      <Disclaimer />
      <CommentsTree postSlug={post.slug} />
    </>
  );
}
