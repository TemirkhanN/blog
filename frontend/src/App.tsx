import PostList from "./post/PostList";
import * as React from "react";
import {Route, Switch} from "react-router-dom";
import Post from "./post/Post";
import Header from "./Header";
import Footer from "./Footer";

function App() {
    return (
        <div className="application-class">
            <Header/>
            <main>
                <Switch>
                    <Route path="/posts/:slug" component={Post}/>
                    <Route path='/tags/:tag' component={PostList}/>
                    <Route exact path='/' component={PostList}/>
                </Switch>
            </main>
            <Footer/>
        </div>
    );
}

export default App;
