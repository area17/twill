# Upgrade to 2.x -> 3.x

With Twill 3.x some files and classes are moved.

We provide an automated upgrade path using `php artisan twill:upgrade`.


What changed:

- The `resources/views/admin` folder should be renamed `resources/views/twill` 
- The `routes/admin.php` file should be renamed to `routes/twill.php`

Namespace changes:
```
app/Http/Controllers/Admin -> app/Http/Controllers/Twill
app/Http/Requests/Admin -> app/Http/Requests/Twill
```
