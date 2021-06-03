import PostList from "./post/PostList";
import * as React from "react";
import {Route, Switch} from "react-router-dom";
import Post from "./post/Post";
import Header from "./Header";
import Footer from "./Footer";
import NotFound from "./NotFound";

function App() {
    return (
        <div className="application-class">
            <Header/>
            <main>
                <Switch>
                    <Route exact path='/blog/tags/:tag([A-Za-z]+)/page/:page(\d+)' component={PostList}/>
                    <Route exact path='/blog/tags/:tag([A-Za-z]+)' component={PostList}/>
                    <Route exact path='/blog/page/:page(\d+)?' component={PostList}/>
                    <Route exact path='/blog/:slug([A-Za-z0-9_-]+)' component={Post}/>
                    <Route exact path='/blog' component={PostList}/>
                    <Route exact path='/' component={PostList}/>
                    <Route path="*">
                        <NotFound/>
                    </Route>
                </Switch>
            </main>
            <Footer/>
        </div>
    );
}

export default App;
