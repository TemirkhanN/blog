import {FormEvent, useState} from "react";
import axios from "axios";
import {Alert, Button} from "react-bootstrap";

export type CommentReference = {
    guid: string
    comment: string
}

export function NewComment(props: { "postSlug": string, replyToComment: CommentReference | null }) {
    const [comment, setComment] = useState("");
    const [isLoading, setLoading] = useState(false);
    const [error, setError] = useState<string|null>();

    let endpoint = process.env.REACT_APP_BACKEND_URL + "/api/posts/" + props.postSlug + "/comments";
    if (props.replyToComment !== null) {
        endpoint += '/' + props.replyToComment.guid;
    }

    const addComment = (e: FormEvent) => {
        e.preventDefault();

        if (isLoading) {
            return;
        }

        if (comment === '' || comment.length < 30) {
            setError("Meaningful comment has to have at least 5-6 words.")

            return;
        }

        setLoading(true);
        setError(null);
        axios
            .post(endpoint, {
                "text": comment
            })
            .then((response) => {
                if (response.status === 201) {
                    setComment("");
                }
            })
            .catch((err) => {
                console.log(err);
            }).finally(() => setLoading(false))
    }

    return (
        <div className="comment-form">
            {
                props.replyToComment != null &&
                <div>
                    <p>replying to:</p>
                    <p>{props.replyToComment.comment}</p>
                </div>
            }
            <form onSubmit={(e) => addComment(e)}>
                <textarea
                    value={comment}
                    onChange={(e) => {
                        setComment(e.target.value);
                        setError(null);
                    }}
                /><br/>
                <Button size="sm" disabled={isLoading && comment.length > 30} type="submit" className="btn btn-success">Add comment</Button>
            </form>
            {
                error != null &&
                <div>
                    <Alert variant="warning">
                        {error}
                    </Alert>
                </div>
            }
        </div>
    )
}
