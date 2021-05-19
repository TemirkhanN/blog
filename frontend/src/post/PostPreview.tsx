import * as React from "react";
import {Link} from "react-router-dom";
import Preview from "./Type/Preview";
import {Remarkable} from 'remarkable';
import TagList from "./TagList";

class PostPreview extends React.Component<{ post: Preview }, {}> {
    render() {
        const md = new Remarkable();
        const content = md.render(this.props.post.preview);

        const publishedAt = (new Date(this.props.post.publishedAt)).toLocaleDateString(
            'en-gb',
            {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            }
        );

        return (
            <>
                <div className="post-preview">
                    <Link className="preview-link" to={"/posts/" + this.props.post.slug}>{this.props.post.title}</Link>
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor"
                             className="bi bi-calendar" viewBox="0 0 16 16">
                            <path
                                d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                        </svg>
                        <span className="pub-date">{publishedAt}</span>
                    </div>
                    <TagList tags={this.props.post.tags}/>
                    <div className="preview" dangerouslySetInnerHTML={{__html: content}}/>
                </div>
            </>
        );
    }
}

export default PostPreview;