import * as React from "react";
import HttpError from "../basetypes/HttpError";
import {Link} from "react-router-dom";

type PostPreview = {
    title: string,
    slug: string,
    preview: string
}

type PostCollection = {
    items: PostPreview[]
}

class PostList extends React.Component<{}, { error: HttpError | null, isLoaded: boolean, posts: PostCollection | null }> {
    constructor(props: {}) {
        super(props);
        this.state = {
            error: null,
            isLoaded: false,
            posts: null
        };
    }

    componentDidMount() {
        fetch(process.env.REACT_APP_BACKEND_URL + "/api/posts")
            .then(res => res.json())
            .then(
                (result) => {
                    this.setState({
                        isLoaded: true,
                        posts: result
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
        const {error, isLoaded, posts} = this.state;

        if (error) {
            return <div>Error: {error.message}</div>;
        } else if (!isLoaded) {
            return <div>Loading...</div>;
        }

        if (posts === null) {
            return <div>Unexpected error. Post collection is not defined...</div>;
        }

        return (
            <div className="posts">
                {(posts.items.map(post => (
                    <div className="post" key={post.slug}>
                        <div className="post-title">
                            <Link to={"/posts/" + post.slug}>{post.title}</Link>
                        </div>
                        <div className="content">
                            {post.preview}
                        </div>
                    </div>
                )))}
            </div>
        );
    }
}

export default PostList