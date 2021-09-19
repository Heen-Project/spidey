import React from 'react'
import ReactDOM from 'react-dom'
import { BrowserRouter, Route, Switch } from 'react-router-dom'
import IndexPage from './pages/index'

const App = (props) => {
    return (
        <BrowserRouter>
          <div className="container">
            <Switch>
                <Route exact path='/' component={IndexPage} /> 
            </Switch>
          </div>
        </BrowserRouter>
      )
}

ReactDOM.render(<App />, document.getElementById('app'))