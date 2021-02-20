This is a very basic url shortener. It uses [HashIds](https://hashids.org/) to encode the id.

### Shortening an url

Send a POST request with form data to `/@create` that contains two fields:

- `secret` - The secret token defined in config.
- `url` - Url to shorten.

Upon success it will return HTTP 201 and the shorted link will be in `Location` header,
as well as in the body.

If an error occurs, it will return HTTP 400 instead, and body will contain the error.

- Urls may not contain new lines.
- Urls must pass with `parse_url`.
- Urls must have a host.
- Urls must use `https://` scheme.

Example request:

```bash
curl https://example.com/@create -X POST -F 'secret=FOOBAR' -F 'url=https://example.com'
```
