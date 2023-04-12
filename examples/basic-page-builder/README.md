# `examples/basic-page-builder`

This example `basic-page-builder` comes with:

* A page module
* A blade based frontend
* 2 example blocks to use in the block builder
* A navigation module
* A homepage setting

# Setup

## Laravel

To install, let's open up our directory where we work from. Then we use a composer project to install a new Laravel project:

```
composer create-project laravel/laravel laravel-twill
cd laravel-twill
```

In this directory you will have a full Laravel directory structure. Depending on your local development environment go ahead and link it so that you can open it in your web browser.

If you do not have a local environment, you can use `php artisan serve` to run Laravel's built in web server.

You will need a MySQL or SQLite database set up for this example.

Open up the `.env` file in the root of the project, and fill in your database details. 

The [Laravel documentation](https://laravel.com/docs/10.x) is far more extensive about the setup procedure, if this guide went too quick, make sure to give those a read.

## Twill and this example

Lets require Twill:

```
composer require area17/twill:"^3.0"
```

And then, install this example:

```
php artisan twill:install basic-page-builder
```

## Front end

This example uses Tailwind for styling, which is compiled by Vite, so we need to install a few front end dependencies:

```
npm install
npm run build
```

# Adding entries to the database

Everything is installed and the CSS is built, but you'll find a 404 if you visit this project in your browser - so lets add some content to your database.

In your browser, go to your admin path, something like `https://admin.laravel-twill.test` if you followed this guide. And login with the details you entered when you installed Twill.

Firstly, welcome to Twill CMS. You're going to love how straight forward using Twill is. 

At the top of the screen you'll see a menu bar, click on "pages" and then click on "Add new". Give the page a title and click "Create". You can now add content blocks to the page using the "Add content" button. Once you have added some page content, publish the page by toggling the switch in the status bar at the top of the right hand side toolbar and click "Update". 
Repeat this process to add a couple more pages.

Next, go to the "Settings" page and select one of your generated pages as the homepage.

Lastly, lets add some items to the navigation menu. Go to the "Menu" page and click "Add new", give your menu item a name, toggle the status from "Draft" to "Live" and click "Create". In the "Content" block, you'll see an "Add Page" button which will allow you to link one of your generated pages to the menu item. 
Repeat this to link up all the pages you made.

In your browser, head back to `https://laravel-twill.test` et voilà - you'll see your content and a navigation bar.

<br><br><br><br>
Copyright © AREA 17
