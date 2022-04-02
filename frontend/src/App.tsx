import { Route, Switch } from 'react-router-dom';
import PostList from './post/PostList';
import Post from './post/Post';
import Header from './Header';
import Footer from './Footer';
import NotFound from './NotFound';
import CV from './CV';
import Admin from './admin/Admin';

function App() {
  // @ts-ignore
  return (
    <>
      <Header />
      <main className="container-fluid container-md">
        <Switch>
          <Route exact path="/blog/tags/:tag([A-Za-z]+)/page/:page(\d+)" component={PostList} />
          <Route exact path="/blog/tags/:tag([A-Za-z]+)" component={PostList} />
          <Route exact path="/blog/page/:page(\d+)?" component={PostList} />
          <Route exact path="/blog/:slug([A-Za-z0-9_-]+)" component={Post} />
          <Route exact path="/blog" component={PostList} />
          <Route exact path="/cv" component={CV} />
          <Route exact path="/admin" component={Admin} />
          <Route exact path="/" component={PostList} />
          <Route path="*" component={NotFound} />
        </Switch>
      </main>
      <Footer />
    </>
  );
}

export default App;
