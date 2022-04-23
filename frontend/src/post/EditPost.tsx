import { Button, Form } from 'react-bootstrap';
import { useEffect, useState } from 'react';
import { useHistory } from 'react-router-dom';
import API from '../utils/API';
import Logger from '../utils/Logger';
import AdminAccess from '../admin/AdminAccess';

export default function EditPost(props: { match: { params: { slug: string } } }) {
  const [title, setTitle] = useState<string>('');
  const [preview, setPreview] = useState<string>('');
  const [content, setContent] = useState<string>('');
  const [tags, setTags] = useState<string[]>([]);
  const [isLoading, setLoading] = useState<boolean>(true);

  const history = useHistory();
  const { match: { params } } = props;

  useEffect(() => {
    API.getPost(params.slug)
      .then((result) => {
        if (result.isSuccessful()) {
          const post = result.getData();
          setTitle(post.title);
          setPreview(post.preview);
          setContent(post.content);
          setTags(post.tags);
        } else {
          Logger.error(result.getError().message);
        }
      })
      .catch((err) => {
        Logger.error(err);
      })
      .finally(() => setLoading(false));
  }, [params.slug]);

  const handleSubmit = () => {
    if (isLoading) {
      return;
    }

    if (title === '' || preview === '' || content === '') {
      return;
    }

    setLoading(true);

    API.editPost(
      params.slug,
      {
        title,
        preview,
        content,
        tags,
      },
    )
      .then((result) => {
        if (result.isSuccessful()) {
          history.push(`/blog/${result.getData().slug}`);
        } else {
          Logger.error(result.getError().message);
        }
      })
      .catch((err) => {
        Logger.error(err);
      })
      .finally(() => setLoading(false));
  };

  return (
    <AdminAccess>
      <Form.Group className="mb-3" controlId="title">
        <Form.Label>Title</Form.Label>
        <Form.Control
          disabled={isLoading}
          type="text"
          placeholder="Set title"
          value={title}
          onChange={(e) => setTitle(e.target.value)}
        />
      </Form.Group>
      <Form.Group className="mb-3" controlId="intro">
        <Form.Label>Intro/Preview</Form.Label>
        <Form.Control
          disabled={isLoading}
          as="textarea"
          rows={4}
          placeholder="Write short intro"
          value={preview}
          onChange={(e) => setPreview(e.target.value)}
        />
      </Form.Group>
      <Form.Group className="mb-3" controlId="content">
        <Form.Label>Content</Form.Label>
        <Form.Control
          disabled={isLoading}
          as="textarea"
          placeholder="Write post content"
          value={content}
          rows={10}
          onChange={(e) => setContent(e.target.value)}
        />
      </Form.Group>
      <Form.Group className="mb-3" controlId="tags">
        <Form.Label>Tags</Form.Label>
        <Form.Control
          disabled={isLoading}
          type="text"
          placeholder="Set tags separated by comma"
          value={tags}
          onChange={(e) => setTags(e.target.value.split(',').map((tag: string) => tag.trim()))}
        />
      </Form.Group>
      <Button variant="primary" type="submit" onClick={handleSubmit} disabled={isLoading}>
        Save
      </Button>
    </AdminAccess>
  );
}
