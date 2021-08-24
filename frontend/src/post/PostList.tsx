import {Link, useParams, generatePath} from "react-router-dom";
import HttpError from "../basetypes/HttpError";
import PostPreview from "./PostPreview";
import {Helmet} from "react-helmet-async";
import Preview from "./Type/Preview"
import {Alert, Spinner} from "react-bootstrap";
import {useEffect, useState} from "react";

type PostCollection = {
    data: Preview[],
    pagination: {
        limit: number,
        offset: number,
        total: number
    }
}

function PostList() {
    const routerParams = useParams<{ tag: string | undefined, page: string }>();

    const [error, setError] = useState<HttpError | null>();
    const [postsCollection, setPosts] = useState<PostCollection | null>(null);
    const [isLoading, setLoading] = useState(true);
    const page = {
        tag: (typeof routerParams.tag === 'string') ? routerParams.tag : null,
        number: parseInt(routerParams.page) > 0 ? parseInt(routerParams.page) : 1,
        itemsPerPage: 10
    };

    const fetchPosts = () => {
        let filter = [];
        filter.push('limit=' + page.itemsPerPage);
        filter.push('offset=' + page.itemsPerPage * (page.number - 1));

        if (page.tag) {
            filter.push('tag=' + page.tag);
        }

        fetch(process.env.REACT_APP_BACKEND_URL + "/api/posts?" + filter.join('&'))
            .then(res => res.json())
            .then(
                (res) => {
                    // TODO not always posts because it does not check status code
                    setPosts(res);
                },
                // Note: it's important to handle errors here
                // instead of a catch() block so that we don't swallow
                // exceptions from actual bugs in components.
                (error) => {
                    setError(error);
                }
            )
            .then(() => setLoading(false));
    }

    useEffect(() => {
        fetchPosts();
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [page.tag, page.number, page.itemsPerPage]);

    const title = () => {
        let title = 'Blog posts';
        if (page.tag) {
            title = 'Blog posts tagged ' + page.tag;
        }

        return (
            <Helmet>
                <title>{title}</title>
            </Helmet>
        );
    }

    const errorPage = (errorMessage: string) => {
        return (
            <div>
                <Helmet>
                    <title>Error</title>
                </Helmet>
                <Alert variant="danger">
                    Error: {errorMessage}
                </Alert>
            </div>
        );
    }

    const pagination = (paginationInfo: PostCollection["pagination"]) => {
        const generateRoute = (pageNumber: number, tagName: string | null) => {
            if (page.tag != null) {
                return generatePath('/blog/tags/:tag/page/:page', {
                    // @ts-ignore
                    tag: tagName,
                    page: pageNumber
                });
            }

            return generatePath('/blog/page/:page', {page: pageNumber});
        };

        const newerPostsExists = page.number > 1;
        const olderPostsExists = paginationInfo.total > paginationInfo.offset + page.itemsPerPage;

        if (!newerPostsExists && !olderPostsExists) {
            return;
        }

        return (
            <nav className="pagination" aria-label="pagination">
                <ul className="pagination justify-content-center">
                    {
                        newerPostsExists &&
                        <li className="page-item">
                            <Link to={generateRoute(page.number - 1, page.tag)} className="page-link">
                                &laquo; Newer
                            </Link>
                        </li>
                    }
                    {
                        olderPostsExists &&
                        <li className="page-item">
                            <Link to={generateRoute(page.number + 1, page.tag)} className="page-link">
                                Older &raquo;
                            </Link>
                        </li>
                    }
                </ul>
            </nav>
        );
    }

    if (error) {
        return errorPage(error.message);
    }

    if (isLoading) {
        return (
            <>
                <Helmet>
                    <title>Blog posts</title>
                </Helmet>
                <Spinner animation="grow" variant="success"/>
            </>
        );
    }

    if (postsCollection === null) {
        return errorPage('Unexpected error. Post collection is not defined...');
    }

    return (
        <div className="posts">
            {title()}
            {(postsCollection.data.map(post => (
                <PostPreview post={post} key={post.slug}/>
            )))}
            {pagination(postsCollection.pagination)}
        </div>
    );
}

export default PostList;
