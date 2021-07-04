import {useEffect, useState} from "react";
import HttpError from "../basetypes/HttpError";
import {Alert, Spinner} from "react-bootstrap";

type Comment = {
    guid: string,
    creationDate: string,
    comment: string,
    replies: Comment[]
};

function CommentList(props: {postSlug: string}) {
    const [error, setError] = useState<HttpError | null>();
    const [isLoading, setLoading] = useState(false);
    const [comments, setComments] = useState<Comment[] | null>(null);

    useEffect(() => {
        setLoading(true);

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
        const creationDate = (new Date(comment.creationDate)).toLocaleDateString(
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
            <>
                <div className="comment" key={comment.guid}>
                    <p className="pub-date">{creationDate}</p>
                    <p className="comment-text">
                        {comment.comment}
                        <div>
                            <button>reply</button>
                        </div>
                    </p>
                    {replies}
                </div>
            </>
        );
    }

    return (
        <div className="comments clearfix">
            {comments.map(showComment)}
        </div>
    );
}

export default CommentList;
