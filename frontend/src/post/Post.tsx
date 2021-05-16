import * as React from "react";
import HttpError from "../basetypes/HttpError"
import {Remarkable} from "remarkable";

type PostModel = {
    slug: string,
    title: string,
    content: string,
    publishedAt: string
}

class Post extends React.Component<{match:{params: {slug: string}}}, { error: HttpError | null, isLoaded: boolean, post: PostModel | null }> {
    constructor(props: {match:{params: {slug: string}}}) {
        super(props);
        this.state = {
            error: null,
            isLoaded: false,
            post: null
        };
    }

    componentDidMount() {
        fetch(process.env.REACT_APP_BACKEND_URL + "/api/posts/" + this.props.match.params.slug)
            .then(res => res.json())
            .then(
                (result: PostModel) => {
                    this.setState({
                        isLoaded: true,
                        post: result
                    });
                },
                // Note: it's important to handle errors here
                // instead of a catch() block so that we don't swallow
                // exceptions from actual bugs in components.
                (error) => {
                    this.setState({
                        isLoaded: true,
                        error
                    });
                }
            )
    }

    render() {
        const {error, isLoaded, post} = this.state;
        if (error) {
            return <div>Error: {error.message}</div>;
        } else if (!isLoaded) {
            return <div>Loading...</div>;
        }

        // todo Do we really need to check this?
        if (post === null) {
            return <div>Runtime error...</div>;
        }

        const md = new Remarkable();
        const content = md.render(post.content);

        const publishedAt = (new Date(post.publishedAt)).toLocaleDateString(
            'en-gb',
            {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            }
        );

        return (
            <div className="post">
                <h1>{post.title}</h1>
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
                <div className="content" dangerouslySetInnerHTML={{__html: content}}/>
            </div>
        );
    }
}

export default Post;