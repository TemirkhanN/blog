import { Link } from 'react-router-dom';
import { Button, Spinner } from 'react-bootstrap';
import { useState } from 'react';
import API, { Preview } from '../utils/API';
import AdminAccess from './AdminAccess';
import Logger from '../utils/Logger';

export default function PostControl(props: {post: Preview}) {
  const { post } = props;

  const [isProcessing, setProcessing] = useState(false);
  const [isPostPublished, setPostPublished] = useState(post.publishedAt !== null);

  const publish = () => {
    if (isPostPublished || isProcessing) {
      return;
    }
    setProcessing(true);

    API.publishPost(post.slug)
      .then((result) => {
        if (result.isSuccessful()) {
          setPostPublished(true);
        } else {
          Logger.error(result.getError().message);
        }
      })
      .finally(() => setProcessing(false));
  };

  return (
    <AdminAccess>
      <Link to={`/blog/${post.slug}/edit`}>
        <Button variant="primary" size="sm">
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
      {
        !isPostPublished
        && (
          <Button variant="success" disabled={isProcessing} onClick={publish} size="sm">
            {
              isProcessing
                ? <Spinner as="span" animation="border" size="sm" role="status" />
                : 'Publish'
            }
          </Button>
        )
      }
    </AdminAccess>
  );
}
