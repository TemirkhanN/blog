import { Button, Form } from 'react-bootstrap';
import { useState } from 'react';
import { useHistory } from 'react-router-dom';
import { API } from '../utils/API';
import Logger from '../utils/Logger';

export default function AddPost() {
  const [title, setTitle] = useState<string>('');
  const [preview, setPreview] = useState<string>('');
  const [content, setContent] = useState<string>('');
  const [tags, setTags] = useState<string[]>([]);
  const [isLoading, setLoading] = useState<boolean>(false);

  const history = useHistory();

  const handleSubmit = () => {
    if (isLoading) {
      return;
    }

    if (title === '' || preview === '' || content === '') {
      return;
    }

    setLoading(true);

    API.createPost(title, preview, content, tags)
      .then((result) => {
        if (result.isSuccessful()) {
          history.push(`/blog/${result.getData().slug}`);
        } else {
          Logger.error(result.getError().message);
        }
      })
      .catch((err) => {
        Logger.error(err);
      }).finally(() => setLoading(false));
  };

  return (
    <>
      <Form.Group className="mb-3" controlId="title">
        <Form.Label>Title</Form.Label>
        <Form.Control
          type="text"
          placeholder="Set title"
          onChange={(e) => setTitle(e.target.value)}
        />
      </Form.Group>
      <Form.Group className="mb-3" controlId="intro">
        <Form.Label>Intro/Preview</Form.Label>
        <Form.Control
          as="textarea"
          rows={4}
          placeholder="Write short intro"
          onChange={(e) => setPreview(e.target.value)}
        />
      </Form.Group>
      <Form.Group className="mb-3" controlId="content">
        <Form.Label>Content</Form.Label>
        <Form.Control
          as="textarea"
          placeholder="Write post content"
          rows={10}
          onChange={(e) => setContent(e.target.value)}
        />
      </Form.Group>
      <Form.Group className="mb-3" controlId="tags">
        <Form.Label>Tags</Form.Label>
        <Form.Control
          type="text"
          placeholder="Set tags separated by comma"
          onChange={(e) => setTags(e.target.value.split(',').map((tag: string) => tag.trim()))}
        />
      </Form.Group>
      <Button variant="primary" type="submit" onClick={handleSubmit} disabled={isLoading}>
        Publish
      </Button>
    </>
  );
}
