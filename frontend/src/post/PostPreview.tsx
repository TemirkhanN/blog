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
                    <p className="pub-date">{publishedAt}</p>
                    <TagList tags={this.props.post.tags}/>
                    <div className="preview" dangerouslySetInnerHTML={{__html: content}}/>
                </div>
            </>
        );
    }
}

export default PostPreview;