import {Remarkable} from "remarkable";
import {useEffect, useState} from "react";
import {Helmet} from "react-helmet";
import {Alert, Spinner} from "react-bootstrap";
import * as React from "react";
import HttpError from "./basetypes/HttpError";

const markdownRenderer = new Remarkable();

function CV() {
    const [mdContent, setMdContent] = useState<string|null>(null);
    const [isLoading, setLoading] = useState(false);
    const [error, setError] = useState<HttpError | null>(null);

    const cvPath = 'https://raw.githubusercontent.com/TemirkhanN/cv/master/README.md';

    useEffect(() => {
        setLoading(true);

        fetch(cvPath)
            .then(res => res.text())
            .then(
                res => setMdContent(res),
                error => setError(error)
            )
            .then(() => setLoading(false));
    }, [cvPath]);

    if (isLoading) {
        return (
            <>
                <Helmet>
                    <title>CV Temirkhan Nasukhov</title>
                </Helmet>
                <Spinner animation="grow" variant="success"/>
            </>
        );
    }

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

    // TODO between fetching/loading and rendering there is a gap when content is null and it renders into error
    if (mdContent === null) {
        return (
            <div>
                <Helmet>
                    <title>Error</title>
                </Helmet>
                <Alert variant="danger">
                    Error: markdown content is null
                </Alert>
            </div>
        );
    }

    return (
        <>
            <Helmet>
                <title>CV Temirkhan Nasukhov</title>
            </Helmet>
            <div dangerouslySetInnerHTML={{__html: markdownRenderer.render(mdContent)}}/>
        </>
    );
}

export default CV;
