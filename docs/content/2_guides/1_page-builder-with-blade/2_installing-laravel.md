# Installing Laravel

Before we dive in, this guide will have some assumptions about knowing Laravel. However, we do our best to make these
guides as starter-friendly as possible.

We do however, expect knowledge about PHP (>8.0), MySQL, Composer and that you already have a development environment setup.

If you have any questions about a step, let us know on our Discord and we will try to help you out!

## Install Laravel

For the installation of Laravel, we will use some steps from the [official documentation](https://laravel.com/docs/10.x).

The official documentation is a goldmine of knowledge, if you ever have trouble understanding a concept, make sure to
check it out!

To install, let's open up our directory where we work from. Then we use a composer project to install a new Laravel
project:

`composer create-project laravel/laravel laravel-twill`

This command will create a directory called `laravel-twill` (You can give it another name if you wish) and will
download and extract all the required packages inside.

Once the command is complete, we can go into the directory using `cd laravel-twill`.

In this directory you will have a full Laravel directory structure. Now, depending on your local development environment
go ahead and link it so that you can open it in your web browser.

If you do not have a local environment, you can use `php artisan serve` to run Laravel's built in web server.

When you visit your website, you should see a simple Laravel landing page with links to the documentation and other
useful websites.

### Configurations

With Laravel up and running, open up the `.env` file in the root of the project and make sure you have a MySQL database
setup.

While you are there, make sure that `APP_URL` matches your local url. For example:

`APP_URL=http://laravel-twill.test`

If you do not have MySQL setup, you can use [SQLite](https://laravel.com/docs/10.x#databases-and-migrations) as well.

As mentioned before, the Laravel documentation is far more extensive about the setup procedure, if this guide went too quick, make sure to give those a read.

### Initialize GIT

Now that that is done, let's do one more (optional) step.

When working with PHP and other code projects, it is recommended to use GIT. GIT is a versioning system that will allow
you to save your work, roll back if needed, but it wil also help you share your work.

We will not go into details about GIT here, but we will run `git init` inside the `laravel-twill` directory, followed by
a `git add .` and `git commit -m "Initial commit"` to add all the work we did in this step to our git repository.
