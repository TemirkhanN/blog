import { Remarkable } from 'remarkable';
import { Helmet } from 'react-helmet-async';
import { Alert, Button, Spinner } from 'react-bootstrap';
import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import TagList from './TagList';
import HttpError from '../basetypes/HttpError';
import Disclaimer from '../Disclaimer';
import CommentsTree from '../comment/CommentsTree';
import API, { PostModel } from '../utils/API';
import AdminAccess from '../admin/AdminAccess';

export default function Post(props: { match: { params: { slug: string } } }) {
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

  const md = new Remarkable();
  const content = md.render(post.content);

  const publishedAt = (new Date(post.publishedAt)).toLocaleDateString(
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
        <AdminAccess>
          <Link to={`/blog/${post.slug}/edit`}>
            <Button variant="primary">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="16"
                height="16"
                fill="currentColor"
                className="bi bi-pencil-square"
                viewBox="0 0 16 16"
              >
                <path
                  d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"
                />
                <path
                  fillRule="evenodd"
                  d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"
                />
              </svg>
              Edit
            </Button>
          </Link>
        </AdminAccess>
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
