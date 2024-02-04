# S3 Direct Uploads

On AWS, create a IAM user for access to your S3 bucket and use its credentials in your `.env` file. You can use the following IAM permission:

```json
{
    "Version": "2012-10-17",
    "Statement": [
        {
            "Effect": "Allow",
            "Action": "s3:*",
            "Resource": [
                "arn:aws:s3:::YOUR_BUCKET_IDENTIFIER/*",
                "arn:aws:s3:::YOUR_BUCKET_IDENTIFIER"
            ]
        }
    ]
}
```

For improved security, modify the S3 bucket CORS configuration to accept uploads request from your admin domain only:

```json
[
    {
        "AllowedHeaders": [
            "*"
        ],
        "AllowedMethods": [
            "POST",
            "PUT",
            "DELETE",
            "GET"
        ],
        "AllowedOrigins": [
            "https://YOUR_ADMIN_DOMAIN",
            "http://YOUR_ADMIN_DOMAIN"
        ],
        "ExposeHeaders": []
    }
]
```


```xml
<?xml version="1.0" encoding="UTF-8"?>
<CORSConfiguration xmlns="http://s3.amazonaws.com/doc/2006-03-01/">
    <CORSRule>
        <AllowedOrigin>https://YOUR_ADMIN_DOMAIN</AllowedOrigin>
        <AllowedOrigin>http://YOUR_ADMIN_DOMAIN</AllowedOrigin>
        <AllowedMethod>POST</AllowedMethod>
        <AllowedMethod>PUT</AllowedMethod>
        <AllowedMethod>DELETE</AllowedMethod>
        <MaxAgeSeconds>3000</MaxAgeSeconds>
        <ExposeHeader>ETag</ExposeHeader>
        <AllowedHeader>*</AllowedHeader>
    </CORSRule>
</CORSConfiguration>
```
