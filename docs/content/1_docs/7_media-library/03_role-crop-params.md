# Role & Crop Params

Each _Module_ in your application can have its own predefined image *crops* and *roles*.

A _role_ is a way to define different contexts in which an image might be placed. For example, roles for a `People` model could be `profile` and `cover`. This would allow you to include your People model in list and show a cover image for each, or show a single person model with a profile image. You can associate any number of image roles with your Model.

_Crops_ are more self-explanatory. Twill comes with some pre-defined crop settings to allow you to set different variants of a given image, so crops can be used in combination with _roles,_ or they can be used on their own with a single role to define multiple cropping ratios on the same image.

Using the Person example, your `cover` image could have a `square` crop for mobile screens, but could use a `16/9` crop on larger screens. Those values are editable at your convenience for each model, even if there are already some crops created in the CMS.

The only thing you have to do to make it work is to compose your model and repository with the appropriate traits, respectively `HasMedias` and `HandleMedias`, set up your `$mediasParams` configuration and use the `medias` form partial in your form view (more info in the CRUD section).

When it comes to using those data model images in the frontend site, there are a few methods on the `HasMedias` trait that will help you to retrieve them for each of your layouts. You can find the full reference in the [HasMedias API documentation](https://twillcms.com/docs/api/3.x/A17/Twill/Models/Behaviors/HasMedias.html)
