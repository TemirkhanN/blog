import {Button, Form} from "react-bootstrap";
import {useState} from "react";
import {API} from "../utils/API";
import {useHistory} from "react-router-dom";

export default function AddPost() {
    const [title, setTitle] = useState<string>('');
    const [preview, setPreview] = useState<string>('');
    const [content, setContent] = useState<string>('');
    const [tags, setTags] = useState<string[]>([]);
    const [isLoading, setLoading] = useState<boolean>(false);

    let history = useHistory();

    const handleSubmit = () => {
        if (isLoading) {
            return;
        }

        if (title === '' || preview === '' || content === '') {
            return;
        }

        setLoading(true);

        API.createPost(title, preview, content, tags)
            .then((response) => {
                if (response.status === 201) {
                    history.push('/blog/'+ response.data.slug)
                }
            })
            .catch((err) => {
                console.log(err);
            }).finally(() => setLoading(false))

    }

    return (
        <>
            <Form.Group className="mb-3" controlId="login">
                <Form.Label>Title</Form.Label>
                <Form.Control
                    type="text"
                    placeholder="Set title"
                    onChange={(e) => setTitle(e.target.value)}
                />
            </Form.Group>
            <Form.Group className="mb-3" controlId="login">
                <Form.Label>Intro/Preview</Form.Label>
                <Form.Control
                    as="textarea"
                    rows={4}
                    placeholder="Write short intro"
                    onChange={(e) => setPreview(e.target.value)}
                />
            </Form.Group>
            <Form.Group className="mb-3" controlId="login">
                <Form.Label>Content</Form.Label>
                <Form.Control
                    as="textarea"
                    placeholder="Write post content"
                    rows={10}
                    onChange={(e) => setContent(e.target.value)}
                />
            </Form.Group>
            <Button variant="primary" type="submit" onClick={handleSubmit} disabled={isLoading}>
                Publish
            </Button>
        </>
    );
}
