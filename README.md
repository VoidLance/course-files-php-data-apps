# PHP Data Apps Course Examples

Small, standalone PHP examples for learning how to work with common data in application-style scenarios. The repository covers inventory, shopping carts, bookstore stock, and a simple library system.

## Why this project is useful

Each script demonstrates practical PHP concepts using sample data, with no framework or database setup:

- `inventory_management.php` demonstrates inventory updates, sales, validation, low-stock alerts, discounts, reports, and JSON persistence. It includes both a command-line menu and a browser test form.
- `bookstore_inventory.php` demonstrates adding, removing, updating, displaying, and sorting a bookstore inventory.
- `shopping_cart.php` calculates item and cart discounts, tax, and customer totals, then renders an itemized bill in HTML.
- `library_management.php` uses `Book` and `Library` classes to demonstrate searching, borrowing, returning, and removing books.

These are learning examples, not production-ready applications.

## Getting started

### Requirements

- PHP 8.0 or later
- The PHP `mbstring` extension for `library_management.php`

No Composer packages, database, or other dependencies are required.

### Run from the command line

Clone the repository, change into its directory, and run an example:

```sh
php bookstore_inventory.php
php library_management.php
php inventory_management.php
```

The inventory script runs a sample workflow by default. To open its interactive menu instead:

```sh
php inventory_management.php --interactive
```

The inventory demo writes its resulting sample data to `inventory_data.json` in the project directory. The interactive menu also lets you save and load that file.

### View the web examples

Start PHP's built-in development server from the project directory:

```sh
php -S 127.0.0.1:8000
```

Then open these paths in your browser:

- `http://127.0.0.1:8000/shopping_cart.php` — rendered shopping-cart bills
- `http://127.0.0.1:8000/inventory_management.php` — inventory report and test form
- `http://127.0.0.1:8000/bookstore_inventory.php` — bookstore inventory output
- `http://127.0.0.1:8000/library_management.php` — library demonstration output

The inventory test form accepts an operation, item, and value; its quick links demonstrate validation cases. The built-in server is intended for local development, not production hosting.

## Help and documentation

This repository does not currently include separate documentation or a troubleshooting guide. For questions or to report a problem, [open an issue](https://github.com/VoidLance/course-files-php-data-apps/issues) with the script name, what you expected, and what happened.

## Maintainers and contributing

No individual maintainer or separate `CONTRIBUTING.md` is identified in the repository. Contributions are welcome: open an issue to discuss a proposed change, or submit a pull request with a focused improvement and a description of how you checked it. Keep examples self-contained and consistent with the repository's beginner-friendly scope.
