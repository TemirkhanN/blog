import HttpError from "../basetypes/HttpError"
import {Remarkable} from "remarkable";
import TagList from "./TagList";
import {Helmet} from "react-helmet-async";
import {Alert, Spinner} from "react-bootstrap";
import {useEffect, useState} from "react";
import Disclaimer from "../Disclaimer";
import CommentsTree from "../comment/CommentsTree";
import {API, PostModel} from "../utils/API";

export default function Post(props: { match: { params: { slug: string } } }) {
    const [error, setError] = useState<HttpError | null>();
    const [isLoading, setLoading] = useState(true);
    const [post, setPost] = useState<PostModel | null>(null);

    useEffect(() => {
        API.getPost(props.match.params.slug)
            .then((result) => {
                if (result.isSuccessful()) {
                    setPost(result.getData());
                } else {
                    setError(result.getError());
                }
            })
            .then(() => setLoading(false));
    }, [props.match.params.slug]);

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

    if (isLoading) {
        return (
            <div>
                <Helmet>
                    <title>...</title>
                </Helmet>
                <Spinner animation="grow" variant="success"/>
            </div>
        );
    }

    if (post === null) {
        return (
            <div>
                <Helmet>
                    <title>Error</title>
                </Helmet>
                <Alert variant="danger">
                    Unexpected workflow error occurred! Post is null!
                </Alert>
            </div>
        );
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
        <>
            <div className="post">
                <Helmet>
                    <title>{post.title}</title>
                </Helmet>
                <h1>{post.title}</h1>
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor"
                         className="bi bi-calendar" viewBox="0 0 16 16">
                        <path
                            d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                    </svg>
                    <span className="pub-date">{publishedAt}</span>
                </div>
                <TagList tags={post.tags}/>
                <div className="content" dangerouslySetInnerHTML={{__html: content}}/>
            </div>
            <Disclaimer/>
            <CommentsTree postSlug={post.slug}/>
        </>
    );
}
