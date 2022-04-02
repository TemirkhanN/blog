import { FormEvent, useState } from 'react';
import { Alert, Button } from 'react-bootstrap';
import { API, Comment } from '../utils/API';
import Logger from '../utils/Logger';

export default function NewComment(
  props:
    {
      postSlug: string,
      replyToComment?: Comment,
      onCommentAdd: (comment: Comment) => void,
    },
) {
  const [comment, setComment] = useState('');
  const [isLoading, setLoading] = useState(false);
  const [error, setError] = useState<string|null>();

  const { postSlug, onCommentAdd } = props;
  const { replyToComment } = props;

  const addComment = (e: FormEvent) => {
    e.preventDefault();

    if (isLoading) {
      return;
    }

    if (comment === '' || comment.length < 30) {
      setError('Meaningful comment has to have at least 5-6 words.');

      return;
    }

    setLoading(true);
    setError(null);

    API.addComment(postSlug, comment, replyToComment)
      .then((response) => {
        if (response.isSuccessful()) {
          setComment('');
          onCommentAdd(response.getData());
        } else {
          setError(response.getError().message);
        }
      })
      .catch((err) => {
        Logger.error(err);
      }).finally(() => setLoading(false));
  };

  return (
    <div className="comment-form">
      {
        replyToComment !== undefined
                && (
                <div>
                  <p>replying to:</p>
                  <p>{replyToComment.comment}</p>
                </div>
                )
      }
      <form onSubmit={(e) => addComment(e)}>
        <textarea
          value={comment}
          onChange={(e) => {
            setComment(e.target.value);
            setError(null);
          }}
        />
        <br />
        <Button size="sm" disabled={isLoading && comment.length > 30} type="submit" className="btn btn-success">Add comment</Button>
      </form>
      {
        error != null
                && (
                <div>
                  <Alert variant="warning">
                    {error}
                  </Alert>
                </div>
                )
      }
    </div>
  );
}
