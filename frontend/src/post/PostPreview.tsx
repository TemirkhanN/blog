import * as React from "react";
import {Link} from "react-router-dom";
import Preview from "./Type/Preview";
import {Remarkable} from 'remarkable';

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
                    <div className="tags">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                             className="bi bi-tags"
                             viewBox="0 0 16 16">
                            <path
                                d="M3 2v4.586l7 7L14.586 9l-7-7H3zM2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586V2z"/>
                            <path
                                d="M5.5 5a.5.5 0 1 1 0-1 .5.5 0 0 1 0 1zm0 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM1 7.086a1 1 0 0 0 .293.707L8.75 15.25l-.043.043a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 0 7.586V3a1 1 0 0 1 1-1v5.086z"/>
                        </svg>
                        <a href="#">Gaming</a>,
                        <a href="#">IT</a>
                    </div>
                    <div className="preview" dangerouslySetInnerHTML={{__html: content}}/>
                </div>
            </>
        );
    }
}

export default PostPreview;