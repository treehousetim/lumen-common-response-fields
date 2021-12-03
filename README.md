# lumen-common-response-fields
Provides a middleware to use in a lumen project to provide common output fields

## fields added to response output
  * `httpCode`
  * `request_utc`

# Example:

## Success 

```
{
    "httpCode": 200,
    "request_utc": "2021-12-02 17:27:32"
}
```

## Fail

```
{
    "httpCode": 422,
    "request_utc": "2021-12-02 17:48:37"
}
```
