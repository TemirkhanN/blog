import { Link } from 'react-router-dom';
import { Remarkable } from 'remarkable';
import TagList from './TagList';
import { Preview } from '../utils/API';

const markdownRenderer = new Remarkable();

function PostPreview(props: { post: Preview }) {
  const { post } = props;

  const content = markdownRenderer.render(post.preview);

  const publishedAt = (new Date(post.publishedAt)).toLocaleDateString(
    'en-gb',
    {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
    },
  );

  /* eslint-disable react/no-danger */
  return (
    <div className="post-preview">
      <Link className="preview-link" to={`/blog/${post.slug}`}>{post.title}</Link>
      <div>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="15"
          height="15"
          fill="currentColor"
          className="bi bi-calendar"
          viewBox="0 0 16 16"
        >
          <path
            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"
          />
        </svg>
        <span className="pub-date">{publishedAt}</span>
      </div>
      <TagList tags={post.tags} />
      <div className="preview" dangerouslySetInnerHTML={{ __html: content }} />
    </div>
  );
}

export default PostPreview;
