import { useEffect, useState } from 'react';
import { Alert, Button, Spinner } from 'react-bootstrap';
import HttpError from '../basetypes/HttpError';
import { API, Comment, CommentBranch } from '../utils/API';
import NewComment from './NewComment';

function CommentsTree({ postSlug }: { postSlug: string }) {
  const [error, setError] = useState<HttpError | null>();
  const [isLoading, setLoading] = useState(true);
  const [comments, setComments] = useState<CommentBranch[]>([]);
  const [replyTo, setReplyTo] = useState<Comment | undefined>();

  useEffect(() => {
    API.getCommentsTree(postSlug)
      .then((result) => {
        if (result.isSuccessful()) {
          setComments(result.getData());
        } else {
          setError(result.getError());
        }
      })
      .then(() => setLoading(false));
  }, [postSlug]);

  const onCommentAdd = (newComment: Comment) => {
    const commentsTree = comments.slice();
    const newCommentBranch = {
      guid: newComment.guid,
      createdAt: newComment.createdAt,
      comment: newComment.comment,
      replies: [],
    };

    if (replyTo !== undefined) {
      const findComment = (withGuid: string, inTree: CommentBranch[]): CommentBranch | null => {
        /* eslint-disable-next-line no-restricted-syntax */
        for (const existingComment of inTree) {
          if (existingComment.guid === withGuid) {
            return existingComment;
          }

          const commentInBranch = findComment(withGuid, existingComment.replies);
          if (commentInBranch !== null) {
            return commentInBranch;
          }
        }

        return null;
      };

      findComment(replyTo.guid, commentsTree)?.replies.unshift(newCommentBranch);
    } else {
      commentsTree.unshift(newCommentBranch);
    }

    setComments(commentsTree);
    setReplyTo(undefined);
  };

  if (error) {
    return (
      <div>
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
      <Spinner animation="grow" variant="success" />
    );
  }

  const showComment = (comment: CommentBranch, depth: number) => {
    const createdAt = (new Date(comment.createdAt)).toLocaleDateString(
      'en-gb',
      {
        hour: '2-digit',
        minute: '2-digit',
        year: 'numeric',
        month: 'short',
        day: 'numeric',
      },
    );
    let replies;
    if (comment.replies) {
      replies = comment.replies.map((item) => showComment(item, depth));
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
              onClick={() => setReplyTo(comment)}
            >
              reply
            </Button>
          </div>
        </div>
        {replies}
      </div>
    );
  };

  if (replyTo === undefined) {
    return (
      <div className="comments clearfix">
        <NewComment postSlug={postSlug} onCommentAdd={onCommentAdd} />
        {comments.map(showComment)}
      </div>
    );
  }

  return (
    <div className="comments clearfix">
      <NewComment postSlug={postSlug} replyToComment={replyTo} onCommentAdd={onCommentAdd} />
      {comments.map(showComment)}
    </div>
  );
}

export default CommentsTree;
