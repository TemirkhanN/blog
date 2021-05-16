import * as React from "react";
import HttpError from "../basetypes/HttpError";
import PostPreview from "./PostPreview";
import Preview from "./Type/Preview"

type PostCollection = {
    data: Preview[],
    pagination: {
        limit: number,
        offset: number,
        total: number
    }
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
                {(posts.data.map(post => (
                    <PostPreview post={post} key={post.slug}/>
                )))}
            </div>
        );
    }
}

export default PostList;