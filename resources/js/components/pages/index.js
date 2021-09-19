import { useState } from 'react'
import validator from 'validator'
import axios from 'axios'
import Card from './card'

const Index = (props) => {
    const [text, setText] = useState('')
    const [loading, setLoading] = useState(false)
    const [reload, setReload] = useState(true)
    const [data, setData] = useState([])
    const [error, setError] = useState('')

    const sendRequest = () => {
        setLoading(true)
        let url = new URL(text)
        axios.post('/crawl', { url, reload })
            .then(response => {
                setData(response.data)
                setLoading(false)
                setReload(false)
            }).catch(error => { 
                console.log('error:: ', error.response.data)
                setLoading(false)
            })
    }

    const handleSubmit = async (e) => {
        e.preventDefault()
        if (error.length < 1){
            sendRequest()
        }
    }

    const handleChange = async (e) => {
        setText(e.target.value)
        if (validator.isURL(e.target.value, { require_protocol: true, require_host: true })) setError('')
        else setError('Invalid url')
    }

    const handleNewSearch = async (e) => {
        e.preventDefault()
        setReload(true)
        setData([])
    }

    const handleContinue = async (e) => {
        e.preventDefault()
        sendRequest()
    }

    return (
        <div>
            <div className="jumbotron jumbotron-fluid text-center mt-3">
                <div className="container">
                    <h1 className="display-4">Spidey</h1>
                    <p className="lead">Spidey is an online tool that helps you to read and crawl any website content</p>
                    <hr className="my-4" />
                    <form onSubmit={handleSubmit}>
                        <div className="input-group mb-3">
                            <input type="text" pattern="https?://.+" 
                                className="form-control" 
                                onChange={handleChange} 
                                value={text} 
                                disabled={loading || !reload}
                                placeholder="Enter Url..." required />
                            <div className="input-group-append">
                                <button className="btn btn-outline-secondary" disabled={loading || !reload} >Crawl</button>
                            </div>
                        </div>
                        { error.length > 0 && <div className='text-danger'>{error}</div> }
                    </form>
                    {!loading && !reload && <div class="btn-group" >
                        <button type="button" class="btn btn-primary" onClick={handleNewSearch}>New Search</button>
                        <button type="button" class="btn btn-success" onClick={handleContinue}>Continue</button>
                    </div>}
                    {loading && <div className="d-flex justify-content-center"><div className="spinner-border text-primary"><span className="sr-only">Loading...</span></div></div>}
                </div>
            </div>
            <div>
                {data.map((data, key) => {        
                    return (<Card key={key} data={data} />)
                })}
            </div>
        </div>
      )
}

export default Index