import { FormEvent, useState } from 'react';
import { Alert, Button } from 'react-bootstrap';
import API, { Comment } from '../utils/API';
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
    <div className="comment-form form-group">
      <label htmlFor="comment-text">
        {
                replyToComment !== undefined
                && (
                  <>
                    <p>replying to:</p>
                    <p>
                      <blockquote>{replyToComment.comment}</blockquote>
                    </p>
                  </>
                )
              }
      </label>
      <textarea
        name="comment-text"
        required
        rows={4}
        className="form-control"
        value={comment}
        onChange={(e) => {
          setComment(e.target.value);
          setError(null);
        }}
      />
      <br />
      <Button
        onClick={addComment}
        size="sm"
        disabled={isLoading && comment.length > 30}
        className="btn btn-success"
      >
        Add comment
      </Button>
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
