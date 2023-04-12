# File Library

The file library is much simpler but also works with S3 and local storage. To associate files to your model, use the `HasFiles` and `HandleFiles` traits, the `$filesParams` configuration and the `files` form field.

When it comes to using those data model files in the frontend site, there are a few methods on the `HasFiles` trait that will help you to retrieve direct URLs. You can find the full reference in the [HasFiles API documentation](https://twillcms.com/docs/api/3.x/A17/Twill/Models/Behaviors/HasFiles.html)

:::alert=type.info:::
The file library can be used to upload files of any type and to attach those files to records using the `file` form field.
For example, you could store video files and render them on your frontend, with a CDN on top of it. We recommend YouTube and Vimeo for regular video embeds, but for muted, decorative, autoplaying videos, .mp4 files in the file library can be a great solution.
:::#alert:::
