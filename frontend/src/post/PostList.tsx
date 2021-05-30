import * as React from "react";
import HttpError from "../basetypes/HttpError";
import PostPreview from "./PostPreview";
import {Helmet} from "react-helmet";
import Preview from "./Type/Preview"
import {Alert, Spinner} from "react-bootstrap";

type PostCollection = {
    data: Preview[],
    pagination: {
        limit: number,
        offset: number,
        total: number
    }
}

class PostList extends React.Component<{ match: { params: { tag: string } } }, { error: HttpError | null, isLoaded: boolean, posts: PostCollection | null }> {
    constructor(props: { match: { params: { tag: string } } }) {
        super(props);
        this.state = {
            error: null,
            isLoaded: false,
            posts: null
        };
    }

    componentDidMount() {
        this.fetchPosts(this.props.match.params.tag);
    }

    componentDidUpdate(prevProps: Readonly<{ match: { params: { tag: string } } }>, prevState: Readonly<{ error: HttpError | null; isLoaded: boolean; posts: PostCollection | null }>, snapshot?: any) {
        if (prevProps.match.params.tag === this.props.match.params.tag) {
            return;
        }

        this.fetchPosts(this.props.match.params.tag);
    }

    render() {
        const {error, isLoaded, posts} = this.state;

        if (error) {
            return (
                <div>
                    <Helmet>
                        <title>Error</title>
                    </Helmet>
                    <Alert variant="danger">
                        Error: {error.message}
                    </Alert>
                </div>
            );
        }

        if (!isLoaded) {
            return (
                <>
                    <Helmet>
                        <title>Blog posts</title>
                    </Helmet>
                    <Spinner animation="grow" variant="success"/>
                </>
            );
        }

        if (posts === null) {
            return <div>Unexpected error. Post collection is not defined...</div>;
        }

        let title = 'Blog posts';
        if (this.props.match.params.tag) {
            title = 'Blog posts tagged ' + this.props.match.params.tag;
        }
        return (
            <div className="posts">
                <Helmet>
                    <title>{title}</title>
                </Helmet>
                {(posts.data.map(post => (
                    <PostPreview post={post} key={post.slug}/>
                )))}
            </div>
        );
    }

    private fetchPosts(tag?: string) {
        this.setState({isLoaded: false});

        const tagFilter = tag ? "?tag=" + tag : '';
        fetch(process.env.REACT_APP_BACKEND_URL + "/api/posts" + tagFilter)
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
}

export default PostList;