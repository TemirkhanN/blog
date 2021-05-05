import PostList from "./post/PostList";
import * as React from "react";
import {Link, Route, Switch} from "react-router-dom";
import Post from "./post/Post";

function App() {
    return (
        <div className="application-class">
            <header>
                <Link to="/">Home</Link>
            </header>
            <Switch>
                <Route path="/posts/:slug" component={Post}/>
                <Route exact path='/' component={PostList}/>
            </Switch>
            <footer>
                ©{process.env.REACT_APP_AUTHOR_NAME}
            </footer>
        </div>
    );
}

export default App;
