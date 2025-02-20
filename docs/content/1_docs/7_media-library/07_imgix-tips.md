# Imgix tips

When setting up an Imgix source for local uploads, choose the `Web Folder` source type and specify your domain in the `Base URL` settings.

![screenshot](/assets/imgix_source.png)

When setting up an Imgix source for S3 or Azure, create another IAM user for Imgix with read-only access to your bucket and use its credentials to create a source. You can use the following IAM permission:

```json
{
  "Statement": [
    {
      "Effect": "Allow",
      "Action": [
        "s3:GetObject",
        "s3:ListBucket",
        "s3:GetBucketLocation"
      ],
      "Resource": [
        "arn:aws:s3:::YOUR_BUCKET_IDENTIFIER/*",
        "arn:aws:s3:::YOUR_BUCKET_IDENTIFIER"
      ]
    }
  ]
}
```
