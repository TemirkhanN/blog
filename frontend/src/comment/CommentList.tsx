import {useEffect, useState} from "react";
import HttpError from "../basetypes/HttpError";
import {Alert, Button, Spinner} from "react-bootstrap";
import {CommentReference, NewComment} from "./NewComment";

type Comment = {
    guid: string,
    createdAt: string,
    comment: string,
    replies: Comment[]
};

function CommentList(props: { postSlug: string }) {
    const [error, setError] = useState<HttpError | null>();
    const [isLoading, setLoading] = useState(true);
    const [comments, setComments] = useState<Comment[] | null>(null);
    const [replyTo, setReplyTo] = useState<CommentReference|null>(null);

    useEffect(() => {
        fetch(process.env.REACT_APP_BACKEND_URL + "/api/posts/" + props.postSlug + "/comments")
            .then(res => res.json())
            .then(
                (result: Comment[]) => setComments(result),
                (error: HttpError) => setError(error)
            )
            .then(() => setLoading(false));
    }, [props.postSlug]);

    if (error) {
        return (
            <div>
                <Alert variant="danger">
                    Error: {error.message}
                </Alert>
            </div>
        );
    }

    if (isLoading) {
        return (
            <>
                <Spinner animation="grow" variant="success"/>
            </>
        );
    }

    if (comments === null) {
        return (
            <div>
                <Alert variant="danger">
                    Error: Comments are not loaded
                </Alert>
            </div>
        );
    }

    const showComment = (comment: Comment, depth: number) => {
        const createdAt = (new Date(comment.createdAt)).toLocaleDateString(
            'en-gb',
            {
                hour: '2-digit',
                minute: '2-digit',
                year: 'numeric',
                month: 'short',
                day: 'numeric',
            }
        );
        let replies;
        if (comment.replies) {
            replies =  comment.replies.map((item) => showComment(item, depth));
        }

        return (
            <div className="comment" key={comment.guid}>
                <p className="pub-date">{createdAt}</p>
                <div className="comment-text">
                    {comment.comment}
                    <div>
                        <Button
                            size="sm"
                            className="btn btn-primary"
                            onClick={(e) => {
                                setReplyTo({guid: comment.guid, comment: comment.comment})
                            }}
                        >reply</Button>
                    </div>
                </div>
                {replies}
            </div>
        );
    }

    return (
        <div className="comments clearfix">
            <NewComment postSlug={props.postSlug} replyToComment={replyTo}/>
            {comments.map(showComment)}
        </div>
    );
}

export default CommentList;
