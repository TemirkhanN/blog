import * as React from "react";
import HttpError from "../basetypes/HttpError"

type PostModel = {
    slug: string,
    title: string,
    content: string
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

        return (
            <div className="post">
                <h1>{post.title}</h1>
                <div className="content">
                    {post.content}
                </div>
            </div>
        );
    }
}

export default Post;