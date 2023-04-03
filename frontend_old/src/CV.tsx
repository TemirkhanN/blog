import { Remarkable } from 'remarkable';
import { useEffect, useState } from 'react';
import { Helmet } from 'react-helmet-async';
import { Alert, Spinner } from 'react-bootstrap';
import HttpError from './basetypes/HttpError';

const markdownRenderer = new Remarkable();

function CV() {
  const [mdContent, setMdContent] = useState<string>('');
  const [isLoading, setLoading] = useState(false);
  const [error, setError] = useState<HttpError | null>(null);

  const cvPath = process.env.REACT_APP_CV_MARKDOWN_LINK ?? '';

  useEffect(() => {
    setLoading(true);

    fetch(cvPath)
      .then((res) => res.text())
      .then(
        (res) => setMdContent(res),
        (err) => setError(err),
      )
      .then(() => setLoading(false));
  }, [cvPath]);

  if (isLoading) {
    return (
      <>
        <Helmet>
          <title>
            CV
            {process.env.REACT_APP_AUTHOR_NAME}
          </title>
        </Helmet>
        <Spinner animation="grow" variant="success" />
      </>
    );
  }

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

  /* eslint-disable react/no-danger */
  return (
    <>
      <Helmet>
        <title>
          CV
          {process.env.REACT_APP_AUTHOR_NAME}
        </title>
      </Helmet>
      <div dangerouslySetInnerHTML={{ __html: markdownRenderer.render(mdContent) }} />
    </>
  );
}

export default CV;
