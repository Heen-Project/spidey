const Card = ({data}) => {
    return (
        <div className="card my-4" >
            <img className="card-img-top" src={(data.image_path)? data.image_path : 'storage/no-image.jpg'} />
            <div className="card-body">
                <a href={(data.url)? data.url : '#'}><h5 className="card-title">{(data.title)? data.title : 'No title'}</h5></a>
                <code>{(data.url)? data.url : '#'}</code>
            </div>
            <ul className="list-group list-group-flush">
                <li className="list-group-item">Hostname: <code>{(data.hostname)? data.hostname : ''}</code></li>
                <li className="list-group-item">Path: <code>{(data.url_path)? data.url_path : ''}</code></li>
                <li className="list-group-item">Status: <code>{(data.status)? data.status : ''}</code></li>
                <li className="list-group-item">Description: <code>{(data.description)? data.description : ''}</code></li>
            </ul>
            <div className="card-footer">
              <small className="text-muted">{(data.date_http_header)? data.date_http_header : ''}</small>
            </div>
        </div>
    )
}

export default Card