# lumen-common-response-fields
Provides a trait to use in controllers in a lumen project to provide common output fields

## fields added to response output
  * `success_msg`
  * `error_msg`
  * `httpCode`
  * `request_utc`

# Example:

## Success 

```
{
    "success_msg": "Success",
    "error_msg": null,
    "httpCode": 200,
    "request_utc": "2021-12-02 17:27:32"
}
```

## Fail

```
{
    "success_msg": null,
    "error_msg": "Only one address per user",
    "httpCode": 422,
    "request_utc": "2021-12-02 17:48:37"
}
```